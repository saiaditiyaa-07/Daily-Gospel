/**
 * Language switcher functionality.
 * Handles localStorage persistence, cookie setting, and synchronization with PHP session.
 */

document.addEventListener('DOMContentLoaded', () => {
    const localLang = localStorage.getItem('lang');
    const activeLang = window.DailyGospel ? window.DailyGospel.lang : 'en';

    // 1. Sync preference for existing users
    if (localLang && localLang !== activeLang && (localLang === 'en' || localLang === 'ta')) {
        document.cookie = `lang=${localLang}; path=/; max-age=31536000; samesite=lax`;
        const url = new URL(window.location.href);
        url.searchParams.set('lang', localLang);
        window.location.href = url.toString();
        return;
    }

    // 2. Initialize localStorage if empty
    if (!localLang && activeLang) {
        localStorage.setItem('lang', activeLang);
    }

    // 3. Bind click event to dropdown links
    const switchLinks = document.querySelectorAll('.lang-switch-link');
    switchLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetLang = link.getAttribute('data-lang');
            if (targetLang === 'en' || targetLang === 'ta') {
                localStorage.setItem('lang', targetLang);
                document.cookie = `lang=${targetLang}; path=/; max-age=31536000; samesite=lax`;
                
                // Update URL parameter and redirect
                const url = new URL(window.location.href);
                url.searchParams.set('lang', targetLang);
                window.location.href = url.toString();
            }
        });
    });
});
