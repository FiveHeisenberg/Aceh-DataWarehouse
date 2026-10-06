/**
 * Aceh Data Warehouse - Modul Dispenda / Filter bar (shared)
 * File: public/js/Dispenda/filters.js
 *
 * Baris filter memakai GET form biasa, jadi setiap perubahan = full page reload.
 * Script ini menutup dua jurang yang biasanya ditangani Inertia router.get:
 *   1. submit otomatis saat select berubah
 *   2. posisi scroll tetap sama setelah reload (ex preserveScroll: true)
 */
(function () {
    'use strict';

    // ------------------------------------------------------------- auto submit
    document.querySelectorAll('[data-autosubmit]').forEach((el) => {
        el.addEventListener('change', () => el.form.submit());
    });

    // --------------------------------------------------------------- debounce
    document.querySelectorAll('[data-debounce]').forEach((el) => {
        let timer;
        el.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => el.form.submit(), 500);
        });
    });

    // --------------------------------------------------------- preserve scroll
    const KEY = 'dispenda.scrollY';

    window.addEventListener('pagehide', () => {
        try {
            sessionStorage.setItem(KEY, String(window.scrollY));
        } catch (e) { /* mode privat: abaikan */ }
    });

    (function restore() {
        let saved = null;
        try {
            saved = sessionStorage.getItem(KEY);
        } catch (e) {
            return;
        }
        if (saved === null) return;

        try {
            sessionStorage.removeItem(KEY);
        } catch (e) { /* abaikan */ }

        const target = Number(saved);
        if (!target) return;

        requestAnimationFrame(() => window.scrollTo(0, target));
    })();
})();
