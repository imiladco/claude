#!/usr/bin/env bash
if [[ "$OSTYPE" == "darwin"* ]]; then
  launchctl unload ~/Library/LaunchAgents/com.figma.screenshot-server.plist 2>/dev/null
  rm -f ~/Library/LaunchAgents/com.figma.screenshot-server.plist
  echo "Removed from launchd."
else
  systemctl --user stop screenshot-server 2>/dev/null
  systemctl --user disable screenshot-server 2>/dev/null
  rm -f ~/.config/systemd/user/screenshot-server.service
  systemctl --user daemon-reload
  echo "Removed from systemd."
fi
