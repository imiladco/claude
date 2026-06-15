@echo off
setlocal EnableDelayedExpansion
title Screenshot Server — Install
color 0A

echo.
echo  =========================================
echo   Screenshot Server — One-time Setup
echo  =========================================
echo.

:: ── Step 0: Check Node.js ────────────────────────────────────────────
where node >nul 2>&1
if errorlevel 1 (
  echo  ERROR: Node.js is not installed.
  echo  Download it from:  https://nodejs.org
  echo  Install Node.js, then re-run this script.
  echo.
  pause & exit /b 1
)
for /f "tokens=*" %%v in ('node -v') do set NODE_VER=%%v
echo  Node.js %NODE_VER% found.
echo.

:: ── Step 1: Install Node packages ────────────────────────────────────
echo  [1/3] Installing packages...
call npm install --silent
if errorlevel 1 (
  echo  ERROR: npm install failed.
  pause & exit /b 1
)
echo  Done.
echo.

:: ── Step 2: Install Playwright browser ───────────────────────────────
echo  [2/3] Installing Chromium browser for Playwright...
call npx playwright install chromium --with-deps
if errorlevel 1 (
  echo  WARNING: Playwright install may have had issues. Continuing...
)
echo  Done.
echo.

:: ── Step 3: Register auto-start via Task Scheduler ───────────────────
echo  [3/3] Registering auto-start on login (Task Scheduler)...

set "DIR=%~dp0"
if "%DIR:~-1%"=="\" set "DIR=%DIR:~0,-1%"

:: Remove any existing task with this name first
schtasks /delete /tn "FigmaScreenshotServer" /f >nul 2>&1

:: Register a new task that runs at user logon, hidden, no window
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$a = New-ScheduledTaskAction -Execute 'node' -Argument 'index.js' -WorkingDirectory '%DIR%';" ^
  "$t = New-ScheduledTaskTrigger -AtLogOn -User $env:USERNAME;" ^
  "$s = New-ScheduledTaskSettingsSet -ExecutionTimeLimit 0 -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 2);" ^
  "$p = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive -RunLevel Limited;" ^
  "Register-ScheduledTask -TaskName 'FigmaScreenshotServer' -Action $a -Trigger $t -Settings $s -Principal $p -Force | Out-Null;" ^
  "Write-Host 'Task registered.'"

if errorlevel 1 (
  echo  WARNING: Could not register Task Scheduler task. You can still run start.bat manually.
) else (
  echo  Done.
)
echo.

:: ── Start the server right now ────────────────────────────────────────
echo  Starting server in background...
start "" /b node "%DIR%\index.js"
timeout /t 2 /nobreak >nul

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
echo   To stop the server:       stop.bat
echo   To uninstall auto-start:  uninstall.bat
echo  =========================================
echo.
pause
