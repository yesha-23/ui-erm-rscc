// =========================================================
// RSCC — komponen JavaScript bersama
// =========================================================
import Chart from 'chart.js/auto';
import { Modal } from 'bootstrap';

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

/* ---------- E-Resep: cari obat, tambah/hapus baris, salin resep ---------- */
export function initResep() {
    const form = document.querySelector('[data-rs-resep]');
    if (!form) return;

    const katalog = JSON.parse(document.getElementById('data-obat')?.textContent || '[]');
    const tbody = form.querySelector('[data-rs-daftar-resep]');
    const tabel = form.querySelector('[data-rs-tabel-resep]');
    const kosong = form.querySelector('[data-rs-resep-kosong]');
    const jumlah = document.querySelectorAll('[data-rs-jumlah-item]');
    const tpl = document.getElementById('tpl-baris-resep');
    const cari = document.querySelector('[data-rs-cari-obat]');
    const hasil = document.querySelector('[data-rs-hasil-obat]');
    let indeks = Number(form.dataset.rsIndeksAwal || 0);
    let cocok = [];
    let aktif = 0;

    const perbarui = () => {
        const n = tbody.querySelectorAll('[data-rs-baris-resep]').length;
        tabel.hidden = n === 0;
        kosong.hidden = n > 0;
        jumlah.forEach((el) => { el.textContent = n; });
    };

    const barisAda = (kode) =>
        [...tbody.querySelectorAll('[data-rs-baris-resep]')].find((tr) => tr.dataset.kode === kode);

    // Tambah satu baris dari cetakan; kalau obat sudah ada, kembalikan baris yang ada
    function tambahBaris(obat, nilai = {}) {
        const ada = barisAda(obat.kode);
        if (ada) return ada;

        const wadah = document.createElement('tbody');
        wadah.innerHTML = tpl.innerHTML.replaceAll('__I__', String(indeks++));
        const tr = wadah.firstElementChild;

        tr.dataset.kode = obat.kode;
        tr.querySelector('[data-rs-obat-nama]').textContent = obat.nama;
        tr.querySelector('[data-rs-obat-satuan]').textContent = obat.satuan;
        tr.querySelector('[data-rs-hapus-baris]').setAttribute('aria-label', `Hapus ${obat.nama}`);

        const isi = {
            kode: obat.kode, nama: obat.nama, satuan: obat.satuan,
            jumlah: '', signa: '', rute: 'oral', catatan: '', ...nilai,
        };
        Object.entries(isi).forEach(([kunci, v]) => {
            const el = tr.querySelector(`[data-rs-f="${kunci}"]`);
            if (el) el.value = v ?? '';
        });

        tbody.append(tr);
        perbarui();
        return tr;
    }

    function pilih(obat) {
        const tr = tambahBaris(obat);
        cari.value = '';
        hasil.hidden = true;
        tr.querySelector('[data-rs-f="jumlah"]').focus();
    }

    // Hapus baris
    tbody.addEventListener('click', (e) => {
        const tombol = e.target.closest('[data-rs-hapus-baris]');
        if (!tombol) return;
        tombol.closest('[data-rs-baris-resep]').remove();
        perbarui();
        cari?.focus();
    });

    // Hasil pencarian obat
    const tampilkanHasil = () => {
        const q = cari.value.trim().toLowerCase();
        if (!q) { hasil.hidden = true; return; }

        cocok = katalog.filter((o) => o.nama.toLowerCase().includes(q)).slice(0, 8);
        aktif = 0;

        if (!cocok.length) {
            const li = document.createElement('li');
            li.className = 'rs-obat-kosong';
            li.textContent = 'Obat tidak ditemukan';
            hasil.replaceChildren(li);
        } else {
            hasil.replaceChildren(...cocok.map((o, i) => {
                const li = document.createElement('li');
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'rs-obat-opsi' + (i === 0 ? ' active' : '');
                const nama = document.createElement('span');
                nama.textContent = o.nama;
                const info = document.createElement('small');
                info.textContent = `${o.satuan} · ${o.jenis === 'alkes' ? 'alkes' : 'obat'}`;
                b.append(nama, info);
                b.addEventListener('click', () => pilih(o));
                li.append(b);
                return li;
            }));
        }
        hasil.hidden = false;
    };

    cari?.addEventListener('input', tampilkanHasil);
    cari?.addEventListener('keydown', (e) => {
        const opsi = [...hasil.querySelectorAll('.rs-obat-opsi')];
        if (e.key === 'Enter') {
            e.preventDefault();
            if (cocok[aktif]) pilih(cocok[aktif]);
            return;
        }
        if (e.key === 'Escape') { hasil.hidden = true; return; }
        if (!opsi.length || (e.key !== 'ArrowDown' && e.key !== 'ArrowUp')) return;
        e.preventDefault();
        aktif = (aktif + (e.key === 'ArrowDown' ? 1 : -1) + opsi.length) % opsi.length;
        opsi.forEach((b, i) => b.classList.toggle('active', i === aktif));
        opsi[aktif].scrollIntoView({ block: 'nearest' });
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-rs-cari-obat-wadah]')) hasil.hidden = true;
    });

    // Salin resep sebelumnya
    document.querySelector('[data-rs-salin-resep]')?.addEventListener('click', () => {
        const data = JSON.parse(document.getElementById('data-resep-sebelumnya')?.textContent || '[]');
        let n = 0;
        data.forEach((item) => {
            if (!barisAda(item.kode)) { tambahBaris(item, item); n++; }
        });
        const info = document.querySelector('[data-rs-salin-info]');
        if (info) {
            info.textContent = n
                ? `${n} obat disalin. Periksa kembali dosis dan jumlah sebelum mengirim.`
                : 'Semua obat dari resep sebelumnya sudah ada di daftar.';
        }
    });

    perbarui();
}

