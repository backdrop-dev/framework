<?php
/**
 * Filter/Action bootstrap.
 *
 * Any actions/filters added off-the-bat get added here so that they're only
 * called in the WP environment.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop;

# Filters the WordPress element classes.
add_filter( 'body_class',    __NAMESPACE__ . '\body_class_filter',    ~PHP_INT_MAX, 2 );
add_filter( 'post_class',    __NAMESPACE__ . '\post_class_filter',    ~PHP_INT_MAX, 3 );

# Add extra support for post types.
add_action( 'init', __NAMESPACE__ . '\post_type_support', 15 );

# Filters the archive title and description.
add_filter( 'get_the_archive_title',       __NAMESPACE__ . '\archive_title_filter',       5           );
add_filter( 'get_the_archive_description', __NAMESPACE__ . '\archive_description_filter', 0           );
add_filter( 'get_the_archive_description', __NAMESPACE__ . '\archive_description_format', PHP_INT_MAX );

# Don't strip tags on single post titles.
remove_filter( 'single_post_title', 'strip_tags' );

# Filters the title for untitled posts.
add_filter( 'the_title', __NAMESPACE__ . '\untitled_post' );

# Default excerpt more.
add_filter( 'excerpt_more', __NAMESPACE__ . '\excerpt_more', 5 );

# Allow the posts page to be edited.
add_action( 'edit_form_after_title', __NAMESPACE__ . '\enable_posts_page_editor', 0 );

# Adds common theme items to <head>.
add_action( 'wp_head', __NAMESPACE__ . '\meta_charset',   0 );
add_action( 'wp_head', __NAMESPACE__ . '\meta_viewport',  1 );
add_action( 'wp_head', __NAMESPACE__ . '\meta_generator', 1 );
add_action( 'wp_head', __NAMESPACE__ . '\link_pingback',  3 );

# Filter the WordPress title.
add_filter( 'document_title_parts', __NAMESPACE__ . '\document_title_parts', 5 );

# Remove the default emoji styles. We'll handle this in the stylesheet.
remove_action( 'wp_print_styles', 'print_emoji_styles' );

# Filter the comments template.
add_filter( 'comments_template', __NAMESPACE__ . '\comments_template', 5 );
