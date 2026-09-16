<?php
/**
 * Site functions.
 *
 * Helper functions and template tags related to the site.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Site;

/**
 * Outputs the site title HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site title arguments.
 * @return void
 */
function display_title( array $args = [] ) {

	echo render_title( $args );
}

/**
 * Returns the site title HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site title arguments.
 * @return string
 */
function render_title( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'class'      => 'site-header__title',
		'link_class' => 'site-header__title-link',
		'tag'        => 'h1'
	] );

	$html  = '';
	$title = get_bloginfo( 'name', 'display' );

	if ( $title ) {
		$link = render_home_link( [
			'text'  => $title,
			'class' => $args['link_class']
		] );

		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $args['class'] ),
			$link
		);
	}

	return apply_filters( 'backdrop/site/title', $html );
}

/**
 * Outputs the site description HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site description arguments.
 * @return void
 */
function display_description( array $args = [] ) {

	echo render_description( $args );
}

/**
 * Returns the site description HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site description arguments.
 * @return string
 */
function render_description( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'class' => 'site-header__description',
		'tag'   => 'div'
	] );

	$html = '';
	$desc = get_bloginfo( 'description', 'display' );

	if ( $desc ) {
		$html = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $args['tag'] ),
			esc_attr( $args['class'] ),
			$desc
		);
	}

	return apply_filters( 'backdrop/site/description', $html );
}

/**
 * Outputs the site link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site link arguments.
 * @return void
 */
function display_site_link( array $args = [] ): void {

	echo render_site_link( $args );
}

/**
 * Returns the site link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Site link arguments.
 * @return string
 */
function render_site_link( array $args = [] ): string {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'site-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $args['class'] ),
		esc_url( home_url( '/' ) ),
		sprintf( $args['text'], get_bloginfo( 'name', 'display' ) )
	);

	return apply_filters(
		'backdrop/render/site/link',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the theme link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Theme link arguments.
 * @return void
 */
function display_theme_link( array $args = [] ): void {

	echo render_theme_link( $args );
}

/**
 * Returns the theme link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Theme link arguments.
 * @return string
 */
function render_theme_link( array $args = [] ): string {

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
		'backdrop/render/theme/link',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the home link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Home link arguments.
 * @return void
 */
function display_home_link( array $args = [] ) {

	echo render_home_link( $args );
}

/**
 * Returns the home link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Home link arguments.
 * @return string
 */
function render_home_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'home-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%s" href="%s" rel="home">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( home_url( '/' ) ),
		sprintf( $args['text'], get_bloginfo( 'name', 'display' ) )
	);

	return apply_filters(
		'backdrop/site/home_link',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the WordPress.org link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args WordPress link arguments.
 * @return void
 */
function display_wp_link( array $args = [] ) {

	echo render_wp_link( $args );
}

/**
 * Returns the WordPress.org link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args WordPress link arguments.
 * @return string
 */
function render_wp_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'wp-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( __( 'https://wordpress.org', 'backdrop' ) ),
		sprintf( $args['text'], esc_html__( 'WordPress', 'backdrop' ) )
	);

	return apply_filters(
		'backdrop/site/wp_link',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the ClassicPress link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args ClassicPress link arguments.
 * @return void
 */
function display_cp_link( array $args = [] ): void {

	echo render_cp_link( $args );
}

/**
 * Returns the ClassicPress link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args ClassicPress link arguments.
 * @return string
 */
function render_cp_link( array $args = [] ): string {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'cp-link',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $args['class'] ),
		esc_url( __( 'https://www.classicpress.net', 'backdrop' ) ),
		sprintf( $args['text'], esc_html__( 'ClassicPress', 'backdrop' ) )
	);

	return apply_filters(
		'backdrop/render/cp/link',
		$args['before'] . $html . $args['after']
	);
}