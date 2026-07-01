<?php
/**
 * Liturgical calendar data from calapi.inadiutorium.cz
 */

declare(strict_types=1);

class LiturgicalCalendarService
{
    private ApiClient $client;
    private string $baseUrl;
    private string $language;
    private string $calendarId;

    public function __construct(?ApiClient $client = null)
    {
        $this->client = $client ?? new ApiClient(
            CALENDAR_API_TIMEOUT,
            CALENDAR_API_CONNECT_TIMEOUT
        );
        $this->baseUrl = LITURGICAL_CALENDAR_BASE;
        $this->language = LITURGICAL_CALENDAR_LANG;
        $this->calendarId = LITURGICAL_CALENDAR_ID;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDay(DateTimeInterface $date): ?array
    {
        if ($this->isApiDown()) {
            return null;
        }

        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $day = (int) $date->format('j');

        $url = sprintf(
            '%s/%s/calendars/%s/%d/%d/%d',
            $this->baseUrl,
            $this->language,
            $this->calendarId,
            $year,
            $month,
            $day
        );

        try {
            return $this->client->getJson($url);
        } catch (Throwable $e) {
            error_log('Liturgical calendar API error: ' . $e->getMessage());
            $this->markApiDown();
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function normalizeDay(?array $data, DateTimeInterface $date): array
    {
        if ($data === null) {
            return [
                'date' => $date->format('Y-m-d'),
                'season' => '',
                'season_label' => '',
                'season_week' => null,
                'liturgical_colour' => 'green',
                'celebration' => '',
                'saint' => '',
                'weekday' => strtolower($date->format('l')),
            ];
        }

        $celebrations = $data['celebrations'] ?? [];
        $primary = $this->getPrimaryCelebration($celebrations);
        $saint = $this->extractSaint($celebrations);

        return [
            'date' => $data['date'] ?? $date->format('Y-m-d'),
            'season' => $data['season'] ?? '',
            'season_label' => $this->formatSeason($data['season'] ?? '', (int) ($data['season_week'] ?? 0)),
            'season_week' => $data['season_week'] ?? null,
            'liturgical_colour' => $primary['colour'] ?? 'green',
            'celebration' => $primary['title'] !== '' ? $primary['title'] : $this->inferCelebrationTitle($data),
            'saint' => $saint,
            'weekday' => $data['weekday'] ?? strtolower($date->format('l')),
        ];
    }

    /**
     * Build a month of basic day entries without calling the liturgical API.
     *
     * @return array<int, array<string, mixed>>
     */
    public function buildBasicMonth(int $year, int $month): array
    {
        $daysInMonth = get_days_in_month($month, $year);
        $days = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = DateTimeImmutable::createFromFormat('Y-n-j', "$year-$month-$day");
            if ($date === false) {
                continue;
            }

            $days[] = $this->normalizeDay(null, $date);
        }

        return $days;
    }

    /**
     * Get calendar summary for a month (for archive view).
     * Always returns a full month; liturgical details may be partial when the API is slow.
     *
     * @return array{days: array<int, array<string, mixed>>, partial: bool, source: string}
     */
    public function getMonth(int $year, int $month): array
    {
        $cached = $this->readCache($year, $month);
        if ($cached !== null) {
            return $cached;
        }

        $basicDays = $this->buildBasicMonth($year, $month);

        if ($this->isApiDown()) {
            return [
                'days' => $basicDays,
                'partial' => true,
                'source' => 'basic',
            ];
        }

        $deadline = time() + CALENDAR_MONTH_BUDGET;
        $liturgicalCount = 0;
        $consecutiveFailures = 0;
        $days = [];

        foreach ($basicDays as $basic) {
            if (time() >= $deadline || $consecutiveFailures >= 3) {
                $days[] = $basic;
                continue;
            }

            $dateStr = (string) ($basic['date'] ?? '');
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
            if ($date === false) {
                $days[] = $basic;
                continue;
            }

            $raw = $this->getDay($date);
            if ($raw !== null) {
                $consecutiveFailures = 0;
                $liturgicalCount++;
                $days[] = $this->normalizeDay($raw, $date);
            } else {
                $consecutiveFailures++;
                $days[] = $basic;
            }
        }

        $partial = $liturgicalCount < count($basicDays);
        $source = $liturgicalCount === 0 ? 'basic' : ($partial ? 'partial' : 'liturgical');

        $result = [
            'days' => $days,
            'partial' => $partial,
            'source' => $source,
        ];

        if ($liturgicalCount > 0) {
            $this->writeCache($year, $month, $result);
        }

        return $result;
    }

    /**
     * @param array<int, array<string, mixed>> $celebrations
     * @return array<string, mixed>
     */
    private function getPrimaryCelebration(array $celebrations): array
    {
        if ($celebrations === []) {
            return ['title' => '', 'colour' => 'green', 'rank' => '', 'rank_num' => 999];
        }

        usort($celebrations, static function (array $a, array $b): int {
            return ($a['rank_num'] ?? 999) <=> ($b['rank_num'] ?? 999);
        });

        foreach ($celebrations as $celebration) {
            if (($celebration['title'] ?? '') !== '') {
                return $celebration;
            }
        }

        return $celebrations[0];
    }

    /**
     * @param array<int, array<string, mixed>> $celebrations
     */
    private function extractSaint(array $celebrations): string
    {
        foreach ($celebrations as $celebration) {
            $title = trim((string) ($celebration['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            if (preg_match('/^(Saint|Saints|Blessed|St\.|Sts\.)\s/i', $title)) {
                return $title;
            }
        }

        return '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function inferCelebrationTitle(array $data): string
    {
        $season = $this->formatSeason($data['season'] ?? '', (int) ($data['season_week'] ?? 0));
        $weekday = ucfirst((string) ($data['weekday'] ?? ''));

        if ($season !== '' && $weekday !== '') {
            return $weekday . ' in ' . $season;
        }

        return $season;
    }

    private function formatSeason(string $season, int $week): string
    {
        $labels = [
            'ordinary' => 'Ordinary Time',
            'advent' => 'Advent',
            'christmas' => 'Christmas',
            'lent' => 'Lent',
            'easter' => 'Easter',
        ];

        $label = $labels[$season] ?? ucfirst($season);

        if ($week > 0 && in_array($season, ['ordinary', 'advent', 'lent', 'easter'], true)) {
            return $label . ' (Week ' . $week . ')';
        }

        return $label;
    }

    /**
     * @return array{days: array<int, array<string, mixed>>, partial: bool, source: string}|null
     */
    private function readCache(int $year, int $month): ?array
    {
        $path = $this->cachePath($year, $month);
        if (is_file($path)) {
            $raw = file_get_contents($path);
            if ($raw !== false) {
                $entry = json_decode($raw, true);
                if (is_array($entry) && ($entry['expires'] ?? 0) > time() && isset($entry['data']) && is_array($entry['data'])) {
                    return $entry['data'];
                }
                @unlink($path);
            }
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            return null;
        }

        $sessionKey = "calendar_month_{$year}_{$month}";
        if (isset($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey])) {
            $entry = $_SESSION[$sessionKey];
            if (($entry['expires'] ?? 0) > time() && isset($entry['data']) && is_array($entry['data'])) {
                return $entry['data'];
            }
        }

        return null;
    }

    /**
     * @param array{days: array<int, array<string, mixed>>, partial: bool, source: string} $data
     */
    private function writeCache(int $year, int $month, array $data): void
    {
        $entry = [
            'expires' => time() + CALENDAR_CACHE_TTL,
            'data' => $data,
        ];

        $dir = APP_ROOT . '/cache/calendar';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return;
        }

        $path = $this->cachePath($year, $month);
        file_put_contents($path, json_encode($entry, JSON_UNESCAPED_UNICODE), LOCK_EX);

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION["calendar_month_{$year}_{$month}"] = $entry;
        }
    }

    private function cachePath(int $year, int $month): string
    {
        return APP_ROOT . '/cache/calendar/' . $year . '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '.json';
    }

    private function isApiDown(): bool
    {
        $path = APP_ROOT . '/cache/calendar/api_down.flag';
        if (is_file($path)) {
            $expires = (int) @file_get_contents($path);
            if ($expires > time()) {
                return true;
            }
            @unlink($path);
        }
        return false;
    }

    private function markApiDown(): void
    {
        $dir = APP_ROOT . '/cache/calendar';
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return;
        }
        $path = $dir . '/api_down.flag';
        @file_put_contents($path, (string) (time() + 300), LOCK_EX);
    }
}
