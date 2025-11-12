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
	 * Adds a service provider. Developers can pass in an object or a fully
	 * qualified class name.
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` type).
	 * - `string` type hint introduced in PHP 7.0
	 * - `object` type hint introduced in PHP 7.2
	 * - `mixed` type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed  $provider  A service-provider instance or class name.
	 * @return void
	 */
	public function provider( mixed $provider ): void;

	/**
	 * Adds a static proxy alias. Developers must pass in a fully qualified
	 * class name and an alias class name.
	 *
	 * Compatible with PHP 8.0+ (uses scalar and `void` type hints).
	 * - `string` type hint introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $class_name  The fully qualified class name.
	 * @param  string  $alias       The alias class name.
	 * @return void
	 */
	public function proxy( string $class_name, string $alias ): void;
}