/* ---------- Checklist PPI / CP / Psikologi: skor otomatis ---------- */
export function initChecklist() {
    const form = document.querySelector('[data-rs-checklist]');
    if (!form) return;

    const positif = form.dataset.positif;
    const baris = [...form.querySelectorAll('.rs-cek-baris')];
    const skor = form.querySelector('[data-rs-skor]');
    const rinci = form.querySelector('[data-rs-skor-rinci]');
    const bar = form.querySelector('[data-rs-skor-bar]');

    const hitung = () => {
        const jawab = baris.map((b) => b.querySelector('input:checked')?.value ?? null);
        const terjawab = jawab.filter(Boolean);
        const dinilai = terjawab.filter((v) => v !== 'na');
        const baik = dinilai.filter((v) => v === positif).length;
        const persen = dinilai.length ? Math.round((baik / dinilai.length) * 100) : null;

        skor.textContent = persen === null ? '–' : `${persen}%`;
        rinci.textContent = `${baik} dari ${dinilai.length} butir dinilai · ${baris.length - terjawab.length} belum dijawab`;
        bar.style.width = `${persen ?? 0}%`;
        bar.parentElement.dataset.tingkat =
            persen === null ? '' : persen >= 85 ? 'baik' : persen >= 60 ? 'cukup' : 'kurang';

        baris.forEach((b, i) => { if (jawab[i]) b.classList.remove('is-error'); });
    };

    form.addEventListener('change', hitung);

    // Isi butir yang masih kosong dengan jawaban positif (tidak menimpa jawaban yang sudah ada)
    document.querySelector('[data-rs-tandai-semua]')?.addEventListener('click', () => {
        baris.forEach((b) => {
            if (b.querySelector('input:checked')) return;
            const r = b.querySelector(`input[value="${positif}"]`);
            if (r) r.checked = true;
        });
        hitung();
    });

    hitung();
}

/* ---------- Menu rekam medis: saring dengan kotak "Cari form" ---------- */
export function initMenuRmCari() {
    const menu = document.querySelector('[data-rs-rmmenu]');
    const input = menu?.querySelector('[data-rs-rmmenu-cari]');
    if (!input) return;

    const kosong = menu.querySelector('[data-rs-rmmenu-kosong]');

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        let total = 0;
        menu.querySelectorAll('.rs-rmmenu-group').forEach((grup) => {
            let n = 0;
            grup.querySelectorAll('.rs-rmmenu-item').forEach((a) => {
                const cocok = a.textContent.toLowerCase().includes(q);
                a.hidden = !cocok;
                if (cocok) n++;
            });
            grup.hidden = n === 0;
            total += n;
        });
        kosong.hidden = total > 0;
    });
}

