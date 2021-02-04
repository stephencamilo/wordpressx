<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/vendor/autoload.php';

use Core\Wordpressx\Index;

$WPX_index = new Index();

$WPX_index->blog_header();