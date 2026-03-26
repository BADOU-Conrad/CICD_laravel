# 🚀 Guide Rapide - Déploiement Docker Registry

## Démarrage Rapide (Windows)

### 1. Prérequis
- ✅ Docker Desktop installé et en cours d'exécution
- ✅ Compte Docker Hub (gratuit sur https://hub.docker.com)

### 2. Déploiement en 3 étapes

#### Option A: Script PowerShell (Recommandé pour Windows)
```powershell
# Exécuter le script de déploiement
.\deploy.ps1
```

#### Option B: Script Batch
```cmd
deploy.bat
```

#### Option C: Commandes manuelles
```powershell
# 1. Se connecter à Docker Hub
docker login

# 2. Construire l'image (remplacez 'votre-username')
docker build -t votre-username/laravel-app:latest .

# 3. Pousser l'image
docker push votre-username/laravel-app:latest

# 4. Tester localement depuis le registry
docker pull votre-username/laravel-app:latest
docker run -d -p 8000:80 votre-username/laravel-app:latest
```

### 3. Déploiement avec Docker Compose

Modifiez `docker-compose.prod.yml` et remplacez `votre-username`:
```yaml
image: votre-username/laravel-app:latest
```

Puis démarrez:
```powershell
docker-compose -f docker-compose.prod.yml up -d
```

## 🤖 Déploiement Automatisé (CI/CD)

### Config GitHub Actions (déjà prêt!)

1. **Allez dans votre repository GitHub:**
   - Settings → Secrets and variables → Actions
   
2. **Ajoutez ces secrets:**
   - `DOCKERHUB_USERNAME` : votre nom d'utilisateur Docker Hub
   - `DOCKERHUB_TOKEN` : créez un token dans Docker Hub → Account Settings → Security → New Access Token

3. **Push vers GitHub:**
```powershell
git add .
git commit -m "Add Docker Registry deployment"
git push origin main
```

L'image sera automatiquement construite et poussée vers Docker Hub ! 🎉

## 📦 Utiliser l'Image sur un Serveur

### Sur n'importe quel serveur avec Docker:
```bash
# Se connecter au serveur
ssh user@serveur.com

# Installer Docker (si nécessaire)
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Déployer l'application
mkdir laravel-app && cd laravel-app
docker run -d -p 80:80 votre-username/laravel-app:latest
```

## 🔄 Mise à Jour

### Après avoir modifié le code:
```powershell
# 1. Reconstruire et pousser
.\deploy.ps1

# 2. Sur le serveur, mettre à jour
docker pull votre-username/laravel-app:latest
docker-compose -f docker-compose.prod.yml up -d
```

## 📊 Commandes Utiles

```powershell
# Voir les images locales
docker images

# Voir les conteneurs en cours
docker ps

# Voir les logs
docker logs -f laravel_app_prod

# Arrêter l'application
docker-compose -f docker-compose.prod.yml down

# Nettoyer les images non utilisées
docker image prune -a
```

## 🎯 Plateformes de déploiement supportées

L'image peut être déployée sur:
- ✅ Docker Hub (gratuit)
- ✅ Render.com
- ✅ Railway.app
- ✅ AWS ECS/Fargate
- ✅ Google Cloud Run
- ✅ Azure Container Instances
- ✅ DigitalOcean Apps
- ✅ N'importe quel serveur avec Docker

## 🔐 Sécurité

Pour protéger votre image:
```powershell
# Rendre le repository Docker Hub privé
# → Allez sur hub.docker.com → votre image → Settings → Make Private
```

## ❓ Problèmes Courants

### Image trop grande
```powershell
# Utiliser le Dockerfile optimisé
docker build -f Dockerfile.optimized -t votre-username/laravel-app:latest .
```

### Erreur d'authentification
```powershell
# Se reconnecter
docker logout
docker login
```

### Port déjà utilisé
```powershell
# Changer le port dans docker-compose.prod.yml
ports:
  - "8080:80"  # Au lieu de 8000:80
```

## 📚 Documentation Complète

Pour plus de détails, consultez:
- [DOCKER_REGISTRY_DEPLOYMENT.md](DOCKER_REGISTRY_DEPLOYMENT.md) - Guide complet
- [.github/workflows/docker-publish.yml](.github/workflows/docker-publish.yml) - Pipeline CI/CD

---

**Besoin d'aide?** Ouvrez une issue sur GitHub!
