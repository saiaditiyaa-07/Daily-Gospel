<?php
/**
 * English Reading Provider.
 *
 * Wraps the existing UniversalisReadingProvider so that the provider
 * selection layer can reference a consistent class name.
 */

declare(strict_types=1);

require_once APP_ROOT . '/includes/ReadingProviderInterface.php';
require_once APP_ROOT . '/includes/UniversalisReadingProvider.php';

class EnglishProvider implements ReadingProviderInterface
{
    private UniversalisReadingProvider $inner;

    public function __construct(?UniversalisReadingProvider $inner = null)
    {
        $this->inner = $inner ?? new UniversalisReadingProvider();
    }

    /**
     * @return array<string, mixed>
     */
    public function getReadingsForDate(DateTimeInterface $date): array
    {
        return $this->inner->getReadingsForDate($date);
    }
}
