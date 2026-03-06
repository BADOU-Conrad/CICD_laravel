#!/bin/bash
set -e

echo "🚀 Starting deployment process..."

# Attendre que la base de données soit prête
echo "⏳ Waiting for database..."
sleep 5

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Exécuter les migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Optimiser l'application pour la production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour storage
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Définir les permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Deployment complete! Starting web server..."

# Démarrer Apache
exec apache2-foreground
