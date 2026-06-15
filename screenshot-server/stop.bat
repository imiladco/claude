@echo off
echo Stopping Screenshot Server...
taskkill /f /im node.exe /fi "WINDOWTITLE eq Screenshot Server*" >nul 2>&1
:: Kill any node process running index.js from this folder
for /f "tokens=2" %%a in ('wmic process where "name='node.exe'" get processid ^| findstr /r "[0-9]"') do (
  wmic process where processid="%%a" get commandline 2>nul | findstr /i "screenshot-server" >nul
  if not errorlevel 1 taskkill /f /pid %%a >nul 2>&1
)
echo Done.
