/**
 * Daily Gospel - Sports Widget Component
 * Vanilla JS implementation of React SportsWidget
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('sports-widget-container');
        if (!container) return;

        // Fetch configs and language
        const config = window.DailyGospel || {};
        const baseUrl = config.baseUrl || '';
        const lang = config.lang || 'ta';

        // Translation dictionary for localized static labels
        const translations = {
            'sportsAlert': {
                'en': 'Sports Live',
                'ta': 'விளையாட்டு நேரலை'
            },
            'sportsHeadlines': {
                'en': 'Sports Headlines',
                'ta': 'விளையாட்டுச் செய்திகள்'
            }
        };

        function t(key) {
            return (translations[key] && translations[key][lang]) ? translations[key][lang] : key;
        }

        // Render skeleton loader
        function renderLoading() {
            container.innerHTML = `
                <div class="sports-widget-card p-4 border border-light-gray rounded-4 shadow-sm skeleton-pulse">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                        <div class="bg-secondary opacity-25 rounded-pill" style="height: 14px; width: 100px;"></div>
                        <div class="bg-secondary opacity-25 rounded-circle" style="height: 10px; width: 10px;"></div>
                    </div>
                    <div class="p-3 bg-secondary opacity-10 rounded-3 mb-3" style="height: 75px;"></div>
                    <div class="pt-2 border-top">
                        <div class="bg-secondary opacity-25 rounded-pill mb-2" style="height: 12px; width: 120px;"></div>
                        <div class="bg-secondary opacity-10 rounded mb-2" style="height: 30px;"></div>
                        <div class="bg-secondary opacity-10 rounded" style="height: 30px;"></div>
                    </div>
                </div>
            `;
        }

        // Fallback default mock data
        const fallbackData = {
            live_match: {
                teams: "IND vs AUS (T20 World Cup)",
                teams_ta: "இந்தியா எதிர் ஆஸ்திரேலியா (டி20 உலகக் கோப்பை)",
                status: "In Progress - Innings Break",
                status_ta: "விளையாட்டு நடந்து கொண்டிருக்கிறது - இடைவேளை",
                score: "IND: 196/5 (20.0 Over) | AUS: 0/0 (0.0 Over)",
                score_ta: "IND: 196/5 (20.0 ஓவர்) | AUS: 0/0 (0.0 ஓவர்)"
            },
            headlines: [
                "India posts a massive total of 196 against Australia in Super 8 stage",
                "Hardik Pandya slams quick-fire 45 off 18 balls to lift the score",
                "CSK resumes training camp in Chennai ahead of qualifiers"
            ],
            headlines_ta: [
                "சூப்பர் 8 சுற்றில் ஆஸ்திரேலியாவுக்கு எதிராக இந்தியா 196 ரன்கள் குவித்தது",
                "ஹர்திக் பாண்டியா 18 பந்துகளில் 45 ரன்கள் விளாசி ஸ்கோரை உயர்த்தினார்",
                "தகுதிச் சுற்றுக்கு முன்னதாக சென்னையில் சி.எஸ்.கே அணி பயிற்சியை தொடங்கியது"
            ]
        };

        // Render target data
        function renderWidget(sports) {
            const liveMatch = sports.live_match || fallbackData.live_match;
            
            const matchTeams = lang === 'ta' ? liveMatch.teams_ta : liveMatch.teams;
            const matchScore = lang === 'ta' ? liveMatch.score_ta : liveMatch.score;
            const matchStatus = lang === 'ta' ? liveMatch.status_ta : liveMatch.status;
            
            const rawHeadlines = lang === 'ta' ? sports.headlines_ta : sports.headlines;
            const headlines = (Array.isArray(rawHeadlines) ? rawHeadlines : []).slice(0, 2);

            let headlinesHtml = '';
            if (headlines.length > 0) {
                headlinesHtml = `
                    <div class="headlines-box pt-2 border-top border-light-gray mt-3">
                        <span class="headlines-title text-muted text-uppercase d-block mb-2" style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em;">${t('sportsHeadlines')}</span>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            ${headlines.map(hl => `
                                <li class="headline-item d-flex gap-2 align-items-start font-semibold cursor-pointer">
                                    <i class="bi bi-chevron-right mt-0.5" style="font-size: 11px; font-weight: bold;"></i>
                                    <span class="headline-text line-clamp-2">${hl}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            }

            container.innerHTML = `
                <div class="sports-widget-card p-3 border border-light-gray rounded-4 shadow-sm text-dark">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                        <h4 class="sports-widget-title text-xs font-black uppercase tracking-widest text-primary d-flex align-items-center mb-0" style="font-size: 11px; font-weight: 800;">
                            <i class="bi bi-trophy-fill text-danger me-1"></i>
                            <span>${t('sportsAlert')}</span>
                        </h4>
                        <span class="live-dot-ping" aria-label="Live Match"></span>
                    </div>

                    <!-- Live Cricket Score Box -->
                    <div class="live-score-box p-3 border rounded-3">
                        <div class="score-teams text-uppercase font-black mb-1">${matchTeams}</div>
                        <div class="score-runs font-black mb-1">${matchScore}</div>
                        <div class="score-status text-uppercase text-muted">${matchStatus}</div>
                    </div>

                    <!-- Sports Headlines list -->
                    ${headlinesHtml}
                </div>
            `;
        }

        // Initialize fetching
        async function fetchSports() {
            renderLoading();
            
            const apiUrl = baseUrl ? baseUrl + '/api/widgets/sports.php' : '/api/widgets/sports.php';
            
            try {
                const response = await fetch(apiUrl);
                if (response.ok) {
                    const data = await response.json();
                    renderWidget(data);
                } else {
                    renderWidget(fallbackData);
                }
            } catch (err) {
                renderWidget(fallbackData);
            }
        }

        fetchSports();
    });
})();
