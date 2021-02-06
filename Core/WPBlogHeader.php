<?php
namespace Core;

class WPBlogHeader
{
	const WP_USE_THEMES = true;

	function __construct()
	{
		if (!isset($wp_did_header)) {
			$wp_did_header = true;
			new WPLoad;
			wp();
			require_once ABSPATH . WPINC . '/template-loader.php';
		}
	}
}
