<?php
/**
 * Integration test theme.
 *
 * Loads the framework from the repository and boots it the way the readme
 * documents. `BACKDROP_TEXTDOMAIN_HOOK` controls when the theme loads its
 * translations (`after_setup_theme` by default, or `init`).
 *
 * @package Backdrop
 */

require_once getenv( 'BACKDROP_AUTOLOAD' );

$backdrop_test_hook = getenv( 'BACKDROP_TEXTDOMAIN_HOOK' ) ?: 'after_setup_theme';

add_action( $backdrop_test_hook, function() {
	load_theme_textdomain( 'backdrop-test', get_template_directory() . '/languages' );
} );

$app = Backdrop\booted() ? Backdrop\app() : new Backdrop\Core\Application();
$app->boot();
