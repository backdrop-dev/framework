<?php
/**
 * Helper functions.
 *
 * Helpers are functions designed for quickly accessing data from the container
 * that we need throughout the framework.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop;

use Backdrop\Proxies\App;
use Backdrop\Tools\Collection;

/**
 * Returns the application instance or resolves an item from the container.
 *
 * If an abstract is passed, the corresponding value is resolved from the
 * container. Otherwise, the application instance is returned.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $abstract The abstract type or binding key.
 * @param  array  $params   Parameters to pass when resolving.
 * @return mixed
 */
function app( $abstract = '', $params = [] ) {

	return App::resolve( $abstract ?: 'app', $params );
}

/**
 * Wrapper function for the `Collection` class.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $items Items to add to the collection.
 * @return Collection
 */
function collect( $items = [] ) {

	return new Collection( $items );
}

/**
 * Returns the directory path of the framework. If a file is passed in, it'll be
 * appended to the end of the path.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $file Optional file path to append.
 * @return string
 */
function path( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file ? App::resolve( 'path' ) . "/{$file}" : App::resolve( 'path' );
}

/**
 * Returns the framework version.
 *
 * @since  1.0.0
 * @access public
 *
 * @return string
 */
function version() {

	return App::resolve( 'version' );
}

/**
 * Replaces `%1$s` and `%2$s` with the template and stylesheet directory paths.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $value String containing replacement placeholders.
 * @return string
 */
function sprintf_theme_dir( $value ) {

	return sprintf( $value, get_template_directory(), get_stylesheet_directory() );
}

/**
 * Replaces `%1$s` and `%2$s` with the template and stylesheet directory URIs.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $value String containing replacement placeholders.
 * @return string
 */
function sprintf_theme_uri( $value ) {

	return sprintf( $value, get_template_directory_uri(), get_stylesheet_directory_uri() );
}

/**
 * Converts a hex color to RGB.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $hex Hexadecimal color value.
 * @return array
 */
function hex_to_rgb( $hex ) {

	// Remove "#" if it was added.
	$color = trim( $hex, '#' );

	// If the color is three characters, convert it to six.
	if ( 3 === strlen( $color ) ) {
		$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
	}

	// Get the red, green, and blue values.
	$red   = hexdec( $color[0] . $color[1] );
	$green = hexdec( $color[2] . $color[3] );
	$blue  = hexdec( $color[4] . $color[5] );

	// Return the RGB colors as an array.
	return [
		'r' => $red,
		'g' => $green,
		'b' => $blue,
	];
}

/**
 * Conditional check to determine if we are in script debug mode.
 *
 * This is generally used to decide whether to load development versions of
 * scripts and styles.
 *
 * @since  1.0.0
 * @access public
 *
 * @return bool
 */
function is_script_debug() {

	return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
}

/**
 * Helper function for replacing a class in an HTML string.
 *
 * This function only replaces the first class attribute it comes upon and
 * stops.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $class Class name to use.
 * @param  string $html  HTML string to modify.
 * @return string
 */
function replace_html_class( $class, $html ) {

	return preg_replace(
		"/class=(['\"]).+?(['\"])/i",
		'class=$1' . esc_attr( $class ) . '$2',
		$html,
		1
	);
}

/**
 * Checks if a widget exists.
 *
 * Pass in the widget class name. This function is useful for checking if the
 * widget exists before directly calling `the_widget()` within a template.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $widget Widget class name.
 * @return bool
 */
function widget_exists( $widget ) {

	return isset( $GLOBALS['wp_widget_factory']->widgets[ $widget ] );
}

/**
 * Gets the blog posts page URL.
 *
 * `home_url()` will not always work for this because it returns the front page
 * URL. Sometimes the blog page URL is set to a different page. This function
 * handles both scenarios.
 *
 * @since  1.0.0
 * @access public
 *
 * @return string
 */
function blog_url() {

	$blog_url = '';

	if ( 'posts' === get_option( 'show_on_front' ) ) {
		$blog_url = home_url();

	} elseif ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) ) {
		$blog_url = get_permalink( $page_for_posts );
	}

	return $blog_url ?: '';
}

/**
 * Determines whether the current request is for a plural view.
 *
 * In WordPress and ClassicPress, plural views include archives, search results,
 * and the home/blog posts index.
 *
 * @since  1.0.0
 * @access public
 *
 * @return bool
 */
function is_plural() {

	return is_home() || is_archive() || is_search();
}

/**
 * Determines whether the current installation is running ClassicPress.
 *
 * @since  1.0.0
 * @access public
 *
 * @return bool
 */
function is_classicpress(): bool {

	return function_exists( 'classicpress_version' );
}