// Playwright full-page screenshot test
// Usage: node screenshot.js <url> [viewport_width] [scale]
// Example: node screenshot.js https://example.com 1440 2

const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const url   = process.argv[2] || "https://example.com";
const width = parseInt(process.argv[3] || "1440", 10);
const scale = parseFloat(process.argv[4] || "2");

// Network patterns to block outright.
const BLOCKED_PATTERNS = [
  /doubleclick\.net/, /googlesyndication\.com/, /googletagmanager\.com/,
  /google-analytics\.com/, /ga\.js/, /gtag\/js/, /fbevents\.js/,
  /connect\.facebook\.net/, /hotjar\.com/, /clarity\.ms/,
  /intercom\.io/, /widget\.intercom\.io/,
  /crisp\.chat/, /client\.crisp\.chat/,
  /tawk\.to/, /embed\.tawk\.to/,
  /drift\.com/, /js\.driftt\.com/,
  /zdassets\.com/, /zendesk\.com/,
  /hubspot\.com\/hs\/hubs-web\.js/,
  /cdn\.cookielaw\.org/, /cookiehub\.com/,
  /cookieyes\.com/, /cookieinformation\.com/,
  /onetrust\.com/, /quantserve\.com/,
];

// CSS to nuke every known overlay/modal/banner/chat pattern.
const HIDE_CSS = `
  /* Cookie / consent banners */
  [class*="cookie"], [id*="cookie"],
  [class*="consent"], [id*="consent"],
  [class*="gdpr"], [id*="gdpr"],
  [class*="CookieBanner"], [id*="CookieBanner"],
  [class*="cookie-banner"], [class*="cookie-notice"],
  [class*="cookie-bar"], [class*="cookie-policy"],
  [class*="onetrust"], [id*="onetrust"],

  /* Generic modals and overlays */
  [class*="modal"]:not(body), [id*="modal"],
  [class*="overlay"]:not(body), [id*="overlay"],
  [class*="popup"], [id*="popup"],
  [class*="lightbox"], [id*="lightbox"],
  [class*="dialog"]:not(body), [role="dialog"],
  [aria-modal="true"],

  /* Newsletter / email capture */
  [class*="newsletter"], [id*="newsletter"],
  [class*="subscribe"], [id*="subscribe"],

  /* Chat widgets */
  [class*="intercom"], [id*="intercom"],
  [class*="crisp"], [id*="crisp"],
  [class*="tawk"], [id*="tawk"],
  [class*="drift"], [id*="drift"],
  [class*="chat-widget"], [class*="chat_widget"],
  [class*="livechat"], [class*="live-chat"],
  [class*="helpscout"], [id*="helpscout"],
  iframe[src*="intercom"], iframe[src*="tawk"],
  iframe[src*="drift"], iframe[src*="crisp"],
  iframe[src*="zopim"], iframe[src*="zendesk"],

  /* Floating CTAs / banners */
  [class*="sticky-bar"], [class*="sticky-banner"],
  [class*="floating-bar"], [class*="announcement-bar"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
  }

  /* Stop body scroll-lock that overlays add */
  html, body {
    overflow: visible !important;
    position: static !important;
  }
`;

// JS that runs in the page to aggressively dismiss overlays.
function inPageDismiss() {
  // Remove scroll locks.
  document.documentElement.style.overflow = "visible";
  document.body.style.overflow = "visible";
  document.body.style.position = "static";

  // Click "Accept", "Close", "Dismiss", "No thanks" buttons inside overlays.
  const DISMISS_TEXTS = [
    "accept", "accept all", "agree", "got it", "i agree",
    "close", "dismiss", "no thanks", "maybe later", "×", "✕", "✗"
  ];
  document.querySelectorAll("button, [role=button], a").forEach((el) => {
    const t = (el.textContent || el.getAttribute("aria-label") || "").trim().toLowerCase();
    if (DISMISS_TEXTS.some((d) => t === d || t.startsWith(d))) {
      try { el.click(); } catch (_) {}
    }
  });

  // Remove elements with high z-index that are fixed/sticky (overlays).
  document.querySelectorAll("*").forEach((el) => {
    try {
      const s = window.getComputedStyle(el);
      const z = parseInt(s.zIndex, 10);
      const pos = s.position;
      if ((pos === "fixed" || pos === "sticky") && z > 100) {
        const tag = el.tagName.toLowerCase();
        if (tag !== "nav" && tag !== "header") {
          el.style.setProperty("display", "none", "important");
        }
      }
    } catch (_) {}
  });
}

