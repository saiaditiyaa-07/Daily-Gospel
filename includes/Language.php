<?php
/**
 * Language helper class for localization.
 */

declare(strict_types=1);

class Language
{
    private static ?array $translations = null;
    private static ?string $currentLang = null;

    /**
     * Initialize language based on priority:
     * 1. GET query ?lang=
     * 2. Session lang
     * 3. Cookie lang
     * 4. DB setting 'default_language'
     * 5. Default 'en'
     */
    public static function init(): void
    {
        if (self::$currentLang !== null) {
            return;
        }

        start_app_session();

        $lang = null;

        // 1. GET query
        if (!empty($_GET['lang']) && in_array($_GET['lang'], ['en', 'ta'], true)) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
            setcookie('lang', $lang, [
                'expires' => time() + (365 * 24 * 60 * 60),
                'path' => '/',
                'httponly' => false, // accessible to JS
                'samesite' => 'Lax',
            ]);
        }

        // 2. Session
        if ($lang === null && !empty($_SESSION['lang']) && in_array($_SESSION['lang'], ['en', 'ta'], true)) {
            $lang = $_SESSION['lang'];
        }

        // 3. Cookie
        if ($lang === null && !empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['en', 'ta'], true)) {
            $lang = $_COOKIE['lang'];
            $_SESSION['lang'] = $lang;
        }

        // 4. DB setting
        if ($lang === null) {
            $lang = get_setting('default_language', 'en');
            if (!in_array($lang, ['en', 'ta'], true)) {
                $lang = 'en';
            }
        }

        self::$currentLang = $lang;

        // Load translation file
        $filePath = APP_ROOT . '/lang/' . $lang . '.php';
        if (file_exists($filePath)) {
            self::$translations = require $filePath;
        } else {
            self::$translations = [];
        }
    }

    /**
     * Get the current language code ('en' or 'ta').
     */
    public static function get(): string
    {
        if (self::$currentLang === null) {
            self::init();
        }
        return self::$currentLang;
    }

    /**
     * Translate a key, with optional fallbacks/placeholders.
     */
    public static function translate(string $key, array $placeholders = []): string
    {
        if (self::$translations === null) {
            self::init();
        }

        $translation = self::$translations[$key] ?? $key;

        if (!empty($placeholders)) {
            foreach ($placeholders as $k => $v) {
                $translation = str_replace('{' . $k . '}', (string) $v, $translation);
            }
        }

        return $translation;
    }
}

/**
 * Global helper function for translating keys.
 */
function __(string $key, array $placeholders = []): string
{
    return Language::translate($key, $placeholders);
}

/**
 * Global helper function to output translations directly.
 */
function _e(string $key, array $placeholders = []): void
{
    echo e(__($key, $placeholders));
}
