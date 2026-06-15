#!/usr/bin/env bash
if [[ "$OSTYPE" == "darwin"* ]]; then
  launchctl stop com.figma.screenshot-server 2>/dev/null
  echo "Server stopped."
else
  systemctl --user stop screenshot-server
  echo "Server stopped."
fi
