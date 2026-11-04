// =========================================================
// RSCC — komponen JavaScript bersama
// =========================================================
import Chart from 'chart.js/auto';

const KUNCI_SIDEBAR = 'rs-sidebar';

function simpanPilihan(nilai) {
    try {
        localStorage.setItem(KUNCI_SIDEBAR, nilai);
    } catch (e) {
        // browser menolak penyimpanan (mode privat) — abaikan saja
    }
}

/* ---------- Sidebar: mode ikon ---------- */
export function initSidebar() {
    const root = document.documentElement;

    document.querySelectorAll('[data-rs-toggle-sidebar]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const mini = root.classList.toggle('rs-sidebar-mini');
            simpanPilihan(mini ? 'mini' : 'lebar');
        });
    });

    document.querySelectorAll('.rs-menu-parent').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            if (root.classList.contains('rs-sidebar-mini')) {
                root.classList.remove('rs-sidebar-mini');
                simpanPilihan('lebar');
            }
        });
    });
}

/* ---------- Tombol tampilkan / sembunyikan password ---------- */
export function initPasswordToggle() {
    document.querySelectorAll('[data-rs-toggle-password]').forEach((tombol) => {
        const input = document.getElementById(tombol.dataset.rsTogglePassword);
        if (!input) return;

        tombol.addEventListener('click', () => {
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            tombol.setAttribute('aria-pressed', String(tampil));
            tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
            tombol.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
}

/* ---------- Tombol "Memproses…" saat form dikirim ---------- */
export function initFormLoading() {
    document.querySelectorAll('form[data-rs-loading]').forEach((form) => {
        form.addEventListener('submit', () => {
            const tombol = form.querySelector('[type="submit"]');
            if (!tombol) return;
            tombol.disabled = true;
            tombol.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses…';
        });
    });
}

/* ---------- Grafik batang kunjungan per unit ---------- */
export function initGrafik() {
    const kanvas = document.querySelectorAll('canvas[data-rs-chart="bar-unit"]');
    if (!kanvas.length) return;

    const css = getComputedStyle(document.documentElement);
    const warna = (nama) => css.getPropertyValue(nama).trim();

    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

    kanvas.forEach((canvas) => {
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Jumlah kunjungan',
                    data: values,
                    backgroundColor: warna('--rs-primary'),
                    hoverBackgroundColor: warna('--rs-primary-dark'),
                    borderRadius: 4,
                    barThickness: 16,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: (ctx) => ` ${ctx.parsed.x} kunjungan` },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: warna('--rs-border') },
                        ticks: { color: warna('--rs-text-3'), precision: 0 },
                    },
                    y: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: { color: warna('--rs-text-2'), font: { size: 12 } },
                    },
                },
            },
        });
    });
}

/* ---------- Pengukur kekuatan password ---------- */
export function initPasswordStrength() {
    const LABEL = ['Terlalu lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat kuat'];
    const PETUNJUK = 'Minimal 8 karakter, gabungan huruf dan angka.';

    document.querySelectorAll('[data-rs-strength-for]').forEach((meter) => {
        const input = document.getElementById(meter.dataset.rsStrengthFor);
        if (!input) return;

        const batang = meter.querySelectorAll('.rs-strength-bar span');
        const teks = meter.querySelector('.rs-strength-text');

        input.addEventListener('input', () => {
            const v = input.value;
            let skor = 0;
            if (v.length >= 8) skor++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) skor++;
            if (/\d/.test(v)) skor++;
            if (/[^A-Za-z0-9]/.test(v)) skor++;

            meter.dataset.skor = v ? String(skor) : '';
            batang.forEach((b, i) => b.classList.toggle('isi', Boolean(v) && i < skor));
            teks.textContent = v ? `Kekuatan: ${LABEL[skor]}` : PETUNJUK;
        });
    });
}

/* ---------- Saring daftar (mis. daftar dokumen) ---------- */
export function initFilterDaftar() {
    document.querySelectorAll('[data-rs-filter-list]').forEach((input) => {
        const daftar = document.querySelector(input.dataset.rsFilterList);
        if (!daftar) return;

        const kosong = input.dataset.rsFilterEmpty ? document.querySelector(input.dataset.rsFilterEmpty) : null;
        const item = daftar.querySelectorAll('[data-rs-filter-text]');

        input.addEventListener('input', () => {
            const kata = input.value.trim().toLowerCase();
            let tampil = 0;
            item.forEach((el) => {
                const cocok = el.dataset.rsFilterText.includes(kata);
                el.hidden = !cocok;
                if (cocok) tampil++;
            });
            if (kosong) kosong.hidden = tampil > 0;
        });
    });
}

