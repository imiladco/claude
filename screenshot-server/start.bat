@echo off
title Screenshot Server
cd /d "%~dp0"

:: install.bat already starts the server hidden + registers it to
:: auto-start on login, so it's usually already running by the time
:: someone runs this manually. Detect that instead of crashing on
:: EADDRINUSE.
powershell -NoProfile -Command "try { Invoke-WebRequest http://localhost:3000/health -UseBasicParsing -TimeoutSec 2 | Out-Null; exit 0 } catch { exit 1 }" >nul 2>&1
if not errorlevel 1 (
  echo.
  echo  Screenshot Server is already running at http://localhost:3000
  echo  ^(it auto-starts in the background after install.bat — no need to run this^).
  echo.
  pause
  exit /b 0
)

echo.
echo  Screenshot Server
echo  ─────────────────────────────────────
echo  Running on http://localhost:3000
echo  Keep this window open while using Figma.
echo  Close this window to stop the server.
echo  ─────────────────────────────────────
echo.
node index.js
pause
