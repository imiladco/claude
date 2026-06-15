#!/usr/bin/env bash
OS="$(uname -s)"
DIR="$(cd "$(dirname "$0")" && pwd)"

echo "Removing auto-start..."

if [ "$OS" = "Darwin" ]; then
  PLIST="$HOME/Library/LaunchAgents/com.figma.screenshot-server.plist"
  if [ -f "$PLIST" ]; then
    launchctl unload "$PLIST" 2>/dev/null || true
    rm "$PLIST"
    echo "Auto-start removed (launchd)."
  else
    echo "Auto-start was not installed."
  fi
else
  systemctl --user stop figma-screenshot-server 2>/dev/null || true
  systemctl --user disable figma-screenshot-server 2>/dev/null || true
  rm -f "$HOME/.config/systemd/user/figma-screenshot-server.service"
  systemctl --user daemon-reload 2>/dev/null || true
  echo "Auto-start removed (systemd)."
fi

echo ""
echo "Stopping server if running..."
pkill -f "node.*$DIR/index.js" 2>/dev/null && echo "Server stopped." || echo "Server was not running."
echo "Done."
