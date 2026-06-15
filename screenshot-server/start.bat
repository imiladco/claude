@echo off
title Screenshot Server
echo.
echo  Screenshot Server
echo  ─────────────────────────────────────
echo  Running on http://localhost:3000
echo  Keep this window open while using Figma.
echo  Close this window to stop the server.
echo  ─────────────────────────────────────
echo.
cd /d "%~dp0"
node index.js
pause
