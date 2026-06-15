// Playwright full-page screenshot test
// Usage: node screenshot.js <url> [viewport_width] [scale]
// Example: node screenshot.js https://example.com 1440 2

const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const url    = process.argv[2] || "https://example.com";
const width  = parseInt(process.argv[3] || "1440", 10);
const scale  = parseFloat(process.argv[4] || "2");

(async () => {
  console.log("URL      :", url);
  console.log("Viewport :", width + "px");
  console.log("Scale    :", scale + "x");
  console.log("");

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width, height: 900 },
    deviceScaleFactor: scale,
  });
  const page = await context.newPage();

  // Block ads, trackers, cookie banners, chat widgets.
  await page.route("**/*", (route) => {
    const url = route.request().url();
    const blocked = [
      /doubleclick\.net/, /googlesyndication\.com/, /googletagmanager\.com/,
      /google-analytics\.com/, /analytics\.js/, /fbevents\.js/,
      /connect\.facebook\.net/, /hotjar\.com/, /intercom\.io/,
      /crisp\.chat/, /tawk\.to/, /drift\.com/, /zendesk\.com/,
    ];
    if (blocked.some((r) => r.test(url))) return route.abort();
    route.continue();
  });

  // Hide cookie banners and popups via CSS injection.
  await page.addInitScript(() => {
    const style = document.createElement("style");
    style.textContent = `
      [class*="cookie"], [id*="cookie"],
      [class*="consent"], [id*="consent"],
      [class*="gdpr"], [id*="gdpr"],
      [class*="popup"], [class*="modal"],
      [class*="intercom"], [class*="crisp"],
      [class*="chat-widget"], [class*="livechat"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
      }
    `;
    document.head.appendChild(style);
  });

  const t0 = Date.now();
  console.log("Loading page…");
  await page.goto(url, { waitUntil: "networkidle", timeout: 30000 });

  // Scroll to trigger lazy-loaded content.
  console.log("Scrolling to trigger lazy-load…");
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      let y = 0;
      const step = 400;
      const delay = 120;
      const timer = setInterval(() => {
        window.scrollBy(0, step);
        y += step;
        if (y >= document.body.scrollHeight) {
          window.scrollTo(0, 0);
          clearInterval(timer);
          resolve();
        }
      }, delay);
    });
  });

  // Extra wait for any deferred images.
  await page.waitForTimeout(1500);

  // Take the screenshot.
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

  console.log("");
  console.log("Done in", elapsed + "s");
  console.log("Output  :", filename);
  console.log("Size    :", (size / 1024 / 1024).toFixed(2) + " MB");
  console.log("Page    :", dims.w + "×" + dims.h + "px (CSS), " +
    Math.round(dims.w * scale) + "×" + Math.round(dims.h * scale) + "px (actual)");
})();
