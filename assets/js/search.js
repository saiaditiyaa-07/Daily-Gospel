/**
 * Daily Gospel - Search Page
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};
    const form = document.getElementById('search-form');
    if (!form) return;

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
                showError('Please select a date.');
                return;
            }
            url += '&q=' + encodeURIComponent(searchDate.value);
        } else {
            const query = searchQuery.value.trim();
            if (!query) {
                showError('Please enter a search term.');
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
                showError('Search returned an invalid response. The server may have timed out — try a shorter date range or search again.');
                return;
            }

            if (!response.ok || !data.success) {
                showError(data.error || 'Search failed. Please try again.');
                return;
            }

            let html = '';

            if (Array.isArray(data.warnings) && data.warnings.length > 0) {
                html += '<div class="alert alert-warning rounded-4 mb-3">' +
                    data.warnings.map(function (w) { return escapeHtml(w); }).join(' ') +
                    '</div>';
            }

            if (!data.results || data.results.length === 0) {
                html += '<div class="alert alert-info rounded-4 text-center">No results found. Try a different search or month/year.</div>';
                results.innerHTML = html;
                return;
            }

            html += '<h3 class="h5 mb-3">' + data.count + ' result(s) found</h3>';
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
                showError('Search timed out. Reference and saint searches scan one month — try again or pick a closer month.');
            } else {
                showError('Search failed. Check your connection and try again.');
            }
        } finally {
            clearTimeout(timeoutId);
            loading.classList.add('d-none');
        }
    });

    updateFields();
})();
