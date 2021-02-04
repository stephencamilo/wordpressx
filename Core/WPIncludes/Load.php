<?php
namespace WPIncludes;

class load;
function wp_get_server_protocol() {
	$protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : '';
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}
	return $protocol;
}

function wp_fix_server_vars() {
	global $PHP_SELF;

	$default_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI'     => '',
	);

	$_SERVER = array_merge( $default_server_values, $_SERVER );

	if ( empty( $_SERVER['REQUEST_URI'] ) || ( 'cgi-fcgi' !== PHP_SAPI && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

		if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		} elseif ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		} else {
			if ( ! isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
				$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
			}

			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] ) {
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
				} else {
					$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
				}
			}

			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
	}

	if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi' ) == strlen( $_SERVER['SCRIPT_FILENAME'] ) - 7 ) ) {
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	}

	if ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) !== false ) {
		unset( $_SERVER['PATH_INFO'] );
	}

	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty( $PHP_SELF ) ) {
		$_SERVER['PHP_SELF'] = preg_replace( '/(\?.*)?$/', '', $_SERVER['REQUEST_URI'] );
		$PHP_SELF            = $_SERVER['PHP_SELF'];
	}

	wp_populate_basic_auth_from_authorization_header();
}

function wp_populate_basic_auth_from_authorization_header() {
	if ( ! isset( $_SERVER['HTTP_AUTHORIZATION'] ) && ! isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		return;
	}

	if ( isset( $_SERVER['PHP_AUTH_USER'] ) || isset( $_SERVER['PHP_AUTH_PW'] ) ) {
		return;
	}

	$header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? $_SERVER['HTTP_AUTHORIZATION'] : $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

	if ( ! preg_match( '%^Basic [a-z\d/+]*={0,2}$%i', $header ) ) {
		return;
	}

	$token    = substr( $header, 6 );
	$userpass = base64_decode( $token );

	list( $user, $pass ) = explode( ':', $userpass );

	$_SERVER['PHP_AUTH_USER'] = $user;
	$_SERVER['PHP_AUTH_PW']   = $pass;
}

