# 🚀 Guide de Déploiement sur Render

## 📋 Prérequis

- Compte Render (gratuit): https://render.com
- Code sur GitHub avec les branches `dev`, `stage`, et `main`
- Fichiers configurés : `render.yaml`, `Dockerfile`, `docker-entrypoint.sh` ✅

## 🎯 Étapes de Déploiement

### 1. Connexion à Render

1. Allez sur https://render.com
2. Cliquez sur **"Get Started"** ou **"Sign In"**
3. Connectez-vous avec votre compte GitHub

### 2. Création du Projet (Blueprint)

1. Sur le dashboard Render, cliquez sur **"New +"** → **"Blueprint"**
2. Connectez votre repository GitHub : `BADOU-Conrad/CICD_laravel`
3. Render détectera automatiquement le fichier `render.yaml`
4. Donnez un nom au blueprint : `cicd-laravel-app`
5. Cliquez sur **"Apply"**

### 3. Configuration Automatique

Render va créer automatiquement :
- ✅ Un service web (application Laravel)
- ✅ Une base de données PostgreSQL (plan gratuit)
- ✅ Toutes les variables d'environnement nécessaires

### 4. Génération de la Clé d'Application

⚠️ **IMPORTANT** : Laravel nécessite une clé d'application unique.

#### Option 1 : Laisser Render Générer (Recommandé)
La clé sera générée automatiquement au premier déploiement via le script `docker-entrypoint.sh`

#### Option 2 : Générer Manuellement
```bash
# En local
php artisan key:generate --show
```
Puis ajoutez la clé dans les Environment Variables de Render :
- Nom : `APP_KEY`
- Valeur : `base64:VotreCléGénérée...`

### 5. Configuration des Variables d'Environnement (Optionnel)

Si nécessaire, ajoutez ou modifiez les variables dans Render :

1. Allez dans votre service web → **"Environment"**
2. Variables déjà configurées automatiquement :
   - `APP_NAME` : Laravel
   - `APP_ENV` : production
   - `APP_DEBUG` : false
   - `APP_URL` : https://cicd-laravel.onrender.com
   - `DB_CONNECTION` : pgsql
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (depuis PostgreSQL)

3. Ajoutez des variables supplémentaires si nécessaire :
   - Services email (MAIL_*)
   - Services de stockage (AWS S3, etc.)
   - API keys

### 6. Déploiement

1. Render va automatiquement :
   - 📦 Cloner votre repository
   - 🐳 Construire l'image Docker
   - 🗄️ Créer la base de données PostgreSQL
   - 🔄 Exécuter les migrations
   - ⚡ Optimiser l'application
   - 🚀 Démarrer le serveur

2. Le premier déploiement peut prendre 5-10 minutes

### 7. Vérification du Déploiement

Une fois le déploiement terminé :
1. Render vous donnera une URL : `https://cicd-laravel.onrender.com`
2. Cliquez sur l'URL pour vérifier que l'application fonctionne
3. Vérifiez les logs dans Render Dashboard → **"Logs"**

## 🔄 Déploiement Automatique (CI/CD)

### Configuration des Déploiements Automatiques

Render peut déployer automatiquement à chaque push sur certaines branches :

1. Dans votre service web → **"Settings"**
2. Section **"Build & Deploy"**
3. **Auto-Deploy** : Yes
4. **Branch** : `main` (branche de production)

### Workflow Recommandé

```
dev → push → Tests GitHub Actions
  ↓
  PR vers stage
  ↓
stage → Tests OK → Merge
  ↓
  PR vers main
  ↓
main → Merge → 🚀 Déploiement Automatique sur Render
```

### Déploiement Manuel

Si vous voulez déployer manuellement :
1. Allez dans votre service → **"Manual Deploy"**
2. Sélectionnez la branche
3. Cliquez sur **"Deploy"**

## 🗄️ Gestion de la Base de Données

### Accès à PostgreSQL