/* ---------- Cari menu (Ctrl+K / ⌘K) ---------- */
export function initCariMenu() {
    const modalEl = document.querySelector('[data-rs-cari-menu]');
    if (!modalEl) return;

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
    const input = modalEl.querySelector('[data-rs-cari-menu-input]');
    const item = [...modalEl.querySelectorAll('[data-rs-cari-item]')];
    const kosong = modalEl.querySelector('[data-rs-cari-menu-kosong]');
    let aktif = 0;

    const terlihat = () => item.filter((li) => !li.hidden);

    const sorot = (i) => {
        const daftar = terlihat();
        item.forEach((li) => li.firstElementChild.classList.remove('active'));
        if (!daftar.length) return;
        aktif = (i + daftar.length) % daftar.length;
        daftar[aktif].firstElementChild.classList.add('active');
        daftar[aktif].scrollIntoView({ block: 'nearest' });
    };

    const saring = () => {
        const kata = input.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
        item.forEach((li) => { li.hidden = !kata.every((k) => li.dataset.teks.includes(k)); });
        kosong.hidden = terlihat().length > 0;
        sorot(0);
    };

    input.addEventListener('input', saring);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') { e.preventDefault(); sorot(aktif + 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); sorot(aktif - 1); }
        else if (e.key === 'Enter') { e.preventDefault(); terlihat()[aktif]?.firstElementChild.click(); }
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            modal.show();
        }
    });

    modalEl.addEventListener('shown.bs.modal', () => {
        input.value = '';
        saring();
        input.focus();
    });
}

/* ---------- Tanda vital: grafik tren dengan batas normal ---------- */
export function initGrafikTtv() {
    const kanvas = document.querySelector('[data-rs-ttv-grafik]');
    if (!kanvas) return;

    const data = JSON.parse(document.getElementById('data-ttv')?.textContent || '{}');
    const css = getComputedStyle(document.documentElement);
    const garisGrid = css.getPropertyValue('--rs-border').trim() || '#E4E7EC';

    const SERI = {
        suhu: ['suhu'],
        nadi: ['nadi'],
        td: ['td_sistolik', 'td_diastolik'],
        napas: ['napas'],
        spo2: ['spo2'],
    };
    const WARNA = ['#2B6CB0', '#157A4F'];
    let grafik;

    const gambar = (kunci) => {
        const nama = SERI[kunci];
        const acuan = data.seri[nama[0]];

        const datasets = nama.map((n, i) => ({
            label: data.seri[n].label,
            data: data.seri[n].data,
            borderColor: WARNA[i],
            backgroundColor: WARNA[i],
            tension: 0.3,
            pointRadius: 4,
            spanGaps: true,
        }));

        // Garis batas normal (putus-putus)
        ['min', 'max'].forEach((b) => datasets.push({
            label: b === 'min' ? 'Batas bawah normal' : 'Batas atas normal',
            data: data.label.map(() => acuan[b]),
            borderColor: '#98A2B3',
            borderDash: [6, 4],
            borderWidth: 1,
            pointRadius: 0,
            fill: false,
        }));

        grafik?.destroy();
        grafik = new Chart(kanvas, {
            type: 'line',
            data: { labels: data.label, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, filter: (item) => !item.text.startsWith('Batas') },
                    },
                    tooltip: {
                        filter: (item) => !item.dataset.label.startsWith('Batas'),
                        callbacks: {
                            label: (c) => ` ${c.dataset.label}: ${String(c.parsed.y).replace('.', ',')} ${acuan.unit}`,
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: garisGrid } },
                },
            },
        });
    };

    document.querySelectorAll('input[name="grafik-ttv"]').forEach((r) => {
        r.addEventListener('change', () => gambar(r.value));
    });

    gambar(document.querySelector('input[name="grafik-ttv"]:checked')?.value || 'suhu');
}

