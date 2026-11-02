import * as bootstrap from 'bootstrap';
import { initSidebar, initPasswordToggle, initFormLoading, initGrafik, initPasswordStrength } from './rscc';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initPasswordToggle();
    initFormLoading();
    initGrafik();
    initPasswordStrength();
});