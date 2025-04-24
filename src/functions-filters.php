<?php
/**
 * Filter functions.
 *
 * Filters for theme-related WordPress features.  These filters are for handling
 * adding or modifying the output of common WordPress template tags to make for
 * a richer theme development experience.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop;

use WP_User;
use Backdrop\Util\Title;
use function Backdrop\Template\locate as locate_template;

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but
 * they're not registered.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function post_type_support() {

	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', 'excerpt' );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );
}

/**
 * Adds the meta charset to the header.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function meta_charset() {

	echo apply_filters(
		'backdrop/head/meta/charset',
		sprintf( '<meta charset="%s" />' . "\n", esc_attr( get_bloginfo( 'charset' ) ) )
	);
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function meta_viewport() {

	echo apply_filters(
		'backdrop/head/meta/viewport',
		'<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n"
	);
}

/**
 * Adds the theme generator meta tag.  This is particularly useful for checking
 * theme users' version when handling support requests.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function meta_generator() {
	$theme = wp_get_theme( \get_template() );

	$generator = sprintf(
		'<meta name="generator" content="%s %s" />' . "\n",
		esc_attr( $theme->get( 'Name' ) ),
		esc_attr( $theme->get( 'Version' ) )
	);

	echo apply_filters( 'backdrop/head/meta/generator', $generator );
}

/**
 * Adds the pingback link to the header.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function link_pingback() {

	$link = '';

	if ( 'open' === get_option( 'default_ping_status' ) ) {

		$link = sprintf(
			'<link rel="pingback" href="%s" />' . "\n",
			esc_url( get_bloginfo( 'pingback_url' ) )
		);
	}

	echo apply_filters( 'backdrop/head/link/pingback', $link );
}

/**
 * Replacement for the older filter on `wp_title` since WP has moved to a new
 * document title system.  This new filter merely alters the `title` key based
 * on the current page being viewed.  It also makes sure that all tags are
 * stripped, which WP doesn't do by default (it escapes HTML).
 *
 * @since  1.0.0
 * @access public
 * @param  array   $doctitle
 * @return array
 */
function document_title_parts( $doctitle ) {

	$doctitle['title'] = Title::current();

	// Return the title and make sure to strip tags.
	return array_map( 'strip_tags', $doctitle );
}

/**
 * Filters `get_the_archve_title` to add better archive titles than core.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $title
 * @return string
 */
function archive_title_filter( $title ) {

	return apply_filters( 'backdrop/archive/title', Title::current() );
}

/**
 * Filters `get_the_archve_description` to add better archive descriptions than core.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function archive_description_filter( $desc ) {

	$new_desc = '';

	if ( is_home() && ! is_front_page() ) {
		$new_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	} elseif ( is_category() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	} elseif ( is_tag() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	} elseif ( is_tax() ) {
		$new_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	} elseif ( is_author() ) {
		$new_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	} elseif ( is_post_type_archive() ) {
		$new_desc = get_the_post_type_description();
	}

	return $new_desc ?: $desc;
}

/**
 * Filters `get_the_archve_description` to add custom formatting.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function archive_description_format( $desc ) {

	return apply_filters( 'backdrop/archive/description', $desc );
}

/**
 * The WordPress.org theme review requires that a link be provided to the single
 * post page for untitled posts.  This is a filter on 'the_title' so that an
 * `(Untitled)` title appears in that scenario, allowing for the normal method
 * to work.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $title
 * @return string
 */
function untitled_post( $title ) {

	// Translators: Used as a placeholder for untitled posts on non-singular views.
	if ( ! $title && ! is_singular() && in_the_loop() && ! is_admin() ) {

		$title = esc_html__( '(Untitled)', 'backdrop' );
	}

	return $title;
}

/**
 * Filters the excerpt more output with internationalized text and a link to the post.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $text
 * @return string
 */
function excerpt_more( $text ) {

	if ( 0 !== strpos( $text, '<a' ) ) {

		$text = sprintf(
			' <a href="%s" class="entry__more-link">%s</a>',
			esc_url( get_permalink() ),
			trim( $text )
		);
	}

	return $text;
}

/**
 * Adds custom classes to the core WP logo.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $logo
 * @return string
 */
function custom_logo_class( $logo ) {

	$logo = preg_replace(
		"/(<a.+?)class=(['\"])(.+?)(['\"])/i",
		'$1class=$2app-header__logo-link $3$4',
		$logo,
		1
	);

	return preg_replace(
		"/(<img.+?)class=(['\"])(.+?)(['\"])/i",
		'$1class=$2app-header__logo $3$4',
		$logo,
		1
	);
}