/* ---------- Rentang tanggal: tanggal selesai tidak boleh sebelum tanggal mulai ---------- */
export function initRentangTanggal() {
    document.querySelectorAll('[data-rs-rentang-tanggal]').forEach((wadah) => {
        const dari = wadah.querySelector('[data-rs-tgl-dari]');
        const sampai = wadah.querySelector('[data-rs-tgl-sampai]');
        if (!dari || !sampai) return;

        const sinkron = () => {
            if (dari.value && sampai.value && sampai.value < dari.value) {
                sampai.value = dari.value;
            }
            sampai.min = dari.value || '';
            dari.max = sampai.value || '';
        };

        dari.addEventListener('change', sinkron);
        sampai.addEventListener('change', sinkron);
        sinkron();
    });
}

/**
 * Filter daftar kunjungan otomatis (tanpa tombol Terapkan).
 * - Dropdown berubah        → langsung menyaring.
 * - Tanggal berubah         → menyaring setelah jeda 0,7 detik
 *                             (saat tahun diketik manual, nilainya berubah tiap tombol ditekan).
 * - Kotak cari              → Enter; tombol × (mengosongkan) langsung menampilkan semua lagi.
 * Selama memuat: garis biru di atas kotak filter (class .is-memuat).
 */
export function initFilterOtomatis() {
    document.querySelectorAll('[data-rs-filter-otomatis]').forEach((form) => {
        let jeda;
        const status = form.querySelector('[data-rs-filter-status]');

        const kirim = () => {
            if (form.classList.contains('is-memuat')) return; // cegah kirim dua kali
            form.requestSubmit();
        };

        form.addEventListener('submit', () => {
            form.classList.add('is-memuat');
            form.setAttribute('aria-busy', 'true');
            if (status) status.textContent = 'Memuat data kunjungan…';
        });

        form.addEventListener('change', (e) => {
            const el = e.target;

            if (el.matches('select')) {
                kirim();
                return;
            }

            if (el.matches('input[type="date"]')) {
                clearTimeout(jeda);
                const tahun = Number((el.value || '').slice(0, 4));
                if (el.value && tahun >= 2000) {
                    jeda = setTimeout(kirim, 700);
                }
            }
        });

        form.querySelectorAll('input[type="search"]').forEach((cari) => {
            cari.addEventListener('search', () => {
                if (cari.value === '') kirim();
            });
        });

        // Kembali dengan tombol Back: hapus tanda memuat yang tersisa di cache halaman
        window.addEventListener('pageshow', () => {
            form.classList.remove('is-memuat');
            form.removeAttribute('aria-busy');
            if (status) status.textContent = '';
        });
    });
}

/**
 * Kotak cari ICD (combobox): ketik → pilih → jadi chip (input hidden name="icd10[]").
 * Keyboard: ↑/↓ memilih, Enter menambah, Esc menutup.
 * data-utama="1": chip pertama diberi tanda "Utama".
 */
