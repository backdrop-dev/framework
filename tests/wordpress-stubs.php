<?php
/**
 * Minimal WordPress function stubs for the test suite.
 *
 * Backdrop runs inside ClassicPress or WordPress, so the tests provide small,
 * predictable versions of the core functions the framework calls. The values
 * that tests need to control live in `$GLOBALS['wp_test']` and can be reset
 * with `wp_test_reset()`.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

// phpcs:disable

/**
 * Resets the test state that the stubs read from.
 *
 * Registered hooks are kept, because the framework registers some of them once
 * when the application is created.
 *
 * @param  array $state Values to override.
 * @return void
 */
function wp_test_reset( array $state = [] ) {

	$GLOBALS['wp_test'] = array_merge( [
		'locale'        => 'en_US',
		'is_rtl'        => false,
		'is_admin'      => false,
		'is_child'      => false,
		'conditionals'  => [],
		'options'       => [],
		'query_vars'    => [],
		'pagenum_link'  => 'https://example.com/',
		'permalink'     => 'https://example.com/hello-world/',
		'term_field'    => '',
		'comments'      => [],
		'comment'       => null,
		'comment_type'  => 'comment',
		'reply_link'    => '',
		'comments_num'  => '0',
		'comments_open' => false,
		'pings_open'    => false,
	], $state );

	$GLOBALS['wp_query'] = (object) [ 'max_num_pages' => 1 ];

	$GLOBALS['wp_rewrite'] = new WP_Rewrite();
}

/**
 * Returns a test state value.
 *
 * @param  string $key     State key.
 * @param  mixed  $default Default value.
 * @return mixed
 */
function wp_test( $key, $default = null ) {

	return array_key_exists( $key, $GLOBALS['wp_test'] ) ? $GLOBALS['wp_test'][ $key ] : $default;
}

/**
 * Stub classes.
 */
class WP_Error {

	public $message;

	public function __construct( $code = '', $message = '' ) {
		$this->message = $message;
	}
}

class WP_Rewrite {

	public $pagination_base          = 'page';
	public $comments_pagination_base = 'comment-page';

	public function using_permalinks() {
		return false;
	}

	public function using_index_permalinks() {
		return false;
	}
}

class WP_Theme_Stub {

	public function get( $header ) {
		return 'TextDomain' === $header ? 'backdrop-test' : '';
	}

	public function display( $header ) {
		return $this->get( $header );
	}
}

class WP_User {

	public $roles = [];
}

class WP_Customize_Manager {}

class WP_Customize_Control {

	public $id      = '';
	public $choices = [];
	public $json    = [];

	public function to_json() {}

	public function get_link() {
		return '';
	}

	public function value() {
		return '';
	}
}

/**
 * Hooks.
 *
 * Callbacks are stored by priority and actually run, so tests can use the
 * framework's filter hooks.
 */
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {

	$GLOBALS['wp_test_hooks'][ $hook ][ $priority ][] = [ $callback, $accepted_args ];

	return true;
}

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {

	return add_filter( $hook, $callback, $priority, $accepted_args );
}

function remove_filter( $hook, $callback, $priority = 10 ) {

	if ( empty( $GLOBALS['wp_test_hooks'][ $hook ][ $priority ] ) ) {
		return false;
	}

	foreach ( $GLOBALS['wp_test_hooks'][ $hook ][ $priority ] as $index => $registered ) {
		if ( $registered[0] === $callback ) {
			unset( $GLOBALS['wp_test_hooks'][ $hook ][ $priority ][ $index ] );
			return true;
		}
	}

	return false;
}

function remove_action( $hook, $callback, $priority = 10 ) {

	return remove_filter( $hook, $callback, $priority );
}

function has_filter( $hook ) {

	return ! empty( $GLOBALS['wp_test_hooks'][ $hook ] );
}

function apply_filters( $hook, $value = null, ...$args ) {

	if ( empty( $GLOBALS['wp_test_hooks'][ $hook ] ) ) {
		return $value;
	}

	$callbacks = $GLOBALS['wp_test_hooks'][ $hook ];
	ksort( $callbacks );

	foreach ( $callbacks as $registered ) {
		foreach ( $registered as list( $callback, $accepted_args ) ) {
			$value = call_user_func_array(
				$callback,
				array_slice( array_merge( [ $value ], $args ), 0, $accepted_args )
			);
		}
	}

	return $value;
}

function do_action( $hook, ...$args ) {

	if ( empty( $GLOBALS['wp_test_hooks'][ $hook ] ) ) {
		return;
	}

	$callbacks = $GLOBALS['wp_test_hooks'][ $hook ];
	ksort( $callbacks );

	foreach ( $callbacks as $registered ) {
		foreach ( $registered as list( $callback, $accepted_args ) ) {
			call_user_func_array( $callback, array_slice( $args, 0, $accepted_args ) );
		}
	}
}

/**
 * Escaping and translation.
 */
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_url( $url ) {
	return str_replace( [ '&', "'" ], [ '&#038;', '&#039;' ], trim( (string) $url ) );
}

function tag_escape( $tag ) {
	return strtolower( preg_replace( '/[^a-zA-Z0-9_:]/', '', $tag ) );
}

function __( $text, $domain = 'default' ) {
	return $text;
}

function _x( $text, $context, $domain = 'default' ) {
	return $text;
}

function esc_html__( $text, $domain = 'default' ) {
	return esc_html( $text );
}

function esc_html_x( $text, $context, $domain = 'default' ) {
	return esc_html( $text );
}

/**
 * Formatting and utilities.
 */
function wp_parse_args( $args, $defaults = [] ) {

	if ( is_object( $args ) ) {
		$args = get_object_vars( $args );
	} elseif ( ! is_array( $args ) ) {
		parse_str( (string) $args, $args );
	}

	return array_merge( $defaults, $args );
}

