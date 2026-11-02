// =========================================================
// RSCC — komponen JavaScript bersama
// =========================================================

const KUNCI_SIDEBAR = 'rs-sidebar';

function simpanPilihan(nilai) {
    try {
        localStorage.setItem(KUNCI_SIDEBAR, nilai);
    } catch (e) {
        // browser menolak penyimpanan (mode privat) — abaikan saja
    }
}

export function initSidebar() {
    const root = document.documentElement;

    // Tombol "Kecilkan menu"
    document.querySelectorAll('[data-rs-toggle-sidebar]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const mini = root.classList.toggle('rs-sidebar-mini');
            simpanPilihan(mini ? 'mini' : 'lebar');
        });
    });

    // Klik menu induk (Rawat Inap / OK) saat mode ikon:
    // lebarkan sidebar dulu supaya sub-menunya terlihat
    document.querySelectorAll('.rs-menu-parent').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            if (root.classList.contains('rs-sidebar-mini')) {
                root.classList.remove('rs-sidebar-mini');
                simpanPilihan('lebar');
            }
        });
    });
}