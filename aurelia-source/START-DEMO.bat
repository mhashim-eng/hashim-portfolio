@echo off
cd /d "%~dp0"
echo Open http://localhost:8000 in your browser. Ctrl+C stops the preview.
py -m http.server 8000 --directory dist
pause
