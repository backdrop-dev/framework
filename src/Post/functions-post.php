<?php
/**
 * Post functions.
 *
 * Helper functions and template tags related to posts.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Post;

/**
 * Creates a hierarchy based on the current post. Its primary purpose is for
 * use with post views/templates.
 *
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function hierarchy() {

	$hierarchy = [];
	$post_type = get_post_type();

	if ( 'attachment' === $post_type ) {

		extract( mime_types() );

		if ( $subtype ) {
			$hierarchy[] = "attachment-{$type}-{$subtype}";
			$hierarchy[] = "attachment-{$subtype}";
		}

		$hierarchy[] = "attachment-{$type}";
	}

	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		$post_format = get_post_format() ?: 'standard';

		$hierarchy[] = "{$post_type}-{$post_format}";
		$hierarchy[] = $post_format;
	}

	$hierarchy[] = $post_type;

	return apply_filters( 'backdrop/post/hierarchy', $hierarchy );
}

/**
 * Outputs the post title HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post title arguments.
 * @return void
 */
function display_title( array $args = [] ) {

	echo render_title( $args );
}

/**
 * Returns the post title HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post title arguments.
 * @return string
 */
function render_title( array $args = [] ) {

	$post_id   = get_the_ID();
	$is_single = is_single( $post_id ) || is_page( $post_id ) || is_attachment( $post_id );

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'tag'    => $is_single ? 'h1' : 'h2',
		'link'   => ! $is_single,
		'class'  => 'entry__title',
		'before' => '',
		'after'  => ''
	] );

	$text = sprintf(
		$args['text'],
		$is_single ? single_post_title( '', false ) : the_title( '', '', false )
	);

	if ( $args['link'] ) {
		$text = sprintf(
			'<a class="entry__permalink" href="%s">%s</a>',
			esc_url( get_permalink( $post_id ) ),
			$text
		);
	}

	$html = sprintf(
		'<%1$s class="%2$s">%3$s</%1$s>',
		tag_escape( $args['tag'] ),
		esc_attr( $args['class'] ),
		$text
	);

	return apply_filters(
		'backdrop/post/title',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the post permalink HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post permalink arguments.
 * @return void
 */
function display_permalink( array $args = [] ) {

	echo render_permalink( $args );
}

/**
 * Returns the post permalink HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post permalink arguments.
 * @return string
 */
function render_permalink( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'entry__permalink',
		'before' => '',
		'after'  => ''
	] );

	$url       = get_permalink();
	$link_text = $args['text'];

	if ( false !== strpos( $link_text, '%s' ) ) {
		$link_text = sprintf( $link_text, esc_html( $url ) );
	}

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $url ),
		$link_text
	);

	return apply_filters(
		'backdrop/post/permalink',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the post author HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post author arguments.
 * @return void
 */
function display_author( array $args = [] ) {

	echo render_author( $args );
}

/**
 * Returns the post author HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post author arguments.
 * @return string
 */
function render_author( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'entry__author',
		'link'   => true,
		'before' => '',
		'after'  => ''
	] );

	$author_id = get_the_author_meta( 'ID' );

	if ( ! $author_id ) {
		$queried_object = get_queried_object();
		$author_id       = isset( $queried_object->post_author )
			? $queried_object->post_author
			: null;
	}

	if ( ! $author_id ) {
		return '';
	}

	$author = get_the_author_meta( 'display_name', $author_id );

	if ( $args['link'] ) {
		$url = get_author_posts_url( $author_id );

		$author = sprintf(
			'<a class="entry__author-link" href="%s">%s</a>',
			esc_url( $url ),
			$author
		);
	}

	$html = sprintf(
		'<span class="%s">%s</span>',
		esc_attr( $args['class'] ),
		$author
	);

	return apply_filters(
		'backdrop/post/author',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the post date HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post date arguments.
 * @return void
 */
function display_date( array $args = [] ) {

	echo render_date( $args );
}

/**
 * Returns the post date HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post date arguments.
 * @return string
 */
function render_date( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'entry__published',
		'format' => '',
		'before' => '',
		'after'  => ''
	] );

	$html = sprintf(
		'<time class="%s" datetime="%s">%s</time>',
		esc_attr( $args['class'] ),
		esc_attr( get_the_date( DATE_W3C ) ),
		sprintf( $args['text'], get_the_date( $args['format'] ) )
	);

	return apply_filters(
		'backdrop/post/date',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the post comments link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Comments link arguments.
 * @return void
 */
function display_comments_link( array $args = [] ) {

	echo render_comments_link( $args );
}

/**
 * Returns the post comments link HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Comments link arguments.
 * @return string
 */
function render_comments_link( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'zero'   => false,
		'one'    => false,
		'more'   => false,
		'class'  => 'entry__comments',
		'before' => '',
		'after'  => ''
	] );

	$number = get_comments_number();

	if ( 0 === $number && ! comments_open() && ! pings_open() ) {
		return '';
	}

	$url  = get_comments_link();
	$text = get_comments_number_text(
		$args['zero'],
		$args['one'],
		$args['more']
	);

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $url ),
		$text
	);

	return apply_filters(
		'backdrop/post/comments',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Outputs the post terms HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post terms arguments.
 * @return void
 */
function display_terms( array $args = [] ) {

	echo render_terms( $args );
}

/**
 * Returns the post terms HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post terms arguments.
 * @return string
 */
function render_terms( array $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'taxonomy' => 'category',
		'text'     => '%s',
		'class'    => '',
		// Translators: Separates tags, categories, etc. when displaying a post.
		'sep'      => _x( ', ', 'taxonomy terms separator', 'backdrop' ),
		'before'   => '',
		'after'    => ''
	] );

	if ( ! $args['class'] ) {
		$args['class'] = "entry__terms entry__terms--{$args['taxonomy']}";
	}

	$terms = get_the_term_list(
		get_the_ID(),
		$args['taxonomy'],
		'',
		$args['sep'],
		''
	);

	if ( $terms ) {
		$html = sprintf(
			'<span class="%s">%s</span>',
			esc_attr( $args['class'] ),
			sprintf( $args['text'], $terms )
		);

		$html = $args['before'] . $html . $args['after'];
	}

	return apply_filters( 'backdrop/post/terms', $html );
}

