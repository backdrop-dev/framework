<?php
/**
 * Runs the WordPress integration tests.
 *
 * Each scenario loads WordPress in a fresh PHP process (`request.php`) with a
 * different locale, theme, or translation setup, then checks the framework's
 * translated strings. Run `setup.sh` first.
 *
 * Usage: php run.php /path/to/wordpress
 *
 * @package Backdrop
 */

$wp = rtrim( $argv[1] ?? '', '/' );

if ( ! $wp || ! file_exists( "{$wp}/wp-load.php" ) ) {
	fwrite( STDERR, "Usage: php run.php /path/to/wordpress (run setup.sh first)\n" );
	exit( 1 );
}

$french = [
	'locale'    => 'fr_FR',
	'framework' => '(Sans titre)',
	'title'     => '404 Introuvable',
	'context'   => 'Chaîne avec contexte',
	'plural_1'  => '%s élément',
	'plural_2'  => '%s éléments',
	'theme'     => 'Bonjour',
];

$english = [
	'locale'    => 'en_US',
	'framework' => '(Untitled)',
	'title'     => '404 Not Found',
	'context'   => 'Context string',
	'plural_1'  => '%s item',
	'plural_2'  => '%s items',
	'theme'     => 'Hello',
];

$parent_locale_file = [ 'backdrop-test/languages/fr-fr.php' ];

$scenarios = [
	'French site, theme loads its translations on after_setup_theme' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'fr_FR' ],
		'expect' => [ 'page' => $french, 'locale_files' => $parent_locale_file ],
	],
	'French site, theme loads its translations on init' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'fr_FR', 'BACKDROP_TEXTDOMAIN_HOOK' => 'init' ],
		'expect' => [ 'page' => $french, 'locale_files' => $parent_locale_file ],
	],
	'English site that switches to French (e.g., an email to a French user)' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'en_US', 'BACKDROP_SWITCH_LOCALE' => 'fr_FR' ],
		'expect' => [ 'page' => $english, 'switched' => $french, 'restored' => $english, 'locale_files' => [] ],
	],
	'French site with a child theme that overrides the parent translations' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'fr_FR', 'BACKDROP_STYLESHEET' => 'backdrop-test-child' ],
		'expect' => [
			'page'         => [ 'framework' => '(Sans titre, enfant)', 'theme' => 'Bonjour (enfant)' ],
			'locale_files' => $parent_locale_file,
		],
	],
	'French site, theme without a Domain Path header (defaults to /languages)' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'fr_FR', 'BACKDROP_TEMPLATE' => 'backdrop-test-nopath' ],
		'expect' => [
			'page'         => array_diff_key( $french, [ 'theme' => true ] ),
			'locale_files' => [ 'backdrop-test-nopath/languages/fr-fr.php' ],
		],
	],
	'English site' => [
		'env'    => [ 'BACKDROP_LOCALE' => 'en_US' ],
		'expect' => [ 'page' => $english, 'locale_files' => [] ],
	],
];

$autoload = dirname( __DIR__, 2 ) . '/vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	fwrite( STDERR, "Run `composer install` first.\n" );
	exit( 1 );
}

$failures = 0;
$version  = '';

foreach ( $scenarios as $name => $scenario ) {

	$env = array_merge( getenv(), [ 'BACKDROP_AUTOLOAD' => $autoload ], $scenario['env'] );

	$process = proc_open(
		[ PHP_BINARY, '-d', 'display_errors=stderr', __DIR__ . '/request.php', $wp ],
		[ 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
		$pipes,
		null,
		$env
	);

	$stdout = stream_get_contents( $pipes[1] );
	$stderr = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] );
	fclose( $pipes[2] );
	proc_close( $process );

	$marker = strrpos( $stdout, '@@BACKDROP_RESULT@@' );
	$result = false === $marker ? null : json_decode( substr( $stdout, $marker + 19 ), true );

	if ( ! is_array( $result ) ) {
		$failures++;
		echo "FAIL  {$name}\n      No result. Output:\n" . trim( $stdout . "\n" . $stderr ) . "\n";
		continue;
	}

	$version = $result['wordpress'];
	$errors  = [];

	foreach ( $scenario['expect'] as $section => $expected ) {
		$actual = $result[ $section ] ?? null;

		if ( 'locale_files' === $section ) {
			if ( $actual !== $expected ) {
				$errors[] = sprintf( 'locale files: expected %s, got %s', json_encode( $expected ), json_encode( $actual ) );
			}
			continue;
		}

		if ( ! is_array( $actual ) ) {
			$errors[] = "{$section}: " . json_encode( $actual );
			continue;
		}

		foreach ( $expected as $key => $value ) {
			if ( ( $actual[ $key ] ?? null ) !== $value ) {
				$errors[] = sprintf( '%s %s: expected "%s", got "%s"', $section, $key, $value, $actual[ $key ] ?? '(missing)' );
			}
		}
	}

	foreach ( $result['problems'] as $problem ) {
		$errors[] = $problem;
	}

	if ( $errors ) {
		$failures++;
		echo "FAIL  {$name}\n      " . implode( "\n      ", $errors ) . "\n";
	} else {
		echo "ok    {$name}\n";
	}
}

printf( "\nWordPress %s, PHP %s: %d of %d scenarios passed.\n", $version, PHP_VERSION, count( $scenarios ) - $failures, count( $scenarios ) );

exit( $failures ? 1 : 0 );
