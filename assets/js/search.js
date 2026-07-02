/**
 * Daily Gospel - Search Page
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};
    const form = document.getElementById('search-form');
    if (!form) return;

    function t(msg) {
        const lang = config.lang || 'en';
        const dictionary = {
            'Please select a date.': {
                'en': 'Please select a date.',
                'ta': 'தயவுசெய்து தேதியைத் தேர்ந்தெடுக்கவும்.'
            },
            'Please enter a search term.': {
                'en': 'Please enter a search term.',
                'ta': 'தயவுசெய்து தேடல் சொல்லை உள்ளிடவும்.'
            },
            'Search returned an invalid response. The server may have timed out — try a shorter date range or search again.': {
                'en': 'Search returned an invalid response. The server may have timed out — try a shorter date range or search again.',
                'ta': 'தேடலில் பிழை ஏற்பட்டது. சர்வர் காலாவதியாகி இருக்கலாம் — மீண்டும் தேட முயற்சிக்கவும்.'
            },
            'Search failed. Please try again.': {
                'en': 'Search failed. Please try again.',
                'ta': 'தேடல் தோல்வியடைந்தது. மீண்டும் முயற்சிக்கவும்.'
            },
            'No results found. Try a different search or month/year.': {
                'en': 'No results found. Try a different search or month/year.',
                'ta': 'முடிவுகள் எதுவும் கிடைக்கவில்லை. வேறு சொற்கள் அல்லது மாதம்/ஆண்டைத் தேர்ந்தெடுக்கவும்.'
            },
            ' result(s) found': {
                'en': ' result(s) found',
                'ta': ' முடிவுகள் கண்டறியப்பட்டன'
            },
            'Search timed out. Reference and saint searches scan one month — try again or pick a closer month.': {
                'en': 'Search timed out. Reference and saint searches scan one month — try again or pick a closer month.',
                'ta': 'தேடல் நேரம் முடிந்தது. விவிலியக் குறிப்பு மற்றும் புனிதர் தேடல் ஒரு மாதத்தை மட்டுமே ஸ்கேன் செய்யும்.'
            },
            'Search failed. Check your connection and try again.': {
                'en': 'Search failed. Check your connection and try again.',
                'ta': 'தேடல் தோல்வியடைந்தது. உங்கள் இணைய இணைப்பைச் சரிபார்த்து மீண்டும் முயற்சிக்கவும்.'
            }
        };
        return (dictionary[msg] && dictionary[msg][lang]) ? dictionary[msg][lang] : msg;
    }

    const typeRadios = form.querySelectorAll('input[name="searchType"]');
    const queryFieldDate = document.getElementById('query-field-date');
    const queryFieldText = document.getElementById('query-field-text');
    const monthYearFields = document.getElementById('month-year-fields');
    const searchDate = document.getElementById('search-date');
    const searchQuery = document.getElementById('search-query');
    const searchMonth = document.getElementById('search-month');
    const searchYear = document.getElementById('search-year');
    const loading = document.getElementById('search-loading');
    const results = document.getElementById('search-results');

    const SEARCH_TIMEOUT_MS = 90000;

    function updateFields() {
        const type = form.querySelector('input[name="searchType"]:checked')?.value || 'date';

        queryFieldDate.classList.toggle('d-none', type !== 'date');
        queryFieldText.classList.toggle('d-none', type === 'date');
        monthYearFields.classList.toggle('d-none', type === 'date');
    }

    typeRadios.forEach(function (radio) {
        radio.addEventListener('change', updateFields);
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function showError(message) {
        results.innerHTML = '<div class="alert alert-danger rounded-4">' + escapeHtml(message) + '</div>';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const type = form.querySelector('input[name="searchType"]:checked')?.value || 'date';
        let url = config.apiUrl + '/search.php?type=' + encodeURIComponent(type);

        if (type === 'date') {
            if (!searchDate.value) {
                showError(t('Please select a date.'));
                return;
            }
            url += '&q=' + encodeURIComponent(searchDate.value);
        } else {
            const query = searchQuery.value.trim();
            if (!query) {
                showError(t('Please enter a search term.'));
                return;
            }
            url += '&q=' + encodeURIComponent(query);
            url += '&year=' + encodeURIComponent(searchYear.value);
            url += '&month=' + encodeURIComponent(searchMonth.value);
        }

        loading.classList.remove('d-none');
        results.innerHTML = '';

        const controller = new AbortController();
        const timeoutId = setTimeout(function () {
            controller.abort();
        }, SEARCH_TIMEOUT_MS);

        try {
            const response = await fetch(url, { signal: controller.signal });
            let data;

            try {
                data = await response.json();
            } catch (parseErr) {
                showError(t('Search returned an invalid response. The server may have timed out — try a shorter date range or search again.'));
                return;
            }

            if (!response.ok || !data.success) {
                showError(data.error || t('Search failed. Please try again.'));
                return;
            }

            let html = '';

            if (Array.isArray(data.warnings) && data.warnings.length > 0) {
                html += '<div class="alert alert-warning rounded-4 mb-3">' +
                    data.warnings.map(function (w) { return escapeHtml(w); }).join(' ') +
                    '</div>';
            }

            if (!data.results || data.results.length === 0) {
                html += '<div class="alert alert-info rounded-4 text-center">' + t('No results found. Try a different search or month/year.') + '</div>';
                results.innerHTML = html;
                return;
            }

            html += '<h3 class="h5 mb-3">' + data.count + t(' result(s) found') + '</h3>';
            data.results.forEach(function (item) {
                html += '<a href="' + escapeHtml(item.url) + '" class="search-result-item">';
                html += '<div class="d-flex justify-content-between align-items-start">';
                html += '<div><strong>' + escapeHtml(item.title) + '</strong>';
                html += '<p class="text-muted small mb-0">' + escapeHtml(item.subtitle) + '</p></div>';
                html += '<i class="bi bi-arrow-right text-primary"></i></div></a>';
            });

            results.innerHTML = html;
        } catch (err) {
            if (err.name === 'AbortError') {
                showError(t('Search timed out. Reference and saint searches scan one month — try again or pick a closer month.'));
            } else {
                showError(t('Search failed. Check your connection and try again.'));
            }
        } finally {
            clearTimeout(timeoutId);
            loading.classList.add('d-none');
        }
    });

    updateFields();
})();
