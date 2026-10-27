<?php
/**
 * Loads WordPress like a front-end request and prints the translated strings
 * as JSON. Run by `run.php`, once per scenario, in a fresh PHP process.
 *
 * Usage: php request.php /path/to/wordpress
 *
 * @package Backdrop
 */

$_SERVER['HTTP_HOST']       = 'example.test';
$_SERVER['REQUEST_URI']     = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

define( 'WP_USE_THEMES', false );

require rtrim( $argv[1] ?? '', '/' ) . '/wp-load.php';

/**
 * Returns the strings to check in the current locale.
 *
 * @return array
 */
function backdrop_test_strings() {

	return [
		'locale'    => determine_locale(),
		'framework' => __( '(Untitled)', 'backdrop' ),
		'title'     => Backdrop\Util\Title::error(),
		'context'   => _x( 'Context string', 'test context', 'backdrop' ),
		'plural_1'  => _n( '%s item', '%s items', 1, 'backdrop' ),
		'plural_2'  => _n( '%s item', '%s items', 2, 'backdrop' ),
		'theme'     => __( 'Hello', 'backdrop-test' ),
	];
}

$result = [ 'page' => backdrop_test_strings() ];

if ( getenv( 'BACKDROP_SWITCH_LOCALE' ) ) {
	$result['switched'] = switch_to_locale( getenv( 'BACKDROP_SWITCH_LOCALE' ) )
		? backdrop_test_strings()
		: 'switch_to_locale() failed';

	restore_previous_locale();

	$result['restored'] = backdrop_test_strings();
}

$result['locale_files'] = $GLOBALS['backdrop_test_locale_files'];
$result['problems']     = $GLOBALS['backdrop_test_problems'];
$result['wordpress']    = get_bloginfo( 'version' );

echo "\n@@BACKDROP_RESULT@@" . json_encode( $result );
