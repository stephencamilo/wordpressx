<?php
require_once __DIR__ . '/vendor/autoload.php';

use Core\WPIncludes;

$load = new Load;

$load->wp_check_php_mysql_versions();

require ABSPATH . WPINC . '/class-wp-paused-extensions-storage.php';
require ABSPATH . WPINC . '/class-wp-fatal-error-handler.php';
require ABSPATH . WPINC . '/class-wp-recovery-mode-cookie-service.php';
require ABSPATH . WPINC . '/class-wp-recovery-mode-key-service.php';
require ABSPATH . WPINC . '/class-wp-recovery-mode-link-service.php';
require ABSPATH . WPINC . '/class-wp-recovery-mode-email-service.php';
require ABSPATH . WPINC . '/class-wp-recovery-mode.php';
require ABSPATH . WPINC . '/error-protection.php';
require ABSPATH . WPINC . '/default-constants.php';
require_once ABSPATH . WPINC . '/plugin.php';

global $blog_id;

wp_initial_constants();

wp_register_fatal_error_handler();

date_default_timezone_set( 'UTC' );

wp_fix_server_vars();

wp_maintenance();

timer_start();

wp_debug_mode();

if ( WP_CACHE && apply_filters( 'enable_loading_advanced_cache_dropin', true ) && file_exists( WP_CONTENT_DIR . '/advanced-cache.php' ) ) {
	include WP_CONTENT_DIR . '/advanced-cache.php';

	if ( $wp_filter ) {
		$wp_filter = WP_Hook::build_preinitialized_hooks( $wp_filter );
	}
}

wp_set_lang_dir();

require ABSPATH . WPINC . '/compat.php';
require ABSPATH . WPINC . '/class-wp-list-util.php';
require ABSPATH . WPINC . '/formatting.php';
require ABSPATH . WPINC . '/meta.php';
require ABSPATH . WPINC . '/functions.php';
require ABSPATH . WPINC . '/class-wp-meta-query.php';
require ABSPATH . WPINC . '/class-wp-matchesmapregex.php';
require ABSPATH . WPINC . '/class-wp.php';
require ABSPATH . WPINC . '/class-wp-error.php';
require ABSPATH . WPINC . '/pomo/mo.php';

global $wpdb;
require_wp_db();

$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();

wp_start_object_cache();

require ABSPATH . WPINC . '/default-filters.php';

if ( is_multisite() ) {
	require ABSPATH . WPINC . '/class-wp-site-query.php';
	require ABSPATH . WPINC . '/class-wp-network-query.php';
	require ABSPATH . WPINC . '/ms-blogs.php';
	require ABSPATH . WPINC . '/ms-settings.php';
} elseif ( ! defined( 'MULTISITE' ) ) {
	define( 'MULTISITE', false );
}

register_shutdown_function( 'shutdown_action_hook' );

if ( SHORTINIT ) {
	return false;
}

require_once ABSPATH . WPINC . '/l10n.php';
require_once ABSPATH . WPINC . '/class-wp-locale.php';
require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';

wp_not_installed();

