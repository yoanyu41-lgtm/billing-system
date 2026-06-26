@echo off
title CityTech Billing System - Developer Console
color 0B
cls

echo ======================================================================
echo    ______ _ _         _______        _      ____  _ _ _ _             
echo   ^|  ____(_) ^|       ^|__   __^|      ^| ^|    ^|  _ \(_^|_^|_^|_)            
echo   ^| ^|__   _^| ^| ___     ^| ^| ___  ___^| ^|__  ^| ^|_) ^|_ _ _ _ _ __   __ _ 
echo   ^|  __^| ^| ^| ^|/ _ \    ^| ^|/ _ \/ __^| '_ \ ^|  _ ^| ^| ^| ^| ^| ^| '_ \ / _` ^|
echo   ^| ^|    ^| ^| ^|  __/    ^| ^|  __/ (__^| ^| ^| ^| ^| ^|_) ^| ^| ^| ^| ^| ^| ^| ^| (_^| ^|
echo   ^|_^|    ^|_^|_^|\___^|    ^|_^|\___^|\___^|_^| ^|_^| ^|____/^|_^|_^|_^|_^|_^| ^|_^|\__, ^|
echo                                                               __/ ^|
echo                                                              ^|___/ 
echo ======================================================================
echo.
echo  [SYSTEM] Starting developer environment initialization check...
echo.

:: 1. Check for .env file
if not exist .env (
    echo [INFO] .env file not found. Copying from .env.example...
    copy .env.example .env > nul
    if errorlevel 1 (
        color 0C
        echo [ERROR] Failed to copy .env.example to .env. Please check folder permissions.
        pause
        exit /b 1
    )
    echo [SUCCESS] .env file created.
) else (
    echo [OK] .env file exists.
)

:: 2. Check for Composer vendor folder
if not exist vendor (
    echo [INFO] vendor directory not found. Running "composer install"...
    call composer install
    if errorlevel 1 (
        color 0C
        echo [ERROR] Composer install failed. Please make sure Composer is installed and in your PATH.
        pause
        exit /b 1
    )
    echo [SUCCESS] Composer packages installed.
) else (
    echo [OK] PHP dependencies are already installed.
)

:: 3. Check if Laravel App Key is generated
findstr /C:"APP_KEY=base64" .env > nul
if errorlevel 1 (
    echo [INFO] APP_KEY is empty. Generating Laravel App Key...
    call php artisan key:generate
    if errorlevel 1 (
        color 0C
        echo [ERROR] Failed to generate Laravel App Key.
        pause
        exit /b 1
    )
    echo [SUCCESS] Laravel App Key generated.
) else (
    echo [OK] Laravel App Key is already configured.
)

:: 4. Check for node_modules folder
if not exist node_modules (
    echo [INFO] node_modules directory not found. Running "npm install"...
    call npm install
    if errorlevel 1 (
        color 0C
        echo [ERROR] npm install failed. Please make sure Node.js/NPM is installed.
        pause
        exit /b 1
    )
    echo [SUCCESS] Node.js packages installed.
) else (
    echo [OK] Node.js packages are already installed.
)

echo.
echo ======================================================================
echo  [READY] Launching all development servers concurrently:
echo        - Laravel local server (http://127.0.0.1:8000)
echo        - Vite dev server (Assets builder)
echo        - Laravel queue listener (Telegram / Background Jobs)
echo ======================================================================
echo.

:: Run development servers
call npm run dev-all

if errorlevel 1 (
    color 0C
    echo [ERROR] Development servers exited with an error.
    pause
)
