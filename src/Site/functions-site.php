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
 * @param  array  $args
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
 * @param  array  $args
 * @return string
 */
function render_title( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'        => 'h1',
		'class'      => 'site-header-title',
		'link_class' => 'site-header-title-link'
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
 * @param  array  $args
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
 * @param  array  $args
 * @return string
 */
function render_description( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'tag'   => 'div',
		'class' => 'app-header__description',
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
 * @param  array  $args
 * @return void
 */
function display_home_link( array $args = [] ) {

	echo render_home_link( $args );
}

/**
 * Returns the site link HTML.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $args
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
		esc_url( home_url() ),
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
 * @param  array  $args
 * @return void
 */
function display_wp_link( array $args = [] ) {

	echo render_wp_link();
}

/**
 * Returns the WordPress.org link HTML.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $args
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