/**
 * Overrides the default comments template.  This filter allows for a
 * `comments-{$post_type}.php` template based on the post type of the current
 * single post view.  If this template is not found, it falls back to the
 * default `comments.php` template.
 *
 * @since  1.0.0
 * @access public
 * @param  string $template
 * @return string
 */
function comments_template( $template ) {

	$templates = [];

	// Allow for custom templates entered into comments_template( $file ).
	$template = str_replace( trailingslashit( get_stylesheet_directory() ), '', $template );

	if ( 'comments.php' !== $template ) {
		$templates[] = $template;
	}

	// Add a comments template based on the post type.
	$templates[] = sprintf( 'comments/%s.php', get_post_type() );

	// Add the default comments template.
	$templates[] = 'comments/default.php';
	$templates[] = 'comments.php';

	// Return the found template.
	return locate_template( $templates );
}

/**
 * Fix for users who want to display content on the posts page above the posts
 * list, which is a theme feature common to themes built from the framework.
 *
 * @since  1.0.0
 * @access public
 * @param  object  $post
 * @return void
 */
function enable_posts_page_editor( $post ) {

	if ( get_option( 'page_for_posts' ) != $post->ID ) {
		return;
	}

	remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
	add_post_type_support( $post->post_type, 'editor' );
}

/**
 * Filters the WordPress body class with a better set of classes that are more
 * consistently handled and are backwards compatible with the original body
 * class functionality that existed prior to WordPress core adopting this feature.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $classes
 * @param  array  $class
 * @return array
 */
function body_class_filter( $classes, $class ) {

	$classes = [];

	// Text direction.
	$classes[] = is_rtl() ? 'rtl' : 'ltr';

	// Locale and language.
	$locale = get_locale();
	$lang   = substr( $locale, 0, strpos( $locale, '_' ) );

	if ( $lang && $locale !== $lang ) {
		$classes[] = $lang;
	}

	$classes[] = strtolower( str_replace( '_', '-', $locale ) );

	// Multisite check adds the 'multisite' class and the blog ID.
	if ( is_multisite() ) {
		$classes[] = 'multisite';
	}

	// Plural/multiple-post view (opposite of singular).
	if ( is_home() || is_archive() || is_search() ) {
		$classes[] = 'plural';
	}

	// Front page of the site.
	if ( is_front_page() ) {
		$classes[] = 'home';
	}

	// Blog page.
	if ( is_home() ) {
		$classes[] = 'blog';

	// Singular views.
	} elseif ( is_singular() ) {

		// Get the queried post object.
		$post      = get_queried_object();
		$post_id   = get_queried_object_id();
		$post_type = $post->post_type;

		$classes[] = 'single';
		$classes[] = "single-{$post_type}";
		$classes[] = "single-{$post_type}-{$post_id}";

		// Checks for custom template.
		$template = str_replace(
			[ "{$post_type}-template-", "{$post_type}-", 'template-', 'tmpl-' ],
			'',
			basename( get_page_template_slug( $post_id ), '.php' )
		);

		$classes[] = $template ? "{$post_type}-template-{$template}" : "{$post_type}-template-default";

		// Post format.
		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) ) {
			$post_format = \get_post_format( $post_id );

			$classes[] = $post_format && ! is_wp_error( $post_format ) ? "{$post_type}-format-{$post_format}" : "{$post_type}-format-standard";
		}

		// Attachment mime types.
		if ( is_attachment() ) {

			foreach ( explode( '/', get_post_mime_type() ) as $type ) {
				$classes[] = "attachment-{$type}";
			}
		}

	// Archive views.
	} elseif ( is_archive() ) {
		$classes[] = 'archive';

		// Post type archives.
		if ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );

			$classes[] = sprintf(
				'archive-%s',
				is_array( $post_type ) ? reset( $post_type ) : $post_type
			);
		}

		// Taxonomy archives.
		if ( is_tax() || is_category() || is_tag() ) {

			// Get the queried term object.
			$term     = get_queried_object();
			$term_id  = get_queried_object_id();
			$taxonomy = $term->taxonomy;

			$slug = 'post_format' === $taxonomy ? str_replace( 'post-format-', '', $term->slug ) : $term->slug;

			$classes[] = 'taxonomy';
			$classes[] = "taxonomy-{$taxonomy}";
			$classes[] = "taxonomy-{$taxonomy}-" . sanitize_html_class( $slug, $term_id );
		}

		// User/author archives.
		if ( is_author() ) {
			$user_id = get_query_var( 'author' );

			$classes[] = 'author';
			$classes[] = 'author-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $user_id ), $user_id );
		}

		// Date archives.
		if ( is_date() ) {
			$classes[] = 'date';

			if ( is_year() ) {
				$classes[] = 'year';
			}

			if ( is_month() ) {
				$classes[] = 'month';
			}

			if ( get_query_var( 'w' ) ) {
				$classes[] = 'week';
			}

			if ( is_day() ) {
				$classes[] = 'day';
			}
		}

		// Time archives.
		if ( is_time() ) {
			$classes[] = 'time';

			if ( get_query_var( 'hour' ) ) {
				$classes[] = 'hour';
			}

			if ( get_query_var( 'minute' ) ) {
				$classes[] = 'minute';
			}
		}
	}

	// Search results.
	elseif ( is_search() ) {
		$classes[] = 'search';
	}

	// Error 404 pages.
	elseif ( is_404() ) {
		$classes[] = 'error-404';
	}

	// Paged views.
	if ( is_paged() ) {
		$classes[] = 'paged';
		$classes[] = 'paged-' . intval( get_query_var( 'paged' ) );

	// Singular post paged views using <!- nextpage ->.
	} elseif ( is_singular() && 1 < get_query_var( 'page' ) ) {
		$classes[] = 'paged';
		$classes[] = 'paged-' . intval( get_query_var( 'page' ) );
	}

	// Is the current user logged in.
	$classes[] = is_user_logged_in() ? 'logged-in' : 'logged-out';

	// WP admin bar.
	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
	}

	// Use the '.custom-background' class to integrate with the WP background feature.
	if ( get_background_image() || get_background_color() ) {
		$classes[] = 'custom-background';
	}

	// Add the '.custom-header' class if the user is using a custom header.
	if ( get_header_image() || ( display_header_text() && get_header_textcolor() ) ) {
		$classes[] = 'custom-header';
	}

	// Add the `.custom-logo` class if user is using a custom logo.
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$classes[] = 'wp-custom-logo';
	}

	// Add the `.wp-embed-responsive` class if the theme supports it.
	if ( current_theme_supports( 'responsive-embeds' ) ) {
		$classes[] = 'wp-embed-responsive';
	}

	// Add the '.display-header-text' class if the user chose to display it.
	if ( display_header_text() ) {
		$classes[] = 'display-header-text';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}

