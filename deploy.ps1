# Script de déploiement Docker Registry pour Windows PowerShell
# Ce script construit et pousse l'image vers Docker Hub

param(
    [string]$Username = "votre-username",
    [string]$ImageName = "laravel-app",
    [string]$Version = "latest"
)

# Configuration des couleurs
$Success = "Green"
$Info = "Cyan"
$Error = "Red"
$Warning = "Yellow"

Write-Host "========================================" -ForegroundColor $Info
Write-Host "   Déploiement Docker Registry" -ForegroundColor $Info
Write-Host "========================================" -ForegroundColor $Info
Write-Host ""

# Demander le nom d'utilisateur Docker Hub
$inputUsername = Read-Host "Entrez votre nom d'utilisateur Docker Hub [$Username]"
if ($inputUsername) { $Username = $inputUsername }

# Demander la version
$inputVersion = Read-Host "Entrez la version de l'image [$Version]"
if ($inputVersion) { $Version = $inputVersion }

$FullImageName = "$Username/$ImageName`:$Version"
$LatestImageName = "$Username/$ImageName`:latest"

Write-Host ""
Write-Host "Image à construire: $FullImageName" -ForegroundColor $Info
Write-Host ""

# Vérifier si Docker est en cours d'exécution
Write-Host "Vérification de Docker..." -ForegroundColor $Info
try {
    docker info | Out-Null
    Write-Host "✓ Docker est en cours d'exécution" -ForegroundColor $Success
} catch {
    Write-Host "✗ Docker n'est pas en cours d'exécution" -ForegroundColor $Error
    Write-Host "Lancez Docker Desktop et réessayez" -ForegroundColor $Warning
    exit 1
}

# Se connecter à Docker Hub
Write-Host ""
Write-Host "Connexion à Docker Hub..." -ForegroundColor $Info
try {
    docker login
    if ($LASTEXITCODE -ne 0) { throw }
    Write-Host "✓ Connecté à Docker Hub" -ForegroundColor $Success
} catch {
    Write-Host "✗ Échec de la connexion à Docker Hub" -ForegroundColor $Error
    exit 1
}

# Construire l'image
Write-Host ""
Write-Host "Construction de l'image Docker..." -ForegroundColor $Info
try {
    docker build -t $FullImageName .
    if ($LASTEXITCODE -ne 0) { throw }
    Write-Host "✓ Image construite avec succès" -ForegroundColor $Success
} catch {
    Write-Host "✗ Échec de la construction de l'image" -ForegroundColor $Error
    exit 1
}

# Tagger également comme 'latest' si ce n'est pas déjà le tag
if ($Version -ne "latest") {
    Write-Host "Tagging de l'image comme 'latest'..." -ForegroundColor $Info
    docker tag $FullImageName $LatestImageName
    Write-Host "✓ Image taggée comme 'latest'" -ForegroundColor $Success
}

# Afficher la taille de l'image
Write-Host ""
Write-Host "Taille de l'image:" -ForegroundColor $Info
docker images $FullImageName

# Pousser l'image
Write-Host ""
$push = Read-Host "Voulez-vous pousser l'image vers Docker Hub? (o/n)"
if ($push -eq "o" -or $push -eq "O") {
    Write-Host "Pushing de l'image $FullImageName..." -ForegroundColor $Info
    try {
        docker push $FullImageName
        if ($LASTEXITCODE -ne 0) { throw }
        Write-Host "✓ Image $FullImageName poussée avec succès" -ForegroundColor $Success
        
        if ($Version -ne "latest") {
            Write-Host "Pushing de l'image $LatestImageName..." -ForegroundColor $Info
            docker push $LatestImageName
            if ($LASTEXITCODE -ne 0) { throw }
            Write-Host "✓ Image $LatestImageName poussée avec succès" -ForegroundColor $Success
        }
        
        Write-Host ""
        Write-Host "✓ Image poussée avec succès!" -ForegroundColor $Success
        Write-Host "URL: https://hub.docker.com/r/$Username/$ImageName" -ForegroundColor $Info
    } catch {
        Write-Host "✗ Échec du push de l'image" -ForegroundColor $Error
        exit 1
    }
} else {
    Write-Host "Push annulé" -ForegroundColor $Warning
}

# Nettoyer les images de build
Write-Host ""
$clean = Read-Host "Voulez-vous nettoyer les images de build? (o/n)"
if ($clean -eq "o" -or $clean -eq "O") {
    Write-Host "Nettoyage des images..." -ForegroundColor $Info
    docker image prune -f
    Write-Host "✓ Nettoyage terminé" -ForegroundColor $Success
}

Write-Host ""
Write-Host "========================================" -ForegroundColor $Success
Write-Host "   Déploiement terminé!" -ForegroundColor $Success
Write-Host "========================================" -ForegroundColor $Success
Write-Host ""
Write-Host "Pour déployer cette image sur un serveur:" -ForegroundColor $Info
Write-Host "  docker pull $FullImageName" -ForegroundColor $Warning
Write-Host "  docker-compose -f docker-compose.prod.yml up -d" -ForegroundColor $Warning
Write-Host ""
