"use strict";

const express  = require("express");
const { chromium } = require("playwright");

const PORT        = process.env.PORT        || 3000;
const AUTH_TOKEN  = process.env.AUTH_TOKEN  || "";
const MAX_QUEUE   = parseInt(process.env.MAX_QUEUE   || "5",  10);
const TIMEOUT_MS  = parseInt(process.env.TIMEOUT_MS  || "60000", 10);
const RESTART_AFTER = parseInt(process.env.RESTART_AFTER || "50", 10); // restart browser every N requests

// ── Browser management ───────────────────────────────────────────────
let browser = null;
let requestCount = 0;

async function launchBrowser() {
  browser = await chromium.launch({
    headless: true,
    args: [
      "--no-sandbox", "--disable-setuid-sandbox",
      "--disable-dev-shm-usage", "--disable-gpu",
      "--disable-background-timer-throttling",
      "--disable-renderer-backgrounding",
    ],
  });
  browser.on("disconnected", () => {
    browser = null;
    requestCount = 0;
    console.log("[browser] disconnected — will restart on next request.");
    // Auto-restart after 2s so next request doesn't cold-start
    setTimeout(() => launchBrowser().catch(() => {}), 2000);
  });
  requestCount = 0;
  console.log("[browser] launched.");
  return browser;
}

async function getBrowser() {
  // Periodic restart to free accumulated memory
  if (browser && browser.isConnected() && requestCount >= RESTART_AFTER) {
    console.log(`[browser] restarting after ${RESTART_AFTER} requests…`);
    await browser.close().catch(() => {});
    // 'disconnected' handler will set browser=null and relaunch
    await new Promise(r => setTimeout(r, 2500));
  }
  if (!browser || !browser.isConnected()) {
    await launchBrowser();
  }
  requestCount++;
  return browser;
}

// Pre-warm on startup
launchBrowser().catch(() => {});

// ── Concurrency queue ────────────────────────────────────────────────
let activeCount = 0;
const waitQueue = [];

function acquireSlot() {
  return new Promise((resolve, reject) => {
    if (activeCount < MAX_QUEUE) { activeCount++; resolve(); return; }
    if (waitQueue.length >= MAX_QUEUE * 2) {
      reject(new Error("Server busy — too many concurrent requests."));
      return;
    }
    waitQueue.push({ resolve, reject });
  });
}

function releaseSlot() {
  activeCount = Math.max(0, activeCount - 1);
  if (waitQueue.length) {
    const next = waitQueue.shift();
    activeCount++;
    next.resolve();
  }
}

// ── Block list ───────────────────────────────────────────────────────
const BLOCKED = [
  /doubleclick\.net/, /googlesyndication\.com/, /googletagmanager\.com/,
  /google-analytics\.com/, /ga\.js/, /gtag\/js/, /fbevents\.js/,
  /connect\.facebook\.net/, /hotjar\.com/, /clarity\.ms/,
  /intercom\.io/, /widget\.intercom\.io/,
  /crisp\.chat/, /client\.crisp\.chat/,
  /tawk\.to/, /embed\.tawk\.to/,
  /drift\.com/, /js\.driftt\.com/,
  /zdassets\.com/, /zendesk\.com/,
  /cdn\.cookielaw\.org/, /cookiehub\.com/,
  /cookieyes\.com/, /cookieinformation\.com/,
  /onetrust\.com/, /quantserve\.com/,
];

// ── CSS injected before page scripts ────────────────────────────────
const HIDE_CSS = `
  [class*="cookie"],[id*="cookie"],[class*="consent"],[id*="consent"],
  [class*="gdpr"],[id*="gdpr"],[class*="CookieBanner"],[id*="CookieBanner"],
  [class*="cookie-banner"],[class*="cookie-notice"],[class*="cookie-bar"],
  [class*="onetrust"],[id*="onetrust"],
  [class*="modal"]:not(body),[id*="modal"],
  [class*="overlay"]:not(body),[id*="overlay"],
  [class*="popup"],[id*="popup"],
  [class*="lightbox"],[id*="lightbox"],
  [class*="dialog"]:not(body),[role="dialog"],[aria-modal="true"],
  [class*="newsletter"],[id*="newsletter"],
  [class*="subscribe"],[id*="subscribe"],
  [class*="intercom"],[id*="intercom"],
  [class*="crisp"],[id*="crisp"],
  [class*="tawk"],[id*="tawk"],
  [class*="drift"],[id*="drift"],
  [class*="chat-widget"],[class*="chat_widget"],
  [class*="livechat"],[class*="live-chat"],
  [class*="helpscout"],[id*="helpscout"],
  iframe[src*="intercom"],iframe[src*="tawk"],
  iframe[src*="drift"],iframe[src*="crisp"],
  iframe[src*="zopim"],iframe[src*="zendesk"],
  [class*="sticky-bar"],[class*="sticky-banner"],
  [class*="floating-bar"],[class*="announcement-bar"] {
    display:none!important;visibility:hidden!important;
    opacity:0!important;pointer-events:none!important;
  }
  html,body { overflow:visible!important; position:static!important; }
`;

