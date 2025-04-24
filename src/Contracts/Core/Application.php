<?php
/**
 * Application contract.
 *
 * The Application class should be the be the primary class for working with and
 * launching the app. It extends the `Container` contract.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\Core;

use Backdrop\Contracts\Container\Container;

/**
 * Application interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Application extends Container {

	/**
	 * Adds a service provider. Developers can pass in an object or a fully-
	 * qualified class name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string|object  $provider
	 * @return void
	 */
	public function provider( $provider );

	/**
	 * Adds a static proxy alias. Developers must pass in fully-qualified
	 * class name and alias class name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $class_name
	 * @param  string  $alias
	 * @return void
	 */
	public function proxy( $class_name, $alias );
}
