(function () {
  function initBackground() {
    const canvas = document.getElementById("bg");

    if (!canvas || window.__cineboxCanvasBackgroundReady) {
      return;
    }

    const ctx = canvas.getContext("2d");

    if (!ctx) {
      return;
    }

    window.__cineboxCanvasBackgroundReady = true;

    function draw() {
      const width = canvas.width = canvas.offsetWidth;
      const height = canvas.height = canvas.offsetHeight;

      ctx.fillStyle = "#181a1b";
      ctx.fillRect(0, 0, width, height);

      const topLeftGlow = ctx.createRadialGradient(0, 0, 0, 0, 0, width * 0.55);
      topLeftGlow.addColorStop(0, "rgba(255,170,84,0.34)");
      topLeftGlow.addColorStop(0.4, "rgba(255,145,56,0.14)");
      topLeftGlow.addColorStop(1, "rgba(0,0,0,0)");
      ctx.fillStyle = topLeftGlow;
      ctx.fillRect(0, 0, width, height);

      const bottomRightGlow = ctx.createRadialGradient(width, height, 0, width, height, width * 0.55);
      bottomRightGlow.addColorStop(0, "rgba(255,162,64,0.58)");
      bottomRightGlow.addColorStop(0.4, "rgba(255,138,44,0.22)");
      bottomRightGlow.addColorStop(1, "rgba(0,0,0,0)");
      ctx.fillStyle = bottomRightGlow;
      ctx.fillRect(0, 0, width, height);

      const imageData = ctx.getImageData(0, 0, width, height);
      const pixels = imageData.data;

      for (let i = 0; i < pixels.length; i += 4) {
        const noise = (Math.random() - 0.5) * 32;
        pixels[i] = Math.min(255, Math.max(0, pixels[i] + noise));
        pixels[i + 1] = Math.min(255, Math.max(0, pixels[i + 1] + noise));
        pixels[i + 2] = Math.min(255, Math.max(0, pixels[i + 2] + noise));
      }

      ctx.putImageData(imageData, 0, 0);
    }

    draw();
    window.addEventListener("resize", draw);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initBackground, { once: true });
  } else {
    initBackground();
  }
})();
