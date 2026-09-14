<script>
    (function () {
        const TEMPLATES = {
            pilihan_ganda: {
                opsi: JSON.stringify([
                    { label: 'A', teks: '' },
                    { label: 'B', teks: '' },
                    { label: 'C', teks: '' },
                    { label: 'D', teks: '' },
                ], null, 2),
                kunci: JSON.stringify({ jawaban: 'A' }, null, 2),
            },
            susun_kalimat: {
                opsi: JSON.stringify(['kata1', 'kata2', 'kata3'], null, 2),
                kunci: JSON.stringify({ susunan: ['kata1', 'kata2', 'kata3'] }, null, 2),
            },
            pencocokan_arti: {
                opsi: JSON.stringify([{ kiri: 'mangan', kanan: 'makan' }], null, 2),
                kunci: JSON.stringify({ pasangan: { mangan: 'makan' } }, null, 2),
            },
            puzzle_pakaian_adat: {
                opsi: JSON.stringify([{ id: 1, nama: 'Blangkon' }, { id: 2, nama: 'Beskap' }], null, 2),
                kunci: JSON.stringify({ urutan: [1, 2] }, null, 2),
            },
            menulis_aksara: {
                opsi: JSON.stringify({ aksara: 'ha', petunjuk: 'Tlusuri bayangan aksara.' }, null, 2),
                kunci: JSON.stringify({ paths: [[[0.3, 0.3], [0.5, 0.5], [0.7, 0.3]]] }, null, 2),
            },
            kuis_suara: {
                opsi: JSON.stringify({ instruksi: 'Ngucapna ukara kanthi cetha.' }, null, 2),
                kunci: JSON.stringify({ teks: 'sugeng enjing' }, null, 2),
            },
        };

        document.querySelectorAll('[data-soal-form]').forEach((form) => {
            const tipe = form.querySelector('[data-tipe]');
            const opsi = form.querySelector('[data-opsi]');
            const kunci = form.querySelector('[data-kunci]');

            const apply = (force) => {
                const tpl = TEMPLATES[tipe.value];
                if (!tpl) return;
                if (force || !opsi.value.trim()) opsi.value = tpl.opsi;
                if (force || !kunci.value.trim()) kunci.value = tpl.kunci;
            };

            tipe.addEventListener('change', () => apply(true));
            apply(false);
        });
    })();
</script>
