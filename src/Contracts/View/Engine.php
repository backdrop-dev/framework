<?php
/**
 * Engine contract.
 *
 * Engine classes are wrappers around the View system.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\View;

/**
 * View interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Engine {

	/**
	 * Returns a View object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string            $name
	 * @param  array|string      $slugs
	 * @param  array|Collection  $data
	 * @return View
	 */
	public function view( $name, $slugs = [], $data = [] );

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
	public function display( $name, $slugs = [], $data = [] );

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
	function render( $name, $slugs = [], $data = [] );
}