function wp_check_php_mysql_versions() {
	global $required_php_version, $wp_version;
	$php_version = phpversion();

	if ( version_compare( $required_php_version, $php_version, '>' ) ) {
		$protocol = wp_get_server_protocol();
		header( sprintf( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header( 'Content-Type: text/html; charset=utf-8' );
		printf( 'Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.', $php_version, $wp_version, $required_php_version );
		exit( 1 );
	}

	if ( ! extension_loaded( 'mysql' ) && ! extension_loaded( 'mysqli' ) && ! extension_loaded( 'mysqlnd' )
		// This runs before default constants are defined, so we can't assume WP_CONTENT_DIR is set yet.
		&& ( defined( 'WP_CONTENT_DIR' ) && ! file_exists( WP_CONTENT_DIR . '/db.php' )
			|| ! file_exists( ABSPATH . 'wp-content/db.php' ) )
	) {
		require_once ABSPATH . WPINC . '/functions.php';
		wp_load_translations_early();
		$args = array(
			'exit' => false,
			'code' => 'mysql_not_found',
		);
		wp_die(
			__( 'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.' ),
			__( 'Requirements Not Met' ),
			$args
		);
		exit( 1 );
	}
}

function wp_get_environment_type() {
	static $current_env = '';

	if ( $current_env ) {
		return $current_env;
	}

	$wp_environments = array(
		'local',
		'development',
		'staging',
		'production',
	);

	if ( defined( 'WP_ENVIRONMENT_TYPES' ) && function_exists( '_deprecated_argument' ) ) {
		if ( function_exists( '__' ) ) {
			$message = sprintf( __( 'The %s constant is no longer supported.' ), 'WP_ENVIRONMENT_TYPES' );
		} else {
			$message = sprintf( 'The %s constant is no longer supported.', 'WP_ENVIRONMENT_TYPES' );
		}

		_deprecated_argument(
			'define()',
			'5.5.1',
			$message
		);
	}

	if ( function_exists( 'getenv' ) ) {
		$has_env = getenv( 'WP_ENVIRONMENT_TYPE' );
		if ( false !== $has_env ) {
			$current_env = $has_env;
		}
	}

	if ( defined( 'WP_ENVIRONMENT_TYPE' ) ) {
		$current_env = WP_ENVIRONMENT_TYPE;
	}

	if ( ! in_array( $current_env, $wp_environments, true ) ) {
		$current_env = 'production';
	}

	return $current_env;
}

function wp_favicon_request() {
	if ( '/favicon.ico' === $_SERVER['REQUEST_URI'] ) {
		header( 'Content-Type: image/vnd.microsoft.icon' );
		exit;
	}
}

function wp_maintenance() {
	// Return if maintenance mode is disabled.
	if ( ! wp_is_maintenance_mode() ) {
		return;
	}

	if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
		require_once WP_CONTENT_DIR . '/maintenance.php';
		die();
	}

	require_once ABSPATH . WPINC . '/functions.php';
	wp_load_translations_early();

	header( 'Retry-After: 600' );

	wp_die(
		__( 'Briefly unavailable for scheduled maintenance. Check back in a minute.' ),
		__( 'Maintenance' ),
		503
	);
}

function wp_is_maintenance_mode() {
	global $upgrading;

	if ( ! file_exists( ABSPATH . '.maintenance' ) || wp_installing() ) {
		return false;
	}

	require ABSPATH . '.maintenance';
	// If the $upgrading timestamp is older than 10 minutes, consider maintenance over.
	if ( ( time() - $upgrading ) >= 10 * MINUTE_IN_SECONDS ) {
		return false;
	}

	if ( ! apply_filters( 'enable_maintenance_mode', true, $upgrading ) ) {
		return false;
	}

	return true;
}

function timer_start() {
	global $timestart;
	$timestart = microtime( true );
	return true;
}

function timer_stop( $display = 0, $precision = 3 ) {
	global $timestart, $timeend;
	$timeend   = microtime( true );
	$timetotal = $timeend - $timestart;
	$r         = ( function_exists( 'number_format_i18n' ) ) ? number_format_i18n( $timetotal, $precision ) : number_format( $timetotal, $precision );
	if ( $display ) {
		echo $r;
	}
	return $r;
}

function wp_debug_mode() {
	if ( ! apply_filters( 'enable_wp_debug_mode_checks', true ) ) {
		return;
	}

	if ( WP_DEBUG ) {
		error_reporting( E_ALL );

		if ( WP_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 1 );
		} elseif ( null !== WP_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 0 );
		}

		if ( in_array( strtolower( (string) WP_DEBUG_LOG ), array( 'true', '1' ), true ) ) {
			$log_path = WP_CONTENT_DIR . '/debug.log';
		} elseif ( is_string( WP_DEBUG_LOG ) ) {
			$log_path = WP_DEBUG_LOG;
		} else {
			$log_path = false;
		}

		if ( $log_path ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', $log_path );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}

	if ( defined( 'XMLRPC_REQUEST' ) || defined( 'REST_REQUEST' ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || wp_doing_ajax() || wp_is_json_request() ) {
		ini_set( 'display_errors', 0 );
	}
}

function wp_set_lang_dir() {
	if ( ! defined( 'WP_LANG_DIR' ) ) {
		if ( file_exists( WP_CONTENT_DIR . '/languages' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) || ! @is_dir( ABSPATH . WPINC . '/languages' ) ) {

			define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old static relative path maintained for limited backward compatibility - won't work in some cases.
				define( 'LANGDIR', 'wp-content/languages' );
			}
		} else {

			define( 'WP_LANG_DIR', ABSPATH . WPINC . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old relative path maintained for backward compatibility.
				define( 'LANGDIR', WPINC . '/languages' );
			}
		}
	}
}


function require_wp_db() {
	global $wpdb;

	require_once ABSPATH . WPINC . '/wp-db.php';
	if ( file_exists( WP_CONTENT_DIR . '/db.php' ) ) {
		require_once WP_CONTENT_DIR . '/db.php';
	}

	if ( isset( $wpdb ) ) {
		return;
	}

	$dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
	$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
	$dbname     = defined( 'DB_NAME' ) ? DB_NAME : '';
	$dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

	$wpdb = new wpdb( $dbuser, $dbpassword, $dbname, $dbhost );
}

function wp_set_wpdb_vars() {
	global $wpdb, $table_prefix;
	if ( ! empty( $wpdb->error ) ) {
		dead_db();
	}

	$wpdb->field_types = array(
		'post_author'      => '%d',
		'post_parent'      => '%d',
		'menu_order'       => '%d',
		'term_id'          => '%d',
		'term_group'       => '%d',
		'term_taxonomy_id' => '%d',
		'parent'           => '%d',
		'count'            => '%d',
		'object_id'        => '%d',
		'term_order'       => '%d',
		'ID'               => '%d',
		'comment_ID'       => '%d',
		'comment_post_ID'  => '%d',
		'comment_parent'   => '%d',
		'user_id'          => '%d',
		'link_id'          => '%d',
		'link_owner'       => '%d',
		'link_rating'      => '%d',
		'option_id'        => '%d',
		'blog_id'          => '%d',
		'meta_id'          => '%d',
		'post_id'          => '%d',
		'user_status'      => '%d',
		'umeta_id'         => '%d',
		'comment_karma'    => '%d',
		'comment_count'    => '%d',
		// Multisite:
		'active'           => '%d',
		'cat_id'           => '%d',
		'deleted'          => '%d',
		'lang_id'          => '%d',
		'mature'           => '%d',
		'public'           => '%d',
		'site_id'          => '%d',
		'spam'             => '%d',
	);

	$prefix = $wpdb->set_prefix( $table_prefix );

	if ( is_wp_error( $prefix ) ) {
		wp_load_translations_early();
		wp_die(
			sprintf(
				/* translators: 1: $table_prefix, 2: wp-config.php */
				__( '<strong>Error</strong>: %1$s in %2$s can only contain numbers, letters, and underscores.' ),
				'<code>$table_prefix</code>',
				'<code>wp-config.php</code>'
			)
		);
	}
}

function wp_using_ext_object_cache( $using = null ) {
	global $_wp_using_ext_object_cache;
	$current_using = $_wp_using_ext_object_cache;
	if ( null !== $using ) {
		$_wp_using_ext_object_cache = $using;
	}
	return $current_using;
}

function wp_start_object_cache() {
	global $wp_filter;
	static $first_init = true;

	// Only perform the following checks once.
	if ( $first_init ) {
		if ( ! function_exists( 'wp_cache_init' ) ) {
			if ( file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
				require_once WP_CONTENT_DIR . '/object-cache.php';
				if ( function_exists( 'wp_cache_init' ) ) {
					wp_using_ext_object_cache( true );
				}

				// Re-initialize any hooks added manually by object-cache.php.
				if ( $wp_filter ) {
					$wp_filter = WP_Hook::build_preinitialized_hooks( $wp_filter );
				}
			}
		} elseif ( ! wp_using_ext_object_cache() && file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
			wp_using_ext_object_cache( true );
		}
	}

	if ( ! wp_using_ext_object_cache() ) {
		require_once ABSPATH . WPINC . '/cache.php';
	}

	require_once ABSPATH . WPINC . '/cache-compat.php';

	if ( ! $first_init && function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( get_current_blog_id() );
	} elseif ( function_exists( 'wp_cache_init' ) ) {
		wp_cache_init();
	}

	if ( function_exists( 'wp_cache_add_global_groups' ) ) {
		wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'blog-lookup', 'blog-details', 'site-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'blog_meta' ) );
		wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
	}

	$first_init = false;
}

function wp_not_installed() {
	if ( is_multisite() ) {
		if ( ! is_blog_installed() && ! wp_installing() ) {
			nocache_headers();

			wp_die( __( 'The site you have requested is not installed properly. Please contact the system administrator.' ) );
		}
	} elseif ( ! is_blog_installed() && ! wp_installing() ) {
		nocache_headers();

		require ABSPATH . WPINC . '/kses.php';
		require ABSPATH . WPINC . '/pluggable.php';

		$link = wp_guess_url() . '/wp-admin/install.php';

		wp_redirect( $link );
		die();
	}
}

function wp_get_mu_plugins() {
	$mu_plugins = array();
	if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
		return $mu_plugins;
	}
	$dh = opendir( WPMU_PLUGIN_DIR );
	if ( ! $dh ) {
		return $mu_plugins;
	}
	while ( ( $plugin = readdir( $dh ) ) !== false ) {
		if ( '.php' === substr( $plugin, -4 ) ) {
			$mu_plugins[] = WPMU_PLUGIN_DIR . '/' . $plugin;
		}
	}
	closedir( $dh );
	sort( $mu_plugins );

	return $mu_plugins;
}

