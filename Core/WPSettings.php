<?php
namespace Core;

class WPSettings
{
    function __construct()
    {
        $default_contants = new \Core\WPIncludes\DefaultConstants();
        $load = new \Core\WPIncludes\Load;

        $load->wp_check_php_mysql_versions();

        global $blog_id;

        $default_contants->wp_initial_constants();

        wp_register_fatal_error_handler();

        date_default_timezone_set('UTC');

        wp_fix_server_vars();

        wp_maintenance();

        timer_start();

        wp_debug_mode();


                $wp_filter = WP_Hook::build_preinitialized_hooks($wp_filter);

        wp_set_lang_dir();



        global $wpdb;
        require_wp_db();

        $GLOBALS['table_prefix'] = $table_prefix;
        wp_set_wpdb_vars();

        wp_start_object_cache();

        require ABSPATH . WPINC . '/default-filters.php';

        if (!is_multisite()) {
            define('MULTISITE', false);
        }

        register_shutdown_function('shutdown_action_hook');

        if (SHORTINIT) {
            return false;
        }



        wp_not_installed();


        $GLOBALS['wp_embed'] = new WP_Embed();


        wp_plugin_directory_constants();

        $GLOBALS['wp_plugin_paths'] = array();

        foreach (wp_get_mu_plugins() as $mu_plugin) {

            do_action('mu_plugin_loaded', $mu_plugin);
        }
        unset($mu_plugin);

        if (is_multisite()) {
            foreach (wp_get_active_network_plugins() as $network_plugin) {
                wp_register_plugin_realpath($network_plugin);

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

        require ABSPATH . WPINC . '/vars.php';

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

        $GLOBALS['wp_the_query'] = new WP_Query();

        $GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];

        $GLOBALS['wp_rewrite'] = new WP_Rewrite();

        $GLOBALS['wp'] = new WP();

        $GLOBALS['wp_widget_factory'] = new WP_Widget_Factory();

        $GLOBALS['wp_roles'] = new WP_Roles();

        do_action('setup_theme');

        wp_templating_constants();

        load_default_textdomain();

        $locale = get_locale();
        $locale_file = WP_LANG_DIR . "/$locale.php";
        if ((0 === validate_file($locale)) && is_readable($locale_file)) {
            require $locale_file;
        }
        unset($locale_file);

        $GLOBALS['wp_locale'] = new WP_Locale();

        $GLOBALS['wp_locale_switcher'] = new WP_Locale_Switcher();
        $GLOBALS['wp_locale_switcher']->init();

        unset($theme);

        do_action('after_setup_theme');

        WP_Site_Health::get_instance();

        $GLOBALS['wp']->init();

        do_action('init');

// Check site status.
        if (is_multisite()) {
            $file = ms_site_check();
            if (true !== $file) {
                require $file;
                die();
            }
            unset($file);
        }

        do_action('wp_loaded');
    }
}