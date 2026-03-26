# Déploiement avec Docker Registry

## 1. Préparation du Docker Hub

### Créer un compte Docker Hub
1. Allez sur https://hub.docker.com
2. Créez un compte gratuit
3. Créez un repository (ex: `votre-username/laravel-app`)

### Se connecter à Docker Hub
```bash
docker login
# Entrez votre username et password Docker Hub
```

## 2. Construire et Pousser l'Image

### Construire l'image avec un tag
```bash
# Remplacez 'votre-username' par votre nom d'utilisateur Docker Hub
docker build -t votre-username/laravel-app:latest .
docker build -t votre-username/laravel-app:v1.0.0 .
```

### Pousser l'image vers Docker Hub
```bash
docker push votre-username/laravel-app:latest
docker push votre-username/laravel-app:v1.0.0
```

### Vérifier l'image
```bash
# Lister les images locales
docker images | grep laravel-app

# Vérifier sur Docker Hub
# Visitez: https://hub.docker.com/r/votre-username/laravel-app
```

## 3. Déployer depuis le Registry

### Option A: Utiliser docker run
```bash
# Télécharger et exécuter l'image
docker pull votre-username/laravel-app:latest

docker run -d \
  --name laravel_app \
  -p 8000:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg= \
  -e DB_CONNECTION=sqlite \
  -v $(pwd)/storage:/var/www/html/storage \
  -v $(pwd)/database:/var/www/html/database \
  votre-username/laravel-app:latest
```

### Option B: Utiliser docker-compose (Recommandé)
Utilisez le fichier `docker-compose.prod.yml` fourni ci-dessous.

```bash
# Démarrer avec docker-compose
docker-compose -f docker-compose.prod.yml up -d

# Voir les logs
docker-compose -f docker-compose.prod.yml logs -f

# Arrêter
docker-compose -f docker-compose.prod.yml down
```

## 4. Déploiement Automatisé avec GitHub Actions

Le workflow `.github/workflows/docker-publish.yml` est configuré pour :
1. Se déclencher à chaque push sur `main`
2. Construire l'image Docker
3. La pousser automatiquement vers Docker Hub
4. Tagger avec le numéro de version et `latest`

### Configuration des secrets GitHub
Allez dans **Settings > Secrets and variables > Actions** et ajoutez :
- `DOCKERHUB_USERNAME` : votre nom d'utilisateur Docker Hub
- `DOCKERHUB_TOKEN` : votre token d'accès Docker Hub (créé dans Account Settings > Security)

## 5. Déploiement sur un Serveur de Production

### Sur un serveur distant
```bash
# Se connecter au serveur
ssh user@votre-serveur.com

# Se connecter à Docker Hub
docker login

# Créer un répertoire pour l'application
mkdir -p ~/laravel-app
cd ~/laravel-app

# Télécharger le docker-compose.prod.yml
wget https://raw.githubusercontent.com/votre-repo/docker-compose.prod.yml

# Créer les volumes nécessaires
mkdir -p storage database

# Créer la base de données SQLite
touch database/database.sqlite

# Démarrer l'application
docker-compose -f docker-compose.prod.yml up -d
```

### Mise à jour de l'application
```bash
# Télécharger la nouvelle version
docker-compose -f docker-compose.prod.yml pull

# Redémarrer avec la nouvelle image
docker-compose -f docker-compose.prod.yml up -d

# Nettoyer les anciennes images
docker image prune -f
```

## 6. Bonnes Pratiques

### Utiliser des Tags de Version
```bash
# Au lieu de toujours utiliser 'latest'
docker build -t votre-username/laravel-app:v1.0.0 .
docker build -t votre-username/laravel-app:v1.0.1 .
```

### Multi-Stage Build (Optimisation)
Voir le fichier `Dockerfile.optimized` pour une version optimisée.

### Scanner la Sécurité
```bash
# Utiliser Docker Scout
docker scout cves votre-username/laravel-app:latest

# Utiliser Trivy
docker run aquasec/trivy image votre-username/laravel-app:latest
```

## 7. Registries Alternatifs

### GitHub Container Registry (ghcr.io)
```bash
# Se connecter
echo $GITHUB_TOKEN | docker login ghcr.io -u USERNAME --password-stdin

# Construire et pousser
docker build -t ghcr.io/votre-username/laravel-app:latest .
docker push ghcr.io/votre-username/laravel-app:latest
```

### Amazon ECR
```bash
# Se connecter
aws ecr get-login-password --region region | docker login --username AWS --password-stdin account-id.dkr.ecr.region.amazonaws.com

# Construire et pousser
docker build -t account-id.dkr.ecr.region.amazonaws.com/laravel-app:latest .
docker push account-id.dkr.ecr.region.amazonaws.com/laravel-app:latest
```

### Google Container Registry (gcr.io)
```bash
# Se connecter
gcloud auth configure-docker

# Construire et pousser
docker build -t gcr.io/project-id/laravel-app:latest .
docker push gcr.io/project-id/laravel-app:latest
```

## 8. Dépannage

### Erreur d'authentification
```bash
# Vérifier la connexion
docker logout
docker login
```

### Image trop grande
```bash
# Vérifier la taille
docker images votre-username/laravel-app:latest

# Utiliser .dockerignore
# Utiliser multi-stage build
```

### Erreurs de permissions
```bash
# Sur le serveur
sudo chown -R 82:82 storage database
sudo chmod -R 775 storage database
```

## Commandes Utiles

```bash
# Voir les images locales
docker images

# Supprimer une image locale
docker rmi votre-username/laravel-app:v1.0.0

# Voir les conteneurs en cours d'exécution
docker ps

# Inspecter un conteneur
docker inspect laravel_app

# Entrer dans le conteneur
docker exec -it laravel_app bash

# Voir les logs
docker logs -f laravel_app
```