/**
 * Filters the WordPress post class with a better set of classes that are more
 * consistently handled and are backwards compatible with the original post
 * class functionality that existed prior to WordPress core adopting this feature.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $classes
 * @param  array  $class
 * @param  int    $post_id
 * @return array
 */
function post_class_filter( $classes, $class, $post_id ) {

	if ( is_admin() ) {
		return $classes;
	}

	$classes = [];
	$post    = get_post( $post_id );

	// Entry class.
	$classes[] = 'entry';

	// Post field classes.
	$classes[] = sprintf( 'entry-type-%s', get_post_type() );

	// Add post formt class.
	if ( post_type_supports( get_post_type(), 'post-formats' ) ) {

		$format = \get_post_format();

		$classes[] = sprintf(
			'entry-format-%s',
			$format && ! is_wp_error( $format ) ? $format : 'standard'
		);
	}

	// Add taxonomy term classes.  By default, no taxonomies (except for
	// post formats added above) are added.
	$taxonomies = apply_filters( 'backdrop/attr/post/class/taxonomy', [] );

	foreach ( (array) $taxonomies as $taxonomy ) {

		if ( is_object_in_taxonomy( get_post_type(), $taxonomy ) ) {

			$terms = get_the_terms( $post_id, $taxonomy );

			foreach ( (array) $terms as $term ) {

				$name = 'post_tag' === $taxonomy ? 'tag' : $taxonomy;
				$slug = sanitize_html_class( $term->slug, $term->term_id );

				$classes[] = sprintf( 'entry-%s-%s', $name, $slug );
			}
		}
	}

	// Sticky posts.
	if ( is_home() && ! is_paged() && is_sticky( $post_id ) ) {
		$classes[] = 'is-sticky';
	}

	// Password-protected posts.
	if ( post_password_required( $post_id ) ) {
		$classes[] = 'post-password-required';
	} elseif ( $post->post_password ) {
		$classes[] = 'post-password-protected';
	}

	// Post thumbnails.
	if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post_id ) ) {
		$classes[] = 'has-post-thumbnail';
	}

	// Has excerpt.
	if ( post_type_supports( get_post_type(), 'excerpt' ) && has_excerpt() ) {
		$classes[] = 'has-excerpt';
	}

	// Has <!-more-> link.
	if ( ! is_singular() && false !== strpos( $post->post_content, '<!-more' ) ) {
		$classes[] = 'has-more-link';
	}

	// Has <!-nextpage-> links.
	if ( false !== strpos( $post->post_content, '<!-nextpage' ) ) {
		$classes[] = 'has-pages';
	}

	return array_map( 'esc_attr', array_unique( array_merge( $classes, (array) $class ) ) );
}