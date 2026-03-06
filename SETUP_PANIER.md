# 🛒 Système de Produits et Panier - Guide d'installation

## Commandes à exécuter

### 1. Exécuter les migrations
```bash
php artisan migrate
```

### 2. Remplir la base de données avec des produits de test
```bash
php artisan db:seed --class=ProductSeeder
```

### 3. Démarrer le serveur
```bash
php artisan serve
```

## Pages disponibles

- **Page produits** : http://localhost:8000/products
- **Page panier** : http://localhost:8000/cart

## Fonctionnalités

### Page Produits (`/products`)
- Affiche tous les produits avec leurs informations (nom, prix, stock, date d'ajout)
- Bouton "Ajouter au panier" pour chaque produit
- Indication visuelle des produits déjà dans le panier
- Badge sur le lien panier montrant le nombre total d'articles
- Désactivation du bouton si le stock est épuisé

### Page Panier (`/cart`)
- Liste des produits ajoutés au panier
- Pour chaque produit :
  - Nom et prix unitaire
  - Quantité au panier (avec boutons +/-)
  - Stock restant disponible
  - Sous-total (prix × quantité)
- Total général du panier
- Bouton pour retirer un produit
- Alertes visuelles pour le stock faible

## Structure de la base de données

### Table `products`
- `id` : Identifiant unique
- `name` : Nom du produit
- `price` : Prix (avec 2 décimales)
- `stock` : Quantité disponible
- `created_at` : Date d'ajout
- `updated_at` : Date de modification

## Données de test

Le seeder crée 10 produits informatiques avec différents prix et stocks variés.
