"use strict";

const express  = require("express");
const { chromium } = require("playwright");

const PORT       = process.env.PORT       || 3000;
const AUTH_TOKEN = process.env.AUTH_TOKEN || "";   // Set this env var on deploy.
const MAX_QUEUE  = parseInt(process.env.MAX_QUEUE  || "5", 10);
const TIMEOUT_MS = parseInt(process.env.TIMEOUT_MS || "60000", 10);

// ── Browser pool ────────────────────────────────────────────────────
// One long-lived Chromium instance, recreated if it crashes.
let browser = null;

async function getBrowser() {
  if (browser && browser.isConnected()) return browser;
  browser = await chromium.launch({
    headless: true,
    args: [
      "--no-sandbox", "--disable-setuid-sandbox",
      "--disable-dev-shm-usage",          // Needed on Railway / Render.
      "--disable-gpu",
      "--disable-background-timer-throttling",
      "--disable-renderer-backgrounding",
    ],
  });
  browser.on("disconnected", () => { browser = null; });
  return browser;
}

// Pre-warm on startup so first request is fast.
getBrowser().catch(() => {});

// ── Concurrency queue ───────────────────────────────────────────────
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
  activeCount--;
  if (waitQueue.length) {
    const next = waitQueue.shift();
    activeCount++;
    next.resolve();
  }
}

// ── Network patterns to block ───────────────────────────────────────
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

// ── CSS injected before any page script runs ───────────────────────
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

// ── In-page JS: dismiss overlays + remove scroll-lock ──────────────
function inPageDismiss() {
  document.documentElement.style.overflow = "visible";
  document.body.style.overflow = "visible";
  document.body.style.position = "static";
  const DISMISS = [
    "accept","accept all","agree","got it","i agree",
    "close","dismiss","no thanks","maybe later","×","✕","✗",
  ];
  document.querySelectorAll("button,[role=button],a").forEach((el) => {
    const t = (el.textContent || el.getAttribute("aria-label") || "").trim().toLowerCase();
    if (DISMISS.some((d) => t === d || t.startsWith(d))) {
      try { el.click(); } catch (_) {}
    }
  });
  document.querySelectorAll("*").forEach((el) => {
    try {
      const s = window.getComputedStyle(el);
      const z = parseInt(s.zIndex, 10);
      if ((s.position === "fixed" || s.position === "sticky") && z > 100) {
        const tag = el.tagName.toLowerCase();
        if (tag !== "nav" && tag !== "header")
          el.style.setProperty("display", "none", "important");
      }
    } catch (_) {}
  });
}

// ── Core screenshot logic ───────────────────────────────────────────
async function takeScreenshot({ url, width, scale }) {
  const b = await getBrowser();
  const ctx = await b.newContext({
    viewport: { width, height: 900 },
    deviceScaleFactor: scale,
    userAgent:
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) " +
      "AppleWebKit/537.36 (KHTML, like Gecko) " +
      "Chrome/124.0.0.0 Safari/537.36",
  });

  try {
    const page = await ctx.newPage();

    // Block ad/tracker networks.
    await page.route("**/*", (route) =>
      BLOCKED.some((r) => r.test(route.request().url()))
        ? route.abort()
        : route.continue()
    );

    // Inject CSS + IO patch before any page scripts.
    await page.addInitScript(`
      (function() {
        // Hide overlays via CSS as early as possible.
        const s = document.createElement("style");
        s.textContent = ${JSON.stringify(HIDE_CSS)};
        const inject = () => document.head && document.head.appendChild(s);
        document.readyState === "loading"
          ? document.addEventListener("DOMContentLoaded", inject)
          : inject();

        // Patch IntersectionObserver so every observed element gets an
        // immediate full-intersection callback — reveals AOS / ScrollReveal /
        // GSAP ScrollTrigger content without real scrolling.
        const NativeIO = window.IntersectionObserver;
        window.IntersectionObserver = class extends NativeIO {
          constructor(cb, opts) { super(cb, opts); this._cb = cb; }
          observe(el) {
            super.observe(el);
            try {
              const r = el.getBoundingClientRect();
              this._cb([{
                isIntersecting: true, intersectionRatio: 1, target: el,
                boundingClientRect: r, intersectionRect: r,
                rootBounds: null, time: performance.now()
              }], this);
            } catch(_) {}
          }
        };
      })();
    `);

    // Navigate.
    await page.goto(url, { waitUntil: "networkidle", timeout: TIMEOUT_MS });

    // First dismiss pass.
    await page.evaluate(inPageDismiss);
    await page.waitForTimeout(600);

    // Expand accordions / disclosure widgets.
    await page.evaluate(() => {
      [
        "[aria-expanded='false']",
        "[data-toggle='collapse']", "[data-bs-toggle='collapse']",
        ".accordion-toggle", ".accordion-header", ".accordion-button",
        ".faq-toggle", ".faq-question", ".faq-header",
        "[class*='accordion'] [class*='header']",
        "[class*='accordion'] [class*='title']",
      ].forEach((sel) =>
        document.querySelectorAll(sel).forEach((el) => {
          try { el.click(); } catch (_) {}
        })
      );
    });
    await page.waitForTimeout(500);

    // Slow scroll (200 px / 350 ms) to trigger lazy images and
    // any animations the IO patch didn't catch.
    await page.evaluate(async () => {
      await new Promise((resolve) => {
        const step = 200, delay = 350;
        let y = 0;
        const id = setInterval(() => {
          window.scrollBy(0, step);
          y += step;
          if (y >= document.body.scrollHeight) {
            window.scrollTo(0, 0);
            clearInterval(id);
            resolve();
          }
        }, delay);
      });
    });

    // Force all still-hidden animated elements to their final visible state.
    await page.evaluate(() => {
      const ANIM_CLASSES = [
        "aos-animate","animated","wow","is-visible",
        "is-inview","in-view","entered","revealed","show",
      ];
      document.querySelectorAll("*").forEach((el) => {
        try {
          const s = window.getComputedStyle(el);
          const hidden =
            parseFloat(s.opacity) < 0.5 ||
            s.visibility === "hidden" ||
            (s.transform !== "none" && s.transform !== "matrix(1, 0, 0, 1, 0, 0)");
          if (hidden) {
            const r = el.getBoundingClientRect();
            if (r.width > 0 || el.children.length > 0) {
              ANIM_CLASSES.forEach((c) => el.classList.add(c));
              el.style.setProperty("opacity", "1", "important");
              el.style.setProperty("visibility", "visible", "important");
              el.style.setProperty("transform", "none", "important");
            }
          }
        } catch (_) {}
      });
    });
    await page.waitForTimeout(400);

    // Second dismiss pass (accordions may have triggered new overlays).
    await page.evaluate(inPageDismiss);

    // Freeze all ongoing CSS animations / transitions.
    await page.evaluate(() => {
      const s = document.createElement("style");
      s.textContent = `*,*::before,*::after {
        animation-play-state:paused!important;
        animation-delay:-9999s!important;
        transition-duration:0s!important;
        transition-delay:0s!important;
      }`;
      document.head.appendChild(s);
    });
    await page.waitForTimeout(500);

    // Capture.
    const buffer = await page.screenshot({ fullPage: true, type: "png" });
    return buffer;

  } finally {
    await ctx.close();
  }
}

