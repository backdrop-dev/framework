<?php
/**
 * Writes the translation files used by the integration tests.
 *
 * The files are generated with WordPress's own MO writer, so the repository
 * doesn't need to contain binary `.mo` files.
 *
 * Usage: php make-translations.php /path/to/wordpress
 *
 * @package Backdrop
 */

$wp = rtrim( $argv[1] ?? '', '/' );

// WordPress's MO writer relies on functions that `compat.php` polyfills for
// older PHP versions (e.g., `array_last()`).
define( 'ABSPATH', "{$wp}/" );
define( 'WPINC', 'wp-includes' );

require "{$wp}/wp-includes/compat.php";
require "{$wp}/wp-includes/pomo/mo.php";

/**
 * Writes an MO file.
 *
 * @param string $file    File path.
 * @param array  $entries Translation entries.
 */
function backdrop_test_write_mo( $file, array $entries ) {

	$mo = new MO();
	$mo->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
	$mo->set_header( 'Plural-Forms', 'nplurals=2; plural=(n > 1);' );

	foreach ( $entries as $entry ) {
		$mo->add_entry( new Translation_Entry( $entry ) );
	}

	if ( ! is_dir( dirname( $file ) ) ) {
		mkdir( dirname( $file ), 0777, true );
	}

	if ( ! $mo->export_to_file( $file ) ) {
		fwrite( STDERR, "Could not write {$file}\n" );
		exit( 1 );
	}
}

// Strings used by the framework (the `backdrop` textdomain) and the theme.
$parent = [
	[ 'singular' => '(Untitled)', 'translations' => [ '(Sans titre)' ] ],
	[ 'singular' => '404 Not Found', 'translations' => [ '404 Introuvable' ] ],
	[ 'singular' => 'Context string', 'context' => 'test context', 'translations' => [ 'Chaîne avec contexte' ] ],
	[ 'singular' => '%s item', 'plural' => '%s items', 'translations' => [ '%s élément', '%s éléments' ] ],
	[ 'singular' => 'Hello', 'translations' => [ 'Bonjour' ] ],
];

$themes = "{$wp}/wp-content/themes";

backdrop_test_write_mo( "{$themes}/backdrop-test/languages/fr_FR.mo", $parent );
backdrop_test_write_mo( "{$themes}/backdrop-test-nopath/languages/fr_FR.mo", $parent );

// A child theme override of the parent theme's translations.
backdrop_test_write_mo( "{$themes}/backdrop-test-child/languages/backdrop-test-fr_FR.mo", [
	[ 'singular' => '(Untitled)', 'translations' => [ '(Sans titre, enfant)' ] ],
	[ 'singular' => 'Hello', 'translations' => [ 'Bonjour (enfant)' ] ],
] );

// WordPress only switches to locales it has core translations for.
backdrop_test_write_mo( "{$wp}/wp-content/languages/fr_FR.mo", [] );

echo "Translation files written.\n";
