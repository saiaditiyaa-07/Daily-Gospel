/**
 * Daily Gospel - Contact Forms
 */
(function () {
    'use strict';

    const config = window.DailyGospel || {};

    /* Feedback form */
    const feedbackForm = document.getElementById('feedback-form');
    const feedbackAlert = document.getElementById('feedback-alert');
    const ratingValue = document.getElementById('feedback-rating-value');
    const starBtns = document.querySelectorAll('.star-btn');

    starBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const rating = parseInt(this.dataset.rating, 10);
            ratingValue.value = rating;
            starBtns.forEach(function (b, i) {
                const icon = b.querySelector('i');
                icon.className = i < rating ? 'bi bi-star-fill fs-4' : 'bi bi-star fs-4';
            });
        });
    });

    if (feedbackForm) {
        feedbackForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(feedbackForm);

            try {
                const response = await fetch(config.apiUrl + '/feedback.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                const data = await response.json();
                feedbackAlert.innerHTML = '<div class="alert alert-' + (data.success ? 'success' : 'danger') + ' rounded-3">' + (data.message || data.error) + '</div>';
                if (data.success) feedbackForm.reset();
            } catch (err) {
                feedbackAlert.innerHTML = '<div class="alert alert-danger rounded-3">Unable to submit feedback.</div>';
            }
        });
    }

    /* Prayer request form */
    const prayerForm = document.getElementById('prayer-form');
    const prayerAlert = document.getElementById('prayer-alert');

    if (prayerForm) {
        prayerForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(prayerForm);
            const payload = Object.fromEntries(formData);
            payload.anonymous = prayerForm.querySelector('#prayer-anonymous')?.checked ? 1 : 0;

            try {
                const response = await fetch(config.apiUrl + '/prayer-request.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                prayerAlert.innerHTML = '<div class="alert alert-' + (data.success ? 'success' : 'danger') + ' rounded-3">' + (data.message || data.error) + '</div>';
                if (data.success) prayerForm.reset();
            } catch (err) {
                prayerAlert.innerHTML = '<div class="alert alert-danger rounded-3">Unable to submit prayer request.</div>';
            }
        });
    }
})();
