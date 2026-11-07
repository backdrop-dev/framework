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

// Content for the theme output checks: a post in a month archive, and a menu
// with one item without custom classes and one with a custom class.
wp_insert_post( [
	'post_title'  => 'March post',
	'post_status' => 'publish',
	'post_date'   => '2026-03-15 10:00:00',
] );

$menu = wp_create_nav_menu( 'Backdrop Test Menu' );

wp_update_nav_menu_item( $menu, 0, [
	'menu-item-object-id' => 2,
	'menu-item-object'    => 'page',
	'menu-item-type'      => 'post_type',
	'menu-item-status'    => 'publish',
] );

wp_update_nav_menu_item( $menu, 0, [
	'menu-item-title'   => 'Custom',
	'menu-item-url'     => 'https://example.org/',
	'menu-item-classes' => 'my-class',
	'menu-item-status'  => 'publish',
] );

echo 'WordPress ' . get_bloginfo( 'version' ) . " installed.\n";
