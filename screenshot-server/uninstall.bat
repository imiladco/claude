@echo off
echo Removing auto-start...
set "VBS=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\screenshot-server.vbs"
if exist "%VBS%" (
  del "%VBS%"
  echo Auto-start removed.
) else (
  echo Auto-start was not installed.
)
echo.
echo Stopping server if running...
taskkill /f /im node.exe /fi "WINDOWTITLE eq Screenshot Server*" >nul 2>&1
echo Done.
pause