function wp_get_active_and_valid_plugins() {
	$plugins        = array();
	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Check for hacks file if the option is enabled.
	if ( get_option( 'hack_file' ) && file_exists( ABSPATH . 'my-hacks.php' ) ) {
		_deprecated_file( 'my-hacks.php', '1.5.0' );
		array_unshift( $plugins, ABSPATH . 'my-hacks.php' );
	}

	if ( empty( $active_plugins ) || wp_installing() ) {
		return $plugins;
	}

	$network_plugins = is_multisite() ? wp_get_active_network_plugins() : false;

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin )                     // $plugin must validate as file.
			&& '.php' === substr( $plugin, -4 )             // $plugin must end with '.php'.
			&& file_exists( WP_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist.
			// Not already included as a network plugin.
			&& ( ! $network_plugins || ! in_array( WP_PLUGIN_DIR . '/' . $plugin, $network_plugins, true ) )
			) {
			$plugins[] = WP_PLUGIN_DIR . '/' . $plugin;
		}
	}

	/*
	 * Remove plugins from the list of active plugins when we're on an endpoint
	 * that should be protected against WSODs and the plugin is paused.
	 */
	if ( wp_is_recovery_mode() ) {
		$plugins = wp_skip_paused_plugins( $plugins );
	}

	return $plugins;
}

