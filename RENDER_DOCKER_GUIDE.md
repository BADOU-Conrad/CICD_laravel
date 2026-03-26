# 🚀 Guide Render - Déploiement depuis Docker Hub

## 🔗 Obtenir le lien de votre image Docker

### Format du lien
```
docker.io/VOTRE-USERNAME/laravel-app:latest
```

**Exemple :**
- Username Docker Hub : `john123`
- Lien complet : `docker.io/john123/laravel-app:latest`
- Format court : `john123/laravel-app:latest`

---

## 📦 Étape 1 : Publier votre image sur Docker Hub

### Option A : Script automatique (Recommandé)
```powershell
# Exécuter le script de déploiement
.\deploy.ps1
```

### Option B : Commandes manuelles
```powershell
# 1. Se connecter
docker login

# 2. Construire (remplacez 'votre-username')
docker build -t votre-username/laravel-app:latest .

# 3. Pousser
docker push votre-username/laravel-app:latest
```

### Vérifier sur Docker Hub
Visitez : `https://hub.docker.com/r/votre-username/laravel-app`

Vous verrez votre image avec le bouton **"Copy pull command"** :
```bash
docker pull votre-username/laravel-app:latest
```

C'est votre lien d'image ! (sans le `docker pull`)

---

## 🎨 Étape 2 : Déployer sur Render

### **Méthode 1 : Interface Web (Plus simple)**

#### A. Créer le service
1. Allez sur https://render.com
2. Cliquez **Dashboard**
3. **New +** → **Web Service**
4. Sélectionnez **"Deploy an existing image from a registry"**

#### B. Configuration de l'image
- **Image URL** : 
  ```
  docker.io/votre-username/laravel-app:latest
  ```
  ⚠️ **Important** : Remplacez `votre-username` par votre vrai nom d'utilisateur Docker Hub

- **Name** : `laravel-app` (ou un nom de votre choix)
- **Region** : Oregon (US West) ou Frankfurt (Europe)
- **Branch** : main (si connecté à GitHub)

#### C. Paramètres du service
- **Instance Type** : 
  - Free (pour tester)
  - Starter $7/mois (pour production)

#### D. Variables d'environnement
Cliquez **"Advanced"** puis ajoutez ces variables :

| Key | Value |
|-----|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:p6FRhi2xDkoon5ayy8WhhbIpw3SPRif7+c8+OaYpOjg=` |
| `DB_CONNECTION` | `sqlite` |
| `DB_DATABASE` | `/var/www/html/database/database.sqlite` |
| `LOG_CHANNEL` | `stderr` |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |

#### E. Déployer
1. Cliquez **"Create Web Service"**
2. Render va télécharger votre image et la déployer
3. Attendez 2-3 minutes
4. Votre app sera disponible sur : `https://votre-app.onrender.com`

---

### **Méthode 2 : Fichier YAML (Automatique)**

#### A. Modifier render.docker.yaml
```yaml
services:
  - type: web
    name: laravel-app
    runtime: image
    plan: free
    image:
      url: docker.io/VOTRE-USERNAME/laravel-app:latest  # ← CHANGEZ ICI
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

#### B. Déployer avec GitHub
1. Poussez render.docker.yaml vers GitHub
   ```powershell
   git add render.docker.yaml
   git commit -m "Add Render Docker config"
   git push
   ```

2. Sur Render :
   - **New +** → **Blueprint**
   - Sélectionnez votre repository
   - Render détectera automatiquement `render.docker.yaml`
   - **Apply**

---

## 🔄 Mise à jour de l'application

### Après avoir modifié votre code :

```powershell
# 1. Reconstruire et pousser l'image
.\deploy.ps1

# 2. Sur Render, déclencher un redéploiement :
# - Dashboard → votre service → "Manual Deploy" → "Deploy latest commit"
# OU
# - Render téléchargera automatiquement la nouvelle image :latest
```

### Déploiement automatique
Pour que Render redéploie automatiquement quand vous publiez une nouvelle image :

1. Sur Render Dashboard → votre service
2. **Settings** → **Deploy**
3. Cochez **"Auto-Deploy"** : Yes

---

## 📊 Exemples de liens d'images valides

### Docker Hub (public)
```
docker.io/john123/laravel-app:latest
john123/laravel-app:latest
john123/laravel-app:v1.0.0
```

### Docker Hub (privé)
```
docker.io/john123/private-app:latest
```
⚠️ Vous devrez ajouter vos credentials Docker Hub dans Render :
- Settings → Environment → Add Secret File
- Ajoutez `.dockerconfigjson` avec vos credentials

### GitHub Container Registry
```
ghcr.io/username/laravel-app:latest
```

### Google Container Registry
```
gcr.io/project-id/laravel-app:latest
```

---

## ✅ Vérification

### Tester votre image localement avant de déployer
```powershell
# Télécharger votre image
docker pull votre-username/laravel-app:latest

# Tester localement
docker run -d -p 8000:80 votre-username/laravel-app:latest

# Ouvrir http://localhost:8000
```

Si ça fonctionne localement, ça fonctionnera sur Render !

---

## 🆘 Problèmes courants

### "Image not found"
- ✅ Vérifiez que l'image existe sur Docker Hub
- ✅ Vérifiez l'orthographe du nom d'utilisateur et du nom d'image
- ✅ Si l'image est privée, ajoutez vos credentials Docker Hub

### "Application failed to respond"
- ✅ Vérifiez que votre app écoute sur le port 80 (pas 8000)
- ✅ Ajoutez la variable d'environnement `PORT=80`

### "Authentication required"
Si votre image Docker Hub est **privée** :
1. Render Dashboard → votre service
2. **Settings** → **Environment**
3. Ajoutez :
   - `DOCKER_USERNAME` = votre username
   - `DOCKER_PASSWORD` = votre token (créé sur hub.docker.com)

---

## 📋 Checklist complète

- [ ] Image construite et poussée sur Docker Hub
- [ ] Image testée localement
- [ ] Lien d'image copié (format : `username/laravel-app:latest`)
- [ ] Service créé sur Render
- [ ] Variables d'environnement configurées
- [ ] Service déployé et accessible

---

## 🎯 Résumé rapide

**Votre lien d'image :**
```
docker.io/VOTRE-USERNAME/laravel-app:latest
```

**Sur Render :**
1. New → Web Service → Deploy from registry
2. Coller votre lien d'image
3. Ajouter les variables d'environnement
4. Deploy !

**URL de votre app :**
```
https://votre-app-name.onrender.com
```

---

Besoin d'aide ? Consultez :
- [render.docker.yaml](render.docker.yaml) - Configuration complète
- [DOCKER_REGISTRY_DEPLOYMENT.md](DOCKER_REGISTRY_DEPLOYMENT.md) - Guide Docker Hub
- https://render.com/docs/deploy-an-image