// ── In-page dismiss function ─────────────────────────────────────────
function inPageDismiss() {
  document.documentElement.style.overflow = "visible";
  document.body.style.overflow = "visible";
  document.body.style.position = "static";
  const DISMISS = [
    "accept","accept all","agree","got it","i agree",
    "close","dismiss","no thanks","maybe later","×","✕","✗",
  ];
  document.querySelectorAll("button,[role=button],a").forEach(el => {
    const t = (el.textContent || el.getAttribute("aria-label") || "").trim().toLowerCase();
    if (DISMISS.some(d => t === d || t.startsWith(d)))
      try { el.click(); } catch(_) {}
  });
  document.querySelectorAll("*").forEach(el => {
    try {
      const s = window.getComputedStyle(el);
      const z = parseInt(s.zIndex, 10);
      if ((s.position === "fixed" || s.position === "sticky") && z > 100) {
        const tag = el.tagName.toLowerCase();
        if (tag !== "nav" && tag !== "header")
          el.style.setProperty("display", "none", "important");
      }
    } catch(_) {}
  });
}

// ── Core screenshot ──────────────────────────────────────────────────
async function takeScreenshot({ url, width, scale }) {
  const b = await getBrowser();
  const ctx = await b.newContext({
    viewport: { width, height: 900 },
    deviceScaleFactor: scale,
    userAgent:
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) " +
      "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
  });

  try {
    const page = await ctx.newPage();

    await page.route("**/*", route =>
      BLOCKED.some(r => r.test(route.request().url()))
        ? route.abort() : route.continue()
    );

    await page.addInitScript(`
      (function() {
        const s = document.createElement("style");
        s.textContent = ${JSON.stringify(HIDE_CSS)};
        const inject = () => document.head && document.head.appendChild(s);
        document.readyState === "loading"
          ? document.addEventListener("DOMContentLoaded", inject)
          : inject();
        const NativeIO = window.IntersectionObserver;
        window.IntersectionObserver = class extends NativeIO {
          constructor(cb, opts) { super(cb, opts); this._cb = cb; }
          observe(el) {
            super.observe(el);
            try {
              const r = el.getBoundingClientRect();
              this._cb([{
                isIntersecting:true, intersectionRatio:1, target:el,
                boundingClientRect:r, intersectionRect:r,
                rootBounds:null, time:performance.now()
              }], this);
            } catch(_) {}
          }
        };
      })();
    `);

    await page.goto(url, { waitUntil: "networkidle", timeout: TIMEOUT_MS });
    await page.evaluate(inPageDismiss);
    await page.waitForTimeout(600);

    // Expand accordions
    await page.evaluate(() => {
      [
        "[aria-expanded='false']",
        "[data-toggle='collapse']","[data-bs-toggle='collapse']",
        ".accordion-toggle",".accordion-header",".accordion-button",
        ".faq-toggle",".faq-question",".faq-header",
        "[class*='accordion'] [class*='header']",
        "[class*='accordion'] [class*='title']",
      ].forEach(sel =>
        document.querySelectorAll(sel).forEach(el => { try { el.click(); } catch(_){} })
      );
    });
    await page.waitForTimeout(500);

    // Adaptive slow scroll — speed based on page height
    await page.evaluate(async () => {
      const pageH = document.body.scrollHeight;
      const step  = pageH > 10000 ? 300 : 200;
      const delay = pageH > 10000 ? 250 : 350;
      await new Promise(resolve => {
        let y = 0;
        const id = setInterval(() => {
          window.scrollBy(0, step);
          y += step;
          if (y >= pageH) { window.scrollTo(0, 0); clearInterval(id); resolve(); }
        }, delay);
      });
    });

    // Force hidden animated elements visible
    await page.evaluate(() => {
      const ANIM = ["aos-animate","animated","wow","is-visible","is-inview","in-view","entered","revealed","show"];
      document.querySelectorAll("*").forEach(el => {
        try {
          const s = window.getComputedStyle(el);
          const hidden =
            parseFloat(s.opacity) < 0.5 ||
            s.visibility === "hidden" ||
            (s.transform !== "none" && s.transform !== "matrix(1, 0, 0, 1, 0, 0)");
          if (hidden) {
            const r = el.getBoundingClientRect();
            if (r.width > 0 || el.children.length > 0) {
              ANIM.forEach(c => el.classList.add(c));
              el.style.setProperty("opacity", "1", "important");
              el.style.setProperty("visibility", "visible", "important");
              el.style.setProperty("transform", "none", "important");
            }
          }
        } catch(_) {}
      });
    });
    await page.waitForTimeout(400);

    await page.evaluate(inPageDismiss);

    // Freeze animations
    await page.evaluate(() => {
      const s = document.createElement("style");
      s.textContent = `*,*::before,*::after{
        animation-play-state:paused!important;
        animation-delay:-9999s!important;
        transition-duration:0s!important;
        transition-delay:0s!important;}`;
      document.head.appendChild(s);
    });
    await page.waitForTimeout(500);

    return await page.screenshot({ fullPage: true, type: "png" });
  } finally {
    await ctx.close();
  }
}

