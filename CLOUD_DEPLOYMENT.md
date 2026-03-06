# Déploiement sur Plateformes Cloud avec Docker Registry

Ce guide montre comment déployer votre image Docker depuis Docker Hub vers différentes plateformes cloud.

## 🌐 Railway.app

### Via l'interface web:
1. Connectez-vous sur https://railway.app
2. New Project → Deploy from Docker Hub
3. Entrez: `votre-username/laravel-app:latest`
4. Ajoutez les variables d'environnement:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
   DB_CONNECTION=sqlite
   PORT=80
   ```
5. Deploy!

### Via CLI:
```bash
# Installer Railway CLI
npm install -g @railway/cli

# Se connecter
railway login

# Créer un nouveau projet
railway init

# Déployer
railway up
```

## 🎨 Render.com

Créez `render.yaml`:
```yaml
services:
  - type: web
    name: laravel-app
    env: docker
    dockerfilePath: ./Dockerfile
    # OU utiliser directement l'image Docker Hub:
    image:
      url: docker.io/votre-username/laravel-app:latest
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: APP_KEY
        value: base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
      - key: DB_CONNECTION
        value: sqlite
```

Ou via l'interface web:
1. New → Web Service
2. Choisir "Deploy from Docker Hub"
3. Image URL: `votre-username/laravel-app:latest`
4. Ajouter les variables d'environnement
5. Deploy

## ☁️ Google Cloud Run

```bash
# Installer gcloud CLI
# https://cloud.google.com/sdk/docs/install

# Se connecter
gcloud auth login

# Configurer le projet
gcloud config set project your-project-id

# Option 1: Utiliser l'image de Docker Hub
gcloud run deploy laravel-app \
  --image=docker.io/votre-username/laravel-app:latest \
  --platform=managed \
  --region=europe-west1 \
  --allow-unauthenticated \
  --set-env-vars="APP_ENV=production,APP_DEBUG=false,APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg="

# Option 2: Pousser vers Google Container Registry
docker tag votre-username/laravel-app:latest gcr.io/your-project-id/laravel-app:latest
docker push gcr.io/your-project-id/laravel-app:latest

gcloud run deploy laravel-app \
  --image=gcr.io/your-project-id/laravel-app:latest \
  --platform=managed \
  --region=europe-west1 \
  --allow-unauthenticated
```

## 🔷 Azure Container Instances

```bash
# Installer Azure CLI
# https://learn.microsoft.com/en-us/cli/azure/install-azure-cli

# Se connecter
az login

# Créer un groupe de ressources
az group create --name laravel-rg --location westeurope

# Déployer depuis Docker Hub
az container create \
  --resource-group laravel-rg \
  --name laravel-app \
  --image votre-username/laravel-app:latest \
  --dns-name-label laravel-app-unique-name \
  --ports 80 \
  --environment-variables \
    APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg= \
    DB_CONNECTION=sqlite

# Obtenir l'URL
az container show \
  --resource-group laravel-rg \
  --name laravel-app \
  --query ipAddress.fqdn \
  --output tsv
```

## 📦 AWS ECS (Elastic Container Service)

### Via AWS CLI:
```bash
# Installer AWS CLI
# https://aws.amazon.com/cli/

# Se connecter
aws configure

# Créer un cluster
aws ecs create-cluster --cluster-name laravel-cluster

# Créer une définition de tâche (task-definition.json)
```

`task-definition.json`:
```json
{
  "family": "laravel-app",
  "networkMode": "awsvpc",
  "requiresCompatibilities": ["FARGATE"],
  "cpu": "256",
  "memory": "512",
  "containerDefinitions": [
    {
      "name": "laravel-app",
      "image": "votre-username/laravel-app:latest",
      "portMappings": [
        {
          "containerPort": 80,
          "protocol": "tcp"
        }
      ],
      "environment": [
        {"name": "APP_ENV", "value": "production"},
        {"name": "APP_DEBUG", "value": "false"},
        {"name": "APP_KEY", "value": "base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg="},
        {"name": "DB_CONNECTION", "value": "sqlite"}
      ],
      "essential": true
    }
  ]
}
```

```bash
# Enregistrer la définition de tâche
aws ecs register-task-definition --cli-input-json file://task-definition.json

# Créer un service
aws ecs create-service \
  --cluster laravel-cluster \
  --service-name laravel-service \
  --task-definition laravel-app \
  --desired-count 1 \
  --launch-type FARGATE \
  --network-configuration "awsvpcConfiguration={subnets=[subnet-12345],securityGroups=[sg-12345],assignPublicIp=ENABLED}"