/**
 * Outputs the post format HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post format arguments.
 * @return void
 */
function display_format( array $args = [] ) {

	echo render_format( $args );
}

/**
 * Returns the post format HTML.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  array $args Post format arguments.
 * @return string
 */
function render_format( array $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'class'  => 'entry__format',
		'before' => '',
		'after'  => ''
	] );

	$format = get_post_format();
	$url    = $format ? get_post_format_link( $format ) : get_permalink();
	$string = get_post_format_string( $format );

	$html = sprintf(
		'<a class="%s" href="%s">%s</a>',
		esc_attr( $args['class'] ),
		esc_url( $url ),
		sprintf( $args['text'], $string )
	);

	return apply_filters(
		'backdrop/post/format',
		$args['before'] . $html . $args['after']
	);
}

/**
 * Splits the post MIME type into two distinct parts: type and subtype.
 *
 * For example, `image/png` is returned as `image` and `png`.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  \WP_Post|int|null $post Post object or ID.
 * @return array
 */
function mime_types( $post = null ) {

	$type    = get_post_mime_type( $post );
	$subtype = '';

	if ( false !== strpos( $type, '/' ) ) {
		list( $type, $subtype ) = explode( '/', $type, 2 );
	}

	return [
		'type'    => $type,
		'subtype' => $subtype
	];
}

/**
 * Checks if a post has any content.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  \WP_Post|int|null $post Post object or ID.
 * @return bool
 */
function has_content( $post = null ) {

	$post = get_post( $post );

	return $post && ! empty( $post->post_content );
}

/**
 * Returns the number of items in all galleries for the post.
 *
 * If the post does not contain galleries, attached images are counted instead.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  \WP_Post|int|null $post Post object or ID.
 * @return int
 */
function gallery_count( $post = null ) {

	$post = get_post( $post );

	if ( ! $post ) {
		return 0;
	}

	$images = [];

	foreach ( get_post_galleries_images( $post ) as $gallery_images ) {
		$images = array_merge( $images, $gallery_images );
	}

	if ( ! $images ) {
		$images = get_posts( [
			'fields'         => 'ids',
			'post_parent'    => $post->ID,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => -1
		] );
	}

	return count( $images );
}