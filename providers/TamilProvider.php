<?php
/**
 * Tamil Reading Provider.
 *
 * Fetches daily Mass readings in Tamil from the Catholic Gallery
 * WordPress REST API:
 *   https://bible.catholicgallery.org/wp-json/wp/v2/posts?slug=tr-{DDMMYY}
 *
 * Slug format: tr-020726  (DD=02, MM=07, YY=26 for 2026-07-02)
 *
 * Content structure inside post.content.rendered:
 *   <h2 class="dayTitle clrgreen">  — Liturgical day
 *   <div class="readings" id="read1">  — First Reading
 *   <div class="readings" id="read2">  — Responsorial Psalm
 *   <div class="readings" id="read3">  — Second Reading (optional)
 *   <div class="readings" id="read5">  — Gospel Acclamation
 *   <div class="readings" id="read6">  — Gospel
 *
 * Caching: cache/tamil/YYYY-MM-DD.json (24-hour TTL)
 */

declare(strict_types=1);

require_once APP_ROOT . '/includes/ReadingProviderInterface.php';
require_once APP_ROOT . '/includes/ApiClient.php';

class TamilProvider implements ReadingProviderInterface
{
    private const API_BASE    = 'https://bible.catholicgallery.org/wp-json/wp/v2/posts';
    private const CACHE_DIR   = APP_ROOT . '/cache/tamil';
    private const CACHE_TTL   = 86400; // 24 hours

    private ApiClient $client;

    public function __construct(?ApiClient $client = null)
    {
        $this->client = $client ?? new ApiClient();
    }

