<?php
namespace Core;

class SetupConfig {

	public static function setup_config_display_header( $body_classes = array() ) {
		$body_classes   = (array) $body_classes;
		$body_classes[] = 'wp-core-ui';
		$dir_attr       = '';
		if ( is_rtl() ) {
			$body_classes[] = 'rtl';
			$dir_attr       = ' dir="rtl"';
		}

		header( 'Content-Type: text/html; charset=utf-8' );
		?>
        <!DOCTYPE html>
    <html<?php echo $dir_attr; ?>>
        <head>
            <meta name="viewport" content="width=device-width"/>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <meta name="robots" content="noindex,nofollow"/>
            <title><?php _e( 'WordPress &rsaquo; Setup Configuration File' ); ?></title>
			<?php wp_admin_css( 'install', true ); ?>
        </head>
    <body class="<?php echo implode( ' ', $body_classes ); ?>">
    <p id="logo"><?php _e( 'WordPress' ); ?></p>
		<?php
	}
}