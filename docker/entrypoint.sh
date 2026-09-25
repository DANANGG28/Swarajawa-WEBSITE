#!/usr/bin/env bash
# =============================================================================
# Entrypoint container produksi (Dokploy).
# Menyiapkan runtime Laravel + edge-tts, lalu menyalakan supervisord
# (php-fpm + nginx + queue worker).
# =============================================================================
set -euo pipefail

cd /var/www/html

log() { echo "[entrypoint] $*"; }

# --- 1. .env fallback (Dokploy biasanya inject lewat environment variables) ---
if [ ! -f .env ] && [ -f .env.example ]; then
    log ".env tidak ditemukan — menyalin dari .env.example"
    cp .env.example .env
fi

# --- 2. Direktori runtime & permission ---------------------------------------
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    storage/image \
    bootstrap/cache

# --- 3. Restore data seed bila volume persistent masih kosong -----------------
seed_if_empty() {
    local target="$1" seed="$2"
    mkdir -p "$target"
    if [ -z "$(ls -A "$target" 2>/dev/null)" ] && [ -d "$seed" ]; then
        log "Volume '$target' kosong — menyalin data awal dari '$seed'"
        cp -a "$seed/." "$target/"
    fi
}
seed_if_empty storage/app/corpus /opt/app-seed/corpus
seed_if_empty storage/image /opt/app-seed/image

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# --- 4. Render konfigurasi nginx dengan PORT dari Dokploy ---------------------
export PORT="${PORT:-80}"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/conf.d/default.conf
log "Nginx listen di port ${PORT}"

# --- 5. Validasi APP_KEY ------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    log "PERINGATAN: APP_KEY kosong. Set APP_KEY di environment Dokploy."
fi

# --- 6. Bootstrap Laravel -----------------------------------------------------
log "Menautkan storage publik"
php artisan storage:link || true

log "Menjalankan migrasi database"
php artisan migrate --force

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    log "Menjalankan seeder (RUN_SEEDERS=true)"
    php artisan db:seed --force
fi

log "Membangun cache konfigurasi/route/view"
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache

# --- 7. Cek ketersediaan edge-tts --------------------------------------------
if command -v edge-tts >/dev/null 2>&1; then
    log "edge-tts ditemukan: $(command -v edge-tts)"
else
    log "PERINGATAN: edge-tts tidak ditemukan di PATH — fitur TTS akan memakai mode mock."
fi

log "Menyalakan supervisord (php-fpm + nginx + queue)"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
