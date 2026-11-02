import * as bootstrap from 'bootstrap';
import { initSidebar, initPasswordToggle, initFormLoading, initGrafik } from './rscc';

// Supaya Bootstrap (modal, offcanvas, collapse) bisa dipakai dari skrip lain
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initPasswordToggle();
    initFormLoading();
    initGrafik();
});