<?php
/**
 * Theme functions.
 *
 * Helper functions and template tags related to the theme itself.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Theme;

/**
 * Returns a theme modification value.
 *
 * This is a wrapper around WordPress' `get_theme_mod()` function that provides
 * an additional filter for the default value. This is useful for child themes
 * that need to override defaults.
 *
 * To filter the final theme modification value, use the core
 * `theme_mod_{$name}` filter hook.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string $name    Theme modification name.
 * @param  mixed  $default Default value.
 * @return mixed
 */
function mod( $name, $default = false ) {

	return get_theme_mod(
		$name,
		apply_filters(
			"backdrop/theme/mod/{$name}/default",
			$default
		)
	);
}

/**
 * Outputs the parent theme link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Theme link arguments.
 * @return void
 */
function display_link( array $args = [] ) {

	echo render_link( $args );
}

/**
 * Returns the parent theme link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Theme link arguments.
 * @return string
 */
function render_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'class'  => 'theme-link',
		'before' => '',
		'after'  => ''
	] );

	$theme = wp_get_theme( get_template() );

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);

	return apply_filters(
		'backdrop/theme/link/parent',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the child theme link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Child theme link arguments.
 * @return void
 */
function display_child_link( array $args = [] ) {

	echo render_child_link( $args );
}

/**
 * Returns the child theme link HTML.
 *
 * Returns an empty string when a child theme is not active.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Child theme link arguments.
 * @return string
 */
function render_child_link( array $args = [] ) {

	if ( ! is_child_theme() ) {
		return '';
	}

	$args = wp_parse_args( $args, [
		'class'  => 'child-link',
		'before' => '',
		'after'  => ''
	] );

	$theme = wp_get_theme();

	$allowed = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true
	];

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $theme->display( 'ThemeURI' ) ),
		wp_kses( $theme->display( 'Name' ), $allowed )
	);

	return apply_filters(
		'backdrop/theme/link/child',
		$args['before'] . $html . $args['after']
	);
}