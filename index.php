<?php

define('WP_USE_THEMES', true);

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH_CORE')) {
    define('ABSPATH_CORE', dirname(__DIR__) . '/core/');
}

require ABSPATH_CORE . '/wp-blog-header.php';
