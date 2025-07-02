@echo off
REM Archivo: run-scheduler.bat
REM Script para ejecutar Laravel Scheduler desde Task Scheduler de Windows

cd /d "C:\laragon\www\HOSPROGRESO"
PowerShell -ExecutionPolicy Bypass -File "schedule-runner.ps1" 