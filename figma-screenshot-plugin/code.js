// Website Screenshot — Figma plugin sandbox (code.js)
//
// Responsibilities:
//   • Persist settings + API keys in figma.clientStorage (reliable across
//     sessions, unlike the UI iframe's localStorage).
//   • Self-update: cache a newer UI build and show it instead of the
//     bundled one — no fragile document.write inside the iframe.
//   • Assemble received image tiles into a single named frame. Tiling is
//     required because Figma caps images at 4096px per dimension while
//     full-page screenshots are frequently much taller.

"use strict";

const BASE_VERSION = "1.5.4";          // Must match CURRENT_VERSION in ui.html.
const STORE_KEY    = "ws:settings";    // clientStorage: user settings + keys.
const UI_CACHE_KEY = "ws:ui-cache";    // clientStorage: { version, html }.
const WINDOW = { width: 380, height: 560, title: "Website Screenshot" };

function versionGT(a, b) {
  const x = String(a).split(".").map(Number);
  const y = String(b).split(".").map(Number);
  for (let i = 0; i < 3; i++) {
    if ((x[i] || 0) > (y[i] || 0)) return true;
    if ((x[i] || 0) < (y[i] || 0)) return false;
  }
  return false;
}

function clamp(v, min, max) { return Math.max(min, Math.min(max, v)); }

// ── Boot: show the newest available UI ──────────────────────────────
(async function boot() {
  let html = __html__;
  try {
    const cache = await figma.clientStorage.getAsync(UI_CACHE_KEY);
    if (cache && cache.html && versionGT(cache.version, BASE_VERSION)) {
      html = cache.html;               // A newer build was installed earlier.
    } else if (cache) {
      // Bundled version has caught up — drop the stale cache.
      await figma.clientStorage.deleteAsync(UI_CACHE_KEY).catch(() => {});
    }
  } catch (_) {}
  figma.showUI(html, WINDOW);
})();

// ── Messages from the UI ────────────────────────────────────────────
figma.ui.onmessage = async (msg) => {
  try {
    switch (msg.type) {
      case "ui-ready": {
        const settings = (await figma.clientStorage.getAsync(STORE_KEY)) || {};
        figma.ui.postMessage({ type: "init", settings });
        break;
      }
      case "save-settings":
        await figma.clientStorage.setAsync(STORE_KEY, msg.settings || {});
        break;
      case "add-image":
        await addImage(msg);
        break;
      case "start-bulk":
        await startBulk(msg);
        break;
      case "add-bulk-page":
        await addBulkPage(msg);
        break;
      case "finish-bulk":
        finishBulk(msg);
        break;
      case "cancel-bulk":
        cancelBulk();
        break;
      case "apply-update":
        await figma.clientStorage.setAsync(UI_CACHE_KEY, {
          version: msg.version, html: msg.html
        });
        figma.notify("Updated to v" + msg.version);
        figma.showUI(msg.html, WINDOW);   // Swap the running UI immediately.
        break;
      case "resize":
        figma.ui.resize(clamp(msg.width, 340, 900), clamp(msg.height, 420, 1000));
        break;
      case "notify":
        figma.notify(msg.message, msg.options || {});
        break;
      case "close":
        figma.closePlugin();
        break;
    }
  } catch (err) {
    const message = (err && err.message) ? err.message : String(err);
    figma.ui.postMessage({ type: "error", message });
    figma.notify("Failed: " + message, { error: true });
  }
};

// ── Shared: assemble tiles into a single node ───────────────────────
async function assembleTiles(tiles, totalWidth, totalHeight, name) {
  if (tiles.length === 1) {
    const t = tiles[0];
    const image = figma.createImage(new Uint8Array(t.data));
    const rect = figma.createRectangle();
    rect.name = name;
    rect.resize(t.width, t.height);
    rect.fills = [{ type: "IMAGE", scaleMode: "FILL", imageHash: image.hash }];
    return rect;
  }
  const frame = figma.createFrame();
  frame.name = name;
  frame.resize(totalWidth, totalHeight);
  frame.clipsContent = true;
  frame.fills = [];
  let i = 0;
  for (const t of tiles) {
    i++;
    const image = figma.createImage(new Uint8Array(t.data));
    const rect = figma.createRectangle();
    rect.name = "slice-" + i;
    rect.resize(t.width, t.height);
    rect.x = 0;
    rect.y = t.y;
    rect.fills = [{ type: "IMAGE", scaleMode: "FILL", imageHash: image.hash }];
    frame.appendChild(rect);
  }
  return frame;
}

