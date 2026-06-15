@echo off
setlocal EnableDelayedExpansion
title Screenshot Server — Setup
color 0A

echo.
echo  ==========================================
echo   Screenshot Server — One-time Setup
echo  ==========================================
echo.

:: ── Check Node.js ──────────────────────────────────────────────────
echo  [0/3] Checking Node.js...
where node >/dev/null 2>&1
if errorlevel 1 (
  echo.
  echo  ERROR: Node.js is not installed.
  echo  Download from: https://nodejs.org  (LTS version)
  echo.
  pause & exit /b 1
)
for /f "tokens=*" %%v in ('node --version') do set NODE_VER=%%v
echo  Found Node.js %NODE_VER%
echo.

:: ── Install packages ───────────────────────────────────────────────
echo  [1/3] Installing packages...
cd /d "%~dp0"
call npm install --silent
if errorlevel 1 (
  echo  ERROR: npm install failed.
  pause & exit /b 1
)
echo  Done.
echo.

:: ── Install Playwright browser ─────────────────────────────────────
echo  [2/3] Installing Chromium (this may take a minute)...
call npx playwright install chromium --with-deps
echo  Done.
echo.

:: ── Register Windows Task Scheduler (no VBS, no AV issues) ────────
echo  [3/3] Registering auto-start on login...

set "DIR=%~dp0"
if "%DIR:~-1%"=="\" set "DIR=%DIR:~0,-1%"

:: Find node.exe path
for /f "tokens=*" %%n in ('where node.exe') do set "NODE_EXE=%%n" & goto :found_node
:found_node

:: Use PowerShell to create a properly hidden scheduled task
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$dir = '%DIR:\=\\%'; " ^
  "$node = '%NODE_EXE:\=\\%'; " ^
  "$action = New-ScheduledTaskAction -Execute $node -Argument 'index.js' -WorkingDirectory $dir; " ^
  "$trigger = New-ScheduledTaskTrigger -AtLogOn; " ^
  "$settings = New-ScheduledTaskSettingsSet -Hidden -ExecutionTimeLimit 0 -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1); " ^
  "$principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -RunLevel Limited; " ^
  "Register-ScheduledTask -TaskName 'FigmaScreenshotServer' -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Force | Out-Null; " ^
  "Write-Host 'Task registered.'"

if errorlevel 1 (
  echo  WARNING: Could not register Task Scheduler. Try running as Administrator.
  echo  The server will still work — just run start.bat manually.
) else (
  echo  Done. Server will auto-start on every login (hidden, no window).
)
echo.

:: ── Start the server right now ─────────────────────────────────────
echo  Starting server now...
powershell -NoProfile -Command ^
  "Start-Process -FilePath '%NODE_EXE%' -ArgumentList 'index.js' " ^
  "-WorkingDirectory '%DIR%' -WindowStyle Hidden"

:: Wait a moment then verify
timeout /t 3 /nobreak >/dev/null
powershell -NoProfile -Command ^
  "try { $r=(Invoke-WebRequest http://localhost:3000/health -UseBasicParsing -TimeoutSec 5).Content; " ^
  "if($r -match 'ok') { Write-Host ' Server is running!' } } " ^
  "catch { Write-Host ' Server starting (check http://localhost:3000/health)' }"

echo.
echo  ==========================================
echo   Setup complete!
echo.
echo   Server: http://localhost:3000
echo   Auto-starts silently on every Windows login.
echo.
echo   In Figma plugin Settings:
echo     Provider:    Self-hosted Playwright
echo     Server URL:  http://localhost:3000
echo     Token:       (leave empty)
echo.
echo   stop.bat       — stop the server
echo   uninstall.bat  — remove auto-start
echo  ==========================================
echo.
pause