// Retry wrapper — 2 retries with 1.5s delay
async function takeScreenshotWithRetry(params) {
  let lastErr;
  for (let i = 0; i < 3; i++) {
    try {
      return await takeScreenshot(params);
    } catch (err) {
      lastErr = err;
      if (i < 2) {
        console.warn(`[retry ${i+1}] ${params.url} — ${err.message}`);
        await new Promise(r => setTimeout(r, 1500));
      }
    }
  }
  throw lastErr;
}

// ── Express ──────────────────────────────────────────────────────────
const app = express();

app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Authorization, X-Token");
  if (req.method === "OPTIONS") return res.sendStatus(204);
  next();
});

function auth(req, res, next) {
  if (!AUTH_TOKEN) return next();
  const token =
    req.query.token ||
    (req.headers.authorization || "").replace(/^Bearer\s+/i, "");
  if (token !== AUTH_TOKEN)
    return res.status(401).json({ error: "Invalid token." });
  next();
}

app.get("/screenshot", auth, async (req, res) => {
  const { url, width = "1440", scale = "2" } = req.query;
  if (!url) return res.status(400).json({ error: "Missing ?url=" });

  let parsedUrl;
  try {
    parsedUrl = new URL(url);
    if (!["http:","https:"].includes(parsedUrl.protocol))
      throw new Error("Protocol must be http or https.");
  } catch (e) {
    return res.status(400).json({ error: "Invalid URL: " + e.message });
  }

  const w = Math.max(320, Math.min(2560, parseInt(width, 10) || 1440));
  const s = Math.max(1,   Math.min(3,    parseFloat(scale)   || 2));

  try { await acquireSlot(); }
  catch { return res.status(503).json({ error: "Server busy — try again shortly." }); }

  const t0 = Date.now();
  try {
    const buffer = await takeScreenshotWithRetry({ url, width: w, scale: s });
    res.setHeader("Content-Type", "image/png");
    res.setHeader("X-Capture-Ms", Date.now() - t0);
    res.setHeader("Cache-Control", "no-store");
    res.send(buffer);
    console.log(`[ok] ${url} ${w}px@${s}x — ${(buffer.length/1024).toFixed(0)}KB in ${Date.now()-t0}ms`);
  } catch (err) {
    console.error(`[err] ${url} —`, err.message);
    res.status(500).json({ error: err.message || "Screenshot failed." });
  } finally {
    releaseSlot();
  }
});

