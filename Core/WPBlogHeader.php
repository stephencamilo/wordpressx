<?php

namespace Core;

class WPBlogHeader
{
	const WP_USE_THEMES = true;
	static public $wp_did_header;

	static function __constructStatic()
	{
		$klein = new \Klein\Klein();

		$klein->respond('GET', '/', function () {
			if (is_null(self::$wp_did_header)) {
				self::$wp_did_header = true;
				WPLoad::__constructStatic();
				wp();
				require_once ABSPATH . WPINC . '/template-loader.php';
			}
		});

		$klein->respond('GET', '/wp-admin/setup-config', function () {
			WPAdmin\SetupConfig::__constructStatic();
		});
		$klein->respond('POST', '/wp-admin/setup-config', function () {
			WPAdmin\SetupConfig::__constructStatic();
		});

		$klein->dispatch();
	}
}
