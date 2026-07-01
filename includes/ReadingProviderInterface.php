<?php
/**
 * Contract for swappable daily reading providers.
 */

declare(strict_types=1);

interface ReadingProviderInterface
{
    /**
     * Fetch mass readings for a specific date.
     *
     * @return array<string, mixed>
     */
    public function getReadingsForDate(DateTimeInterface $date): array;
}