function wp_skip_paused_plugins( array $plugins ) {
	$paused_plugins = wp_paused_plugins()->get_all();

	if ( empty( $paused_plugins ) ) {
		return $plugins;
	}

	foreach ( $plugins as $index => $plugin ) {
		list( $plugin ) = explode( '/', plugin_basename( $plugin ) );

		if ( array_key_exists( $plugin, $paused_plugins ) ) {
			unset( $plugins[ $index ] );

			// Store list of paused plugins for displaying an admin notice.
			$GLOBALS['_paused_plugins'][ $plugin ] = $paused_plugins[ $plugin ];
		}
	}

	return $plugins;
}

function wp_get_active_and_valid_themes() {
	global $pagenow;

	$themes = array();

	if ( wp_installing() && 'wp-activate.php' !== $pagenow ) {
		return $themes;
	}

	if ( TEMPLATEPATH !== STYLESHEETPATH ) {
		$themes[] = STYLESHEETPATH;
	}

	$themes[] = TEMPLATEPATH;

	/*
	 * Remove themes from the list of active themes when we're on an endpoint
	 * that should be protected against WSODs and the theme is paused.
	 */
	if ( wp_is_recovery_mode() ) {
		$themes = wp_skip_paused_themes( $themes );

		// If no active and valid themes exist, skip loading themes.
		if ( empty( $themes ) ) {
			add_filter( 'wp_using_themes', '__return_false' );
		}
	}

	return $themes;
}

