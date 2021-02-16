<?php
/**
 * Helper functions for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Fetches an instance of a WP_List_Table class.
 *
 * @access private
 *
 * @param string $class The type of the list table, which is the class name.
 * @param array $args Optional. Arguments to pass to the class. Accepts 'screen'.
 *
 * @return WP_List_Table|bool List table object on success, false if the class does not exist.
 * @global string $hook_suffix
 *
 * @since 3.1.0
 *
 */
function _get_list_table( $class, $args = array() ) {
	$core_classes = array(
		// Site Admin.
		'WP_Posts_List_Table'                         => 'posts',
		'WP_Media_List_Table'                         => 'media',
		'WP_Terms_List_Table'                         => 'terms',
		'WP_Users_List_Table'                         => 'users',
		'WP_Comments_List_Table'                      => 'comments',
		'WP_Post_Comments_List_Table'                 => array( 'comments', 'post-comments' ),
		'WP_Links_List_Table'                         => 'links',
		'WP_Plugin_Install_List_Table'                => 'plugin-install',
		'WP_Themes_List_Table'                        => 'themes',
		'WP_Theme_Install_List_Table'                 => array( 'themes', 'theme-install' ),
		'WP_Plugins_List_Table'                       => 'plugins',
		'WP_Application_Passwords_List_Table'         => 'application-passwords',

		// Network Admin.
		'WP_MS_Sites_List_Table'                      => 'ms-sites',
		'WP_MS_Users_List_Table'                      => 'ms-users',
		'WP_MS_Themes_List_Table'                     => 'ms-themes',

		// Privacy requests tables.
		'WP_Privacy_Data_Export_Requests_List_Table'  => 'privacy-data-export-requests',
		'WP_Privacy_Data_Removal_Requests_List_Table' => 'privacy-data-removal-requests',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		foreach ( (array) $core_classes[ $class ] as $required ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-' . $required . '-list-table.php';
		}

		if ( isset( $args['screen'] ) ) {
			$args['screen'] = convert_to_screen( $args['screen'] );
		} elseif ( isset( $GLOBALS['hook_suffix'] ) ) {
			$args['screen'] = get_current_screen();
		} else {
			$args['screen'] = null;
		}

		return new $class( $args );
	}

	return false;
}

/**
 * Register column headers for a particular screen.
 *
 * @param string $screen The handle for the screen to register column headers for. This is
 *                          usually the hook name returned by the `add_*_page()` functions.
 * @param string[] $columns An array of columns with column IDs as the keys and translated
 *                          column names as the values.
 *
 * @see get_column_headers(), print_column_headers(), get_hidden_columns()
 *
 * @since 2.7.0
 *
 */
function register_column_headers( $screen, $columns ) {
	new _WP_List_Table_Compat( $screen, $columns );
}

/**
 * Prints column headers for a particular screen.
 *
 * @param string|WP_Screen $screen The screen hook name or screen object.
 * @param bool $with_id Whether to set the ID attribute or not.
 *
 * @since 2.7.0
 *
 */
function print_column_headers( $screen, $with_id = true ) {
	$wp_list_table = new _WP_List_Table_Compat( $screen );

	$wp_list_table->print_column_headers( $with_id );
}
