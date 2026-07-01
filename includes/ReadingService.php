<?php
/**
 * Orchestrates liturgical calendar and reading providers.
 */

declare(strict_types=1);

class ReadingService
{
    private ReadingProviderInterface $readingProvider;
    private LiturgicalCalendarService $calendarService;

    public function __construct(
        ?ReadingProviderInterface $readingProvider = null,
        ?LiturgicalCalendarService $calendarService = null
    ) {
        $this->readingProvider = $readingProvider ?? new UniversalisReadingProvider();
        $this->calendarService = $calendarService ?? new LiturgicalCalendarService();
    }

    /**
     * Get readings for today.
     *
     * @return array<string, mixed>
     */
    public function getToday(): array
    {
        return $this->getByDate(new DateTimeImmutable('today'));
    }

    /**
     * Get readings for a specific date string (Y-m-d) or DateTimeInterface.
     *
     * @param string|DateTimeInterface $date
     * @return array<string, mixed>
     */
    public function getByDate(string|DateTimeInterface $date): array
    {
        $dateObj = $this->resolveDate($date);

        try {
            $readings = $this->readingProvider->getReadingsForDate($dateObj);
            $calendarRaw = $this->calendarService->getDay($dateObj);
            $calendar = $this->calendarService->normalizeDay($calendarRaw, $dateObj);

            return $this->mergeResults($readings, $calendar, true);
        } catch (Throwable $e) {
            error_log('ReadingService error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => "Unable to load today's readings. Please try again later.",
                'date' => $dateObj->format('Y-m-d'),
                'formatted_date' => $dateObj->format('l j F Y'),
            ];
        }
    }

    /**
     * Replace the reading provider (for future extensibility).
     */
    public function setReadingProvider(ReadingProviderInterface $provider): void
    {
        $this->readingProvider = $provider;
    }

    private function resolveDate(string|DateTimeInterface $date): DateTimeImmutable
    {
        if ($date instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($date);
        }

        $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($parsed === false) {
            throw new InvalidArgumentException('Invalid date format. Use Y-m-d.');
        }

        return $parsed;
    }

    /**
     * @param array<string, mixed> $readings
     * @param array<string, mixed> $calendar
     * @return array<string, mixed>
     */
    private function mergeResults(array $readings, array $calendar, bool $success): array
    {
        $celebration = $calendar['celebration'] !== ''
            ? $calendar['celebration']
            : ($readings['celebration'] ?? '');

        $saint = $calendar['saint'] !== ''
            ? $calendar['saint']
            : $this->extractSaintFromCelebration($readings['celebration'] ?? '');

        return array_merge($readings, [
            'success' => $success,
            'error' => null,
            'date' => $readings['date'] ?? $calendar['date'],
            'celebration' => $celebration,
            'season' => $calendar['season_label'] ?: $this->inferSeasonFromCelebration($celebration),
            'season_key' => $calendar['season'] ?? '',
            'season_week' => $calendar['season_week'],
            'liturgical_colour' => $calendar['liturgical_colour'] ?? 'green',
            'saint' => $saint,
            'weekday' => $calendar['weekday'] ?? '',
        ]);
    }

    private function extractSaintFromCelebration(string $celebration): string
    {
        if (preg_match('/(?:Saint|Saints|Blessed|St\.|Sts\.)\s[^,(]+/i', $celebration, $matches)) {
            return trim($matches[0]);
        }

        return '';
    }

    private function inferSeasonFromCelebration(string $celebration): string
    {
        if (preg_match('/(Advent|Christmas|Lent|Easter|Ordinary Time)/i', $celebration, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