```

## 🌊 DigitalOcean App Platform

### Via l'interface web:
1. Apps → Create App
2. Service Provider → Docker Hub
3. Repository: `votre-username/laravel-app`
4. Tag: `latest`
5. Environment Variables:
   - APP_ENV=production
   - APP_DEBUG=false
   - APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
6. Launch App

### Via fichier de configuration `.do/app.yaml`:
```yaml
name: laravel-app
services:
  - name: web
    image:
      registry_type: DOCKER_HUB
      registry: votre-username
      repository: laravel-app
      tag: latest
    http_port: 80
    envs:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_KEY
        value: base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
      - key: DB_CONNECTION
        value: sqlite
```

```bash
# Déployer avec doctl
doctl apps create --spec .do/app.yaml
```

## 🐋 Portainer (Auto-hébergé)

Si vous avez un serveur avec Portainer:
1. Stacks → Add Stack
2. Web editor:
```yaml
version: '3.8'
services:
  laravel:
    image: votre-username/laravel-app:latest
    ports:
      - "80:80"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
      - DB_CONNECTION=sqlite
    restart: unless-stopped
```
3. Deploy the stack

## 🚀 Fly.io

```bash
# Installer flyctl
# https://fly.io/docs/hands-on/install-flyctl/

# Se connecter
flyctl auth login

# Créer l'app
flyctl apps create laravel-app

# Créer fly.toml
```

`fly.toml`:
```toml
app = "laravel-app"

[build]
  image = "votre-username/laravel-app:latest"

[env]
  APP_ENV = "production"
  APP_DEBUG = "false"
  DB_CONNECTION = "sqlite"

[[services]]
  http_checks = []
  internal_port = 80
  processes = ["app"]
  protocol = "tcp"
  script_checks = []

  [[services.ports]]
    force_https = true
    handlers = ["http"]
    port = 80

  [[services.ports]]
    handlers = ["tls", "http"]
    port = 443
```

```bash
# Déployer
flyctl deploy

# Ajouter les secrets
flyctl secrets set APP_KEY=base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=
```

## 📊 Comparaison des Plateformes

| Plateforme | Gratuit | Facile | Prix (estimé) | Recommandation |
|------------|---------|--------|---------------|----------------|
| Railway | ✅ $5/mois | ⭐⭐⭐⭐⭐ | $5-20/mois | Débutants |
| Render | ✅ Limité | ⭐⭐⭐⭐ | $7-20/mois | Production |
| Fly.io | ✅ Généreux | ⭐⭐⭐⭐ | $0-10/mois | Petits projets |
| DigitalOcean | ❌ | ⭐⭐⭐ | $5-15/mois | Startups |
| Google Cloud Run | ✅ 2M req/mois | ⭐⭐⭐ | Pay-per-use | Trafic variable |
| AWS ECS | ❌ | ⭐⭐ | $10-50/mois | Entreprises |
| Azure ACI | ❌ | ⭐⭐⭐ | $10-30/mois | Entreprises Microsoft |

## 🔐 Image Privée Docker Hub

Si votre image est privée sur Docker Hub, ajoutez l'authentification:

### Railway/Render
Ajoutez dans les variables d'environnement:
```
DOCKER_USERNAME=votre-username
DOCKER_PASSWORD=votre-token
```

### Google Cloud Run
```bash
docker login
gcloud auth configure-docker
```

### AWS ECS
Créez un secret dans AWS Secrets Manager avec vos credentials Docker Hub.

## ⚡ Optimisations

Pour des déploiements plus rapides:
1. **Utilisez le cache de build** (déjà configuré dans GitHub Actions)
2. **Images multi-architecture** (linux/amd64, linux/arm64)
3. **Compression** des layers Docker
4. **CDN** pour les assets statiques

## 🆘 Support

Chaque plateforme a sa propre documentation:
- Railway: https://docs.railway.app/
- Render: https://render.com/docs
- Google Cloud: https://cloud.google.com/run/docs
- AWS ECS: https://docs.aws.amazon.com/ecs/
- Azure: https://docs.microsoft.com/azure/container-instances/
- Fly.io: https://fly.io/docs/
- DigitalOcean: https://docs.digitalocean.com/products/app-platform/

---

**Conseil:** Commencez avec Railway ou Render pour la simplicité, puis migrez vers AWS/GCP pour plus de contrôle.
