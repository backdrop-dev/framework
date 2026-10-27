<?php
/**
 * Installs WordPress for the integration tests.
 *
 * Usage: php install.php /path/to/wordpress
 *
 * @package Backdrop
 */

$_SERVER['HTTP_HOST']   = 'example.test';
$_SERVER['REQUEST_URI'] = '/';

define( 'WP_INSTALLING', true );

require rtrim( $argv[1] ?? '', '/' ) . '/wp-load.php';
require ABSPATH . 'wp-admin/includes/upgrade.php';

// Don't try to send the "new site" email.
add_filter( 'pre_wp_mail', '__return_true' );

$result = wp_install( 'Backdrop Test', 'admin', 'admin@example.test', true, '', wp_generate_password() );

if ( is_wp_error( $result ) ) {
	fwrite( STDERR, $result->get_error_message() . "\n" );
	exit( 1 );
}

echo 'WordPress ' . get_bloginfo( 'version' ) . " installed.\n";