function absint( $value ) {
	return abs( (int) $value );
}

function trailingslashit( $value ) {
	return untrailingslashit( $value ) . '/';
}

function untrailingslashit( $value ) {
	return rtrim( (string) $value, '/\\' );
}

function user_trailingslashit( $value, $type = '' ) {
	return trailingslashit( $value );
}

function sanitize_key( $key ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
}

function sanitize_html_class( $class, $fallback = '' ) {
	$class = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', (string) $class );
	$class = preg_replace( '/[^A-Za-z0-9_-]/', '', $class );
	return '' === $class ? (string) $fallback : $class;
}

function number_format_i18n( $number, $decimals = 0 ) {
	return number_format( (float) $number, $decimals );
}

function wp_parse_str( $string, &$array ) {
	parse_str( (string) $string, $array );
}

function urlencode_deep( $value ) {
	return is_array( $value ) ? array_map( 'urlencode_deep', $value ) : urlencode( (string) $value );
}

function add_query_arg( $args, $url ) {

	$parts = explode( '?', (string) $url, 2 );
	$query = [];

	if ( isset( $parts[1] ) && '' !== $parts[1] ) {
		foreach ( explode( '&', $parts[1] ) as $pair ) {
			$pair              = explode( '=', $pair, 2 );
			$query[ $pair[0] ] = isset( $pair[1] ) ? $pair[1] : '';
		}
	}

	foreach ( (array) $args as $key => $value ) {
		$query[ $key ] = $value;
	}

	$pairs = [];

	foreach ( $query as $key => $value ) {
		$pairs[] = "{$key}={$value}";
	}

	return $parts[0] . ( $pairs ? '?' . implode( '&', $pairs ) : '' );
}

function is_wp_error( $thing ) {
	return $thing instanceof WP_Error;
}

function get_option( $option, $default = false ) {
	$options = wp_test( 'options', [] );
	return array_key_exists( $option, $options ) ? $options[ $option ] : $default;
}

/**
 * Environment, theme, and locale.
 */
function is_admin() {
	return (bool) wp_test( 'is_admin' );
}

function is_rtl() {
	return (bool) wp_test( 'is_rtl' );
}

function get_locale() {
	return wp_test( 'locale' );
}

function get_user_locale() {
	return wp_test( 'locale' );
}

function is_child_theme() {
	return (bool) wp_test( 'is_child' );
}

function get_template() {
	return 'backdrop-test';
}

function get_template_directory() {
	return BACKDROP_TEST_THEME_DIR;
}

function get_stylesheet_directory() {
	return BACKDROP_TEST_THEME_DIR;
}

function get_template_directory_uri() {
	return 'https://example.com/wp-content/themes/backdrop-test';
}

function get_stylesheet_directory_uri() {
	return 'https://example.com/wp-content/themes/backdrop-test';
}

function wp_get_theme( $stylesheet = '' ) {
	return new WP_Theme_Stub();
}

function load_textdomain( $domain, $mofile ) {
	return false;
}

/**
 * Conditional tags read from `$GLOBALS['wp_test']['conditionals']`.
 */
foreach ( [
	'is_home', 'is_front_page', 'is_singular', 'is_single', 'is_page',
	'is_archive', 'is_search', 'is_404', 'is_attachment', 'is_category',
	'is_tag', 'is_tax', 'is_author', 'is_post_type_archive', 'is_date',
	'is_year', 'is_month', 'is_day', 'is_time', 'is_paged', 'in_the_loop',
	'is_multisite', 'is_user_logged_in',
] as $wp_test_conditional ) {
	eval( "function {$wp_test_conditional}( ...\$args ) { \$c = wp_test( 'conditionals', [] ); return ! empty( \$c['{$wp_test_conditional}'] ); }" );
}

unset( $wp_test_conditional );

/**
 * Queries, posts, terms, and pagination.
 */
function get_query_var( $var, $default = '' ) {
	$vars = wp_test( 'query_vars', [] );
	return array_key_exists( $var, $vars ) ? $vars[ $var ] : $default;
}

function get_queried_object_id() {
	return 1;
}

function get_queried_object() {
	return null;
}

function get_term_field( $field, $term, $taxonomy = '', $context = 'display' ) {
	return wp_test( 'term_field' );
}

function get_pagenum_link( $pagenum = 1 ) {
	return wp_test( 'pagenum_link' );
}

function get_permalink( $post = 0 ) {
	return wp_test( 'permalink' );
}

function get_post_type( $post = null ) {
	return 'post';
}

function get_comments_number( $post = 0 ) {
	return wp_test( 'comments_num' );
}

function comments_open( $post = null ) {
	return (bool) wp_test( 'comments_open' );
}

function pings_open( $post = null ) {
	return (bool) wp_test( 'pings_open' );
}

function get_comments_link( $post = 0 ) {
	return wp_test( 'permalink' ) . '#comments';
}

function get_comments_number_text( $zero = false, $one = false, $more = false ) {
	return wp_test( 'comments_num' ) . ' Comments';
}

/**
 * Comments.
 */
function get_comment( $comment = null ) {

	$comments = wp_test( 'comments', [] );

	if ( null === $comment ) {
		$comment = wp_test( 'comment' );
	}

	if ( is_object( $comment ) ) {
		return $comment;
	}

	return isset( $comments[ $comment ] ) ? $comments[ $comment ] : null;
}

function wp_get_comment_status( $comment_id ) {

	$comment = get_comment( $comment_id );

	return $comment ? $comment->status : false;
}

function get_comment_type( $comment = null ) {
	return wp_test( 'comment_type' );
}

function get_comment_reply_link( $args = [] ) {
	return wp_test( 'reply_link' );
}
