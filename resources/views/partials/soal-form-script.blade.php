<script>
    (function () {
        const TEMPLATES = {
            pilihan_ganda: {
                opsi: [{ label: 'A', teks: '' }, { label: 'B', teks: '' }, { label: 'C', teks: '' }, { label: 'D', teks: '' }],
                kunci: { jawaban: 'A' },
            },
            susun_kalimat: {
                opsi: ['kata1', 'kata2', 'kata3'],
                kunci: { susunan: ['kata1', 'kata2', 'kata3'] },
            },
            pencocokan_arti: {
                opsi: [{ kiri: 'mangan', kanan: 'makan' }],
                kunci: { pasangan: { mangan: 'makan' } },
            },
            puzzle_pakaian_adat: {
                opsi: [{ id: 1, nama: 'Blangkon' }, { id: 2, nama: 'Beskap' }],
                kunci: { urutan: [1, 2] },
            },
            menulis_aksara: {
                opsi: { aksara: 'ha', petunjuk: 'Tlusuri bayangan aksara.' },
                kunci: { paths: [[[0.3, 0.3], [0.5, 0.5], [0.7, 0.3]]] },
            },
            kuis_suara: {
                opsi: { instruksi: 'Ngucapna ukara kanthi cetha.' },
                kunci: { teks: 'sugeng enjing' },
            },
        };

        document.querySelectorAll('[data-soal-form]').forEach((form) => {
            const tipe = form.querySelector('[data-tipe]');
            const opsiRaw = form.querySelector('[data-opsi]');
            const kunciRaw = form.querySelector('[data-kunci]');
            const container = form.querySelector('[id^="dynamic-fields-"]');

            const parseJSON = (str, defaultObj) => {
                try { return JSON.parse(str); } catch (e) { return defaultObj; }
            };

            const renderUI = () => {
                const tipeSoal = tipe.value;
                container.innerHTML = '';
                
                let opsi = parseJSON(opsiRaw.value, TEMPLATES[tipeSoal]?.opsi || {});
                let kunci = parseJSON(kunciRaw.value, TEMPLATES[tipeSoal]?.kunci || {});

                // Jika ganti tipe tapi datanya belum sesuai struktur baru, bisa ditimpa saat apply()
                if (!TEMPLATES[tipeSoal]) return;

                const updateData = () => {
                    opsiRaw.value = JSON.stringify(opsi, null, 2);
                    kunciRaw.value = JSON.stringify(kunci, null, 2);
                };

                const createInput = (value, onInput, placeholder = '') => {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'flex-1 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 font-body text-body outline-none focus:border-primary-500';
                    input.value = value;
                    input.placeholder = placeholder;
                    input.addEventListener('input', (e) => { onInput(e.target.value); updateData(); });
                    return input;
                };

                const createButton = (text, icon, onClick, isDanger = false) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `flex items-center gap-1 px-3 py-2 rounded-full text-caption font-bold ${isDanger ? 'text-error bg-error-container hover:bg-red-200' : 'text-primary-700 bg-primary-100 hover:bg-primary-200'} transition-colors whitespace-nowrap`;
                    btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">${icon}</span> ${text}`;
                    btn.addEventListener('click', () => { onClick(); renderUI(); updateData(); });
                    return btn;
                };

                if (tipeSoal === 'pilihan_ganda') {
                    if (!Array.isArray(opsi)) opsi = TEMPLATES.pilihan_ganda.opsi;
                    
                    const helpText = document.createElement('p');
                    helpText.className = 'text-caption text-gray-500 mb-2';
                    helpText.textContent = 'Isi teks opsi dan klik radio button di sebelah kiri untuk menentukan Kunci Jawaban.';
                    container.appendChild(helpText);

                    const radioGroupName = `pg_jawaban_${form.id || Math.random().toString(36).substring(2, 9)}`;

                    opsi.forEach((op, index) => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-3';
                        
                        const radioContainer = document.createElement('div');
                        radioContainer.className = 'flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 cursor-pointer';
                        
                        const radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = radioGroupName; // shared name per form to make them mutually exclusive
                        radio.checked = kunci.jawaban === op.label;
                        radio.className = 'w-5 h-5 text-primary-600 focus:ring-primary-500 cursor-pointer';
                        radio.addEventListener('change', () => { kunci.jawaban = op.label; updateData(); });
                        
                        radioContainer.appendChild(radio);

                        const labelSpan = document.createElement('span');
                        labelSpan.className = 'font-bold w-6 text-center text-primary-800';
                        labelSpan.textContent = op.label;

                        const input = createInput(op.teks, (val) => op.teks = val, `Teks opsi ${op.label}`);
                        
                        const delBtn = createButton('', 'delete', () => {
                            opsi.splice(index, 1);
                            opsi.forEach((o, i) => o.label = String.fromCharCode(65 + i)); // reassign A, B, C
                            if(!opsi.find(o => o.label === kunci.jawaban) && opsi.length > 0) kunci.jawaban = opsi[0].label;
                        }, true);

                        row.append(radioContainer, labelSpan, input, delBtn);
                        container.appendChild(row);
                    });
                    
                    const btnContainer = document.createElement('div');
                    btnContainer.className = 'mt-2';
                    if (opsi.length < 4) {
                        btnContainer.appendChild(createButton('Tambah Opsi', 'add', () => {
                            const nextLabel = String.fromCharCode(65 + opsi.length);
                            opsi.push({ label: nextLabel, teks: '' });
                        }));
                    }
                    container.appendChild(btnContainer);
                } 
                else if (tipeSoal === 'susun_kalimat') {
                    if (!Array.isArray(opsi)) opsi = TEMPLATES.susun_kalimat.opsi;
                    if (!kunci.susunan) kunci.susunan = [...opsi];

                    const helpText = document.createElement('p');
                    helpText.className = 'text-caption text-gray-500 mb-2';
                    helpText.textContent = 'Opsi adalah kata-kata acak. Kunci adalah urutan kata yang benar.';
                    container.appendChild(helpText);

                    // Opsi (Kata-kata)
                    const opsiDiv = document.createElement('div');
                    opsiDiv.className = 'flex flex-col gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl';
                    opsiDiv.innerHTML = '<span class="font-bold text-caption uppercase tracking-wider text-gray-500">Kata Acak (Opsi)</span>';
                    
                    opsi.forEach((kata, i) => {
                        const row = document.createElement('div');
                        row.className = 'flex gap-2';
                        row.append(
                            createInput(kata, (val) => {
                                const oldKata = opsi[i];
                                opsi[i] = val;
                                // Optionally update kunci if it matches perfectly, but better to let user do it via text.
                                const kIdx = kunci.susunan.indexOf(oldKata);
                                if(kIdx !== -1) kunci.susunan[kIdx] = val;
                            }, 'Kata'),
                            createButton('', 'delete', () => {
                                const oldKata = opsi[i];
                                opsi.splice(i, 1);
                                kunci.susunan = kunci.susunan.filter(k => k !== oldKata);
                            }, true)
                        );
                        opsiDiv.appendChild(row);
                    });
                    
                    const addKataDiv = document.createElement('div');
                    addKataDiv.className = 'mt-1';
                    addKataDiv.appendChild(createButton('Tambah Kata', 'add', () => {
                        opsi.push('kata_baru');
                        kunci.susunan.push('kata_baru');
                    }));
                    opsiDiv.appendChild(addKataDiv);
                    container.appendChild(opsiDiv);

                    // Kunci (Susunan)
                    const kunciDiv = document.createElement('div');
                    kunciDiv.className = 'flex flex-col gap-2 p-4 bg-primary-50/50 border border-primary-200 rounded-xl mt-2';
                    kunciDiv.innerHTML = '<span class="font-bold text-caption uppercase tracking-wider text-primary-700">Susunan Benar (Kunci)</span><p class="text-[12px] text-gray-600">Pisahkan dengan koma.</p>';
                    
                    const susunanInput = createInput(kunci.susunan.join(', '), (val) => {
                        kunci.susunan = val.split(',').map(s => s.trim()).filter(s => s);
                    }, 'Contoh: ibu, menyang, pasar');
                    kunciDiv.appendChild(susunanInput);
                    container.appendChild(kunciDiv);
                }
                else if (tipeSoal === 'pencocokan_arti') {
                    if (!Array.isArray(opsi)) opsi = TEMPLATES.pencocokan_arti.opsi;
                    if (!kunci.pasangan) kunci.pasangan = {};

                    const helpText = document.createElement('p');
                    helpText.className = 'text-caption text-gray-500 mb-2';
                    helpText.textContent = 'Masukkan pasangan Kiri (Kata) dan Kanan (Arti). Opsi akan diacak otomatis saat siswa mengerjakan kuis.';
                    container.appendChild(helpText);

                    opsi.forEach((pair, i) => {
                        const row = document.createElement('div');
                        row.className = 'flex gap-3 items-center bg-gray-50 p-2 rounded-xl border border-gray-100';
                        
                        const inputKiri = createInput(pair.kiri, (val) => {
                            const oldKiri = pair.kiri;
                            pair.kiri = val;
                            if (kunci.pasangan[oldKiri] !== undefined) {
                                kunci.pasangan[val] = kunci.pasangan[oldKiri];
                                delete kunci.pasangan[oldKiri];
                            } else {
                                kunci.pasangan[val] = pair.kanan;
                            }
                        }, 'Kiri (Jawa)');
                        
                        const inputKanan = createInput(pair.kanan, (val) => {
                            pair.kanan = val;
                            kunci.pasangan[pair.kiri] = val;
                        }, 'Kanan (Arti)');
                        
                        const arrow = document.createElement('span');
                        arrow.className = 'material-symbols-outlined text-gray-400';
                        arrow.textContent = 'arrow_forward';

                        row.append(
                            inputKiri, 
                            arrow, 
                            inputKanan,
                            createButton('', 'delete', () => {
                                delete kunci.pasangan[pair.kiri];
                                opsi.splice(i, 1);
                            }, true)
                        );
                        container.appendChild(row);
                    });
                    
                    const addPairDiv = document.createElement('div');
                    addPairDiv.className = 'mt-2';
                    addPairDiv.appendChild(createButton('Tambah Pasangan', 'add', () => {
                        const newKiri = 'kiri_baru_' + opsi.length;
                        opsi.push({kiri: newKiri, kanan: 'kanan_baru'});
                        kunci.pasangan[newKiri] = 'kanan_baru';
                    }));
                    container.appendChild(addPairDiv);
                }
                else if (tipeSoal === 'puzzle_pakaian_adat') {
                    if (!Array.isArray(opsi)) opsi = TEMPLATES.puzzle_pakaian_adat.opsi;
                    if (!kunci.urutan) kunci.urutan = [];

                    const helpText = document.createElement('p');
                    helpText.className = 'text-caption text-gray-500 mb-2';
                    helpText.textContent = 'Daftar potongan puzzle. Urutan benar diatur dengan memasukkan ID yang dipisah koma.';
                    container.appendChild(helpText);

                    const opsiDiv = document.createElement('div');
                    opsiDiv.className = 'flex flex-col gap-3 p-4 bg-gray-50 border border-gray-200 rounded-xl';
                    opsiDiv.innerHTML = '<span class="font-bold text-caption uppercase tracking-wider text-gray-500">Daftar Potongan (Opsi)</span>';
                    
                    opsi.forEach((item, i) => {
                        const row = document.createElement('div');
                        row.className = 'flex gap-2 items-center';
                        
                        const idInput = createInput(item.id, (val) => { item.id = parseInt(val) || val; }, 'ID (Angka)');
                        idInput.type = 'number';
                        idInput.className = 'w-24 rounded-full border border-gray-200 bg-white px-4 py-2 font-body text-body outline-none focus:border-primary-500';
                        
                        row.append(
                            idInput,
                            createInput(item.nama, (val) => { item.nama = val; }, 'Nama Potongan (opsional)'),
                            createButton('', 'delete', () => { opsi.splice(i, 1); }, true)
                        );
                        opsiDiv.appendChild(row);
                    });
                    
                    const addPzlDiv = document.createElement('div');
                    addPzlDiv.className = 'mt-1';
                    addPzlDiv.appendChild(createButton('Tambah Potongan', 'add', () => {
                        const nextId = opsi.length > 0 ? Math.max(...opsi.map(o => parseInt(o.id) || 0)) + 1 : 1;
                        opsi.push({id: nextId, nama: 'Nama Bagian'});
                        kunci.urutan.push(nextId);
                    }));
                    opsiDiv.appendChild(addPzlDiv);
                    container.appendChild(opsiDiv);

                    const kunciDiv = document.createElement('div');
                    kunciDiv.className = 'flex flex-col gap-2 p-4 bg-primary-50/50 border border-primary-200 rounded-xl mt-2';
                    kunciDiv.innerHTML = '<span class="font-bold text-caption uppercase tracking-wider text-primary-700">Urutan Benar (Kunci)</span><p class="text-[12px] text-gray-600">ID dipisah koma.</p>';
                    
                    kunciDiv.appendChild(createInput(kunci.urutan.join(', '), (val) => {
                        kunci.urutan = val.split(',').map(s => parseInt(s.trim())).filter(s => !isNaN(s));
                    }, 'Contoh: 1, 2, 3'));
                    container.appendChild(kunciDiv);
                }
                else if (tipeSoal === 'menulis_aksara') {
                    if (!opsi.aksara) opsi = TEMPLATES.menulis_aksara.opsi;
                    if (!kunci.paths) kunci = TEMPLATES.menulis_aksara.kunci;

                    const row1 = document.createElement('div');
                    row1.className = 'flex flex-col gap-1.5';
                    row1.innerHTML = '<span class="font-bold text-caption text-gray-700">Aksara</span>';
                    row1.appendChild(createInput(opsi.aksara, (val) => opsi.aksara = val, 'Contoh: ha'));
                    
                    const row2 = document.createElement('div');
                    row2.className = 'flex flex-col gap-1.5 mt-2';
                    row2.innerHTML = '<span class="font-bold text-caption text-gray-700">Petunjuk</span>';
                    row2.appendChild(createInput(opsi.petunjuk, (val) => opsi.petunjuk = val, 'Instruksi untuk siswa'));
                    
                    const row3 = document.createElement('div');
                    row3.className = 'flex flex-col gap-1.5 mt-2';
                    row3.innerHTML = '<span class="font-bold text-caption text-primary-700">Paths (Koordinat JSON)</span><p class="text-[12px] text-gray-500">Karena kompleksitas garis (banyak titik kordinat), tempelkan array path berformat JSON di sini.</p>';
                    
                    const pathArea = document.createElement('textarea');
                    pathArea.rows = 5;
                    pathArea.className = 'rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-[12px] outline-none focus:border-primary-500';
                    pathArea.value = JSON.stringify(kunci.paths, null, 2);
                    pathArea.addEventListener('input', (e) => {
                        try { 
                            kunci.paths = JSON.parse(e.target.value); 
                            updateData(); 
                            pathArea.classList.remove('border-error');
                            pathArea.classList.add('border-gray-200');
                        } 
                        catch (err) { 
                            pathArea.classList.remove('border-gray-200');
                            pathArea.classList.add('border-error');
                        }
                    });
                    row3.appendChild(pathArea);

                    container.append(row1, row2, row3);
                }
                else if (tipeSoal === 'kuis_suara') {
                    if (!opsi.instruksi) opsi = TEMPLATES.kuis_suara.opsi;
                    if (!kunci.teks) kunci = TEMPLATES.kuis_suara.kunci;

                    const row1 = document.createElement('div');
                    row1.className = 'flex flex-col gap-1.5';
                    row1.innerHTML = '<span class="font-bold text-caption text-gray-700">Instruksi Kuis Suara</span>';
                    row1.appendChild(createInput(opsi.instruksi, (val) => opsi.instruksi = val, 'Contoh: Ngucapna ukara kanthi cetha.'));
                    
                    const row2 = document.createElement('div');
                    row2.className = 'flex flex-col gap-1.5 mt-2';
                    row2.innerHTML = '<span class="font-bold text-caption text-primary-700">Teks Jawaban Benar (Kunci)</span><p class="text-[12px] text-gray-500">Teks ini akan dicocokkan dengan hasil rekam suara siswa (STT).</p>';
                    row2.appendChild(createInput(kunci.teks, (val) => kunci.teks = val, 'Teks yang diharapkan dari siswa'));
                    
                    container.append(row1, row2);
                }
            };

            const apply = (forceInit) => {
                const tpl = TEMPLATES[tipe.value];
                if (!tpl) return;
                
                if (forceInit || !opsiRaw.value.trim()) {
                    opsiRaw.value = JSON.stringify(tpl.opsi, null, 2);
                }
                if (forceInit || !kunciRaw.value.trim()) {
                    kunciRaw.value = JSON.stringify(tpl.kunci, null, 2);
                }
                renderUI();
            };

            tipe.addEventListener('change', () => apply(true));
            
            // Initial render delay a bit to ensure elements are ready
            setTimeout(() => {
                if (opsiRaw.value.trim() && kunciRaw.value.trim()) {
                    renderUI(); // Render based on existing data
                } else {
                    apply(false); // Force default if empty
                }
            }, 50);
        });
    })();
</script>
