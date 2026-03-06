# Guide du Workflow Git et CI/CD

## 🌿 Structure des Branches

Votre projet utilise maintenant trois branches principales :

- **`dev`** : Branche de développement (branche par défaut pour les push locaux)
- **`stage`** : Branche de staging/pré-production
- **`main`** : Branche de production

## ✅ Configuration Actuelle

### 1. Branches créées et synchronisées
- ✅ Branche `dev` créée et poussée vers GitHub
- ✅ Branche `stage` créée et poussée vers GitHub
- ✅ Votre branche locale est maintenant sur `dev`

### 2. Pipeline CI/CD configurée
- ✅ Workflow GitHub Actions créé : `.github/workflows/tests.yml`
- ✅ Les tests s'exécutent automatiquement lors de :
  - Pull Requests vers `stage` ou `main`
  - Push vers `dev` ou `stage`

## 🔄 Workflow de Développement

### Développement quotidien
```bash
# Vous êtes déjà sur dev
git add .
git commit -m "votre message"
git push  # Pousse automatiquement vers origin/dev
```

### Pull Request de dev vers stage
1. Créez un PR sur GitHub : `dev` → `stage`
2. Les tests unitaires s'exécutent automatiquement
3. Le merge n'est autorisé que si les tests passent ✅

### Pull Request de stage vers main
1. Créez un PR sur GitHub : `stage` → `main`
2. Les tests s'exécutent à nouveau
3. Le merge n'est autorisé que si les tests passent ✅

## 🔒 Configuration des Règles de Protection (IMPORTANT)

Pour forcer l'exécution des tests avant le merge, configurez les règles de protection sur GitHub :

### Étape 1 : Accéder aux paramètres
1. Allez sur : https://github.com/BADOU-Conrad/CICD_laravel
2. Cliquez sur **Settings** (Paramètres)
3. Dans le menu latéral, cliquez sur **Branches**

### Étape 2 : Protéger la branche `stage`
1. Cliquez sur **Add branch protection rule**
2. Dans "Branch name pattern", écrivez : `stage`
3. Cochez les options suivantes :
   - ✅ **Require a pull request before merging**
   - ✅ **Require status checks to pass before merging**
   - ✅ **Require branches to be up to date before merging**
   - Dans la recherche de status checks, tapez : `tests` et sélectionnez **tests (8.2)**
4. Cliquez sur **Create** ou **Save changes**

### Étape 3 : Protéger la branche `main`
1. Répétez les mêmes étapes pour la branche `main`
2. Branch name pattern : `main`
3. Mêmes options cochées

## 📋 Exécution des Tests

Les tests sont définis dans votre fichier `phpunit.xml` et situés dans le dossier `tests/`.

### Exécuter les tests localement
```bash
php artisan test
```

### Ce que teste la pipeline
- Tests unitaires (Unit)
- Tests de fonctionnalités (Feature)
- Utilise PHP 8.2
- Base de données SQLite pour les tests

## 🚀 Exemple de Workflow Complet

```bash
# 1. Développer sur dev
git checkout dev
# ... faire vos modifications ...
git add .
git commit -m "Ajout d'une nouvelle fonctionnalité"
git push

# 2. Créer un PR dev → stage sur GitHub
# Les tests s'exécutent automatiquement

# 3. Si les tests passent, merger le PR

# 4. Créer un PR stage → main sur GitHub
# Les tests s'exécutent à nouveau

# 5. Si les tests passent, merger en production
```

## ⚠️ Points Importants

- **Toujours développer sur `dev`**
- **Ne jamais pousser directement sur `main` ou `stage`**
- **Toujours passer par des Pull Requests**
- **Les tests doivent passer avant le merge**
- **Bien synchroniser les branches avant de créer un PR**

## 🔧 Commandes Utiles

```bash
# Voir votre branche actuelle
git branch

# Changer de branche
git checkout dev
git checkout stage
git checkout main

# Mettre à jour votre branche locale
git pull

# Voir l'état des modifications
git status

# Voir l'historique
git log --oneline --graph --all
```

## 📞 Support

En cas de problème avec les tests ou le workflow, vérifiez :
1. Le fichier `.github/workflows/tests.yml`
2. Les logs de GitHub Actions sur votre repo
3. Que les dépendances composer sont à jour
