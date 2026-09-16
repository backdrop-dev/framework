<?php
/**
 * View functions.
 *
 * Helper functions and template tags for creating, displaying, and rendering
 * views.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\View;

use Backdrop\Contracts\View\Engine;
use Backdrop\Contracts\View\View;
use Backdrop\Proxies\App;
use Backdrop\Tools\Collection;

/**
 * Creates and returns a view object.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string           $name  View name.
 * @param  array|string     $slugs Optional view slugs.
 * @param  array|Collection $data  Data passed to the view.
 * @return View
 */
function view( $name, $slugs = [], $data = [] ) {

	return App::resolve( Engine::class )->view(
		$name,
		$slugs,
		$data
	);
}

/**
 * Outputs a view.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string           $name  View name.
 * @param  array|string     $slugs Optional view slugs.
 * @param  array|Collection $data  Data passed to the view.
 * @return void
 */
function display( $name, $slugs = [], $data = [] ) {

	view( $name, $slugs, $data )->display();
}

/**
 * Renders and returns a view as a string.
 *
 * @since  1.0.0
 * @access public
 *
 * @param  string           $name  View name.
 * @param  array|string     $slugs Optional view slugs.
 * @param  array|Collection $data  Data passed to the view.
 * @return string
 */
function render( $name, $slugs = [], $data = [] ) {

	return view( $name, $slugs, $data )->render();
}