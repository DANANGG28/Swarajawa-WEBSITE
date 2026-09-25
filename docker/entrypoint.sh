#!/usr/bin/env bash
# =============================================================================
# Entrypoint container produksi (Dokploy).
# Menyiapkan runtime Laravel + edge-tts, lalu menyalakan supervisord
# (php-fpm + nginx). Migrasi database dijalankan di background agar web
# server tidak menunggu DB (mencegah 502 saat DB belum siap).
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
export PORT="${PORT:-8088}"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/conf.d/default.conf
log "Nginx listen di port ${PORT}"

# --- 5. Validasi APP_KEY ------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    log "PERINGATAN: APP_KEY kosong. Set APP_KEY di environment Dokploy."
fi

# --- 6. Tautkan storage publik ------------------------------------------------
log "Menautkan storage publik"
php artisan storage:link || true

# --- 7. Cache konfigurasi/route/view (tidak membutuhkan database) -------------
log "Membangun cache konfigurasi/route/view"
php artisan config:cache || log "PERINGATAN: config:cache gagal"
php artisan route:cache || log "PERINGATAN: route:cache gagal"
php artisan view:cache || log "PERINGATAN: view:cache gagal"

# --- 8. Migrasi + seeder di BACKGROUND (agar web server langsung siap) --------
migrate_in_background() {
    case "${DB_CONNECTION:-}" in
        pgsql|mysql|mariadb) ;;
        *) return 0 ;;
    esac

    local host="${DB_HOST:-127.0.0.1}"
    local port="${DB_PORT:-5432}"

    local resolved
    resolved="$(getent hosts "$host" 2>/dev/null | awk '{print $1}' | paste -sd, - || true)"
    log "Resolusi DNS ${host} -> ${resolved:-GAGAL (host tidak dikenal dari container ini)}"
    log "Hostname container ini: $(hostname)"

    local ready=false
    for i in $(seq 1 90); do
        if (echo > "/dev/tcp/${host}/${port}") >/dev/null 2>&1; then
            log "Database ${host}:${port} siap (percobaan ${i})"
            ready=true
            break
        fi
        log "Menunggu database ${host}:${port}... (${i}/90)"
        sleep 2
    done

    if [ "$ready" != "true" ]; then
        log "PERINGATAN: database ${host}:${port} tidak merespons — migrasi dilewati"
        return 1
    fi

    for i in $(seq 1 10); do
        if php artisan migrate --force; then
            log "Migrasi selesai"
            if [ "${RUN_SEEDERS:-false}" = "true" ]; then
                log "Menjalankan seeder (RUN_SEEDERS=true)"
                php artisan db:seed --force || log "PERINGATAN: seeder gagal"
            fi
            return 0
        fi
        log "Migrasi gagal (percobaan ${i}/10) — ulangi dalam 5 detik"
        sleep 5
    done

    log "PERINGATAN: migrasi gagal — cek kredensial/jaringan database"
    return 1
}
migrate_in_background &

# --- 9. Cek ketersediaan edge-tts --------------------------------------------
if command -v edge-tts >/dev/null 2>&1; then
    log "edge-tts ditemukan: $(command -v edge-tts)"
else
    log "PERINGATAN: edge-tts tidak ditemukan di PATH — fitur TTS akan memakai mode mock."
fi

log "Menyalakan supervisord (php-fpm + nginx)"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
