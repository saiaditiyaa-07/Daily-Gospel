/**
 * Daily Gospel - Dark Mode Toggle
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'dailyGospelDarkMode';
    const html = document.documentElement;
    const toggleBtn = document.getElementById('dark-mode-toggle');
    const icon = document.getElementById('dark-mode-icon');

    function applyTheme(isDark) {
        html.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        if (icon) {
            icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
    }

    function init() {
        const saved = localStorage.getItem(STORAGE_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = saved === 'true' || (saved === null && prefersDark);
        applyTheme(isDark);
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            const newDark = !isDark;
            applyTheme(newDark);
            localStorage.setItem(STORAGE_KEY, String(newDark));
        });
    }

    init();
})();