/* ---------- Hitung lama hari dari dua tanggal ---------- */
export function initHitungHari() {
    document.querySelectorAll('[data-rs-hitung-hari]').forEach((wadah) => {
        const mulai = wadah.querySelector('[data-rs-mulai]');
        const sampai = wadah.querySelector('[data-rs-sampai]');
        const hasil = wadah.querySelector('[data-rs-lama]');
        if (!mulai || !sampai || !hasil) return;

        const hitung = () => {
            if (!mulai.value || !sampai.value) {
                hasil.textContent = '—';
                return;
            }
            const hari = Math.round((new Date(sampai.value) - new Date(mulai.value)) / 86400000) + 1;
            hasil.textContent = hari > 0 ? `${hari} hari` : 'Tanggal tidak valid';
            hasil.classList.toggle('text-danger', hari <= 0);
        };

        mulai.addEventListener('change', hitung);
        sampai.addEventListener('change', hitung);
        hitung();
    });
}

/* ---------- Saat panel dibuka, langsung fokus ke kotak isiannya ---------- */
export function initFokusSaatBuka() {
    document.querySelectorAll('[data-rs-focus-on-show]').forEach((panel) => {
        panel.addEventListener('shown.bs.collapse', () => {
            panel.querySelector('input')?.focus();
        });
    });
}

/* ---------- Permintaan lab / radiologi: pilih, cari, ringkasan ---------- */
export function initPilihPemeriksaan() {
    const form = document.querySelector('[data-rs-pemeriksaan]');
    if (!form) return;

    const pilihan = [...form.querySelectorAll('input[name="items[]"]')];
    const daftar = form.querySelector('[data-rs-ringkasan]');
    const kosong = form.querySelector('[data-rs-ringkasan-kosong]');
    const jumlah = form.querySelectorAll('[data-rs-jumlah]');
    const cari = form.querySelector('[data-rs-cari-pemeriksaan]');
    const tidakAda = form.querySelector('[data-rs-cari-kosong]');

    // Satu baris di ringkasan: nama, catatan persiapan, tombol hapus
    const buatItem = (cb) => {
        const li = document.createElement('li');
        li.className = 'rs-ringkasan-item';

        const teks = document.createElement('span');
        teks.textContent = cb.dataset.nama;

        if (cb.dataset.catatan) {
            const catatan = document.createElement('small');
            const ikon = document.createElement('i');
            ikon.className = 'bi bi-exclamation-triangle me-1';
            ikon.setAttribute('aria-hidden', 'true');
            catatan.append(ikon, cb.dataset.catatan);
            teks.append(catatan);
        }

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.className = 'rs-ringkasan-hapus';
        hapus.setAttribute('aria-label', `Hapus ${cb.dataset.nama}`);
        hapus.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';
        hapus.addEventListener('click', () => {
            cb.checked = false;
            perbarui();
            cb.focus(); // fokus kembali ke pilihan yang baru dihapus
        });

        li.append(teks, hapus);
        return li;
    };

    function perbarui() {
        const dipilih = pilihan.filter((cb) => cb.checked);
        daftar.replaceChildren(...dipilih.map(buatItem));
        daftar.hidden = dipilih.length === 0;
        kosong.hidden = dipilih.length > 0;
        jumlah.forEach((el) => { el.textContent = dipilih.length; });
    }

    pilihan.forEach((cb) => cb.addEventListener('change', perbarui));

    // Enter di kotak cari jangan mengirim form
    cari?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Saring pemeriksaan; kelompok yang kosong ikut disembunyikan
    cari?.addEventListener('input', () => {
        const q = cari.value.trim().toLowerCase();
        let total = 0;

        form.querySelectorAll('[data-rs-grup]').forEach((grup) => {
            let n = 0;
            grup.querySelectorAll('[data-rs-item]').forEach((item) => {
                const cocok = item.dataset.rsItem.includes(q);
                item.hidden = !cocok;
                if (cocok) n++;
            });
            grup.hidden = n === 0;
            total += n;
        });

        tidakAda.hidden = total > 0;
    });

    perbarui(); // tampilkan pilihan lama (setelah validasi gagal)
}

