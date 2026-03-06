#!/bin/bash

# Script de déploiement Docker Registry
# Ce script construit et pousse l'image vers Docker Hub

set -e  # Arrêter en cas d'erreur

# Configuration
DOCKERHUB_USERNAME="votre-username"  # À MODIFIER
IMAGE_NAME="laravel-app"
VERSION="latest"

# Couleurs pour l'affichage
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   Déploiement Docker Registry${NC}"
echo -e "${BLUE}========================================${NC}"

# Demander le nom d'utilisateur Docker Hub
read -p "Entrez votre nom d'utilisateur Docker Hub [$DOCKERHUB_USERNAME]: " input
DOCKERHUB_USERNAME="${input:-$DOCKERHUB_USERNAME}"

# Demander la version
read -p "Entrez la version de l'image [$VERSION]: " input
VERSION="${input:-$VERSION}"

FULL_IMAGE_NAME="${DOCKERHUB_USERNAME}/${IMAGE_NAME}:${VERSION}"
LATEST_IMAGE_NAME="${DOCKERHUB_USERNAME}/${IMAGE_NAME}:latest"

echo ""
echo -e "${BLUE}Image à construire:${NC} $FULL_IMAGE_NAME"
echo ""

# Vérifier si connecté à Docker Hub
echo -e "${BLUE}Vérification de la connexion Docker Hub...${NC}"
if ! docker info | grep -q "Username: ${DOCKERHUB_USERNAME}"; then
    echo -e "${RED}Vous n'êtes pas connecté à Docker Hub${NC}"
    echo -e "${BLUE}Connexion en cours...${NC}"
    docker login
fi

# Construire l'image
echo ""
echo -e "${BLUE}Construction de l'image Docker...${NC}"
docker build -t $FULL_IMAGE_NAME .

# Tagger également comme 'latest' si ce n'est pas déjà le tag
if [ "$VERSION" != "latest" ]; then
    echo -e "${BLUE}Tagging de l'image comme 'latest'...${NC}"
    docker tag $FULL_IMAGE_NAME $LATEST_IMAGE_NAME
fi

# Afficher la taille de l'image
echo ""
echo -e "${BLUE}Taille de l'image:${NC}"
docker images $FULL_IMAGE_NAME

# Pousser l'image
echo ""
read -p "Voulez-vous pousser l'image vers Docker Hub? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${BLUE}Pushing de l'image $FULL_IMAGE_NAME...${NC}"
    docker push $FULL_IMAGE_NAME
    
    if [ "$VERSION" != "latest" ]; then
        echo -e "${BLUE}Pushing de l'image $LATEST_IMAGE_NAME...${NC}"
        docker push $LATEST_IMAGE_NAME
    fi
    
    echo ""
    echo -e "${GREEN}✓ Image poussée avec succès!${NC}"
    echo -e "${GREEN}URL: https://hub.docker.com/r/${DOCKERHUB_USERNAME}/${IMAGE_NAME}${NC}"
else
    echo -e "${BLUE}Push annulé${NC}"
fi

# Nettoyer les images de build
echo ""
read -p "Voulez-vous nettoyer les images de build? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${BLUE}Nettoyage des images...${NC}"
    docker image prune -f
    echo -e "${GREEN}✓ Nettoyage terminé${NC}"
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   Déploiement terminé!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Pour déployer cette image sur un serveur:"
echo "  docker pull $FULL_IMAGE_NAME"
echo "  docker-compose -f docker-compose.prod.yml up -d"
