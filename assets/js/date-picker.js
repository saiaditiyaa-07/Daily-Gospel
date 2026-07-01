/**
 * Daily Gospel - Date Picker & AJAX Readings Loader
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};
    const datePicker = document.getElementById('date-picker');
    const prevBtn = document.getElementById('prev-day-btn');
    const nextBtn = document.getElementById('next-day-btn');
    const currentDateInput = document.getElementById('current-date');
    const loadingSkeleton = document.getElementById('loading-skeleton');
    const readingsContent = document.getElementById('readings-content');

    if (!datePicker || !readingsContent) return;

    const colourClassMap = {
        green: 'liturgical-green',
        violet: 'liturgical-violet',
        white: 'liturgical-white',
        red: 'liturgical-red',
        rose: 'liturgical-rose',
        black: 'liturgical-black'
    };

    function showLoading(show) {
        if (loadingSkeleton) {
            loadingSkeleton.classList.toggle('d-none', !show);
        }
        if (readingsContent) {
            readingsContent.style.opacity = show ? '0.35' : '1';
            readingsContent.style.pointerEvents = show ? 'none' : '';
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function buildAccordionItem(label, icon, block, id, index, isLast) {
        if (!block || !block.text) {
            if (id === 'reading-r2') return '';
            block = null;
        }

        const collapseId = 'collapse-' + id;
        const expanded = index === 0;
        let html = '<article class="accordion-item reading-accordion-item animate-slide-up" id="' + id + '-card">';
        html += '<h2 class="accordion-header">';
        html += '<button class="accordion-button' + (expanded ? '' : ' collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '" aria-expanded="' + expanded + '" aria-controls="' + collapseId + '">';
        html += '<i class="bi ' + icon + ' me-2"></i>' + label;
        if (block && block.source) {
            html += '<span class="reading-source badge bg-primary-subtle text-primary-emphasis ms-2">' + escapeHtml(block.source) + '</span>';
        }
        html += '</button></h2>';
        html += '<div id="' + collapseId + '" class="accordion-collapse collapse' + (expanded ? ' show' : '') + '" data-bs-parent="#readings-cards">';
        html += '<div class="accordion-body">';
        if (block && block.heading) {
            html += '<p class="reading-heading">' + escapeHtml(block.heading) + '</p>';
        }
        html += '<div class="reading-text">';
        html += block && block.text ? block.text : '<p class="text-muted mb-0">No reading available for this day.</p>';
        html += '</div></div></div></article>';
        return html;
    }

    function updateSidebar(data) {
        const colour = data.liturgical_colour || 'green';
        const colourEl = document.getElementById('sidebar-colour');
        const seasonEl = document.getElementById('sidebar-season');
        const colourWidget = document.querySelector('.liturgical-colour-widget');

        if (colourEl) colourEl.textContent = colour;
        if (seasonEl) {
            if (data.season) {
                seasonEl.textContent = data.season;
                seasonEl.classList.remove('d-none');
            } else {
                seasonEl.classList.add('d-none');
            }
        }
        if (colourWidget) {
            Object.values(colourClassMap).forEach(function (cls) {
                colourWidget.classList.remove(cls);
            });
            colourWidget.classList.add(colourClassMap[colour] || 'liturgical-green');
            const dot = colourWidget.querySelector('.colour-dot');
            if (dot) {
                ['green', 'violet', 'white', 'red', 'rose', 'black'].forEach(function (c) {
                    dot.classList.remove('colour-dot-' + c);
                });
                dot.classList.add('colour-dot-' + colour);
            }
        }
    }

    function updateTodayButton(date) {
        const todayBtn = document.getElementById('today-btn');
        if (!todayBtn) return;

        const today = new Date();
        const todayStr = today.getFullYear() + '-' +
            String(today.getMonth() + 1).padStart(2, '0') + '-' +
            String(today.getDate()).padStart(2, '0');

        if (date === todayStr) {
            todayBtn.outerHTML = '<button type="button" class="btn btn-primary" id="today-btn" disabled title="Currently viewing today"><i class="bi bi-sun"></i><span class="d-none d-sm-inline ms-1">Today</span></button>';
        } else {
            todayBtn.outerHTML = '<a href="' + config.baseUrl + '/index.php" class="btn btn-primary" id="today-btn" title="Go to today"><i class="bi bi-sun"></i><span class="d-none d-sm-inline ms-1">Today</span></a>';
        }
    }

    function renderReadings(data) {
        if (!data.success) {
            readingsContent.innerHTML =
                '<div class="alert alert-danger text-center rounded-4 shadow-sm" role="alert">' +
                '<i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>' +
                escapeHtml(data.error || "Unable to load today's readings. Please try again later.") +
                '<div class="mt-3"><button type="button" class="btn btn-danger rounded-pill" id="retry-btn">Try Again</button></div></div>';

            document.getElementById('retry-btn')?.addEventListener('click', function () {
                loadReadings(currentDateInput.value);
            });
            return;
        }

        const colourClass = colourClassMap[data.liturgical_colour] || 'liturgical-green';

        let html = '<div class="liturgical-meta row g-3 mb-4 animate-slide-up" id="liturgical-meta">';
        html += '<div class="col-sm-4"><div class="meta-chip h-100"><i class="bi bi-calendar-event"></i><div>';
        html += '<span class="meta-label">Celebration</span><span class="meta-value" id="celebration-text">' + escapeHtml(data.celebration) + '</span></div></div></div>';
        html += '<div class="col-sm-4"><div class="meta-chip h-100"><i class="bi bi-flower1"></i><div>';
        html += '<span class="meta-label">Liturgical Season</span><span class="meta-value" id="season-text">' + escapeHtml(data.season) + '</span></div></div></div>';
        html += '<div class="col-sm-4"><div class="meta-chip h-100 ' + colourClass + '" id="colour-chip"><i class="bi bi-palette"></i><div>';
        html += '<span class="meta-label">Liturgical Colour</span><span class="meta-value text-capitalize" id="colour-text">' + escapeHtml(data.liturgical_colour) + '</span></div></div></div></div>';

        if (data.saint) {
            html += '<div class="saint-highlight mb-4 animate-slide-up" id="saint-banner">';
            html += '<div class="saint-highlight-icon"><i class="bi bi-star-fill"></i></div><div>';
            html += '<span class="saint-highlight-label">Saint of the Day</span>';
            html += '<strong class="saint-highlight-name" id="saint-text">' + escapeHtml(data.saint) + '</strong></div></div>';
        }

        html += '<nav class="reading-jump-nav mb-4 animate-slide-up" id="reading-jump-nav" aria-label="Jump to reading">';
        html += '<a href="#reading-r1" class="jump-link">First Reading</a>';
        html += '<a href="#reading-ps" class="jump-link">Psalm</a>';
        if (data.second_reading && data.second_reading.text) {
            html += '<a href="#reading-r2" class="jump-link" id="jump-r2">Second Reading</a>';
        }
        html += '<a href="#reading-g" class="jump-link">Gospel</a></nav>';

        html += '<div id="readings-cards" class="readings-accordion accordion">';
        let idx = 0;
        const items = [
            ['First Reading', 'bi-book', data.first_reading, 'reading-r1'],
            ['Responsorial Psalm', 'bi-music-note-beamed', data.psalm, 'reading-ps'],
            ['Second Reading', 'bi-journal-text', data.second_reading, 'reading-r2'],
            ['Gospel', 'bi-cross', data.gospel, 'reading-g']
        ];
        items.forEach(function (item) {
            const built = buildAccordionItem(item[0], item[1], item[2], item[3], idx, false);
            if (built) {
                html += built;
                idx++;
            }
        });
        html += '</div>';

        html += '<div class="share-bar mt-4 animate-slide-up"><span class="share-bar-label">Share:</span>';
        html += '<div class="share-buttons">';
        html += '<a href="#" class="share-btn share-facebook" data-share="facebook" title="Facebook"><i class="bi bi-facebook"></i></a>';
        html += '<a href="#" class="share-btn share-whatsapp" data-share="whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>';
        html += '<a href="#" class="share-btn share-twitter" data-share="twitter" title="X"><i class="bi bi-twitter-x"></i></a>';
        html += '<a href="#" class="share-btn share-telegram" data-share="telegram" title="Telegram"><i class="bi bi-telegram"></i></a>';
        html += '<a href="#" class="share-btn share-email" data-share="email" title="Email"><i class="bi bi-envelope"></i></a>';
        html += '</div></div>';

        if (data.copyright_html) {
            html += '<div class="copyright-notice mt-4 small text-muted" id="copyright-notice">' + data.copyright_html + '</div>';
        }

        readingsContent.innerHTML = html;

        if (data.formatted_date) {
            const displayDate = document.getElementById('display-date');
            if (displayDate) displayDate.textContent = data.formatted_date;
        }

        const headerCelebration = document.getElementById('header-celebration');
        if (headerCelebration) {
            headerCelebration.textContent = data.celebration || '';
            headerCelebration.classList.toggle('d-none', !data.celebration);
        }

        if (prevBtn && data.prev_date) prevBtn.dataset.date = data.prev_date;
        if (nextBtn && data.next_date) nextBtn.dataset.date = data.next_date;

        updateSidebar(data);
        updateTodayButton(data.date);
        document.title = 'Daily Gospel — ' + (data.formatted_date || data.date);
        history.replaceState(null, '', config.baseUrl + '/index.php?date=' + data.date);

        if (window.DailyGospelInitShareButtons) {
            window.DailyGospelInitShareButtons();
        }
        if (window.DailyGospelApplyTextSettings) {
            window.DailyGospelApplyTextSettings();
        }
    }

    async function loadReadings(date) {
        if (!navigator.onLine) {
            window.location.href = config.baseUrl + '/pages/offline.php';
            return;
        }

        showLoading(true);

        try {
            const response = await fetch(config.apiUrl + '/readings.php?date=' + encodeURIComponent(date), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();
            currentDateInput.value = date;
            datePicker.value = date;
            renderReadings(data);
        } catch (err) {
            renderReadings({
                success: false,
                error: "Unable to load today's readings. Please try again later."
            });
        } finally {
            showLoading(false);
        }
    }

    window.DailyGospelLoadReadings = loadReadings;

    datePicker.addEventListener('change', function () {
        loadReadings(this.value);
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            loadReadings(this.dataset.date);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            loadReadings(this.dataset.date);
        });
    }
})();