1. Dashboard Render → Votre base de données `cicd-laravel-db`
2. Onglet **"Info"** : Vous trouverez :
   - Internal Database URL
   - External Database URL (pour connexion depuis l'extérieur)
   - PSQL Command (ligne de commande)

### Exécuter des Commandes

Pour exécuter des commandes Artisan en production :

1. Allez dans votre service web → **"Shell"**
2. Exécutez des commandes :
```bash
php artisan migrate:status
php artisan db:seed
php artisan cache:clear
php artisan optimize
```

### Sauvegardes

Render Pro offre des sauvegardes automatiques. Pour le plan gratuit :
- Sauvegardes manuelles via l'interface Render
- Exportation de la base de données via `pg_dump`

## 📊 Monitoring et Logs

### Voir les Logs

1. Service web → **"Logs"**
2. Logs en temps réel du déploiement et de l'application
3. Filtres disponibles pour rechercher des erreurs

### Métriques

1. Service web → **"Metrics"**
2. Voir :
   - CPU Usage
   - Memory Usage
   - Request Count
   - Response Times

## ⚙️ Paramètres du Plan Gratuit

Le plan gratuit de Render inclut :
- ✅ 750 heures de build par mois
- ✅ 512 MB RAM
- ✅ Service s'endort après 15 min d'inactivité
- ✅ 90 jours de rétention des logs
- ✅ PostgreSQL avec 1 GB de stockage
- ⚠️ Le service prend 30-60 secondes pour se réveiller

### Éviter l'Endormissement (Optionnel)

Utilisez un service de ping gratuit :
- https://uptimerobot.com
- https://cron-job.org

Configurez un ping toutes les 5-10 minutes vers votre URL.

## 🔧 Dépannage

### Problème : Build échoue

**Solution :**
1. Vérifiez les logs de build
2. Vérifiez que `Dockerfile` et `docker-entrypoint.sh` sont présents
3. Vérifiez que `docker-entrypoint.sh` a les bonnes permissions (chmod +x)

### Problème : Erreur 500

**Solution :**
1. Vérifiez les logs : `php artisan log:tail` ou dans Render Logs
2. Vérifiez que `APP_KEY` est définie
3. Vérifiez la connexion à la base de données
4. Exécutez dans le Shell :
```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
```

### Problème : CSS/JS ne charge pas

**Solution :**
1. Vérifiez que `APP_URL` est correct
2. Si vous utilisez Vite, compilez les assets avant le déploiement :
```bash
npm run build
git add public/build
git commit -m "Add compiled assets"
git push
```

### Problème : Base de données vide

**Solution :**
```bash
# Dans le Shell Render
php artisan migrate:fresh --force
php artisan db:seed --force
```

## 📝 Fichiers de Configuration

### render.yaml
Définit l'infrastructure :
- Service web (Docker)
- Base de données PostgreSQL
- Variables d'environnement

### Dockerfile
- Image PHP 8.2 avec Apache
- Extensions PHP nécessaires (pdo_pgsql, mbstring, etc.)
- Configuration Apache pour Laravel
- Installation des dépendances Composer

### docker-entrypoint.sh
Script exécuté au démarrage :
- Attente de la base de données
- Génération de APP_KEY
- Migrations
- Optimisations (cache config, routes, views)
- Permissions

## 🔗 Ressources

- Documentation Render : https://render.com/docs
- Documentation Laravel Deployment : https://laravel.com/docs/deployment
- Render Community : https://community.render.com

## ✅ Checklist Avant Déploiement

- [x] Fichier `render.yaml` créé
- [x] Fichier `docker-entrypoint.sh` créé et exécutable
- [x] `Dockerfile` mis à jour avec support PostgreSQL
- [ ] Code pushé sur la branche `main`
- [ ] Compte Render créé
- [ ] Repository GitHub connecté à Render
- [ ] Variables d'environnement sensibles configurées (si nécessaire)
- [ ] Tests passent en local

## 🚀 Prêt à Déployer !

Une fois tous les fichiers commitées et pushés sur `main`, suivez les étapes de déploiement ci-dessus. Bonne chance ! 🎉
