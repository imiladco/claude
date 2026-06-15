#!/usr/bin/env bash
OS="$(uname -s)"

echo "Stopping Screenshot Server..."

# Kill any node process running index.js from this folder
DIR="$(cd "$(dirname "$0")" && pwd)"
pkill -f "node.*$DIR/index.js" 2>/dev/null && echo "Stopped." || echo "Server was not running."
