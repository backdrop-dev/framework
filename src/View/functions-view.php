<?php
/**
 * View template tags.
 *
 * Template functions related to views.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\View;

use Backdrop\Contracts\View\Engine;
use Backdrop\Proxies\App;
use Backdrop\Tools\Collection;

/**
 * Returns a view object.
 *
 * @since  1.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return View
 */
function view( $name, $slugs = [], $data = [] ) {

	return App::resolve( Engine::class )->view( $name, $slugs, $data );
}

/**
 * Outputs a view template.
 *
 * @since  1.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return void
 */
function display( $name, $slugs = [], $data = [] ) {

	view( $name, $slugs, $data )->display();
}

/**
 * Returns a view template as a string.
 *
 * @since  1.0.0
 * @access public
 * @param  string            $name
 * @param  array|string      $slugs
 * @param  array|Collection  $data
 * @return string
 */
function render( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->render();
}