/* ---------- Unggah berkas: tarik-lepas, pratinjau, validasi ---------- */
export function initUnggahBerkas() {
    const modalEl = document.querySelector('[data-rs-unggah]');
    if (!modalEl) return;

    const form = modalEl.querySelector('form');
    const input = modalEl.querySelector('input[type="file"]');
    const zona = modalEl.querySelector('[data-rs-dropzone]');
    const daftar = modalEl.querySelector('[data-rs-daftar-berkas]');
    const tombol = modalEl.querySelector('[data-rs-tombol-unggah]');
    const jumlahEl = modalEl.querySelector('[data-rs-jumlah-berkas]');

    const MAKS_UKURAN = 10 * 1024 * 1024; // 10 MB
    const MAKS_JUMLAH = 10;
    const TIPE_OK = ['application/pdf', 'image/jpeg', 'image/png'];

    let berkas = [];

    const formatUkuran = (byte) => byte < 1024 * 1024
        ? `${Math.max(1, Math.round(byte / 1024))} KB`
        : `${(byte / 1024 / 1024).toFixed(1).replace('.', ',')} MB`;

    const masalah = (f) => {
        if (!TIPE_OK.includes(f.type)) return 'Format tidak didukung';
        if (f.size > MAKS_UKURAN) return 'Lebih dari 10 MB';
        return null;
    };

    // Salin daftar berkas ke <input type="file"> supaya ikut terkirim saat submit
    const sinkron = () => {
        const dt = new DataTransfer();
        berkas.forEach((f) => dt.items.add(f));
        input.files = dt.files;
    };

    const buatBaris = (f, i) => {
        const li = document.createElement('li');
        const salah = masalah(f);
        li.className = 'rs-berkas-baris' + (salah ? ' is-salah' : '');

        // Thumbnail gambar / ikon PDF
        let gambar;
        if (f.type.startsWith('image/') && !salah) {
            gambar = document.createElement('img');
            gambar.className = 'rs-berkas-thumb';
            gambar.alt = '';
            gambar.src = URL.createObjectURL(f);
            gambar.onload = () => URL.revokeObjectURL(gambar.src);
        } else {
            gambar = document.createElement('span');
            gambar.className = 'rs-berkas-thumb rs-berkas-thumb-ikon';
            gambar.innerHTML = '<i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>';
        }

        const teks = document.createElement('div');
        teks.className = 'min-w-0 flex-grow-1';
        const nama = document.createElement('div');
        nama.className = 'rs-berkas-nama';
        nama.textContent = f.name;
        const info = document.createElement('div');
        info.className = 'small ' + (salah ? 'text-danger fw-semibold' : 'rs-text-muted');
        info.textContent = salah ? `${salah} · ${formatUkuran(f.size)}` : formatUkuran(f.size);
        teks.append(nama, info);

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.className = 'rs-ringkasan-hapus';
        hapus.setAttribute('aria-label', `Batalkan ${f.name}`);
        hapus.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';
        hapus.addEventListener('click', () => {
            berkas.splice(i, 1);
            sinkron();
            tampilkan();
            input.focus();
        });

        li.append(gambar, teks, hapus);
        return li;
    };

    function tampilkan() {
        daftar.replaceChildren(...berkas.map(buatBaris));
        daftar.hidden = berkas.length === 0;

        const semuaValid = berkas.length > 0 && berkas.every((f) => !masalah(f));
        tombol.disabled = !semuaValid;
        jumlahEl.textContent = berkas.length ? ` (${berkas.length})` : '';
    }

    function tambah(fileList) {
        for (const f of [...fileList]) {
            const sudahAda = berkas.some((b) => b.name === f.name && b.size === f.size);
            if (!sudahAda && berkas.length < MAKS_JUMLAH) berkas.push(f);
        }
        sinkron();
        tampilkan();
    }

    // Pilih lewat dialog komputer
    input.addEventListener('change', () => tambah(input.files));

    // Tarik & lepas
    ['dragenter', 'dragover'].forEach((ev) => zona.addEventListener(ev, (e) => {
        e.preventDefault();
        zona.classList.add('is-drag');
    }));
    ['dragleave', 'dragend', 'drop'].forEach((ev) => zona.addEventListener(ev, () => {
        zona.classList.remove('is-drag');
    }));
    zona.addEventListener('drop', (e) => {
        e.preventDefault();
        tambah(e.dataTransfer.files);
    });

    // Berkas yang dilepas di luar area jangan membuka berkas di tab browser
    ['dragover', 'drop'].forEach((ev) => modalEl.addEventListener(ev, (e) => e.preventDefault()));

    // Tutup popup → kosongkan pilihan
    modalEl.addEventListener('hidden.bs.modal', () => {
        berkas = [];
        sinkron();
        tampilkan();
    });

    // Cegah klik ganda saat mengunggah
    form.addEventListener('submit', () => {
        tombol.disabled = true;
        tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Mengunggah…';
    });

    // Validasi server gagal → buka popup lagi
    if (modalEl.hasAttribute('data-rs-buka-otomatis')) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    tampilkan();
}

