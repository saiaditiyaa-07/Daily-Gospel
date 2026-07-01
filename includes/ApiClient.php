<?php
/**
 * HTTP client for external API requests.
 */

declare(strict_types=1);

class ApiClient
{
    private int $timeout;
    private int $connectTimeout;

    public function __construct(
        int $timeout = API_TIMEOUT,
        int $connectTimeout = API_CONNECT_TIMEOUT
    ) {
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * Perform a GET request and return the response body.
     *
     * @throws RuntimeException When the request fails
     */
    public function get(string $url, array $headers = []): string
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL extension is required.');
        }

        $ch = curl_init($url);

        $defaultHeaders = [
            'Accept: application/json, text/javascript, */*',
            'User-Agent: DailyGospel/1.0 (+https://dailygospel.local)',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_ENCODING => '',
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('API request failed: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new RuntimeException('API returned HTTP ' . $httpCode);
        }

        return $response;
    }

    /**
     * Perform a GET request and decode JSON response.
     *
     * @return array<string, mixed>
     */
    public function getJson(string $url, array $headers = []): array
    {
        $body = $this->get($url, $headers);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON response from API.');
        }

        return $data;
    }
}