function wp_skip_paused_themes( array $themes ) {
	$paused_themes = wp_paused_themes()->get_all();

	if ( empty( $paused_themes ) ) {
		return $themes;
	}

	foreach ( $themes as $index => $theme ) {
		$theme = basename( $theme );

		if ( array_key_exists( $theme, $paused_themes ) ) {
			unset( $themes[ $index ] );

			// Store list of paused themes for displaying an admin notice.
			$GLOBALS['_paused_themes'][ $theme ] = $paused_themes[ $theme ];
		}
	}

	return $themes;
}

function wp_is_recovery_mode() {
	return wp_recovery_mode()->is_active();
}

function is_protected_endpoint() {
	// Protect login pages.
	if ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		return true;
	}

	// Protect the admin backend.
	if ( is_admin() && ! wp_doing_ajax() ) {
		return true;
	}

	// Protect Ajax actions that could help resolve a fatal error should be available.
	if ( is_protected_ajax_action() ) {
		return true;
	}

	return (bool) apply_filters( 'is_protected_endpoint', false );
}

function is_protected_ajax_action() {
	if ( ! wp_doing_ajax() ) {
		return false;
	}

	if ( ! isset( $_REQUEST['action'] ) ) {
		return false;
	}

	$actions_to_protect = array(
		'edit-theme-plugin-file', // Saving changes in the core code editor.
		'heartbeat',              // Keep the heart beating.
		'install-plugin',         // Installing a new plugin.
		'install-theme',          // Installing a new theme.
		'search-plugins',         // Searching in the list of plugins.
		'search-install-plugins', // Searching for a plugin in the plugin install screen.
		'update-plugin',          // Update an existing plugin.
		'update-theme',           // Update an existing theme.
	);

	$actions_to_protect = (array) apply_filters( 'wp_protected_ajax_actions', $actions_to_protect );

	if ( ! in_array( $_REQUEST['action'], $actions_to_protect, true ) ) {
		return false;
	}

	return true;
}

function wp_set_internal_encoding() {
	if ( function_exists( 'mb_internal_encoding' ) ) {
		$charset = get_option( 'blog_charset' );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $charset || ! @mb_internal_encoding( $charset ) ) {
			mb_internal_encoding( 'UTF-8' );
		}
	}
}

function wp_magic_quotes() {
	// Escape with wpdb.
	$_GET    = add_magic_quotes( $_GET );
	$_POST   = add_magic_quotes( $_POST );
	$_COOKIE = add_magic_quotes( $_COOKIE );
	$_SERVER = add_magic_quotes( $_SERVER );

	// Force REQUEST to be GET + POST.
	$_REQUEST = array_merge( $_GET, $_POST );
}

function shutdown_action_hook() {

	do_action( 'shutdown' );

	wp_cache_close();
}

function wp_clone( $object ) {
	return clone( $object );
}

function is_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin();
	} elseif ( defined( 'WP_ADMIN' ) ) {
		return WP_ADMIN;
	}

	return false;
}

function is_blog_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'site' );
	} elseif ( defined( 'WP_BLOG_ADMIN' ) ) {
		return WP_BLOG_ADMIN;
	}

	return false;
}

function is_network_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'network' );
	} elseif ( defined( 'WP_NETWORK_ADMIN' ) ) {
		return WP_NETWORK_ADMIN;
	}

	return false;
}

function is_user_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'user' );
	} elseif ( defined( 'WP_USER_ADMIN' ) ) {
		return WP_USER_ADMIN;
	}

	return false;
}

function is_multisite() {
	if ( defined( 'MULTISITE' ) ) {
		return MULTISITE;
	}

	if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) ) {
		return true;
	}

	return false;
}

function get_current_blog_id() {
	global $blog_id;
	return absint( $blog_id );
}

function get_current_network_id() {
	if ( ! is_multisite() ) {
		return 1;
	}

	$current_network = get_network();

	if ( ! isset( $current_network->id ) ) {
		return get_main_network_id();
	}

	return absint( $current_network->id );
}

