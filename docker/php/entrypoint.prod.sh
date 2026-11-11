#!/bin/sh
set -e

# public_assets is a named volume mounted over /var/www/html/public in
# production so nginx (a separate container/image) can serve the built
# assets. Docker only seeds a named volume from the image on first
# creation, so any public/ files added or changed in later image builds
# never reach the volume on their own. Re-sync on every start so the
# volume always mirrors what was just built, without touching the
# storage/ symlink target (that lives on its own persistent volume).
rsync -a --delete --exclude 'storage' /var/www/html/public-build/ /var/www/html/public/

# storage/ é um volume nomeado: o mkdir do Dockerfile só vale quando o volume é criado vazio. Se ele vier
# de antes, ou perder as subpastas, o Laravel quebra com "Please provide a valid cache path". Recria a cada start.
for dir in \
    storage/app/private/livewire-tmp \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
do
    mkdir -p "/var/www/html/$dir"
    chown www-data:www-data "/var/www/html/$dir"
    chmod 775 "/var/www/html/$dir"
done
# Pastas intermediárias criadas acima pelo mkdir -p (como root).
chown www-data:www-data /var/www/html/storage /var/www/html/storage/app /var/www/html/storage/framework \
    /var/www/html/storage/framework/cache /var/www/html/storage/app/private

php artisan storage:link --force >/dev/null 2>&1 || true

chown -R www-data:www-data /var/www/html/public

exec "$@"
