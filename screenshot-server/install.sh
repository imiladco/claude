#!/usr/bin/env bash
set -e
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo ""
echo " ========================================"
echo "  Screenshot Server — One-time Setup"
echo " ========================================"
echo ""

# Check Node.js
echo " [0/3] Checking Node.js..."
if ! command -v node &>/dev/null; then
  echo " ERROR: Node.js is not installed."
  echo " Install from: https://nodejs.org (LTS)"
  exit 1
fi
echo " Found $(node --version)"
echo ""

# Install packages
echo " [1/3] Installing packages..."
cd "$DIR"
npm install --silent
echo " Done."
echo ""

# Install Playwright
echo " [2/3] Installing Chromium..."
npx playwright install chromium --with-deps
echo " Done."
echo ""

# Auto-start
echo " [3/3] Registering auto-start..."

if [[ "$OSTYPE" == "darwin"* ]]; then
  # macOS — launchd plist
  PLIST="$HOME/Library/LaunchAgents/com.figma.screenshot-server.plist"
  NODE_PATH="$(command -v node)"
  cat > "$PLIST" <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
  "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key>             <string>com.figma.screenshot-server</string>
  <key>ProgramArguments</key>
  <array>
    <string>$NODE_PATH</string>
    <string>$DIR/index.js</string>
  </array>
  <key>WorkingDirectory</key>  <string>$DIR</string>
  <key>RunAtLoad</key>         <true/>
  <key>KeepAlive</key>         <true/>
  <key>StandardOutPath</key>   <string>$DIR/server.log</string>
  <key>StandardErrorPath</key> <string>$DIR/server.log</string>
</dict>
</plist>
EOF
  launchctl load "$PLIST" 2>/dev/null || true
  launchctl start com.figma.screenshot-server 2>/dev/null || true
  echo " Registered with launchd (macOS). Starts on every login."

else
  # Linux — systemd user service
  mkdir -p "$HOME/.config/systemd/user"
  NODE_PATH="$(command -v node)"
  cat > "$HOME/.config/systemd/user/screenshot-server.service" <<EOF
[Unit]
Description=Figma Screenshot Server
After=network.target

[Service]
ExecStart=$NODE_PATH $DIR/index.js
WorkingDirectory=$DIR
Restart=on-failure
RestartSec=5

[Install]
WantedBy=default.target
EOF
  systemctl --user daemon-reload
  systemctl --user enable screenshot-server
  systemctl --user start screenshot-server
  echo " Registered with systemd (Linux). Starts on every login."
fi

echo ""

# Verify
sleep 2
if curl -sf http://localhost:3000/health | grep -q '"ok":true'; then
  echo " Server is running at http://localhost:3000"
else
  echo " Server starting... check http://localhost:3000/health"
fi

echo ""
echo " ========================================"
echo "  Setup complete!"
echo ""
echo "  In Figma plugin Settings:"
echo "    Provider:   Self-hosted Playwright"
echo "    Server URL: http://localhost:3000"
echo "    Token:      (leave empty)"
echo ""
echo "  To stop:      bash stop.sh"
echo "  To uninstall: bash uninstall.sh"
echo " ========================================"
echo ""
