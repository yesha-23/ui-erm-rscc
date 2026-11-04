import * as bootstrap from 'bootstrap';
import {
    initSidebar, initPasswordToggle, initFormLoading, initGrafik,
    initPasswordStrength, initFilterDaftar, initHitungHari, initFokusSaatBuka,
    initPilihPemeriksaan, initUnggahBerkas, initRentangTanggal, initFilterOtomatis,
    initAsesmen, initSisipTeks, initResep, initChecklist, initMenuRmCari, initCariMenu,
} from './rscc';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    // Yesha
    initSidebar();
    initPasswordToggle();
    initFormLoading();
    initGrafik();
    initPasswordStrength();
    initFilterDaftar();
    initHitungHari();
    initFokusSaatBuka();
    initPilihPemeriksaan();
    initUnggahBerkas();
    initRentangTanggal();
    initFilterOtomatis();

    // Clara
    initAsesmen();
    initSisipTeks();
    initResep();
    initChecklist();
    initMenuRmCari();
    initCariMenu();
});