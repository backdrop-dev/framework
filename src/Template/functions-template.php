<?php
/**
 * Template functions.
 *
 * Helper functions and template tags related to templates.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Contracts\Template\Hierarchy;
use Backdrop\Proxies\App;

/**
 * Returns the global template hierarchy.
 *
 * This is a wrapper around the values stored by the template hierarchy object.
 *
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function hierarchy() {

	return apply_filters(
		'backdrop/template/hierarchy',
		App::resolve( Hierarchy::class )->hierarchy()
	);
}

/**
 * Locates a template file.
 *
 * This function locates templates without loading them. Use the core
 * `load_template()` function to load a located template.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array|string $templates Template name or array of template names.
 * @return string
 */
function locate( $templates ) {

	foreach ( (array) $templates as $template ) {

		if ( ! $template ) {
			continue;
		}

		$template = ltrim( $template, '/' );

		foreach ( locations() as $location ) {

			$file = trailingslashit( $location ) . $template;

			if ( is_file( $file ) ) {
				return $file;
			}
		}
	}

	return '';
}

/**
 * Returns the relative path where templates are stored in the theme.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $file Optional file path to append.
 * @return string
 */
function path( $file = '' ) {

	$file = ltrim( $file, '/' );
	$path = apply_filters( 'backdrop/template/path', 'resources/views' );

	return $file ? trailingslashit( $path ) . $file : $path;
}

/**
 * Returns the locations to search for template files.
 *
 * The active theme directory is searched first. When a child theme is active,
 * the parent theme directory is searched second.
 *
 * Note that this does not work with the core WordPress template hierarchy due
 * to an issue that has not been addressed since 2010.
 *
 * @link   https://core.trac.wordpress.org/ticket/13239
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function locations() {

	$path = ltrim( path(), '/' );

	$locations = [
		trailingslashit( get_stylesheet_directory() ) . $path
	];

	if ( is_child_theme() ) {
		$locations[] = trailingslashit( get_template_directory() ) . $path;
	}

	return (array) apply_filters(
		'backdrop/template/locations',
		$locations
	);
}

/**
 * Prefixes an array of templates with the configured template path.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $templates Template names.
 * @return array
 */
function filter_templates( $templates ) {

	$path = path();

	if ( $path ) {
		array_walk(
			$templates,
			function( &$template ) use ( $path ) {

				$template = ltrim(
					str_replace( $path, '', $template ),
					'/'
				);

				$template = trailingslashit( $path ) . $template;
			}
		);
	}

	return $templates;
}