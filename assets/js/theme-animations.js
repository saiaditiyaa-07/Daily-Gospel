/**
 * Daily Gospel - Premium Theme Animations & Scroll Interactions
 * Features: Reading progress bar, current paragraph scroll highlight, card scroll reveal
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Reading Scroll Progress Bar
        const progressBar = document.getElementById('scroll-progress-bar');
        const readingsContent = document.getElementById('readings-content');
        
        function updateScrollProgress() {
            if (!progressBar) return;
            
            let totalHeight = 0;
            let scrolled = 0;
            
            if (readingsContent) {
                // Calculate progress relative to the readings content area
                const rect = readingsContent.getBoundingClientRect();
                const contentHeight = rect.height;
                const topOffset = rect.top;
                
                totalHeight = contentHeight - window.innerHeight;
                scrolled = -topOffset;
                
                if (totalHeight <= 0) {
                    progressBar.style.width = '0%';
                    return;
                }
            } else {
                // Fallback to global page scroll
                totalHeight = document.documentElement.scrollHeight - window.innerHeight;
                scrolled = window.scrollY;
            }
            
            const progress = Math.max(0, Math.min(100, (scrolled / totalHeight) * 100));
            progressBar.style.width = progress + '%';
        }

        window.addEventListener('scroll', updateScrollProgress);
        window.addEventListener('resize', updateScrollProgress);

        // 2. Active Paragraph Scrolling Highlight
        function initParagraphHighlighting() {
            const paragraphs = document.querySelectorAll('.reading-text p, .reading-text div');
            if (paragraphs.length === 0) return;

            const observerOptions = {
                root: null,
                rootMargin: '-30% 0px -40% 0px', // Target middle center of screen
                threshold: 0
            };

            const paragraphObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        // Remove active class from all paragraphs in the same section
                        const parent = entry.target.closest('.reading-text');
                        if (parent) {
                            parent.querySelectorAll('.active-paragraph').forEach(function (p) {
                                p.classList.remove('active-paragraph');
                            });
                        }
                        entry.target.classList.add('active-paragraph');
                    } else {
                        entry.target.classList.remove('active-paragraph');
                    }
                });
            }, observerOptions);

            paragraphs.forEach(function (p) {
                paragraphObserver.observe(p);
            });
        }

        // Initialize paragraphs observer
        initParagraphHighlighting();

        // 3. Scroll Reveal for Cards & Widgets
        const revealElements = document.querySelectorAll('.card, .sidebar-widget, .meta-chip, .saint-highlight, .reading-accordion-item');
        
        const revealObserverOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px', // Trigger slightly before entering screen
            threshold: 0.05
        };

        const revealObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Reveal only once
                }
            });
        }, revealObserverOptions);

        revealElements.forEach(function (el) {
            el.classList.add('reveal-on-scroll');
            revealObserver.observe(el);
        });

        // Re-run paragraph highlighters and scroll recalculation when content is updated dynamically (AJAX calls)
        const readingsCardsContainer = document.getElementById('readings-cards');
        if (readingsCardsContainer) {
            const observer = new MutationObserver(function () {
                setTimeout(function () {
                    initParagraphHighlighting();
                    updateScrollProgress();
                    
                    // Apply reveal class to new cards
                    document.querySelectorAll('.card, .meta-chip, .saint-highlight, .reading-accordion-item').forEach(function (el) {
                        if (!el.classList.contains('reveal-on-scroll')) {
                            el.classList.add('reveal-on-scroll');
                            revealObserver.observe(el);
                        }
                    });
                }, 100);
            });
            observer.observe(readingsCardsContainer, { childList: true });
        }
        
        // Initial call
        updateScrollProgress();
    });
})();
