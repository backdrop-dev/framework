<?php
/**
 * Engine contract.
 *
 * Defines the interface for view engines that create, display, and render
 * view objects.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Contracts\View;

use Backdrop\Tools\Collection;

/**
 * View engine interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Engine {

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
	public function view( $name, $slugs = [], $data = [] );

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
	public function display( $name, $slugs = [], $data = [] );

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
	public function render( $name, $slugs = [], $data = [] );
}