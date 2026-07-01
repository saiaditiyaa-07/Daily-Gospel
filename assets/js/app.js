/**
 * Daily Gospel - Core Application JavaScript
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};

    /* ---- Offline detection ---- */
    function initOfflineDetection() {
        const banner = document.getElementById('offline-banner');
        const closeBtn = document.getElementById('offline-banner-close');

        function updateOnlineStatus() {
            if (!banner) return;
            if (!navigator.onLine) {
                banner.classList.remove('d-none');
            } else {
                banner.classList.add('d-none');
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                banner.classList.add('d-none');
            });
        }

        updateOnlineStatus();
    }

    /* ---- Share URL helpers ---- */
    function getShareUrl() {
        const dateInput = document.getElementById('current-date');
        const date = dateInput ? dateInput.value : '';
        return date
            ? config.baseUrl + '/index.php?date=' + date
            : window.location.href;
    }

    function getShareTitle() {
        const displayDate = document.getElementById('display-date');
        const celebration = document.getElementById('header-celebration') ||
            document.getElementById('celebration-text');
        let title = displayDate
            ? 'Daily Gospel — ' + displayDate.textContent
            : 'Daily Gospel';
        if (celebration && celebration.textContent.trim()) {
            title += ' — ' + celebration.textContent.trim();
        }
        return title;
    }

    function openShareWindow(url) {
        window.open(url, '_blank', 'noopener,noreferrer,width=600,height=500');
    }

    function initShareButtons() {
        document.querySelectorAll('[data-share]').forEach(function (btn) {
            btn.replaceWith(btn.cloneNode(true));
        });

        document.querySelectorAll('[data-share]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const platform = this.dataset.share;
                const url = encodeURIComponent(getShareUrl());
                const title = encodeURIComponent(getShareTitle());
                const text = encodeURIComponent(getShareTitle());

                switch (platform) {
                    case 'facebook':
                        openShareWindow('https://www.facebook.com/sharer/sharer.php?u=' + url);
                        break;
                    case 'whatsapp':
                        openShareWindow('https://wa.me/?text=' + text + '%20' + url);
                        break;
                    case 'twitter':
                        openShareWindow('https://twitter.com/intent/tweet?text=' + text + '&url=' + url);
                        break;
                    case 'telegram':
                        openShareWindow('https://t.me/share/url?url=' + url + '&text=' + text);
                        break;
                    case 'email':
                        window.location.href = 'mailto:?subject=' + title + '&body=' + text + '%0A%0A' + url;
                        break;
                }
            });
        });
    }

    window.DailyGospelInitShareButtons = initShareButtons;

    /* ---- Native share ---- */
    function initShare() {
        const shareBtn = document.getElementById('share-btn');
        if (!shareBtn) return;

        shareBtn.addEventListener('click', async function () {
            const url = getShareUrl();
            const title = getShareTitle();

            if (navigator.share) {
                try {
                    await navigator.share({ title: title, url: url });
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        copyToClipboard(url);
                    }
                }
            } else {
                copyToClipboard(url);
            }
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            showToast('Link copied to clipboard!');
        }).catch(function () {
            prompt('Copy this link:', text);
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-2 bg-primary text-white rounded-pill shadow';
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 3000);
    }

    /* ---- Print ---- */
    function initPrint() {
        const printBtn = document.getElementById('print-btn');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                window.print();
            });
        }
    }

    /* ---- Bookmark ---- */
    function initBookmark() {
        const bookmarkBtn = document.getElementById('bookmark-btn');
        if (!bookmarkBtn) return;

        bookmarkBtn.addEventListener('click', async function () {
            const dateInput = document.getElementById('current-date');
            const celebration = document.getElementById('celebration-text');

            if (!dateInput) return;

            try {
                const response = await fetch(config.apiUrl + '/bookmark.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        date: dateInput.value,
                        title: celebration ? celebration.textContent : ''
                    })
                });

                const data = await response.json();
                showToast(data.success ? data.message : (data.error || 'Failed to bookmark'));
            } catch (err) {
                showToast('Unable to save bookmark. Database may not be configured.');
            }
        });
    }

    /* ---- Retry ---- */
    function initRetry() {
        const retryBtn = document.getElementById('retry-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', function () {
                const dateInput = document.getElementById('current-date');
                if (dateInput && window.DailyGospelLoadReadings) {
                    window.DailyGospelLoadReadings(dateInput.value);
                } else {
                    window.location.reload();
                }
            });
        }
    }

    /* ---- Smooth scroll for anchor links ---- */
    document.addEventListener('click', function (e) {
        const anchor = e.target.closest('a[href^="#"]');
        if (!anchor) return;
        const target = document.querySelector(anchor.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const collapse = target.querySelector('.accordion-collapse');
            if (collapse && !collapse.classList.contains('show')) {
                const btn = target.querySelector('.accordion-button');
                if (btn) btn.click();
            }
        }
    });

    /* ---- Text size & style customizer ---- */
    let currentFontFamily = localStorage.getItem('reading_font_family') || 'serif';
    let currentFontSizePercent = parseInt(localStorage.getItem('reading_font_size_percent') || '100', 10);

    function applyTextSettings() {
        const readingsContent = document.getElementById('readings-content');
        if (!readingsContent) return;

        // Apply font family
        if (currentFontFamily === 'sans') {
            readingsContent.style.setProperty('--reading-font-family', 'var(--dg-font-sans)');
        } else {
            readingsContent.style.setProperty('--reading-font-family', 'var(--dg-font-serif)');
        }

        // Apply font size (base is 1.125rem)
        const sizeRem = (1.125 * (currentFontSizePercent / 100)) + 'rem';
        readingsContent.style.setProperty('--reading-font-size', sizeRem);

        // Update UI elements in dropdown if they exist
        const serifBtn = document.getElementById('font-serif-btn');
        const sansBtn = document.getElementById('font-sans-btn');
        const sizeLabel = document.getElementById('font-size-label');

        if (serifBtn && sansBtn) {
            serifBtn.classList.toggle('active', currentFontFamily === 'serif');
            sansBtn.classList.toggle('active', currentFontFamily === 'sans');
        }
        if (sizeLabel) {
            sizeLabel.textContent = currentFontSizePercent + '%';
        }
    }

    function initTextSettings() {
        applyTextSettings();

        // Listen for events if elements exist
        document.addEventListener('click', function (e) {
            const serifBtn = e.target.closest('#font-serif-btn');
            const sansBtn = e.target.closest('#font-sans-btn');
            const decBtn = e.target.closest('#font-dec-btn');
            const incBtn = e.target.closest('#font-inc-btn');

            if (serifBtn) {
                currentFontFamily = 'serif';
                localStorage.setItem('reading_font_family', currentFontFamily);
                applyTextSettings();
            }
            if (sansBtn) {
                currentFontFamily = 'sans';
                localStorage.setItem('reading_font_family', currentFontFamily);
                applyTextSettings();
            }
            if (decBtn) {
                if (currentFontSizePercent > 80) {
                    currentFontSizePercent -= 10;
                    localStorage.setItem('reading_font_size_percent', currentFontSizePercent.toString());
                    applyTextSettings();
                }
            }
            if (incBtn) {
                if (currentFontSizePercent < 150) {
                    currentFontSizePercent += 10;
                    localStorage.setItem('reading_font_size_percent', currentFontSizePercent.toString());
                    applyTextSettings();
                }
            }
        });
    }

    window.DailyGospelApplyTextSettings = applyTextSettings;

    /* ---- Init ---- */
    document.addEventListener('DOMContentLoaded', function () {
        initOfflineDetection();
        initShare();
        initShareButtons();
        initPrint();
        initBookmark();
        initRetry();
        initTextSettings();
    });

    window.DailyGospelToast = showToast;
})();
