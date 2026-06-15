@echo off
setlocal EnableDelayedExpansion
title Screenshot Server — Install
color 0A

echo.
echo  =========================================
echo   Screenshot Server — One-time Setup
echo  =========================================
echo.

:: ── Step 1: Install Node packages ──────────────────────────────────
echo  [1/3] Installing packages...
call npm install --silent
if errorlevel 1 (
  echo  ERROR: npm install failed. Make sure Node.js is installed.
  echo  Download: https://nodejs.org
  pause & exit /b 1
)
echo  Done.
echo.

:: ── Step 2: Install Playwright browser ─────────────────────────────
echo  [2/3] Installing Chromium browser for Playwright...
call npx playwright install chromium --with-deps
if errorlevel 1 (
  echo  WARNING: Playwright install may have had issues. Continuing...
)
echo  Done.
echo.

:: ── Step 3: Register auto-start on Windows login ───────────────────
echo  [3/3] Registering auto-start on login...

set "DIR=%~dp0"
:: Remove trailing backslash
if "%DIR:~-1%"=="\" set "DIR=%DIR:~0,-1%"

set "VBS=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\screenshot-server.vbs"

:: Write VBS that silently starts the server in background
(
  echo Set sh = CreateObject^("WScript.Shell"^)
  echo sh.CurrentDirectory = "%DIR%"
  echo sh.Run "node index.js", 0, False
) > "%VBS%"

echo  Done.
echo.

:: ── Start the server right now ──────────────────────────────────────
echo  Starting server in background...
start "" /b wscript "%VBS%"

echo.
echo  =========================================
echo   Setup complete!
echo.
echo   Server is running on http://localhost:3000
echo   It will auto-start every time you log in.
echo.
echo   In Figma plugin Settings:
echo     Server URL:  http://localhost:3000
echo     Auth token:  (leave empty)
echo.
echo   To stop the server, run:  stop.bat
echo   To uninstall auto-start:  uninstall.bat
echo  =========================================
echo.
pause
