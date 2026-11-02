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