<?php
namespace Core;
class WPSettings
{
	static function __constructStatic()
	{
		define('WPINC', 'wp-includes');
		define('ABSPATH', '');
		global $wp_version, $wp_db_version, $tinymce_version, $required_php_version, $required_mysql_version, $wp_local_package;
		wp_check_php_mysql_versions();
		global $blog_id;
		wp_initial_constants();
		wp_register_fatal_error_handler();
		date_default_timezone_set('UTC');
		wp_fix_server_vars();
		wp_maintenance();
		timer_start();
		wp_debug_mode();
		if (WP_CACHE && apply_filters('enable_loading_advanced_cache_dropin', true) && file_exists(WP_CONTENT_DIR . '/advanced-cache.php')) {
			include WP_CONTENT_DIR . '/advanced-cache.php';
			if ($wp_filter) {
				$wp_filter = WP_Hook::build_preinitialized_hooks($wp_filter);
			}
		}
		wp_set_lang_dir();
		global $wpdb;
		require_wp_db();
		if (isset($table_prefix)) {
			$GLOBALS['table_prefix'] = $table_prefix;
		} else {
			$GLOBALS['table_prefix'] = 'wp_';
		}
		wp_set_wpdb_vars();
		wp_start_object_cache();
		if (is_multisite()) {
		} elseif (!defined('MULTISITE')) {
			define('MULTISITE', false);
		}
		register_shutdown_function('shutdown_action_hook');
		if (SHORTINIT) {
			return false;
		}
		wp_not_installed();
		$GLOBALS['wp_embed'] = new \Core\WPIncludes\WP_Embed();
		if (is_multisite()) {
		}
		wp_plugin_directory_constants();
		$GLOBALS['wp_plugin_paths'] = array();
		foreach (wp_get_mu_plugins() as $mu_plugin) {
			include_once $mu_plugin;
			do_action('mu_plugin_loaded', $mu_plugin);
		}
		unset($mu_plugin);
		if (is_multisite()) {
			foreach (wp_get_active_network_plugins() as $network_plugin) {
				wp_register_plugin_realpath($network_plugin);
				include_once $network_plugin;
				do_action('network_plugin_loaded', $network_plugin);
			}
			unset($network_plugin);
		}
		do_action('muplugins_loaded');
		if (is_multisite()) {
			ms_cookie_constants();
		}
		wp_cookie_constants();
		wp_ssl_constants();
		create_initial_taxonomies();
		create_initial_post_types();
		wp_start_scraping_edited_file_errors();
		register_theme_directory(get_theme_root());
		if (!is_multisite()) {
			wp_recovery_mode()->initialize();
		}
		foreach (wp_get_active_and_valid_plugins() as $plugin) {
			wp_register_plugin_realpath($plugin);
			include_once $plugin;
			do_action('plugin_loaded', $plugin);
		}
		unset($plugin);
		wp_set_internal_encoding();
		if (WP_CACHE && function_exists('wp_cache_postload')) {
			wp_cache_postload();
		}
		do_action('plugins_loaded');
		wp_functionality_constants();
		wp_magic_quotes();
		do_action('sanitize_comment_cookies');
		$GLOBALS['wp_the_query'] = new \Core\WPIncludes\WP_Query();
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
		$GLOBALS['wp_rewrite'] = new \Core\WPIncludes\WP_Rewrite();
		$GLOBALS['wp'] = new \Core\WPIncludes\WP();
		$GLOBALS['wp_widget_factory'] = new \Core\WPIncludes\WP_Widget_Factory();
		$GLOBALS['wp_roles'] = new \Core\WPIncludes\WP_Roles();
		do_action('setup_theme');
		wp_templating_constants();
		load_default_textdomain();
		$locale      = get_locale();
		$locale_file = WP_LANG_DIR . "/$locale.php";
		if ((0 === validate_file($locale)) && is_readable($locale_file)) {
			require_once $locale_file;
		}
		unset($locale_file);
		$GLOBALS['wp_locale'] = new \Core\WPIncludes\WP_Locale();
		$GLOBALS['wp_locale_switcher'] = new \Core\WPIncludes\WP_Locale_Switcher();
		$GLOBALS['wp_locale_switcher']->init();
		foreach (wp_get_active_and_valid_themes() as $theme) {
			if (file_exists($theme . '/functions.php')) {
				include $theme . '/functions.php';
			}
		}
		unset($theme);
		do_action('after_setup_theme');
		if (!class_exists('WP_Site_Health')) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}
		\Core\WPadmin\Includes\WP_Site_Health::get_instance();
		$GLOBALS['wp']->init();
		do_action('init');
		if (is_multisite()) {
			$file = ms_site_check();
			if (true !== $file) {
				require_once $file;
				die();
			}
			unset($file);
		}
		do_action('wp_loaded');
	}
}