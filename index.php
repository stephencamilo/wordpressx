<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . $_SERVER["REQUEST_URI"] );
}

if ( ! defined( 'ABSPATH_CORE' ) ) {
	define( 'ABSPATH_CORE', dirname( __DIR__ ) . $_SERVER["REQUEST_URI"] . 'core/backend/' );
}

/** Loads the WordPress Environment and Template */
require __DIR__ . '/core/backend/wp-blog-header.php';