// ── Express app ─────────────────────────────────────────────────────
const app = express();

// CORS — allow any origin (Figma plugin iframes have null origin).
app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "GET, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Authorization, X-Token");
  if (req.method === "OPTIONS") return res.sendStatus(204);
  next();
});

// Auth middleware.
function auth(req, res, next) {
  if (!AUTH_TOKEN) return next();
  const token =
    req.query.token ||
    (req.headers.authorization || "").replace(/^Bearer\s+/i, "");
  if (token !== AUTH_TOKEN)
    return res.status(401).json({ error: "Invalid token." });
  next();
}

// ── GET /screenshot ──────────────────────────────────────────────────
app.get("/screenshot", auth, async (req, res) => {
  const { url, width = "1440", scale = "2" } = req.query;
  if (!url) return res.status(400).json({ error: "Missing ?url=" });

  let parsedUrl;
  try {
    parsedUrl = new URL(url);
    if (!["http:", "https:"].includes(parsedUrl.protocol))
      throw new Error("Protocol must be http or https.");
  } catch (e) {
    return res.status(400).json({ error: "Invalid URL: " + e.message });
  }

  const w = Math.max(320, Math.min(2560, parseInt(width, 10) || 1440));
  const s = Math.max(1, Math.min(3, parseFloat(scale) || 2));

  try {
    await acquireSlot();
  } catch {
    return res.status(503).json({ error: "Server busy — try again shortly." });
  }

  const t0 = Date.now();
  try {
    const buffer = await takeScreenshot({ url, width: w, scale: s });
    const elapsed = Date.now() - t0;
    res.setHeader("Content-Type", "image/png");
    res.setHeader("X-Capture-Ms", elapsed);
    res.setHeader("Cache-Control", "no-store");
    res.send(buffer);
    console.log(`[${new Date().toISOString()}] ${url} ${w}px@${s}x → ${(buffer.length/1024).toFixed(0)} KB in ${elapsed}ms`);
  } catch (err) {
    console.error(`[ERROR] ${url} —`, err.message);
    res.status(500).json({ error: err.message || "Screenshot failed." });
  } finally {
    releaseSlot();
  }
});

// ── GET /health ──────────────────────────────────────────────────────
app.get("/health", (_req, res) => {
  res.json({ ok: true, active: activeCount, queue: waitQueue.length });
});

// ── Start ─────────────────────────────────────────────────────────────
app.listen(PORT, () => {
  console.log(`Screenshot server running on port ${PORT}`);
  if (!AUTH_TOKEN)
    console.warn("WARNING: AUTH_TOKEN not set — server is open to anyone.");
});