require ABSPATH . WPINC . '/class-wp-walker.php';
require ABSPATH . WPINC . '/class-wp-ajax-response.php';
require ABSPATH . WPINC . '/capabilities.php';
require ABSPATH . WPINC . '/class-wp-roles.php';
require ABSPATH . WPINC . '/class-wp-role.php';
require ABSPATH . WPINC . '/class-wp-user.php';
require ABSPATH . WPINC . '/class-wp-query.php';
require ABSPATH . WPINC . '/query.php';
require ABSPATH . WPINC . '/class-wp-date-query.php';
require ABSPATH . WPINC . '/theme.php';
require ABSPATH . WPINC . '/class-wp-theme.php';
require ABSPATH . WPINC . '/template.php';
require ABSPATH . WPINC . '/class-wp-user-request.php';
require ABSPATH . WPINC . '/user.php';
require ABSPATH . WPINC . '/class-wp-user-query.php';
require ABSPATH . WPINC . '/class-wp-session-tokens.php';
require ABSPATH . WPINC . '/class-wp-user-meta-session-tokens.php';
require ABSPATH . WPINC . '/class-wp-metadata-lazyloader.php';
require ABSPATH . WPINC . '/general-template.php';
require ABSPATH . WPINC . '/link-template.php';
require ABSPATH . WPINC . '/author-template.php';
require ABSPATH . WPINC . '/post.php';
require ABSPATH . WPINC . '/class-walker-page.php';
require ABSPATH . WPINC . '/class-walker-page-dropdown.php';
require ABSPATH . WPINC . '/class-wp-post-type.php';
require ABSPATH . WPINC . '/class-wp-post.php';
require ABSPATH . WPINC . '/post-template.php';
require ABSPATH . WPINC . '/revision.php';
require ABSPATH . WPINC . '/post-formats.php';
require ABSPATH . WPINC . '/post-thumbnail-template.php';
require ABSPATH . WPINC . '/category.php';
require ABSPATH . WPINC . '/class-walker-category.php';
require ABSPATH . WPINC . '/class-walker-category-dropdown.php';
require ABSPATH . WPINC . '/category-template.php';
require ABSPATH . WPINC . '/comment.php';
require ABSPATH . WPINC . '/class-wp-comment.php';
require ABSPATH . WPINC . '/class-wp-comment-query.php';
require ABSPATH . WPINC . '/class-walker-comment.php';
require ABSPATH . WPINC . '/comment-template.php';
require ABSPATH . WPINC . '/rewrite.php';
require ABSPATH . WPINC . '/class-wp-rewrite.php';
require ABSPATH . WPINC . '/feed.php';
require ABSPATH . WPINC . '/bookmark.php';
require ABSPATH . WPINC . '/bookmark-template.php';
require ABSPATH . WPINC . '/kses.php';
require ABSPATH . WPINC . '/cron.php';
require ABSPATH . WPINC . '/deprecated.php';
require ABSPATH . WPINC . '/script-loader.php';
require ABSPATH . WPINC . '/taxonomy.php';
require ABSPATH . WPINC . '/class-wp-taxonomy.php';
require ABSPATH . WPINC . '/class-wp-term.php';
require ABSPATH . WPINC . '/class-wp-term-query.php';
require ABSPATH . WPINC . '/class-wp-tax-query.php';
require ABSPATH . WPINC . '/update.php';
require ABSPATH . WPINC . '/canonical.php';
require ABSPATH . WPINC . '/shortcodes.php';
require ABSPATH . WPINC . '/embed.php';
require ABSPATH . WPINC . '/class-wp-embed.php';
require ABSPATH . WPINC . '/class-wp-oembed.php';
require ABSPATH . WPINC . '/class-wp-oembed-controller.php';
require ABSPATH . WPINC . '/media.php';
require ABSPATH . WPINC . '/http.php';
require ABSPATH . WPINC . '/class-http.php';
require ABSPATH . WPINC . '/class-wp-http-streams.php';
require ABSPATH . WPINC . '/class-wp-http-curl.php';
require ABSPATH . WPINC . '/class-wp-http-proxy.php';
require ABSPATH . WPINC . '/class-wp-http-cookie.php';
require ABSPATH . WPINC . '/class-wp-http-encoding.php';
require ABSPATH . WPINC . '/class-wp-http-response.php';
require ABSPATH . WPINC . '/class-wp-http-requests-response.php';
require ABSPATH . WPINC . '/class-wp-http-requests-hooks.php';
require ABSPATH . WPINC . '/widgets.php';
require ABSPATH . WPINC . '/class-wp-widget.php';
require ABSPATH . WPINC . '/class-wp-widget-factory.php';
require ABSPATH . WPINC . '/nav-menu.php';
require ABSPATH . WPINC . '/nav-menu-template.php';
require ABSPATH . WPINC . '/admin-bar.php';
require ABSPATH . WPINC . '/class-wp-application-passwords.php';
require ABSPATH . WPINC . '/rest-api.php';
require ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php';
require ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php';
require ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-posts-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-attachments-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-types-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-statuses-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-revisions-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-autosaves-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-taxonomies-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-terms-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-users-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-comments-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-search-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-blocks-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-types-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-renderer-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-settings-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-themes-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-plugins-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-directory-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-application-passwords-controller.php';
require ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-site-health-controller.php';
require ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-meta-fields.php';
require ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-comment-meta-fields.php';
require ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-post-meta-fields.php';
require ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-term-meta-fields.php';
require ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-user-meta-fields.php';
require ABSPATH . WPINC . '/rest-api/search/class-wp-rest-search-handler.php';
require ABSPATH . WPINC . '/rest-api/search/class-wp-rest-post-search-handler.php';
require ABSPATH . WPINC . '/rest-api/search/class-wp-rest-term-search-handler.php';
require ABSPATH . WPINC . '/rest-api/search/class-wp-rest-post-format-search-handler.php';
require ABSPATH . WPINC . '/sitemaps.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-index.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-provider.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-registry.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-renderer.php';
require ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-stylesheet.php';
require ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-posts.php';
require ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-taxonomies.php';
require ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-users.php';
require ABSPATH . WPINC . '/class-wp-block-type.php';
require ABSPATH . WPINC . '/class-wp-block-pattern-categories-registry.php';
require ABSPATH . WPINC . '/class-wp-block-patterns-registry.php';
require ABSPATH . WPINC . '/class-wp-block-styles-registry.php';
require ABSPATH . WPINC . '/class-wp-block-type-registry.php';
require ABSPATH . WPINC . '/class-wp-block.php';
require ABSPATH . WPINC . '/class-wp-block-list.php';
require ABSPATH . WPINC . '/class-wp-block-parser.php';
require ABSPATH . WPINC . '/blocks.php';
require ABSPATH . WPINC . '/blocks/index.php';
require ABSPATH . WPINC . '/block-patterns.php';
require ABSPATH . WPINC . '/class-wp-block-supports.php';
require ABSPATH . WPINC . '/block-supports/align.php';
require ABSPATH . WPINC . '/block-supports/colors.php';
require ABSPATH . WPINC . '/block-supports/custom-classname.php';
require ABSPATH . WPINC . '/block-supports/generated-classname.php';
require ABSPATH . WPINC . '/block-supports/typography.php';

