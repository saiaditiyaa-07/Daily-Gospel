<?php
/**
 * Universalis JSONP reading provider (official webmaster service).
 */

declare(strict_types=1);

class UniversalisReadingProvider implements ReadingProviderInterface
{
    private ApiClient $client;
    private string $baseUrl;
    private string $region;

    public function __construct(?ApiClient $client = null)
    {
        $this->client = $client ?? new ApiClient();
        $this->baseUrl = UNIVERSALIS_BASE;
        $this->region = UNIVERSALIS_REGION;
    }

    /**
     * @return array<string, mixed>
     */
    public function getReadingsForDate(DateTimeInterface $date): array
    {
        $url = $this->buildJsonpUrl($date);
        $body = $this->client->get($url);
        $payload = $this->parseJsonpResponse($body);

        return $this->normalizeReadings($payload, $date);
    }

    private function buildJsonpUrl(DateTimeInterface $date): string
    {
        $dateKey = $date->format('Ymd');
        $regionPath = $this->region !== '' ? rtrim($this->region, '/') . '/' : '';

        return sprintf(
            '%s/%s%s/jsonpmass.js?callback=universalisCallback',
            rtrim($this->baseUrl, '/'),
            $regionPath,
            $dateKey
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function parseJsonpResponse(string $body): array
    {
        if (!preg_match('/universalisCallback\s*\(\s*(\{.*\})\s*\)\s*;?\s*$/s', trim($body), $matches)) {
            throw new RuntimeException('Unable to parse Universalis JSONP response.');
        }

        $data = json_decode($matches[1], true);

        if (!is_array($data)) {
            throw new RuntimeException('Invalid Universalis reading data.');
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalizeReadings(array $payload, DateTimeInterface $date): array
    {
        $celebrationHtml = (string) ($payload['day'] ?? '');
        $celebration = $this->stripHtml($celebrationHtml);

        return [
            'provider' => 'universalis',
            'date' => $date->format('Y-m-d'),
            'formatted_date' => (string) ($payload['date'] ?? $date->format('l j F Y')),
            'celebration' => $celebration,
            'celebration_html' => $celebrationHtml,
            'first_reading' => $this->normalizeReadingBlock($payload['Mass_R1'] ?? null),
            'psalm' => $this->normalizeReadingBlock($payload['Mass_Ps'] ?? null, true),
            'second_reading' => $this->normalizeReadingBlock($payload['Mass_R2'] ?? null),
            'gospel_acclamation' => $this->normalizeReadingBlock($payload['Mass_GA'] ?? null),
            'gospel' => $this->normalizeReadingBlock($payload['Mass_G'] ?? null),
            'copyright' => $this->stripHtml((string) ($payload['copyright']['text'] ?? '')),
            'copyright_html' => (string) ($payload['copyright']['text'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed>|null $block
     * @return array<string, string>|null
     */
    private function normalizeReadingBlock(?array $block, bool $allowEmptyHeading = false): ?array
    {
        if ($block === null || $block === []) {
            return null;
        }

        $text = (string) ($block['text'] ?? '');
        if ($text === '') {
            return null;
        }

        return [
            'heading' => $allowEmptyHeading ? (string) ($block['heading'] ?? '') : (string) ($block['heading'] ?? ''),
            'source' => html_entity_decode((string) ($block['source'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'text' => $text,
            'text_plain' => $this->stripHtml($text),
        ];
    }

    private function stripHtml(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