export function initPilihIcd() {
    document.querySelectorAll('[data-rs-icd]').forEach((kotak) => {
        const nama = kotak.dataset.nama;
        const katalog = JSON.parse(kotak.querySelector('[data-rs-icd-katalog]').textContent);
        const semuaKode = Object.keys(katalog);
        const cari = kotak.querySelector('[data-rs-icd-cari]');
        const saran = kotak.querySelector('[data-rs-icd-daftar]');
        const terpilih = kotak.querySelector('[data-rs-icd-terpilih]');
        let hasil = [];
        let aktif = -1;

        const kodeTerpilih = () => [...terpilih.querySelectorAll('input')].map((i) => i.value);
        const beriTahuBerubah = () => kotak.dispatchEvent(new Event('change', { bubbles: true }));

        const tandaiUtama = () => {
            if (kotak.dataset.utama !== '1') return;
            terpilih.querySelectorAll('.rs-px-utama').forEach((el) => el.remove());
            const pertama = terpilih.querySelector('li');
            if (pertama) {
                const tanda = document.createElement('span');
                tanda.className = 'rs-px-utama';
                tanda.textContent = 'Utama';
                pertama.prepend(tanda);
            }
        };

        const tambah = (kode) => {
            if (!katalog[kode] || kodeTerpilih().includes(kode)) return;
            const li = document.createElement('li');
            li.className = 'rs-px-chip';
            li.innerHTML = '<input type="hidden"><strong></strong><span></span>'
                + '<button type="button" class="rs-px-chip-hapus" data-rs-icd-hapus>'
                + '<i class="bi bi-x-lg" aria-hidden="true"></i></button>';
            const input = li.querySelector('input');
            input.name = `${nama}[]`;
            input.value = kode;
            li.querySelector('strong').textContent = kode;
            li.querySelector('span').textContent = katalog[kode];
            li.querySelector('button').setAttribute('aria-label', `Hapus ${kode}`);
            terpilih.append(li);
            tandaiUtama();
            beriTahuBerubah();
        };

        const tutup = () => {
            saran.hidden = true;
            cari.setAttribute('aria-expanded', 'false');
            cari.removeAttribute('aria-activedescendant');
            aktif = -1;
        };

        const tampilkan = () => {
            const q = cari.value.trim().toLowerCase();
            saran.replaceChildren();
            if (!q) { tutup(); return; }

            const dipilih = kodeTerpilih();
            hasil = semuaKode
                .filter((k) => !dipilih.includes(k)
                    && (k.toLowerCase().includes(q) || katalog[k].toLowerCase().includes(q)))
                .slice(0, 8);

            if (!hasil.length) {
                const kosong = document.createElement('li');
                kosong.className = 'rs-px-saran-kosong';
                kosong.textContent = 'Tidak ditemukan. Coba kata lain.';
                saran.append(kosong);
            }
            hasil.forEach((k, i) => {
                const li = document.createElement('li');
                li.id = `${saran.id}-${i}`;
                li.setAttribute('role', 'option');
                li.setAttribute('aria-selected', 'false');
                li.className = 'rs-px-saran-item';
                li.dataset.kode = k;
                li.innerHTML = '<strong></strong><span></span>';
                li.querySelector('strong').textContent = k;
                li.querySelector('span').textContent = katalog[k];
                saran.append(li);
            });
            saran.hidden = false;
            cari.setAttribute('aria-expanded', 'true');
            aktif = -1;
        };

        const sorot = (i) => {
            const opsi = saran.querySelectorAll('[role="option"]');
            opsi.forEach((el, j) => el.setAttribute('aria-selected', String(j === i)));
            aktif = i;
            if (opsi[i]) {
                cari.setAttribute('aria-activedescendant', opsi[i].id);
                opsi[i].scrollIntoView({ block: 'nearest' });
            }
        };

        const pilih = (kode) => {
            tambah(kode);
            cari.value = '';
            tutup();
            cari.focus();
        };

        cari.addEventListener('input', tampilkan);
        cari.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' && hasil.length) {
                e.preventDefault();
                sorot(Math.min(aktif + 1, hasil.length - 1));
            } else if (e.key === 'ArrowUp' && hasil.length) {
                e.preventDefault();
                sorot(Math.max(aktif - 1, 0));
            } else if (e.key === 'Enter') {
                e.preventDefault(); // jangan kirim form
                if (aktif >= 0) pilih(hasil[aktif]);
                else if (hasil.length === 1) pilih(hasil[0]);
            } else if (e.key === 'Escape') {
                tutup();
            }
        });
        cari.addEventListener('blur', () => setTimeout(tutup, 120));

        // mousedown (bukan click) supaya terpilih sebelum kotak cari kehilangan fokus
        saran.addEventListener('mousedown', (e) => {
            const li = e.target.closest('[data-kode]');
            if (li) { e.preventDefault(); pilih(li.dataset.kode); }
        });

        terpilih.addEventListener('click', (e) => {
            const tombol = e.target.closest('[data-rs-icd-hapus]');
            if (!tombol) return;
            tombol.closest('li').remove();
            tandaiUtama();
            beriTahuBerubah();
            cari.focus();
        });

        kotak.rsTambah = tambah; // dipakai tombol "Salin"
    });
}