/* ---------- Asesmen awal GD: langkah, GCS, TTV, nyeri, fisik, risiko jatuh ---------- */
export function initAsesmen() {
    const form = document.querySelector('[data-rs-asesmen]');
    if (!form) return;

    /* --- Penanda "ada perubahan" + peringatan sebelum menutup halaman --- */
    const status = document.querySelector('[data-rs-status-simpan]');
    let berubah = false;

    function tandai() {
        if (berubah) return;
        berubah = true;
        status.innerHTML = '<i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Ada perubahan yang belum disimpan';
        status.classList.add('text-warning-emphasis', 'fw-semibold');
    }
    form.addEventListener('input', tandai);
    form.addEventListener('change', tandai);
    form.addEventListener('submit', () => { berubah = false; });
    window.addEventListener('beforeunload', (e) => {
        if (berubah) { e.preventDefault(); e.returnValue = ''; }
    });

    /* --- Langkah 1–4 --- */
    const tab = [...document.querySelectorAll('[data-rs-langkah-tab]')];
    const panel = [...form.querySelectorAll('[data-rs-langkah-panel]')];
    const input = form.querySelector('[data-rs-langkah-input]');
    const tPrev = document.querySelector('[data-rs-langkah-prev]');
    const tNext = document.querySelector('[data-rs-langkah-next]');
    const tFinal = document.querySelector('[data-rs-langkah-final]');
    let sekarang = Number(form.dataset.rsLangkahAwal || 0);

    function buka(i, fokus = false) {
        sekarang = Math.max(0, Math.min(panel.length - 1, i));
        panel.forEach((p, n) => { p.hidden = n !== sekarang; });
        tab.forEach((t, n) => {
            const aktif = n === sekarang;
            t.classList.toggle('active', aktif);
            t.classList.toggle('is-done', n < sekarang);
            t.setAttribute('aria-selected', String(aktif));
            t.tabIndex = aktif ? 0 : -1;
        });
        input.value = sekarang;

        const terakhir = sekarang === panel.length - 1;
        tPrev.disabled = sekarang === 0;
        tNext.hidden = terakhir;
        tFinal.hidden = !terakhir;

        if (fokus) {
            panel[sekarang].scrollIntoView({ block: 'start', behavior: 'smooth' });
            panel[sekarang].querySelector('.rs-langkah-judul')?.focus({ preventScroll: true });
        }
    }

    tab.forEach((t, n) => {
        t.addEventListener('click', () => buka(n, true));
        t.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            e.preventDefault();
            const arah = e.key === 'ArrowRight' ? 1 : -1;
            const n2 = (sekarang + arah + tab.length) % tab.length;
            buka(n2);
            tab[n2].focus();
        });
    });
    tPrev.addEventListener('click', () => buka(sekarang - 1, true));
    tNext.addEventListener('click', () => buka(sekarang + 1, true));
    buka(sekarang);

    /* --- GCS: total + tingkat kesadaran --- */
    const gcs = form.querySelector('[data-rs-gcs]');
    if (gcs) {
        const pilih = [...gcs.querySelectorAll('[data-rs-gcs-nilai]')];
        const total = gcs.querySelector('[data-rs-gcs-total]');
        const kesadaran = gcs.querySelector('[data-rs-gcs-kesadaran]');
        const hitung = () => {
            if (pilih.some((s) => !s.value)) {
                total.textContent = '–';
                kesadaran.textContent = 'Lengkapi E, V, dan M';
                gcs.classList.remove('is-warn');
                return;
            }
            const n = pilih.reduce((jml, s) => jml + Number(s.value), 0);
            total.textContent = n;
            kesadaran.textContent =
                n >= 14 ? 'Compos mentis' : n >= 12 ? 'Apatis' : n >= 10 ? 'Delirium'
                    : n >= 7 ? 'Somnolen' : n >= 5 ? 'Sopor' : n === 4 ? 'Semi-koma' : 'Koma';
            gcs.classList.toggle('is-warn', n < 14);
        };
        pilih.forEach((s) => s.addEventListener('change', hitung));
        hitung();
    }

    /* --- Tanda vital: penanda di luar rentang normal --- */
    form.querySelectorAll('[data-rs-ttv]').forEach((kotak) => {
        const isian = kotak.querySelector('input');
        const catatan = kotak.querySelector('.rs-ttv-catatan');
        const teksAwal = catatan.textContent.trim();
        const min = Number(kotak.dataset.min);
        const max = Number(kotak.dataset.max);

        const cek = () => {
            const v = parseFloat(isian.value);
            let teks = teksAwal;
            let luar = false;
            if (!Number.isNaN(v)) {
                if (v < min) { teks = '↓ Di bawah normal'; luar = true; }
                else if (v > max) { teks = '↑ Di atas normal'; luar = true; }
                else { teks = '✓ Normal'; }
            }
            catatan.textContent = teks;
            kotak.classList.toggle('is-warn', luar);
        };
        isian.addEventListener('input', cek);
        cek();
    });

    /* --- Skala nyeri: label kategori --- */
    const nyeri = form.querySelector('[data-rs-nyeri]');
    if (nyeri) {
        const label = nyeri.querySelector('[data-rs-nyeri-label]');
        const perbarui = () => {
            const v = Number(nyeri.querySelector('input[name="nyeri"]:checked')?.value ?? 0);
            label.textContent = v === 0
                ? 'Tidak nyeri'
                : `Nyeri ${v <= 3 ? 'ringan' : v <= 6 ? 'sedang' : 'berat'} (${v}/10)`;
        };
        nyeri.addEventListener('change', perbarui);
        perbarui();
    }

    /* --- Pemeriksaan fisik: hitung temuan + "Semua normal" --- */
    const fisik = form.querySelector('[data-rs-fisik]');
    if (fisik) {
        const jumlah = fisik.querySelector('[data-rs-fisik-jumlah]');
        const hitung = () => {
            jumlah.textContent = fisik.querySelectorAll('input[value="temuan"]:checked').length;
        };
        fisik.querySelector('[data-rs-fisik-normal]').addEventListener('click', () => {
            fisik.querySelectorAll('input[value="normal"]').forEach((r) => { r.checked = true; });
            hitung();
            tandai();
        });
        fisik.addEventListener('change', hitung);
        hitung();
    }

    /* --- Risiko jatuh (Get Up and Go) --- */
    const jatuh = form.querySelector('[data-rs-jatuh]');
    if (jatuh) {
        const hasil = jatuh.querySelector('[data-rs-jatuh-hasil]');
        const nilai = () => {
            const ya = jatuh.querySelectorAll('input[value="ya"]:checked').length;
            const [teks, kelas] = ya === 0 ? ['Tidak berisiko', 'is-aman']
                : ya === 1 ? ['Risiko rendah', 'is-sedang']
                    : ['Risiko tinggi', 'is-tinggi'];
            hasil.textContent = `Hasil: ${teks}`;
            hasil.className = `rs-jatuh-hasil ${kelas}`;
        };
        jatuh.addEventListener('change', nilai);
        nilai();
    }

    /* --- Jumlah diagnosis terpilih --- */
    const dx = form.querySelector('[data-rs-diagnosa]');
    if (dx) {
        const n = dx.querySelector('[data-rs-diagnosa-jumlah]');
        const hitung = () => { n.textContent = dx.querySelectorAll('input:checked').length; };
        dx.addEventListener('change', hitung);
        hitung();
    }
}

/* ---------- Tombol sisipkan teks ke kotak isian (mis. TTV terakhir di CPPT) ---------- */
export function initSisipTeks() {
    document.querySelectorAll('[data-rs-sisip]').forEach((tombol) => {
        const tujuan = document.querySelector(tombol.dataset.rsSisip);
        if (!tujuan) return;

        tombol.addEventListener('click', () => {
            const teks = tombol.dataset.rsTeks;
            const lama = tujuan.value.trim();
            tujuan.value = lama ? `${lama}\n${teks}` : teks;
            tujuan.focus();
            tujuan.setSelectionRange(tujuan.value.length, tujuan.value.length);
        });
    });
}