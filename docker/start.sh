#!/usr/bin/env sh
set -e

echo "==> 🚀 Démarrage Co-Cooking API..."

cd /var/www/html

# Vider les caches compilés (ils sont recrées au démarrage)
php artisan config:clear  2>/dev/null || true
php artisan route:clear   2>/dev/null || true
php artisan view:clear    2>/dev/null || true

# Recréer les caches avec les vraies variables d'environnement Render
php artisan config:cache
php artisan route:cache
php artisan view:cache

# S'assurer que les permissions sont correctes
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> ✅ Prêt — lancement de Nginx + PHP-FPM..."

# Lancer Nginx + PHP-FPM via Supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf