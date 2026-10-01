<?php
/**
 * Title class.
 *
 * Provides methods for retrieving the current page title across different
 * WordPress page and archive types, including archive types not directly
 * covered by core title functions.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Util;

/**
 * Title utility class.
 *
 * @since  1.0.0
 * @access public
 */
class Title {

	/**
	 * Returns the current page title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function current() {

		$title = '';

		if ( is_front_page() ) {
			$title = static::frontPage();
		} elseif ( is_home() ) {
			$title = static::home();
		} elseif ( is_singular() ) {
			$title = static::post();
		} elseif ( is_archive() ) {
			$title = static::archive();
		} elseif ( is_search() ) {
			$title = static::search();
		} elseif ( is_404() ) {
			$title = static::error();
		}

		return $title;
	}

	/**
	 * Returns the current archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function archive() {

		$title = '';

		if ( is_category() || is_tag() || is_tax() ) {
			$title = static::term();
		} elseif ( is_post_type_archive() ) {
			$title = static::postTypeArchive();
		} elseif ( is_author() ) {
			$title = static::author();
		} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
			$title = static::minuteHour();
		} elseif ( get_query_var( 'minute' ) ) {
			$title = static::minute();
		} elseif ( get_query_var( 'hour' ) ) {
			$title = static::hour();
		} elseif ( is_day() ) {
			$title = static::day();
		} elseif ( get_query_var( 'w' ) ) {
			$title = static::week();
		} elseif ( is_month() ) {
			$title = static::month();
		} elseif ( is_year() ) {
			$title = static::year();
		} else {
			$title = esc_html__( 'Archives', 'backdrop' );
		}

		return $title;
	}

	/**
	 * Returns the front page title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function frontPage() {

		return get_bloginfo( 'name', 'display' );
	}

	/**
	 * Returns the singular post title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function post() {

		return single_post_title( '', false );
	}

	/**
	 * Returns the posts page title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function home() {

		return get_post_field(
			'post_title',
			get_queried_object_id()
		);
	}

	/**
	 * Returns the search results title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function search() {

		return sprintf(
			/* Translators: %s is the search query. */
			esc_html__( 'Search results for: %s', 'backdrop' ),
			get_search_query()
		);
	}

	/**
	 * Returns the 404 page title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function error() {

		return esc_html__( '404 Not Found', 'backdrop' );
	}

	/**
	 * Returns the term archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function term() {

		return single_term_title( '', false );
	}

	/**
	 * Returns the post type archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function postTypeArchive() {

		return post_type_archive_title( '', false );
	}

	/**
	 * Returns the month archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function month() {

		return single_month_title( ' ', false );
	}

	/**
	 * Returns the author archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function author() {

		return get_the_author_meta(
			'display_name',
			absint( get_query_var( 'author' ) )
		);
	}

	/**
	 * Returns the year archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function year() {

		return get_the_date(
			esc_html_x(
				'Y',
				'yearly archives date format',
				'backdrop'
			)
		);
	}

	/**
	 * Returns the week archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function week() {

		return sprintf(
			/* Translators: %1$s is the week number and %2$s is the year. */
			esc_html__( 'Week %1$s of %2$s', 'backdrop' ),
			get_the_time(
				esc_html_x(
					'W',
					'weekly archives date format',
					'backdrop'
				)
			),
			get_the_time(
				esc_html_x(
					'Y',
					'yearly archives date format',
					'backdrop'
				)
			)
		);
	}

	/**
	 * Returns the day archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function day() {

		return get_the_date(
			esc_html_x(
				'F j, Y',
				'daily archives date format',
				'backdrop'
			)
		);
	}

	/**
	 * Returns the hour archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function hour() {

		return get_the_time(
			esc_html_x(
				'g a',
				'hour archives time format',
				'backdrop'
			)
		);
	}

	/**
	 * Returns the minute archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function minute() {

		return sprintf(
			/* Translators: %s is the minute. */
			esc_html__( 'Minute %s', 'backdrop' ),
			get_the_time(
				esc_html_x(
					'i',
					'minute archives time format',
					'backdrop'
				)
			)
		);
	}

	/**
	 * Returns the minute and hour archive title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function minuteHour() {

		return get_the_time(
			esc_html_x(
				'g:i a',
				'minute and hour archives time format',
				'backdrop'
			)
		);
	}
}