<?php

namespace Core;

class WPSettings
{

	static function __constructStatic()
	{
		define('WPINC', 'wp-includes');

		global $wp_version, $wp_db_version, $tinymce_version, $required_php_version, $required_mysql_version, $wp_local_package;
		require_once ABSPATH . WPINC . '/version.php';
		require_once ABSPATH . WPINC . '/load.php';

		wp_check_php_mysql_versions();

		require_once ABSPATH . WPINC . '/class-wp-paused-extensions-storage.php';
		require_once ABSPATH . WPINC . '/class-wp-fatal-error-handler.php';
		require_once ABSPATH . WPINC . '/class-wp-recovery-mode-cookie-service.php';
		require_once ABSPATH . WPINC . '/class-wp-recovery-mode-key-service.php';
		require_once ABSPATH . WPINC . '/class-wp-recovery-mode-link-service.php';
		require_once ABSPATH . WPINC . '/class-wp-recovery-mode-email-service.php';
		require_once ABSPATH . WPINC . '/class-wp-recovery-mode.php';
		require_once ABSPATH . WPINC . '/error-protection.php';
		require_once ABSPATH . WPINC . '/default-constants.php';
		require_once ABSPATH . WPINC . '/plugin.php';

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

		require_once ABSPATH . WPINC . '/compat.php';
		require_once ABSPATH . WPINC . '/class-wp-list-util.php';
		require_once ABSPATH . WPINC . '/formatting.php';
		require_once ABSPATH . WPINC . '/meta.php';
		require_once ABSPATH . WPINC . '/functions.php';
		require_once ABSPATH . WPINC . '/class-wp-meta-query.php';
		require_once ABSPATH . WPINC . '/class-wp-matchesmapregex.php';
		require_once ABSPATH . WPINC . '/class-wp.php';
		require_once ABSPATH . WPINC . '/class-wp-error.php';
		require_once ABSPATH . WPINC . '/pomo/mo.php';

		global $wpdb;

		require_wp_db();

		if (isset($table_prefix)) {
			$GLOBALS['table_prefix'] = $table_prefix;
		} else {
			$GLOBALS['table_prefix'] = 'wp_';
		}
		wp_set_wpdb_vars();

		wp_start_object_cache();

		require_once ABSPATH . WPINC . '/default-filters.php';

		if (is_multisite()) {
			require_once ABSPATH . WPINC . '/class-wp-site-query.php';
			require_once ABSPATH . WPINC . '/class-wp-network-query.php';
			require_once ABSPATH . WPINC . '/ms-blogs.php';
			require_once ABSPATH . WPINC . '/ms-settings.php';
		} elseif (!defined('MULTISITE')) {
			define('MULTISITE', false);
		}

		register_shutdown_function('shutdown_action_hook');

		if (SHORTINIT) {
			return false;
		}

		require_once ABSPATH . WPINC . '/l10n.php';
		require_once ABSPATH . WPINC . '/class-wp-locale.php';
		require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';

		wp_not_installed();

		require_once ABSPATH . WPINC . '/class-wp-walker.php';
		require_once ABSPATH . WPINC . '/class-wp-ajax-response.php';
		require_once ABSPATH . WPINC . '/capabilities.php';
		require_once ABSPATH . WPINC . '/class-wp-roles.php';
		require_once ABSPATH . WPINC . '/class-wp-role.php';
		require_once ABSPATH . WPINC . '/class-wp-user.php';
		require_once ABSPATH . WPINC . '/class-wp-query.php';
		require_once ABSPATH . WPINC . '/query.php';
		require_once ABSPATH . WPINC . '/class-wp-date-query.php';
		require_once ABSPATH . WPINC . '/theme.php';
		require_once ABSPATH . WPINC . '/class-wp-theme.php';
		require_once ABSPATH . WPINC . '/template.php';
		require_once ABSPATH . WPINC . '/class-wp-user-request.php';
		require_once ABSPATH . WPINC . '/user.php';
		require_once ABSPATH . WPINC . '/class-wp-user-query.php';
		require_once ABSPATH . WPINC . '/class-wp-session-tokens.php';
		require_once ABSPATH . WPINC . '/class-wp-user-meta-session-tokens.php';
		require_once ABSPATH . WPINC . '/class-wp-metadata-lazyloader.php';
		require_once ABSPATH . WPINC . '/general-template.php';
		require_once ABSPATH . WPINC . '/link-template.php';
		require_once ABSPATH . WPINC . '/author-template.php';
		require_once ABSPATH . WPINC . '/post.php';
		require_once ABSPATH . WPINC . '/class-walker-page.php';
		require_once ABSPATH . WPINC . '/class-walker-page-dropdown.php';
		require_once ABSPATH . WPINC . '/class-wp-post-type.php';
		require_once ABSPATH . WPINC . '/class-wp-post.php';
		require_once ABSPATH . WPINC . '/post-template.php';
		require_once ABSPATH . WPINC . '/revision.php';
		require_once ABSPATH . WPINC . '/post-formats.php';
		require_once ABSPATH . WPINC . '/post-thumbnail-template.php';
		require_once ABSPATH . WPINC . '/category.php';
		require_once ABSPATH . WPINC . '/class-walker-category.php';
		require_once ABSPATH . WPINC . '/class-walker-category-dropdown.php';
		require_once ABSPATH . WPINC . '/category-template.php';
		require_once ABSPATH . WPINC . '/comment.php';
		require_once ABSPATH . WPINC . '/class-wp-comment.php';
		require_once ABSPATH . WPINC . '/class-wp-comment-query.php';
		require_once ABSPATH . WPINC . '/class-walker-comment.php';
		require_once ABSPATH . WPINC . '/comment-template.php';
		require_once ABSPATH . WPINC . '/rewrite.php';
		require_once ABSPATH . WPINC . '/class-wp-rewrite.php';
		require_once ABSPATH . WPINC . '/feed.php';
		require_once ABSPATH . WPINC . '/bookmark.php';
		require_once ABSPATH . WPINC . '/bookmark-template.php';
		require_once ABSPATH . WPINC . '/kses.php';
		require_once ABSPATH . WPINC . '/cron.php';
		require_once ABSPATH . WPINC . '/deprecated.php';
		require_once ABSPATH . WPINC . '/script-loader.php';
		require_once ABSPATH . WPINC . '/taxonomy.php';
		require_once ABSPATH . WPINC . '/class-wp-taxonomy.php';
		require_once ABSPATH . WPINC . '/class-wp-term.php';
		require_once ABSPATH . WPINC . '/class-wp-term-query.php';
		require_once ABSPATH . WPINC . '/class-wp-tax-query.php';
		require_once ABSPATH . WPINC . '/update.php';
		require_once ABSPATH . WPINC . '/canonical.php';
		require_once ABSPATH . WPINC . '/shortcodes.php';
		require_once ABSPATH . WPINC . '/embed.php';
		require_once ABSPATH . WPINC . '/class-wp-embed.php';
		require_once ABSPATH . WPINC . '/class-wp-oembed.php';
		require_once ABSPATH . WPINC . '/class-wp-oembed-controller.php';
		require_once ABSPATH . WPINC . '/media.php';
		require_once ABSPATH . WPINC . '/http.php';
		require_once ABSPATH . WPINC . '/class-http.php';
		require_once ABSPATH . WPINC . '/class-wp-http-streams.php';
		require_once ABSPATH . WPINC . '/class-wp-http-curl.php';
		require_once ABSPATH . WPINC . '/class-wp-http-proxy.php';
		require_once ABSPATH . WPINC . '/class-wp-http-cookie.php';
		require_once ABSPATH . WPINC . '/class-wp-http-encoding.php';
		require_once ABSPATH . WPINC . '/class-wp-http-response.php';
		require_once ABSPATH . WPINC . '/class-wp-http-requests-response.php';
		require_once ABSPATH . WPINC . '/class-wp-http-requests-hooks.php';
		require_once ABSPATH . WPINC . '/widgets.php';
		require_once ABSPATH . WPINC . '/class-wp-widget.php';
		require_once ABSPATH . WPINC . '/class-wp-widget-factory.php';
		require_once ABSPATH . WPINC . '/nav-menu.php';
		require_once ABSPATH . WPINC . '/nav-menu-template.php';
		require_once ABSPATH . WPINC . '/admin-bar.php';
		require_once ABSPATH . WPINC . '/class-wp-application-passwords.php';
		require_once ABSPATH . WPINC . '/rest-api.php';
		require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php';
		require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php';
		require_once ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-posts-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-attachments-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-types-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-post-statuses-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-revisions-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-autosaves-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-taxonomies-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-terms-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-users-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-comments-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-search-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-blocks-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-types-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-renderer-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-settings-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-themes-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-plugins-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-block-directory-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-application-passwords-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/endpoints/class-wp-rest-site-health-controller.php';
		require_once ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-meta-fields.php';
		require_once ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-comment-meta-fields.php';
		require_once ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-post-meta-fields.php';
		require_once ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-term-meta-fields.php';
		require_once ABSPATH . WPINC . '/rest-api/fields/class-wp-rest-user-meta-fields.php';
		require_once ABSPATH . WPINC . '/rest-api/search/class-wp-rest-search-handler.php';
		require_once ABSPATH . WPINC . '/rest-api/search/class-wp-rest-post-search-handler.php';
		require_once ABSPATH . WPINC . '/rest-api/search/class-wp-rest-term-search-handler.php';
		require_once ABSPATH . WPINC . '/rest-api/search/class-wp-rest-post-format-search-handler.php';
		require_once ABSPATH . WPINC . '/sitemaps.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-index.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-provider.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-registry.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-renderer.php';
		require_once ABSPATH . WPINC . '/sitemaps/class-wp-sitemaps-stylesheet.php';
		require_once ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-posts.php';
		require_once ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-taxonomies.php';
		require_once ABSPATH . WPINC . '/sitemaps/providers/class-wp-sitemaps-users.php';
		require_once ABSPATH . WPINC . '/class-wp-block-type.php';
		require_once ABSPATH . WPINC . '/class-wp-block-pattern-categories-registry.php';
		require_once ABSPATH . WPINC . '/class-wp-block-patterns-registry.php';
		require_once ABSPATH . WPINC . '/class-wp-block-styles-registry.php';
		require_once ABSPATH . WPINC . '/class-wp-block-type-registry.php';
		require_once ABSPATH . WPINC . '/class-wp-block.php';
		require_once ABSPATH . WPINC . '/class-wp-block-list.php';
		require_once ABSPATH . WPINC . '/class-wp-block-parser.php';
		require_once ABSPATH . WPINC . '/blocks.php';
		require_once ABSPATH . WPINC . '/blocks/index.php';
		require_once ABSPATH . WPINC . '/block-patterns.php';
		require_once ABSPATH . WPINC . '/class-wp-block-supports.php';
		require_once ABSPATH . WPINC . '/block-supports/align.php';
		require_once ABSPATH . WPINC . '/block-supports/colors.php';
		require_once ABSPATH . WPINC . '/block-supports/custom-classname.php';
		require_once ABSPATH . WPINC . '/block-supports/generated-classname.php';
		require_once ABSPATH . WPINC . '/block-supports/typography.php';

		$GLOBALS['wp_embed'] = new \Core\WPIncludes\WP_Embed();

		if (is_multisite()) {
			require_once ABSPATH . WPINC . '/ms-functions.php';
			require_once ABSPATH . WPINC . '/ms-default-filters.php';
			require_once ABSPATH . WPINC . '/ms-deprecated.php';
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

		require_once ABSPATH . WPINC . '/vars.php';

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

		require_once ABSPATH . WPINC . '/pluggable.php';
		require_once ABSPATH . WPINC . '/pluggable-deprecated.php';

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