    // -------------------------------------------------------------------------
    // Public interface
    // -------------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    public function getReadingsForDate(DateTimeInterface $date): array
    {
        $dateStr = $date->format('Y-m-d');

        // 1. Try cache first
        $cached = $this->readCache($dateStr);
        if ($cached !== null) {
            return $cached;
        }

        // 2. Fetch from API
        try {
            $slug    = $this->buildSlug($date);
            $url     = self::API_BASE . '?slug=' . urlencode($slug);
            $body    = $this->client->get($url, [
                'Accept: application/json',
                'User-Agent: DailyGospel/1.0 (Tamil; +https://dailygospel.local)',
            ]);
            $posts   = json_decode($body, true);

            if (!is_array($posts) || count($posts) === 0) {
                return $this->unavailableResponse($dateStr, $date);
            }

            $post   = $posts[0];
            $result = $this->normalizePost($post, $date);

            // 3. Write to cache
            $this->writeCache($dateStr, $result);

            return $result;

        } catch (Throwable $e) {
            error_log('TamilProvider fetch error for ' . $dateStr . ': ' . $e->getMessage());

            // Return stale cache if it exists, regardless of age
            $stale = $this->readCache($dateStr, ignoreExpiry: true);
            if ($stale !== null) {
                $stale['from_stale_cache'] = true;
                return $stale;
            }

            return $this->unavailableResponse($dateStr, $date, $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Slug / URL helpers
    // -------------------------------------------------------------------------

    private function buildSlug(DateTimeInterface $date): string
    {
        // Format: tr-DDMMYY  (e.g. tr-020726 for 2026-07-02)
        return 'tr-' . $date->format('d') . $date->format('m') . substr($date->format('Y'), 2);
    }

    // -------------------------------------------------------------------------
    // Normalisation
    // -------------------------------------------------------------------------

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function normalizePost(array $post, DateTimeInterface $date): array
    {
        $title       = html_entity_decode(strip_tags((string)($post['title']['rendered'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $contentHtml = (string)($post['content']['rendered'] ?? '');

        $dom    = $this->loadDom($contentHtml);
        $xpath  = new DOMXPath($dom);

        $liturgicalDay   = $this->extractLiturgicalDay($xpath);
        $firstReading    = $this->extractReading($xpath, 'read1');
        $psalm           = $this->extractPsalm($xpath, 'read2');
        $secondReading   = $this->extractReading($xpath, 'read3');
        $gospelAcclam    = $this->extractGospelAcclamation($xpath, 'read5');
        $gospel          = $this->extractReading($xpath, 'read6');

        return [
            'provider'          => 'tamil',
            'date'              => $date->format('Y-m-d'),
            'formatted_date'    => $date->format('l, j F Y'),
            'celebration'       => $liturgicalDay,
            'celebration_html'  => htmlspecialchars($liturgicalDay, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'first_reading'     => $firstReading,
            'psalm'             => $psalm,
            'second_reading'    => $secondReading,
            'gospel_acclamation'=> $gospelAcclam,
            'gospel'            => $gospel,
            'copyright'         => 'Catholic Gallery — Tamil Mass Readings',
            'copyright_html'    => '<a href="https://www.catholicgallery.org/tamil-mass-readings-today/" target="_blank" rel="noopener">Catholic Gallery</a> — Tamil Mass Readings',
            'success'           => true,
            'error'             => null,
        ];
    }

    // -------------------------------------------------------------------------
    // DOM extraction helpers
    // -------------------------------------------------------------------------

    private function loadDom(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // Wrap in UTF-8 charset declaration so Tamil chars parse correctly
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' .
            '<html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        return $dom;
    }

    private function extractLiturgicalDay(DOMXPath $xpath): string
    {
        $nodes = $xpath->query('//h2[contains(@class,"dayTitle")]');
        if ($nodes === false || $nodes->length === 0) {
            return '';
        }
        return trim($nodes->item(0)->textContent ?? '');
    }

    /**
     * Extract a standard reading block (First Reading / Gospel / Second Reading).
     *
     * @return array<string, string>|null
     */
    private function extractReading(DOMXPath $xpath, string $id): ?array
    {
        $nodes = $xpath->query('//div[@id="' . $id . '"]');
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        /** @var DOMElement $block */
        $block = $nodes->item(0);

        $title   = $this->queryText($xpath, './/p[contains(@class,"readingsTitle")]', $block);
        $source  = $this->queryText($xpath, './/p[not(@class) or @class="" or contains(@class,"clrgreen") or contains(@class,"readingIntro")]', $block);
        $intro   = $this->queryText($xpath, './/p[@class="readingIntro"]', $block);
        $texts   = $this->queryAllText($xpath, './/p[@class="readingTxt"]', $block);

        if (empty($texts)) {
            return null;
        }

        // Build source string: prefer explicit <p class="readingIntro"> or the first <p> with a reference
        $sourceStr = $this->extractSourceReference($xpath, $block);

        $htmlText = '';
        foreach ($texts as $t) {
            $htmlText .= '<p>' . htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
        }

        return [
            'heading'    => $title,
            'source'     => $sourceStr,
            'text'       => $htmlText,
            'text_plain' => implode("\n\n", $texts),
        ];
    }

    /**
     * Extract the Responsorial Psalm, which has a different inner structure.
     *
     * @return array<string, string>|null
     */
    private function extractPsalm(DOMXPath $xpath, string $id): ?array
    {
        $nodes = $xpath->query('//div[@id="' . $id . '"]');
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        /** @var DOMElement $block */
        $block = $nodes->item(0);

        $title     = $this->queryText($xpath, './/p[contains(@class,"readingsTitle")]', $block);
        $sourceStr = $this->extractSourceReference($xpath, $block);

        // Psalm text is in <div class="psalmText"> containing <span> elements
        $psalmDivs = $xpath->query('.//div[@class="psalmText"]', $block);
        $psalmText = '';
        $plainParts = [];

        if ($psalmDivs !== false && $psalmDivs->length > 0) {
            foreach ($psalmDivs as $psalmDiv) {
                $verseText = trim($psalmDiv->textContent ?? '');
                if ($verseText !== '') {
                    $psalmText  .= '<p>' . htmlspecialchars($verseText, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
                    $plainParts[] = $verseText;
                }
            }
        } else {
            // Fallback: plain paragraphs
            $texts = $this->queryAllText($xpath, './/p[@class="readingTxt"]', $block);
            foreach ($texts as $t) {
                $psalmText  .= '<p>' . htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
                $plainParts[] = $t;
            }
        }

        if ($psalmText === '') {
            return null;
        }

        // Refrain (பல்லவி)
        $refrain = '';
        $refrainNodes = $xpath->query('.//p[contains(@class,"italic") or contains(@class,"italics") or contains(@class,"refrain") or contains(@class,"pallavi")]', $block);
        if ($refrainNodes !== false && $refrainNodes->length > 0) {
            $refrain = trim($refrainNodes->item(0)->textContent ?? '');
        }

        if ($refrain !== '') {
            $psalmText = '<p><em>' . htmlspecialchars($refrain, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</em></p>' . $psalmText;
        }

        return [
            'heading'    => $title,
            'source'     => $sourceStr,
            'text'       => $psalmText,
            'text_plain' => implode("\n\n", $plainParts),
        ];
    }

    /**
     * Extract Gospel Acclamation (Alleluia).
     *
     * @return array<string, string>|null
     */
    private function extractGospelAcclamation(DOMXPath $xpath, string $id): ?array
    {
        $nodes = $xpath->query('//div[@id="' . $id . '"]');
        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        /** @var DOMElement $block */
        $block = $nodes->item(0);

        $title     = $this->queryText($xpath, './/p[contains(@class,"readingsTitle")]', $block);
        $sourceStr = $this->extractSourceReference($xpath, $block);
        $texts     = $this->queryAllText($xpath, './/p[@class="alleluiaTxt"]', $block);

        if (empty($texts)) {
            $texts = $this->queryAllText($xpath, './/p[@class="readingTxt"]', $block);
        }

        if (empty($texts)) {
            return null;
        }

        $html = '';
        foreach ($texts as $t) {
            $html .= '<p>' . htmlspecialchars($t, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
        }

        return [
            'heading'    => $title,
            'source'     => $sourceStr,
            'text'       => $html,
            'text_plain' => implode("\n\n", $texts),
        ];
    }

    /**
     * Extract Bible reference from a reading block.
     * Looks for <p> containing only the verse reference (e.g., "ஆமோஸ் நூலிலிருந்து வாசகம் 7: 10-17").
     */
    private function extractSourceReference(DOMXPath $xpath, DOMElement $block): string
    {
        // Priority 1: <p class="readingIntro"> — intro line before verse text
        $intro = $this->queryText($xpath, './/p[@class="readingIntro"]', $block);
        if ($intro !== '') {
            return $intro;
        }

        // Priority 2: <p> containing italicised reference via <span class="clrgreen italics">
        $spans = $xpath->query('.//span[contains(@class,"italics")]', $block);
        if ($spans !== false && $spans->length > 0) {
            $ref = trim($spans->item(0)->textContent ?? '');
            if ($ref !== '') {
                return $ref;
            }
        }

        // Priority 3: <p> not classed that contains a book reference pattern
        $paras = $xpath->query('.//p[not(@class) or @class=""]', $block);
        if ($paras !== false) {
            foreach ($paras as $p) {
                $text = trim($p->textContent ?? '');
                // Match patterns like "7: 10-17" or contains ":"
                if ($text !== '' && (str_contains($text, ':') || str_contains($text, '–') || preg_match('/\d+/', $text))) {
                    return $text;
                }
            }
        }

        return '';
    }

    // -------------------------------------------------------------------------
    // XPath text helpers
    // -------------------------------------------------------------------------

    private function queryText(DOMXPath $xpath, string $query, DOMElement $context): string
    {
        $nodes = $xpath->query($query, $context);
        if ($nodes === false || $nodes->length === 0) {
            return '';
        }
        return trim($nodes->item(0)->textContent ?? '');
    }

    /**
     * @return string[]
     */
    private function queryAllText(DOMXPath $xpath, string $query, DOMElement $context): array
    {
        $nodes = $xpath->query($query, $context);
        if ($nodes === false || $nodes->length === 0) {
            return [];
        }

        $texts = [];
        foreach ($nodes as $node) {
            $text = trim($node->textContent ?? '');
            if ($text !== '') {
                $texts[] = $text;
            }
        }
        return $texts;
    }

    // -------------------------------------------------------------------------
    // Cache helpers
    // -------------------------------------------------------------------------

    private function cachePath(string $dateStr): string
    {
        return self::CACHE_DIR . '/' . $dateStr . '.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readCache(string $dateStr, bool $ignoreExpiry = false): ?array
    {
        $path = $this->cachePath($dateStr);
        if (!file_exists($path)) {
            return null;
        }

        if (!$ignoreExpiry && (time() - filemtime($path)) > self::CACHE_TTL) {
            return null;
        }

        $json = file_get_contents($path);
        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function writeCache(string $dateStr, array $data): void
    {
        $dir = self::CACHE_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $this->cachePath($dateStr);
        file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    // -------------------------------------------------------------------------
    // Error response
    // -------------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    private function unavailableResponse(string $dateStr, DateTimeInterface $date, string $reason = ''): array
    {
        return [
            'provider'       => 'tamil',
            'date'           => $dateStr,
            'formatted_date' => $date->format('l, j F Y'),
            'success'        => false,
            'error'          => 'Tamil readings are not available for this date. ' . ($reason ? "($reason)" : ''),
            'celebration'    => '',
            'first_reading'  => null,
            'psalm'          => null,
            'second_reading' => null,
            'gospel_acclamation' => null,
            'gospel'         => null,
            'copyright'      => '',
            'copyright_html' => '',
        ];
    }
}
