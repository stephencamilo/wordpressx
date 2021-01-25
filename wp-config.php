<?php

/**
 #ddev-generated: Automatically generated WordPress settings file.
 ddev manages this file and may delete or overwrite the file unless this comment is removed.
 */

/** Authentication Unique Keys and Salts. */
define('AUTH_KEY', 'cFTJeKvPxZgZjDVQChCQJKvafuMEuCbLgEpaUKlCJgQnWqmHdbPSyJZBDoPPnEBE');
define('SECURE_AUTH_KEY', 'TTDKwODmyertabludsZqyDNWitFowgKAoLkmnItJiOQhgntCKytwWCcOGAbyTrcu');
define('LOGGED_IN_KEY', 'orBTRjqHvmuCnJGDoPuJuVqCIfcgAwYggOlhRoZlqchqULLMZcpttaBuYjxuOkhS');
define('NONCE_KEY', 'ydsZjBMtTEmoiYvRGvWIeDVRvwkdNulPYCyxJtXSKMvgLGtIwWLkEBbjjJTkLWig');
define('AUTH_SALT', 'uoVOeIhKSWrHsVVzViiAWcXFfDmSVDTThpxLDnuIgjPPrEgkHXhBqcOYKQuMptpe');
define('SECURE_AUTH_SALT', 'tMNsJMemNHSwZcOhtllDrBezxzgKVhwmbcaTfWMwxDYSrUOdXocYAYMAMeLMxZDc');
define('LOGGED_IN_SALT', 'sWdubgrhbWgwzKCcMItUFFQkfCYVjQbeAhNgWqJXWNajDNShsLGiUOWLHQsJwHmV');
define('NONCE_SALT', 'vwqFwGteMTdpnYawYbrzAToPcYpbicMcTvsSVnfZixrCXaGxbxLMrEtQETuwlmkL');

/** Absolute path to the WordPress directory. */
define('ABSPATH', dirname(__FILE__) . '/');

// Include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/wp-config-ddev.php';
if (is_readable($ddev_settings) && !defined('DB_USER') && getenv('IS_DDEV_PROJECT') == 'true') {
    require_once($ddev_settings);
}

/** Include wp-settings.php */
if (file_exists(ABSPATH . '/wp-settings.php')) {
    require_once ABSPATH . '/wp-settings.php';
}