/**
 * Form Pemeriksaan rawat jalan:
 * - isian tambahan sesuai tindak lanjut (tanggal kontrol / tujuan rujukan),
 * - tombol isi cepat, tombol "Salin" dari kunjungan sebelumnya,
 * - penanda "ada perubahan" + peringatan bila meninggalkan halaman sebelum menyimpan.
 */
export function initFormPemeriksaan() {
    const form = document.querySelector('[data-rs-form-pemeriksaan]');
    if (!form) return;

    const status = document.querySelector('[data-rs-status-simpan]');
    let berubah = false;

    function tandaiBerubah() {
        if (berubah) return;
        berubah = true;
        if (status) status.textContent = 'Ada perubahan yang belum disimpan';
    }

    const perbaruiTindakLanjut = () => {
        const nilai = form.querySelector('input[name="tindak_lanjut"]:checked')?.value;
        form.querySelectorAll('[data-rs-tampil-jika]').forEach((el) => {
            el.hidden = el.dataset.rsTampilJika !== nilai;
        });
    };

    form.addEventListener('input', tandaiBerubah);
    form.addEventListener('change', (e) => {
        tandaiBerubah();
        if (e.target.name === 'tindak_lanjut') perbaruiTindakLanjut();
    });
    form.addEventListener('submit', () => { berubah = false; });
    window.addEventListener('beforeunload', (e) => {
        if (berubah) { e.preventDefault(); e.returnValue = ''; }
    });
    perbaruiTindakLanjut();

    // Tombol isi cepat, mis. "Isi: dalam batas normal"
    form.querySelectorAll('[data-rs-isi-teks]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const sasaran = form.querySelector(tombol.dataset.rsTarget);
            if (!sasaran) return;
            const lama = sasaran.value.trim();
            sasaran.value = lama ? `${lama}\n${tombol.dataset.rsIsiTeks}` : tombol.dataset.rsIsiTeks;
            sasaran.focus();
            tandaiBerubah();
        });
    });

    // Salin dari kunjungan sebelumnya
    const info = document.querySelector('[data-rs-salin-info]');
    document.querySelectorAll('[data-rs-salin]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const data = JSON.parse(tombol.dataset.rsSalin);
            ['diagnosa', 'rencana'].forEach((nama) => {
                if (form.elements[nama] && data[nama]) form.elements[nama].value = data[nama];
            });
            const kotakIcd = form.querySelector('[data-rs-icd][data-nama="icd10"]');
            (data.icd10 || []).forEach((kode) => kotakIcd?.rsTambah?.(kode));
            tandaiBerubah();
            if (info) {
                info.textContent = `Diagnosa, ICD-10, dan rencana disalin dari kunjungan ${data.tanggal}. Periksa kembali sebelum menyimpan.`;
                info.hidden = false;
            }
            form.elements.diagnosa?.focus();
        });
    });
}

/**
 * Sesi hemodialisa: hitungan otomatis + salin resep sesi sebelumnya.
 * - Kenaikan BB = BB pre − BB kering
 * - Skor risiko jatuh Morse + kategori
 * - UF aktual = BB pre − BB pasca
 * - URR = (ureum pra − ureum pasca) ÷ ureum pra × 100%
 */