$GLOBALS['wp_embed'] = new WP_Embed();

if ( is_multisite() ) {
	require ABSPATH . WPINC . '/ms-functions.php';
	require ABSPATH . WPINC . '/ms-default-filters.php';
	require ABSPATH . WPINC . '/ms-deprecated.php';
}

wp_plugin_directory_constants();

$GLOBALS['wp_plugin_paths'] = array();

foreach ( wp_get_mu_plugins() as $mu_plugin ) {
	include_once $mu_plugin;

	do_action( 'mu_plugin_loaded', $mu_plugin );
}
unset( $mu_plugin );

if ( is_multisite() ) {
	foreach ( wp_get_active_network_plugins() as $network_plugin ) {
		wp_register_plugin_realpath( $network_plugin );
		include_once $network_plugin;

		do_action( 'network_plugin_loaded', $network_plugin );
	}
	unset( $network_plugin );
}

do_action( 'muplugins_loaded' );

if ( is_multisite() ) {
	ms_cookie_constants();
}

wp_cookie_constants();

wp_ssl_constants();

require ABSPATH . WPINC . '/vars.php';

create_initial_taxonomies();
create_initial_post_types();

wp_start_scraping_edited_file_errors();

register_theme_directory( get_theme_root() );

if ( ! is_multisite() ) {
	wp_recovery_mode()->initialize();
}

foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
	wp_register_plugin_realpath( $plugin );
	include_once $plugin;

	do_action( 'plugin_loaded', $plugin );
}
unset( $plugin );

require ABSPATH . WPINC . '/pluggable.php';
require ABSPATH . WPINC . '/pluggable-deprecated.php';

wp_set_internal_encoding();

if ( WP_CACHE && function_exists( 'wp_cache_postload' ) ) {
	wp_cache_postload();
}

do_action( 'plugins_loaded' );

wp_functionality_constants();

wp_magic_quotes();

do_action( 'sanitize_comment_cookies' );

$GLOBALS['wp_the_query'] = new WP_Query();

$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];

$GLOBALS['wp_rewrite'] = new WP_Rewrite();

$GLOBALS['wp'] = new WP();

$GLOBALS['wp_widget_factory'] = new WP_Widget_Factory();

$GLOBALS['wp_roles'] = new WP_Roles();

do_action( 'setup_theme' );

wp_templating_constants();

load_default_textdomain();

$locale      = get_locale();
$locale_file = WP_LANG_DIR . "/$locale.php";
if ( ( 0 === validate_file( $locale ) ) && is_readable( $locale_file ) ) {
	require $locale_file;
}
unset( $locale_file );

$GLOBALS['wp_locale'] = new WP_Locale();

$GLOBALS['wp_locale_switcher'] = new WP_Locale_Switcher();
$GLOBALS['wp_locale_switcher']->init();

// Load the functions for the active theme, for both parent and child theme if applicable.
foreach ( wp_get_active_and_valid_themes() as $theme ) {
	if ( file_exists( $theme . '/functions.php' ) ) {
		include $theme . '/functions.php';
	}
}
unset( $theme );

do_action( 'after_setup_theme' );

if ( ! class_exists( 'WP_Site_Health' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
}
WP_Site_Health::get_instance();

$GLOBALS['wp']->init();

do_action( 'init' );

// Check site status.
if ( is_multisite() ) {
	$file = ms_site_check();
	if ( true !== $file ) {
		require $file;
		die();
	}
	unset( $file );
}

do_action( 'wp_loaded' );
