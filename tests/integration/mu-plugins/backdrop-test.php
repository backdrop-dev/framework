<?php
/**
 * Must-use plugin for Backdrop's WordPress integration tests.
 *
 * Selects the locale and theme from environment variables and records
 * `_doing_it_wrong()` calls and PHP notices raised by Backdrop's code.
 *
 * @package Backdrop
 */

$GLOBALS['backdrop_test_locale_files'] = [];
$GLOBALS['backdrop_test_problems']     = [];

if ( getenv( 'BACKDROP_LOCALE' ) ) {
	add_filter( 'pre_option_WPLANG', function() {
		return getenv( 'BACKDROP_LOCALE' );
	} );
}

add_filter( 'pre_option_template', function() {
	return getenv( 'BACKDROP_TEMPLATE' ) ?: 'backdrop-test';
} );

add_filter( 'pre_option_stylesheet', function() {
	return getenv( 'BACKDROP_STYLESHEET' ) ?: ( getenv( 'BACKDROP_TEMPLATE' ) ?: 'backdrop-test' );
} );

add_action( 'doing_it_wrong_run', function( $function, $message ) {
	$GLOBALS['backdrop_test_problems'][] = "_doing_it_wrong {$function}: " . wp_strip_all_tags( $message );
}, 10, 2 );

add_filter( 'doing_it_wrong_trigger_error', '__return_false' );

// Record PHP notices from Backdrop itself. Notices from WordPress core are
// ignored, since older WordPress versions emit some on newer PHP versions.
$backdrop_src = realpath( dirname( getenv( 'BACKDROP_AUTOLOAD' ), 2 ) . '/src' );

set_error_handler( function( $level, $message, $file ) use ( $backdrop_src ) {

	if ( $backdrop_src && 0 === strpos( (string) realpath( $file ), $backdrop_src ) ) {
		$GLOBALS['backdrop_test_problems'][] = "PHP notice: {$message} in " . basename( $file );
		return true;
	}

	return false;
} );
