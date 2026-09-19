#!/bin/bash
# Catatan Sail/Docker: Laravel berjalan di container `laravel.test`, jadi
# edge-tts harus terpasang DI DALAM container. Jalankan:
#   docker compose exec laravel.test bash setup-edge-tts.sh
# (atau manual: apt-get install -y python3-pip && pip3 install --break-system-packages edge-tts)
set -e

echo "== Cek Python =="
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 belum terinstall. Install dulu (mis. 'sudo apt install python3 python3-pip') lalu jalankan ulang script ini."
    exit 1
fi
python3 --version

echo "== Install edge-tts =="
pip install edge-tts --break-system-packages

echo "== Cek voice Jawa tersedia =="
if edge-tts --list-voices | grep -q "jv-ID-DimasNeural"; then
    echo "✅ jv-ID-DimasNeural ditemukan"
else
    echo "⚠️  jv-ID-DimasNeural tidak ditemukan di voice list — cek koneksi internet atau versi edge-tts"
fi

echo "== Tes generate audio =="
edge-tts --voice jv-ID-DimasNeural --text "Sugeng enjing, setup rampung" --write-media /tmp/test_edge_tts.mp3

if [ -f /tmp/test_edge_tts.mp3 ]; then
    echo "✅ Setup berhasil! File tes ada di /tmp/test_edge_tts.mp3"
else
    echo "❌ Setup gagal, cek pesan error di atas"
    exit 1
fi
