// Website Screenshot — Figma plugin sandbox code.
// Receives image tiles from the UI and assembles them into a single
// frame on the canvas. Tiling is required because Figma images are
// capped at 4096px per dimension, while full-page screenshots are
// frequently much taller.

const DEFAULT_SIZE = { width: 400, height: 540 };

figma.showUI(__html__, Object.assign({ title: "Website Screenshot" }, DEFAULT_SIZE));

figma.ui.onmessage = async (msg) => {
  switch (msg.type) {
    case "add-tiles":
      await addTiles(msg);
      break;
    case "resize":
      figma.ui.resize(
        clamp(msg.width, 320, 800),
        clamp(msg.height, 400, 900)
      );
      break;
    case "notify":
      figma.notify(msg.message, msg.options || {});
      break;
    case "close":
      figma.closePlugin();
      break;
  }
};

async function addTiles(msg) {
  const { tiles, totalWidth, totalHeight, url } = msg;

  try {
    if (!tiles || !tiles.length) {
      throw new Error("No image data received.");
    }

    const name = frameName(url);
    let node;

    if (tiles.length === 1) {
      // Fits in a single image — a plain rectangle is enough.
      const t = tiles[0];
      const image = figma.createImage(new Uint8Array(t.data));
      const rect = figma.createRectangle();
      rect.name = name;
      rect.resize(t.width, t.height);
      rect.fills = [{ type: "IMAGE", scaleMode: "FILL", imageHash: image.hash }];
      node = rect;
    } else {
      // Tall capture — stack tiles inside a clipping frame.
      const frame = figma.createFrame();
      frame.name = name;
      frame.resize(totalWidth, totalHeight);
      frame.clipsContent = true;
      frame.fills = [];

      for (const t of tiles) {
        const image = figma.createImage(new Uint8Array(t.data));
        const rect = figma.createRectangle();
        rect.resize(t.width, t.height);
        rect.x = 0;
        rect.y = t.y;
        rect.fills = [{ type: "IMAGE", scaleMode: "FILL", imageHash: image.hash }];
        frame.appendChild(rect);
      }
      node = frame;
    }

    placeInViewport(node);
    figma.currentPage.appendChild(node);
    figma.currentPage.selection = [node];
    figma.viewport.scrollAndZoomIntoView([node]);

    figma.ui.postMessage({ type: "done" });
    figma.notify("Screenshot added ✓");
  } catch (err) {
    const message = (err && err.message) ? err.message : String(err);
    figma.ui.postMessage({ type: "error", message });
    figma.notify("Failed: " + message, { error: true });
  }
}

function placeInViewport(node) {
  const c = figma.viewport.center;
  node.x = Math.round(c.x - node.width / 2);
  node.y = Math.round(c.y - node.height / 2);
}

function frameName(url) {
  try {
    const u = new URL(url);
    return "Screenshot — " + u.hostname.replace(/^www\./, "");
  } catch (_) {
    return "Screenshot";
  }
}

function clamp(v, min, max) {
  return Math.max(min, Math.min(max, v));
}
