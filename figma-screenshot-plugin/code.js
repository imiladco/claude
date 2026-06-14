figma.showUI(__html__, { width: 420, height: 380, title: "Website Screenshot" });

figma.ui.onmessage = async (msg) => {
  if (msg.type === "add-image") {
    const { imageData, naturalWidth, naturalHeight, url } = msg;

    try {
      const uint8 = new Uint8Array(imageData);
      const image = figma.createImage(uint8);

      const rect = figma.createRectangle();
      rect.name = `Screenshot: ${url}`;
      rect.resize(naturalWidth, naturalHeight);
      rect.fills = [
        {
          type: "IMAGE",
          scaleMode: "FILL",
          imageHash: image.hash,
        },
      ];

      const { x, y } = figma.viewport.center;
      rect.x = x - naturalWidth / 2;
      rect.y = y - naturalHeight / 2;

      figma.currentPage.appendChild(rect);
      figma.viewport.scrollAndZoomIntoView([rect]);

      figma.ui.postMessage({ type: "done" });
    } catch (err) {
      figma.ui.postMessage({ type: "error", message: String(err) });
    }
  } else if (msg.type === "close") {
    figma.closePlugin();
  }
};