// ── GET /crawl ───────────────────────────────────────────────────────
app.get("/crawl", auth, async (req, res) => {
  const { url } = req.query;
  if (!url) return res.status(400).json({ error: "Missing ?url=" });

  let parsedUrl;
  try {
    parsedUrl = new URL(url);
    if (!["http:", "https:"].includes(parsedUrl.protocol))
      throw new Error("Protocol must be http or https.");
  } catch (e) {
    return res.status(400).json({ error: "Invalid URL: " + e.message });
  }

  try { await acquireSlot(); }
  catch { return res.status(503).json({ error: "Server busy — try again shortly." }); }

  try {
    const b = await getBrowser();
    const ctx = await b.newContext({
      viewport: { width: 1440, height: 900 },
      userAgent: "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    });

    try {
      const page = await ctx.newPage();

      // Block only heavy media — keep CSS so innerText works correctly
      await page.route("**/*", route => {
        const t = route.request().resourceType();
        if (["image", "media", "font"].includes(t) ||
            BLOCKED.some(r => r.test(route.request().url())))
          return route.abort();
        return route.continue();
      });

      await page.goto(url, { waitUntil: "domcontentloaded", timeout: TIMEOUT_MS });
      await page.waitForTimeout(1200);

      const links = await page.evaluate((baseHref) => {
        const base = new URL(baseHref);
        const seen = new Set();
        const results = [];

        function classify(el) {
          let node = el.parentElement;
          while (node && node !== document.body) {
            const tag = node.tagName.toLowerCase();
            const role = (node.getAttribute("role") || "").toLowerCase();
            const cls  = (node.className || "").toString().toLowerCase();
            const id   = (node.id || "").toLowerCase();
            if (tag === "header" || cls.includes("header") || id.includes("header")) return "header";
            if (tag === "nav" || role === "navigation" || cls.includes("nav") || cls.includes("menu")) return "nav";
            if (tag === "footer" || cls.includes("footer") || id.includes("footer")) return "footer";
            node = node.parentElement;
          }
          return "body";
        }

        document.querySelectorAll("a[href]").forEach(a => {
          try {
            const resolved = new URL(a.getAttribute("href"), base);
            if (resolved.hostname !== base.hostname) return;
            // Normalize: strip hash and trailing slash
            const clean = (resolved.origin + resolved.pathname).replace(/\/$/, "") || resolved.origin;
            if (seen.has(clean)) return;
            // Skip if same as base URL
            const baseClean = (base.origin + base.pathname).replace(/\/$/, "") || base.origin;
            if (clean === baseClean) return;
            seen.add(clean);

            // Try multiple sources for label text
            let text = (a.innerText || a.textContent || "").trim().replace(/\s+/g, " ");
            if (!text) text = (a.getAttribute("aria-label") || "").trim();
            if (!text) text = (a.getAttribute("title") || "").trim();
            if (!text) {
              const img = a.querySelector("img");
              if (img) text = (img.getAttribute("alt") || img.getAttribute("title") || "").trim();
            }
            if (!text) {
              // Last resort: use the URL path segment as label
              const seg = resolved.pathname.replace(/\/$/, "").split("/").filter(Boolean).pop();
              if (seg) text = seg.replace(/[-_]/g, " ");
            }
            if (!text || text.length < 1) return;
            text = text.slice(0, 80);
            results.push({ text, href: clean, section: classify(a) });
          } catch (_) {}
        });

        return results;
      }, url);

      // Try sitemap.xml for extra coverage
      let sitemapLinks = [];
      try {
        const sitemapPage = await ctx.newPage();
        await sitemapPage.goto(parsedUrl.origin + "/sitemap.xml", {
          waitUntil: "domcontentloaded", timeout: 8000
        });
        const content = await sitemapPage.content();
        await sitemapPage.close();

        const existingHrefs = new Set(links.map(l => l.href));
        const locs = content.match(/<loc>([^<]+)<\/loc>/gi) || [];
        for (const loc of locs.slice(0, 60)) {
          const href = loc.replace(/<\/?loc>/gi, "").trim();
          try {
            const u = new URL(href);
            if (u.hostname !== parsedUrl.hostname) continue;
            const clean = (u.origin + u.pathname).replace(/\/$/, "") || u.origin;
            if (existingHrefs.has(clean)) continue;
            existingHrefs.add(clean);
            const slug = u.pathname.replace(/\/$/, "").split("/").filter(Boolean).pop() || "home";
            sitemapLinks.push({ text: slug.replace(/-/g, " "), href: clean, section: "sitemap" });
          } catch (_) {}
        }
      } catch (_) {}

      console.log(`[crawl] ${url} → ${links.length} + ${sitemapLinks.length} sitemap links`);
      res.json({ links: [...links, ...sitemapLinks] });
    } finally {
      await ctx.close();
    }
  } catch (err) {
    console.error("[crawl error]", err.message);
    res.status(500).json({ error: err.message || "Crawl failed." });
  } finally {
    releaseSlot();
  }
});

app.get("/health", (_req, res) => {
  res.json({
    ok: true,
    browser: browser ? browser.isConnected() : false,
    active: activeCount,
    queue: waitQueue.length,
    requests: requestCount,
  });
});

app.listen(PORT, () => {
  console.log(`Screenshot server on port ${PORT}`);
  if (!AUTH_TOKEN) console.warn("WARNING: AUTH_TOKEN not set — server is open.");
});
