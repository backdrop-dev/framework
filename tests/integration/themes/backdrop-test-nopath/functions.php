<?php
/**
 * Integration test theme without a `Domain Path` header.
 *
 * @package Backdrop
 */

require_once getenv( 'BACKDROP_AUTOLOAD' );

add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'backdrop-test-nopath', get_template_directory() . '/languages' );
} );

$app = Backdrop\booted() ? Backdrop\app() : new Backdrop\Core\Application();
$app->boot();
