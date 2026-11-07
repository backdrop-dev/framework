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

/**
 * Returns theme output produced by Backdrop: archive titles, HTML attributes,
 * menu classes, and the generator meta tag.
 *
 * @return array
 */
function backdrop_test_theme_output() {

	$output = [];

	// A month archive.
	$GLOBALS['wp_query']->query( [ 'year' => 2026, 'monthnum' => 3 ] );

	$output['month_title']    = Backdrop\Util\Title::month();
	$output['document_title'] = wp_get_document_title();

	// Post and comment attributes.
	$GLOBALS['post'] = get_post( 1 );
	setup_postdata( $GLOBALS['post'] );

	$output['post_attr']  = Backdrop\Attr\render( 'post' );
	$output['entry_attr'] = Backdrop\Attr\render( 'entry' );

	$GLOBALS['comment'] = get_comment( 1 );

	$comment_classes = preg_match( '/class="([^"]*)"/', Backdrop\Attr\render( 'comment' ), $m )
		? explode( ' ', $m[1] )
		: [];

	$output['comment_class_count'] = count( array_keys( $comment_classes, 'comment', true ) );

	// Menu item classes.
	$menu = wp_nav_menu( [ 'menu' => 'Backdrop Test Menu', 'echo' => false, 'fallback_cb' => false ] );

	$output['menu_item_classes'] = preg_match_all( '/<li [^>]*class="([^"]*)"/', (string) $menu, $m ) ? $m[1] : [];

	// Generator meta tag (the test theme has no `Version` header).
	ob_start();
	Backdrop\meta_generator();
	$output['generator'] = trim( ob_get_clean() );

	return $output;
}

$result = [ 'page' => backdrop_test_strings() ];

if ( getenv( 'BACKDROP_SWITCH_LOCALE' ) ) {
	$result['switched'] = switch_to_locale( getenv( 'BACKDROP_SWITCH_LOCALE' ) )
		? backdrop_test_strings()
		: 'switch_to_locale() failed';

	restore_previous_locale();

	$result['restored'] = backdrop_test_strings();
}

if ( getenv( 'BACKDROP_THEME_OUTPUT' ) ) {
	$result['output'] = backdrop_test_theme_output();
}

$result['locale_files'] = $GLOBALS['backdrop_test_locale_files'];
$result['problems']     = $GLOBALS['backdrop_test_problems'];
$result['wordpress']    = get_bloginfo( 'version' );

echo "\n@@BACKDROP_RESULT@@" . json_encode( $result );