(async () => {
  console.log("URL      :", url);
  console.log("Viewport :", width + "px");
  console.log("Scale    :", scale + "x\n");

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width, height: 900 },
    deviceScaleFactor: scale,
    userAgent:
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) " +
      "AppleWebKit/537.36 (KHTML, like Gecko) " +
      "Chrome/124.0.0.0 Safari/537.36",
  });
  const page = await context.newPage();

  // Block ad/tracker/chat networks before they load.
  await page.route("**/*", (route) => {
    if (BLOCKED_PATTERNS.some((r) => r.test(route.request().url())))
      return route.abort();
    route.continue();
  });

  // Inject CSS + dismissal logic before any script runs.
  await page.addInitScript(`
    (function() {
      const s = document.createElement("style");
      s.textContent = ${JSON.stringify(HIDE_CSS)};
      document.addEventListener("DOMContentLoaded", () => {
        document.head.appendChild(s);
        ${inPageDismiss.toString()}
        inPageDismiss();
      }, { once: true });
    })();
  `);

  const t0 = Date.now();
  console.log("Loading page…");
  await page.goto(url, { waitUntil: "networkidle", timeout: 30000 });

  // Run dismissal again after full load (some overlays inject late).
  await page.evaluate(inPageDismiss);
  await page.waitForTimeout(800);
  await page.evaluate(inPageDismiss); // third pass — some fire on delay
  await page.waitForTimeout(400);

  // Slow scroll: triggers IntersectionObserver, scroll animations, lazy images.
  console.log("Scrolling to trigger animations and lazy-load…");
  await page.evaluate(async () => {
    // Patch IntersectionObserver so every observed element gets a
    // full-intersection callback the moment it's observed — this forces
    // AOS, ScrollReveal, GSAP ScrollTrigger, and similar libs to reveal
    // content without waiting for the element to actually enter the viewport.
    const NativeIO = window.IntersectionObserver;
    window.IntersectionObserver = class extends NativeIO {
      observe(el) {
        super.observe(el);
        try {
          const rect = el.getBoundingClientRect();
          this._forceCallback([{
            isIntersecting: true, intersectionRatio: 1,
            target: el, boundingClientRect: rect,
            intersectionRect: rect, rootBounds: null, time: performance.now()
          }], this);
        } catch (_) {}
      }
      // Store callback reference on construction.
      constructor(cb, opts) { super(cb, opts); this._forceCallback = cb; }
    };

    // Re-observe all already-registered elements so the patch applies.
    // (Some libs observe in module-init before our patch — nothing we can do
    // about those, the slow scroll below will catch them.)

    // Slow scroll: 200px steps, 350ms apart.
    // Most enter/reveal CSS transitions are 300–700ms, so this ensures they
    // finish before the next step fires and before the screenshot is taken.
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

  // Final dismiss pass + freeze all animations so screenshot is fully rendered.
  await page.evaluate(inPageDismiss);
  await page.evaluate(() => {
    const style = document.createElement("style");
    style.textContent = `
      *, *::before, *::after {
        animation-play-state: paused !important;
        animation-delay: -9999s !important;
        transition-duration: 0s !important;
        transition-delay: 0s !important;
      }
    `;
    document.head.appendChild(style);
  });
  await page.waitForTimeout(800);

  // Screenshot.
  const filename = "screenshot-" + new URL(url).hostname.replace(/^www\./, "") + ".png";
  const outPath = path.join(__dirname, filename);
  await page.screenshot({ path: outPath, fullPage: true });

  const elapsed = ((Date.now() - t0) / 1000).toFixed(1);
  const { size } = fs.statSync(outPath);
  const dims = await page.evaluate(() => ({
    w: document.documentElement.scrollWidth,
    h: document.documentElement.scrollHeight
  }));

  await browser.close();

  console.log("\nDone in", elapsed + "s");
  console.log("Output  :", filename);
  console.log("Size    :", (size / 1024 / 1024).toFixed(2) + " MB");
  console.log("Page    :",
    dims.w + "×" + dims.h + "px (CSS) →",
    Math.round(dims.w * scale) + "×" + Math.round(dims.h * scale) + "px (actual)");
})();
