/**
 * Daily Gospel - Archive Calendar
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};
    const calendarGrid = document.getElementById('calendar-grid');
    if (!calendarGrid) return;

    const archiveContainer = document.getElementById('archive-calendar');
    const yearSelect = document.getElementById('archive-year');
    const monthSelect = document.getElementById('archive-month');
    const loadBtn = document.getElementById('archive-load-btn');
    const loading = document.getElementById('archive-loading');
    const calendarTitle = document.getElementById('calendar-title');

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    const monthNamesTa = ['ஜனவரி', 'பிப்ரவரி', 'மார்ச்', 'ஏப்ரல்', 'மே', 'ஜூன்',
        'ஜூலை', 'ஆகஸ்ட்', 'செப்டம்பர்', 'அக்டோபர்', 'நவம்பர்', 'டிசம்பர்'];

    function t(msg) {
        const lang = config.lang || 'en';
        const dictionary = {
            'Liturgical colours may be unavailable; all dates still link to readings.': {
                'en': 'Liturgical colours may be unavailable; all dates still link to readings.',
                'ta': 'வழிபாட்டு நிறங்கள் கிடைக்காமல் போகலாம்; அனைத்து தேதிகளும் வாசகங்களுடன் இணைக்கப்பட்டுள்ளன.'
            },
            'Liturgical calendar unavailable — showing dates only.': {
                'en': 'Liturgical calendar unavailable — showing dates only.',
                'ta': 'வழிபாட்டு நாட்காட்டி கிடைக்கவில்லை — தேதிகள் மட்டுமே காண்பிக்கப்படுகின்றன.'
            },
            'Could not reach the calendar service — showing dates only.': {
                'en': 'Could not reach the calendar service — showing dates only.',
                'ta': 'நாட்காட்டிச் சேவையை அணுக முடியவில்லை — தேதிகள் மட்டுமே காண்பிக்கப்படுகின்றன.'
            }
        };
        return (dictionary[msg] && dictionary[msg][lang]) ? dictionary[msg][lang] : msg;
    }

    const colourDotMap = {
        green: 'colour-dot-green',
        violet: 'colour-dot-violet',
        white: 'colour-dot-white',
        red: 'colour-dot-red',
        rose: 'colour-dot-rose'
    };

    function getYearMonth() {
        return {
            year: parseInt(yearSelect?.value || archiveContainer.dataset.year, 10),
            month: parseInt(monthSelect?.value || archiveContainer.dataset.month, 10)
        };
    }

    function buildBasicDays(year, month) {
        const daysInMonth = new Date(year, month, 0).getDate();
        const days = [];

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            days.push({ date: dateStr, liturgical_colour: 'green' });
        }

        return days;
    }

    function showNotice(message, type) {
        const existing = document.getElementById('archive-notice');
        if (existing) {
            existing.remove();
        }

        if (!message) {
            return;
        }

        const notice = document.createElement('div');
        notice.id = 'archive-notice';
        notice.className = 'alert alert-' + (type || 'info') + ' rounded-4 mb-3';
        notice.textContent = message;
        archiveContainer.insertBefore(notice, calendarGrid);
    }

    function renderCalendar(year, month, days) {
        const firstDay = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        const today = new Date();
        const todayStr = today.getFullYear() + '-' +
            String(today.getMonth() + 1).padStart(2, '0') + '-' +
            String(today.getDate()).padStart(2, '0');

        const dayMap = {};
        days.forEach(function (d) {
            const dayNum = parseInt(d.date.split('-')[2], 10);
            dayMap[dayNum] = d;
        });

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            const dayData = dayMap[day] || {};
            const colour = dayData.liturgical_colour || 'green';
            const dotClass = colourDotMap[colour] || 'colour-dot-green';
            const isToday = dateStr === todayStr;
            const title = dayData.celebration || dayData.saint || '';

            html += '<a href="' + config.baseUrl + '/index.php?date=' + dateStr + '" ';
            html += 'class="calendar-day' + (isToday ? ' today' : '') + '" title="' + title.replace(/"/g, '&quot;') + '">';
            html += '<span class="day-num">' + day + '</span>';
            html += '<span class="day-colour ' + dotClass + '"></span>';
            html += '</a>';
        }

        calendarGrid.innerHTML = html;

        if (calendarTitle) {
            const isTamil = config.lang === 'ta';
            calendarTitle.textContent = isTamil
                ? monthNamesTa[month - 1] + ' ' + year
                : monthNames[month - 1] + ' ' + year;
        }

        history.replaceState(null, '', config.baseUrl + '/pages/archive.php?year=' + year + '&month=' + month);
    }

    async function loadCalendar() {
        const { year, month } = getYearMonth();

        loading.classList.remove('d-none');
        calendarGrid.style.opacity = '0.4';
        showNotice(null);

        try {
            const response = await fetch(
                config.apiUrl + '/calendar.php?year=' + year + '&month=' + month
            );

            let data = null;
            try {
                data = await response.json();
            } catch (parseErr) {
                data = null;
            }

            if (data && data.success && Array.isArray(data.days)) {
                renderCalendar(year, month, data.days);

                if (data.partial || data.source === 'basic') {
                    showNotice(
                        data.message || t('Liturgical colours may be unavailable; all dates still link to readings.'),
                        'warning'
                    );
                }
                return;
            }

            renderCalendar(year, month, buildBasicDays(year, month));
            showNotice(t('Liturgical calendar unavailable — showing dates only.'), 'warning');
        } catch (err) {
            renderCalendar(year, month, buildBasicDays(year, month));
            showNotice(t('Could not reach the calendar service — showing dates only.'), 'warning');
        } finally {
            loading.classList.add('d-none');
            calendarGrid.style.opacity = '1';
        }
    }

    if (loadBtn) {
        loadBtn.addEventListener('click', loadCalendar);
    }

    document.addEventListener('DOMContentLoaded', loadCalendar);
})();
