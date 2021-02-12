<?php
namespace Core\WPIncludes;
class ErrorProtection
{
	static function wp_paused_plugins()
	{
		static $storage = null;
		if (null === $storage) {
			$storage = new WP_Paused_Extensions_Storage('plugin');
		}
		return $storage;
	}
	static function wp_paused_themes()
	{
		static $storage = null;
		if (null === $storage) {
			$storage = new WP_Paused_Extensions_Storage('theme');
		}
		return $storage;
	}
	static function wp_get_extension_error_description($error)
	{
		$constants   = get_defined_constants(true);
		$constants   = isset($constants['Core']) ? $constants['Core'] : $constants['internal'];
		$core_errors = array();
		foreach ($constants as $constant => $value) {
			if (0 === strpos($constant, 'E_')) {
				$core_errors[$value] = $constant;
			}
		}
		if (isset($core_errors[$error['type']])) {
			$error['type'] = $core_errors[$error['type']];
		}
		$error_message = __('An error of type %1$s was caused in line %2$s of the file %3$s. Error message: %4$s');
		return sprintf(
			$error_message,
			"<code>{$error['type']}</code>",
			"<code>{$error['line']}</code>",
			"<code>{$error['file']}</code>",
			"<code>{$error['message']}</code>"
		);
	}
	static function wp_register_fatal_error_handler()
	{
		if (!self::wp_is_fatal_error_handler_enabled()) {
			return;
		}
		$handler = null;
		if (defined('WP_CONTENT_DIR') && is_readable(WP_CONTENT_DIR . '/fatal-error-handler.php')) {
			$handler = include WP_CONTENT_DIR . '/fatal-error-handler.php';
		}
		if (!is_object($handler) || !is_callable(array($handler, 'handle'))) {
			$handler = new WP_Fatal_Error_Handler();
		}
		register_shutdown_function(array($handler, 'handle'));
	}
	static function wp_is_fatal_error_handler_enabled()
	{
		$enabled = !defined('WP_DISABLE_FATAL_ERROR_HANDLER') || !WP_DISABLE_FATAL_ERROR_HANDLER;
		return Plugin::apply_filters('wp_fatal_error_handler_enabled', $enabled);
	}
	static function wp_recovery_mode()
	{
		static $wp_recovery_mode;
		if (!$wp_recovery_mode) {
			$wp_recovery_mode = new WP_Recovery_Mode();
		}
		return $wp_recovery_mode;
	}
}