export function initSesiHd() {
    const form = document.querySelector('[data-rs-hd-form]');
    if (!form) return;

    const angka = (nama) => {
        const n = parseFloat(form.querySelector(`[data-rs-hd="${nama}"]`)?.value);
        return Number.isFinite(n) ? n : null;
    };
    const fmt = (n, d = 1) => n.toLocaleString('id-ID', { minimumFractionDigits: d, maximumFractionDigits: d });

    const hitung = () => {
        const idwg = form.querySelector('[data-rs-hd-idwg]');
        if (idwg) {
            const pre = angka('bb_pre');
            const kering = angka('bb_kering');
            idwg.textContent = pre !== null && kering !== null
                ? `${pre - kering >= 0 ? '+' : ''}${fmt(pre - kering)} kg` : '—';
        }

        const uf = form.querySelector('[data-rs-hd-uf]');
        if (uf) {
            const pre = parseFloat(uf.dataset.bbPre);
            const post = angka('bb_post');
            uf.textContent = Number.isFinite(pre) && post !== null ? `${fmt(pre - post)} L` : '—';
        }

        const urr = form.querySelector('[data-rs-hd-urr]');
        if (urr) {
            const a = angka('ureum_pre');
            const b = angka('ureum_post');
            if (a && b !== null && b < a) {
                const nilai = ((a - b) / a) * 100;
                urr.textContent = `${fmt(nilai)} %`;
                urr.classList.toggle('is-kurang', nilai < 65);
            } else {
                urr.textContent = '—';
                urr.classList.remove('is-kurang');
            }
        }

        const morse = form.querySelector('[data-rs-morse]');
        if (morse) {
            const skor = [...morse.querySelectorAll('input:checked')].reduce((t, el) => t + Number(el.value), 0);
            const [teks, kategori] = skor >= 45 ? ['Risiko tinggi', 'tinggi']
                : skor >= 25 ? ['Risiko rendah', 'rendah'] : ['Tidak berisiko', 'aman'];
            const hasil = morse.querySelector('[data-rs-morse-hasil]');
            hasil.textContent = `Skor ${skor} · ${teks}`;
            hasil.dataset.kategori = kategori;
        }
    };

    form.addEventListener('input', hitung);
    form.addEventListener('change', hitung);
    hitung();

    const tombolSalin = document.querySelector('[data-rs-salin-resep]');
    tombolSalin?.addEventListener('click', () => {
        const data = JSON.parse(tombolSalin.dataset.rsSalinResep);
        Object.entries(data).forEach(([nama, nilai]) => {
            const el = form.elements[nama];
            if (el) el.value = String(nilai); // radio (RadioNodeList) ikut tercentang
        });
        const info = document.querySelector('[data-rs-salin-info]');
        if (info) {
            info.textContent = 'Resep disalin dari sesi sebelumnya. Sesuaikan bila perlu, lalu simpan.';
            info.hidden = false;
        }
        hitung();
    });
}

/**
 * Form riwayat alergi (modal di header pasien):
 * kotak keterangan hanya muncul bila "Ada alergi" dipilih.
 * Juga membuka otomatis modal yang ditandai data-rs-buka-otomatis (mis. setelah gagal validasi).
 */
export function initFormAlergi() {
    document.querySelectorAll('[data-rs-form-alergi]').forEach((form) => {
        const detail = form.querySelector('[data-rs-alergi-detail]');
        form.addEventListener('change', (e) => {
            if (e.target.name !== 'status') return;
            const ada = e.target.value === 'ada';
            detail.hidden = !ada;
            if (ada) detail.querySelector('textarea')?.focus();
        });
    });

    document.querySelectorAll('.modal[data-rs-buka-otomatis]').forEach((el) => {
        Modal.getOrCreateInstance(el).show();
    });
}


/* ---------- Menu rekam medis tidak melompat ke atas saat pindah halaman ----------
   Posisi gulir menu samping disimpan sebelum pindah halaman. Posisi itu dipulihkan oleh skrip kecil
   di layouts/rekam-medis (tepat setelah menu), supaya pulih sebelum halaman tampil.
   Di sini cukup memastikan item aktif terlihat (mis. dibuka dari Ctrl+K) dan menyimpan posisi. */
export function initMenuRmTetap() {
    const wadah = document.querySelector('.rs-rm-menu');
    if (!wadah) return;

    wadah.querySelector('.rs-rmmenu-item.active')?.scrollIntoView({ block: 'nearest' });

    const simpan = () => {
        try { sessionStorage.setItem('rs-rmmenu-gulir', String(wadah.scrollTop)); } catch { /* abaikan */ }
    };
    wadah.addEventListener('click', simpan);
    window.addEventListener('pagehide', simpan);
}

