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