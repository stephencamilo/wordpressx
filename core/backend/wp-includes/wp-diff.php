<?php
/**
 * WordPress Diff bastard child of old MediaWiki Diff Formatter.
 *
 * Basically all that remains is the table structure and some method names.
 *
 * @package WordPress
 * @subpackage Diff
 */

if ( ! class_exists( 'Text_Diff', false ) ) {
	/** Text_Diff class */
	require ABSPATH_BACKEND . WPINC . '/Text/Diff.php';
	/** Text_Diff_Renderer class */
	require ABSPATH_BACKEND . WPINC . '/Text/Diff/Renderer.php';
	/** Text_Diff_Renderer_inline class */
	require ABSPATH_BACKEND . WPINC . '/Text/Diff/Renderer/inline.php';
}

require ABSPATH_BACKEND . WPINC . '/class-wp-text-diff-renderer-table.php';
require ABSPATH_BACKEND . WPINC . '/class-wp-text-diff-renderer-inline.php';