function wp_load_translations_early() {
	global $wp_locale;

	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$loaded = true;

	if ( function_exists( 'did_action' ) && did_action( 'init' ) ) {
		return;
	}

	// We need $wp_local_package.
	require ABSPATH . WPINC . '/version.php';

	// Translation and localization.
	require_once ABSPATH . WPINC . '/pomo/mo.php';
	require_once ABSPATH . WPINC . '/l10n.php';
	require_once ABSPATH . WPINC . '/class-wp-locale.php';
	require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';

	// General libraries.
	require_once ABSPATH . WPINC . '/plugin.php';

	$locales   = array();
	$locations = array();

	while ( true ) {
		if ( defined( 'WPLANG' ) ) {
			if ( '' === WPLANG ) {
				break;
			}
			$locales[] = WPLANG;
		}

		if ( isset( $wp_local_package ) ) {
			$locales[] = $wp_local_package;
		}

		if ( ! $locales ) {
			break;
		}

		if ( defined( 'WP_LANG_DIR' ) && @is_dir( WP_LANG_DIR ) ) {
			$locations[] = WP_LANG_DIR;
		}

		if ( defined( 'WP_CONTENT_DIR' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) ) {
			$locations[] = WP_CONTENT_DIR . '/languages';
		}

		if ( @is_dir( ABSPATH . 'wp-content/languages' ) ) {
			$locations[] = ABSPATH . 'wp-content/languages';
		}

		if ( @is_dir( ABSPATH . WPINC . '/languages' ) ) {
			$locations[] = ABSPATH . WPINC . '/languages';
		}

		if ( ! $locations ) {
			break;
		}

		$locations = array_unique( $locations );

		foreach ( $locales as $locale ) {
			foreach ( $locations as $location ) {
				if ( file_exists( $location . '/' . $locale . '.mo' ) ) {
					load_textdomain( 'default', $location . '/' . $locale . '.mo' );
					if ( defined( 'WP_SETUP_CONFIG' ) && file_exists( $location . '/admin-' . $locale . '.mo' ) ) {
						load_textdomain( 'default', $location . '/admin-' . $locale . '.mo' );
					}
					break 2;
				}
			}
		}

		break;
	}

	$wp_locale = new WP_Locale();
}

function wp_installing( $is_installing = null ) {
	static $installing = null;

	// Support for the `WP_INSTALLING` constant, defined before WP is loaded.
	if ( is_null( $installing ) ) {
		$installing = defined( 'WP_INSTALLING' ) && WP_INSTALLING;
	}

	if ( ! is_null( $is_installing ) ) {
		$old_installing = $installing;
		$installing     = $is_installing;
		return (bool) $old_installing;
	}

	return (bool) $installing;
}

function is_ssl() {
	if ( isset( $_SERVER['HTTPS'] ) ) {
		if ( 'on' === strtolower( $_SERVER['HTTPS'] ) ) {
			return true;
		}

		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

function wp_convert_hr_to_bytes( $value ) {
	$value = strtolower( trim( $value ) );
	$bytes = (int) $value;

	if ( false !== strpos( $value, 'g' ) ) {
		$bytes *= GB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'm' ) ) {
		$bytes *= MB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'k' ) ) {
		$bytes *= KB_IN_BYTES;
	}

	// Deal with large (float) values which run into the maximum integer size.
	return min( $bytes, PHP_INT_MAX );
}

function wp_is_ini_value_changeable( $setting ) {
	static $ini_all;

	if ( ! isset( $ini_all ) ) {
		$ini_all = false;
		// Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
		if ( function_exists( 'ini_get_all' ) ) {
			$ini_all = ini_get_all();
		}
	}

	// Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
	if ( isset( $ini_all[ $setting ]['access'] ) && ( INI_ALL === ( $ini_all[ $setting ]['access'] & 7 ) || INI_USER === ( $ini_all[ $setting ]['access'] & 7 ) ) ) {
		return true;
	}

	// If we were unable to retrieve the details, fail gracefully to assume it's changeable.
	if ( ! is_array( $ini_all ) ) {
		return true;
	}

	return false;
}

function wp_doing_ajax() {
	return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}

function wp_using_themes() {
	return apply_filters( 'wp_using_themes', defined( 'WP_USE_THEMES' ) && WP_USE_THEMES );
}