// ── Single-page capture ──────────────────────────────────────────────
async function addImage(msg) {
  const { tiles, totalWidth, totalHeight, url } = msg;
  if (!tiles || !tiles.length) throw new Error("No image data received.");

  const name = siteName(url);
  const node = await assembleTiles(tiles, totalWidth, totalHeight, name);

  const c = figma.viewport.center;
  node.x = Math.round(c.x - node.width / 2);
  node.y = Math.round(c.y - node.height / 2);

  figma.currentPage.appendChild(node);
  figma.currentPage.selection = [node];
  figma.viewport.scrollAndZoomIntoView([node]);

  figma.ui.postMessage({ type: "done" });
  figma.notify("Screenshot added");
}

// ── Bulk capture — Auto Layout container ────────────────────────────
let bulkFrame = null;

async function startBulk(msg) {
  const name    = siteName(msg.url);
  const spacing = Math.max(0, msg.gap || msg.padding || 250);
  bulkFrame = figma.createFrame();
  bulkFrame.name = name;
  bulkFrame.layoutMode = msg.layout === "horizontal" ? "HORIZONTAL" : "VERTICAL";
  bulkFrame.primaryAxisSizingMode = "AUTO";
  bulkFrame.counterAxisSizingMode = "AUTO";
  bulkFrame.itemSpacing = spacing;
  bulkFrame.paddingLeft = bulkFrame.paddingRight  = spacing;
  bulkFrame.paddingTop  = bulkFrame.paddingBottom = spacing;
  bulkFrame.fills = [{ type: "SOLID", color: { r: 0.09, g: 0.09, b: 0.09 } }];

  const c = figma.viewport.center;
  bulkFrame.x = Math.round(c.x);
  bulkFrame.y = Math.round(c.y);
  figma.currentPage.appendChild(bulkFrame);

  figma.ui.postMessage({ type: "bulk-started" });
}

async function addBulkPage(msg) {
  if (!bulkFrame) return;
  const { tiles, totalWidth, totalHeight, url, title, radius } = msg;
  const label = title || siteName(url);

  // Child frame: screenshot + text label, vertical auto layout
  const pageFrame = figma.createFrame();
  pageFrame.name = label;
  pageFrame.layoutMode = "VERTICAL";
  pageFrame.primaryAxisSizingMode = "AUTO";
  pageFrame.counterAxisSizingMode = "AUTO";
  pageFrame.itemSpacing = 12;
  pageFrame.fills = [];
  if (radius > 0) { pageFrame.cornerRadius = radius; pageFrame.clipsContent = true; }

  const shot = await assembleTiles(tiles, totalWidth, totalHeight, label);
  pageFrame.appendChild(shot);

  await figma.loadFontAsync({ family: "Inter", style: "Regular" });
  const text = figma.createText();
  text.characters = label;
  text.fontSize = 14;
  text.fills = [{ type: "SOLID", color: { r: 0.55, g: 0.55, b: 0.6 } }];
  pageFrame.appendChild(text);

  bulkFrame.appendChild(pageFrame);

  figma.ui.postMessage({ type: "bulk-page-added" });
}

function finishBulk(msg) {
  if (bulkFrame) {
    figma.currentPage.selection = [bulkFrame];
    figma.viewport.scrollAndZoomIntoView([bulkFrame]);
  }
  bulkFrame = null;
  figma.ui.postMessage({ type: "bulk-done", count: msg.count || 0 });
  if (msg.count) figma.notify("✓ " + msg.count + " pages added to canvas");
}

function cancelBulk() {
  if (bulkFrame) {
    if (bulkFrame.children.length === 0) bulkFrame.remove();
    else {
      figma.currentPage.selection = [bulkFrame];
      figma.viewport.scrollAndZoomIntoView([bulkFrame]);
    }
  }
  bulkFrame = null;
  figma.ui.postMessage({ type: "bulk-cancelled" });
}

// Name the layer after the site host — no protocol, no www., no trailing slash.
function siteName(url) {
  try {
    const h = new URL(url).hostname.replace(/^www\./, "");
    return h || "screenshot";
  } catch (_) {
    // Fallback: strip protocol manually.
    const m = String(url || "").match(/^https?:\/\/(?:www\.)?([^/?#]+)/i);
    return m ? m[1] : "screenshot";
  }
}
