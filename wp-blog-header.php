<?php

if ( ! isset( wp_globals::$wp_did_header ) ) {

	wp_globals::$wp_did_header = true;

	require_once __DIR__ . '/wp-load.php';

	wp();

	require_once ABSPATH . WPINC . '/template-loader.php';
}
