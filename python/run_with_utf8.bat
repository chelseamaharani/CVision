@echo off
chcp 65001 >nul
set PYTHONIOENCODING=utf-8
cd /d %~dp0
uvicorn main:app --reload --port 8000
pause