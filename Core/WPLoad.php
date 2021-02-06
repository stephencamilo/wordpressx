<?php
namespace Core;

class WPLoad
{
	const ABSPATH =  __DIR__ . '/../';

	function __construct()
	{
		error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);

		if (file_exists(self::ABSPATH . 'wp-config.php')) {
			require_once self::ABSPATH . 'wp-config.php';
		} elseif (@file_exists(dirname(self::ABSPATH) . '/wp-config.php') && !@file_exists(dirname(self::ABSPATH) . '/wp-settings.php')) {
			require_once dirname(self::ABSPATH) . '/wp-config.php';
		} else {
			define('WPINC', 'wp-includes');
			$Load = new \Core\WPIncludes\Load;
			$Load->wp_fix_server_vars();
			$Functions = new \Core\WPIncludes\Functions;
			$path = $Functions->wp_guess_url() . '/wp-admin/setup-config.php';
			if (false === strpos($_SERVER['REQUEST_URI'], 'setup-config')) {
				header('Location: ' . $path);
				exit;
			}

			define('WP_CONTENT_DIR', self::ABSPATH . 'wp-content');

			require_once self::ABSPATH . WPINC . '/version.php';

			wp_check_php_mysql_versions();

			wp_load_translations_early();

			$die = sprintf(
				__("There doesn't seem to be a %s file. I need this before we can get started."),
				'<code>wp-config.php</code>'
			) . '</p>';
			$die .= '<p>' . sprintf(
				__("Need more help? <a href='%s'>We got it</a>."),
				__('https://wordpress.org/support/article/editing-wp-config-php/')
			) . '</p>';
			$die .= '<p>' . sprintf(
				__("You can create a %s file through a web interface, but this doesn't work for all server setups. The safest way is to manually create the file."),
				'<code>wp-config.php</code>'
			) . '</p>';
			$die .= '<p><a href="' . $path . '" class="button button-large">' . __('Create a Configuration File') . '</a>';
			wp_die($die, __('WordPress &rsaquo; Error'));
		}
	}
}
