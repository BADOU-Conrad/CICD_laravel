@echo off
REM Script de déploiement Docker Registry pour Windows
REM Ce script construit et pousse l'image vers Docker Hub

setlocal enabledelayedexpansion

REM Configuration par défaut
set "DOCKERHUB_USERNAME=votre-username"
set "IMAGE_NAME=laravel-app"
set "VERSION=latest"

echo ========================================
echo    Deploiement Docker Registry
echo ========================================
echo.

REM Demander le nom d'utilisateur Docker Hub
set /p "input=Entrez votre nom d'utilisateur Docker Hub [%DOCKERHUB_USERNAME%]: "
if not "!input!"=="" set "DOCKERHUB_USERNAME=!input!"

REM Demander la version
set /p "input=Entrez la version de l'image [%VERSION%]: "
if not "!input!"=="" set "VERSION=!input!"

set "FULL_IMAGE_NAME=%DOCKERHUB_USERNAME%/%IMAGE_NAME%:%VERSION%"
set "LATEST_IMAGE_NAME=%DOCKERHUB_USERNAME%/%IMAGE_NAME%:latest"

echo.
echo Image a construire: %FULL_IMAGE_NAME%
echo.

REM Vérifier la connexion Docker
echo Verification de Docker...
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] Docker n'est pas en cours d'execution
    echo Lancez Docker Desktop et reessayez
    pause
    exit /b 1
)

REM Se connecter à Docker Hub
echo Connexion a Docker Hub...
echo Si vous n'etes pas connecte, veuillez vous connecter maintenant:
docker login
if errorlevel 1 (
    echo [ERREUR] Echec de la connexion a Docker Hub
    pause
    exit /b 1
)

REM Construire l'image
echo.
echo Construction de l'image Docker...
docker build -t %FULL_IMAGE_NAME% .
if errorlevel 1 (
    echo [ERREUR] Echec de la construction de l'image
    pause
    exit /b 1
)

REM Tagger également comme 'latest' si ce n'est pas déjà le tag
if not "%VERSION%"=="latest" (
    echo Tagging de l'image comme 'latest'...
    docker tag %FULL_IMAGE_NAME% %LATEST_IMAGE_NAME%
)

REM Afficher la taille de l'image
echo.
echo Taille de l'image:
docker images %FULL_IMAGE_NAME%

REM Pousser l'image
echo.
set /p "push=Voulez-vous pousser l'image vers Docker Hub? (o/n): "
if /i "%push%"=="o" (
    echo Pushing de l'image %FULL_IMAGE_NAME%...
    docker push %FULL_IMAGE_NAME%
    if errorlevel 1 (
        echo [ERREUR] Echec du push de l'image
        pause
        exit /b 1
    )
    
    if not "%VERSION%"=="latest" (
        echo Pushing de l'image %LATEST_IMAGE_NAME%...
        docker push %LATEST_IMAGE_NAME%
    )
    
    echo.
    echo [OK] Image poussee avec succes!
    echo URL: https://hub.docker.com/r/%DOCKERHUB_USERNAME%/%IMAGE_NAME%
) else (
    echo Push annule
)

REM Nettoyer les images de build
echo.
set /p "clean=Voulez-vous nettoyer les images de build? (o/n): "
if /i "%clean%"=="o" (
    echo Nettoyage des images...
    docker image prune -f
    echo [OK] Nettoyage termine
)

echo.
echo ========================================
echo    Deploiement termine!
echo ========================================
echo.
echo Pour deployer cette image sur un serveur:
echo   docker pull %FULL_IMAGE_NAME%
echo   docker-compose -f docker-compose.prod.yml up -d
echo.

pause
