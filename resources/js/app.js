import * as bootstrap from 'bootstrap';
import {
    initSidebar, initPasswordToggle, initFormLoading, initGrafik,
    initPasswordStrength, initFilterDaftar, initHitungHari, initFokusSaatBuka, initSesiHd,
    initPilihPemeriksaan, initUnggahBerkas, initRentangTanggal, initFilterOtomatis, initPilihIcd, initFormPemeriksaan,
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
    initPilihIcd();
    initFormPemeriksaan();
    initSesiHd();

    // Clara
    initAsesmen();
    initSisipTeks();
    initResep();
    initChecklist();
    initMenuRmCari();
    initCariMenu();
});