<?php
/**
 * Mix Manifest functions.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/benlumia007/backdrop-mix-manifest
 */

namespace Backdrop\Mix;
use Backdrop\App;

function asset( $path ) {

	// Get the Laravel Mix manifest.
	$manifest = App::resolve('backdrop/mix/parent');

	// Make sure to trim any slashes from the front of the path.
	$path = '/' . ltrim($path, '/');

	// Retrieve the path from the manifest, or null if not found.
	$manifestPath = $manifest[$path] ?? null;

	// Get the parent theme's directory URI.
	$themeDirectoryUri = get_template_directory_uri();

	// Construct the URL with the desired path from the manifest.
	return trailingslashit( $themeDirectoryUri ) . 'public' . $manifestPath;
}

function childAsset( $path ) {

	// Get the Laravel Mix manifest.
	$manifest = App::resolve( 'backdrop/mix/child' );

	// Make sure to trim any slashes from the front of the path.
	$path = '/' . ltrim( $path, '/' );

	// Retrieve the path from the manifest, or null if not found.
	$manifestPath = $manifest[$path] ?? null;

	// Get the theme's directory URI.
	$themeDirectoryUri = wp_get_theme()->get_stylesheet_directory_uri();

	// Construct the URL with the desired path from the manifest.
	return trailingslashit( $themeDirectoryUri ) . 'public' . $manifestPath;
}

function pluginAsset( $path ) {

	// Get the Laravel Mix manifest.
	$manifest = App::resolve( 'backdrop/mix/plugin' );

	// Make sure to trim any slashes from the front of the path.
	$path = '/' . ltrim( $path, '/' );

	// Retrieve the path from the manifest, or null if not found.
	$manifestPath = $manifest[ $path ] ?? null;

	return dirname( dirname( dirname( plugin_dir_url( __DIR__ )  )  ) ) . '/public' . $manifestPath;
}