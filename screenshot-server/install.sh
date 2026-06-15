#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "$0")" && pwd)"
OS="$(uname -s)"

echo ""
echo " ========================================="
echo "  Screenshot Server — One-time Setup"
echo " ========================================="
echo ""

# ── Step 0: Check Node.js ─────────────────────────────────────────────
if ! command -v node >/dev/null 2>&1; then
  echo " ERROR: Node.js is not installed."
  echo " Download: https://nodejs.org"
  exit 1
fi
echo " Node.js $(node -v) found."
echo ""

# ── Step 1: Install packages ──────────────────────────────────────────
echo " [1/3] Installing packages..."
cd "$DIR"
npm install --silent
echo " Done."
echo ""

# ── Step 2: Install Playwright browser ───────────────────────────────
echo " [2/3] Installing Chromium for Playwright..."
npx playwright install chromium --with-deps || echo " WARNING: Playwright install had issues. Continuing..."
echo " Done."
echo ""

# ── Step 3: Register auto-start ──────────────────────────────────────
echo " [3/3] Registering auto-start on login..."

if [ "$OS" = "Darwin" ]; then
  # macOS — launchd plist
  PLIST="$HOME/Library/LaunchAgents/com.figma.screenshot-server.plist"
  cat > "$PLIST" <<PLIST_EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key>         <string>com.figma.screenshot-server</string>
  <key>ProgramArguments</key>
  <array>
    <string>$(command -v node)</string>
    <string>$DIR/index.js</string>
  </array>
  <key>WorkingDirectory</key> <string>$DIR</string>
  <key>RunAtLoad</key>        <true/>
  <key>KeepAlive</key>        <true/>
  <key>StandardOutPath</key>  <string>$DIR/server.log</string>
  <key>StandardErrorPath</key><string>$DIR/server.log</string>
</dict>
</plist>
PLIST_EOF
  launchctl unload "$PLIST" 2>/dev/null || true
  launchctl load -w "$PLIST"
  echo " Done (launchd plist: $PLIST)."

else
  # Linux — systemd user service
  SVCDIR="$HOME/.config/systemd/user"
  mkdir -p "$SVCDIR"
  cat > "$SVCDIR/figma-screenshot-server.service" <<SVC_EOF
[Unit]
Description=Figma Screenshot Server
After=network.target

[Service]
Type=simple
ExecStart=$(command -v node) $DIR/index.js
WorkingDirectory=$DIR
Restart=on-failure
RestartSec=5
StandardOutput=append:$DIR/server.log
StandardError=append:$DIR/server.log

[Install]
WantedBy=default.target
SVC_EOF
  systemctl --user daemon-reload
  systemctl --user enable --now figma-screenshot-server
  echo " Done (systemd user service)."
fi

echo ""
echo " ========================================="
echo "  Setup complete!"
echo ""
echo "  Server is running on http://localhost:3000"
echo "  It will auto-start every time you log in."
echo ""
echo "  In Figma plugin Settings:"
echo "    Server URL:  http://localhost:3000"
echo "    Auth token:  (leave empty)"
echo ""
echo "  To stop:       ./stop.sh"
echo "  To uninstall:  ./uninstall.sh"
echo " ========================================="
echo ""
