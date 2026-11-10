import * as bootstrap from 'bootstrap';
import {
    initSidebar, initPasswordToggle, initFormLoading, initGrafik,
    initPasswordStrength, initFilterDaftar, initHitungHari, initFokusSaatBuka, initSesiHd,
    initPilihPemeriksaan, initUnggahBerkas, initRentangTanggal, initFilterOtomatis, initPilihIcd, initFormPemeriksaan,
    initAsesmen, initSisipTeks, initResep, initChecklist, initMenuRmCari, initCariMenu, initFormAlergi, initGrafikTtv,
    initMenuRmBukaTutup, initHeaderPasien, initIdentitasPasien,
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
    initFormAlergi();
    initMenuRmBukaTutup();
    initHeaderPasien();
    initIdentitasPasien();

    // Clara
    initAsesmen();
    initSisipTeks();
    initResep();
    initChecklist();
    initMenuRmCari();
    initCariMenu();
    initGrafikTtv();
});