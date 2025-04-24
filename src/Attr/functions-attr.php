<?php
/**
 * Attribute functions.
 *
 * HTML attribute functions and filters.  The purposes of this is to provide a
 * way for theme/plugin devs to hook into the attributes for specific HTML
 * elements and create new or modify existing attributes. This is sort of like
 * `body_class()`, `post_class()`, and `comment_class()` on steroids.  Plus, it
 * handles attributes for many more elements.  The biggest benefit of using this
 * is to provide richer microdata while being forward compatible with the
 * ever-changing Web.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Attr;

use Backdrop\Contracts\Attr\Attributes;
use Backdrop\Proxies\App;

/**
 * Wrapper for creating a new `Attributes` object.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $name
 * @param  string  $context
 * @param  array   $attr
 * @return Attributes
 */
function attr( $name, $context = '', array $attr = [] ) {

	return App::resolve( Attributes::class, compact( 'name', 'context', 'attr' ) );
}

/**
 * Outputs an HTML element's attributes.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @param  string  $context
 * @param  array   $attr
 * @return void
 */
function display( $slug, $context = '', $attr = [] ) {

	attr( $slug, $context, $attr )->display();
}

/**
 * Returns an HTML element's attributes.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $slug
 * @param  string  $context
 * @param  array   $attr
 * @return string
 */
function render( $slug, $context = '', $attr = [] ) {

	return attr( $slug, $context, $attr )->render();
}