function wp_doing_cron() {
	return apply_filters( 'wp_doing_cron', defined( 'DOING_CRON' ) && DOING_CRON );
}

function is_wp_error( $thing ) {
	$is_wp_error = ( $thing instanceof WP_Error );

	if ( $is_wp_error ) {
		do_action( 'is_wp_error_instance', $thing );
	}

	return $is_wp_error;
}

function wp_is_file_mod_allowed( $context ) {
	return apply_filters( 'file_mod_allowed', ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS, $context );
}

function wp_start_scraping_edited_file_errors() {
	if ( ! isset( $_REQUEST['wp_scrape_key'] ) || ! isset( $_REQUEST['wp_scrape_nonce'] ) ) {
		return;
	}
	$key   = substr( sanitize_key( wp_unslash( $_REQUEST['wp_scrape_key'] ) ), 0, 32 );
	$nonce = wp_unslash( $_REQUEST['wp_scrape_nonce'] );

	if ( get_transient( 'scrape_key_' . $key ) !== $nonce ) {
		echo "###### wp_scraping_result_start:$key ######";
		echo wp_json_encode(
			array(
				'code'    => 'scrape_nonce_failure',
				'message' => __( 'Scrape nonce check failed. Please try again.' ),
			)
		);
		echo "###### wp_scraping_result_end:$key ######";
		die();
	}
	if ( ! defined( 'WP_SANDBOX_SCRAPING' ) ) {
		define( 'WP_SANDBOX_SCRAPING', true );
	}
	register_shutdown_function( 'wp_finalize_scraping_edited_file_errors', $key );
}

function wp_finalize_scraping_edited_file_errors( $scrape_key ) {
	$error = error_get_last();
	echo "\n###### wp_scraping_result_start:$scrape_key ######\n";
	if ( ! empty( $error ) && in_array( $error['type'], array( E_CORE_ERROR, E_COMPILE_ERROR, E_ERROR, E_PARSE, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
		$error = str_replace( ABSPATH, '', $error );
		echo wp_json_encode( $error );
	} else {
		echo wp_json_encode( true );
	}
	echo "\n###### wp_scraping_result_end:$scrape_key ######\n";
}

function wp_is_json_request() {

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) && wp_is_json_media_type( $_SERVER['HTTP_ACCEPT'] ) ) {
		return true;
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && wp_is_json_media_type( $_SERVER['CONTENT_TYPE'] ) ) {
		return true;
	}

	return false;

}

function wp_is_jsonp_request() {
	if ( ! isset( $_GET['_jsonp'] ) ) {
		return false;
	}

	if ( ! function_exists( 'wp_check_jsonp_callback' ) ) {
		require_once ABSPATH . WPINC . '/functions.php';
	}

	$jsonp_callback = $_GET['_jsonp'];
	if ( ! wp_check_jsonp_callback( $jsonp_callback ) ) {
		return false;
	}

	$jsonp_enabled = apply_filters( 'rest_jsonp_enabled', true );

	return $jsonp_enabled;

}

function wp_is_json_media_type( $media_type ) {
	static $cache = array();

	if ( ! isset( $cache[ $media_type ] ) ) {
		$cache[ $media_type ] = (bool) preg_match( '/(^|\s|,)application\/([\w!#\$&-\^\.\+]+\+)?json(\+oembed)?($|\s|;|,)/i', $media_type );
	}

	return $cache[ $media_type ];
}

function wp_is_xml_request() {
	$accepted = array(
		'text/xml',
		'application/rss+xml',
		'application/atom+xml',
		'application/rdf+xml',
		'text/xml+oembed',
		'application/xml+oembed',
	);

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
		foreach ( $accepted as $type ) {
			if ( false !== strpos( $_SERVER['HTTP_ACCEPT'], $type ) ) {
				return true;
			}
		}
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && in_array( $_SERVER['CONTENT_TYPE'], $accepted, true ) ) {
		return true;
	}

	return false;
}
}