<?php
/**
 * Container contract.
 *
 * Container classes should be used for storing, retrieving, and resolving
 * classes, objects, and values passed into them.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\Container;

use Closure;

/**
 * Container interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Container {

	/**
	 * Add a binding.
	 *
	 * The abstract should be a key, abstract class name, or interface name.
	 * The concrete should be the concrete implementation of the abstract. If
	 * no concrete is given, the abstract is used as the concrete.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $concrete The concrete implementation.
	 * @param  bool   $shared   Whether the binding is shared.
	 * @return void
	 */
	public function bind( string $abstract, $concrete = null, bool $shared = false ): void;

	/**
	 * Alias for `bind()`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $concrete The concrete implementation.
	 * @param  bool   $shared   Whether the binding is shared.
	 * @return void
	 */
	public function add( string $abstract, $concrete = null, bool $shared = false ): void;

	/**
	 * Remove a binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return void
	 */
	public function remove( string $abstract ): void;

	/**
	 * Resolve and return a binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract   The key, interface, or abstract class name.
	 * @param  array  $parameters Optional parameters to pass when resolving.
	 * @return mixed
	 */
	public function resolve( string $abstract, array $parameters = [] );

	/**
	 * Alias for `resolve()`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return mixed
	 */
	public function get( string $abstract );

	/**
	 * Check if a binding or instance exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return bool
	 */
	public function has( string $abstract ): bool;

	/**
	 * Add a shared binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string      $abstract The key, interface, or abstract class name.
	 * @param  object|null $concrete The concrete implementation.
	 * @return void
	 */
	public function singleton( string $abstract, ?object $concrete = null ): void;

	/**
	 * Add an existing instance or value.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $instance The existing instance or value to register.
	 * @return mixed
	 */
	public function instance( string $abstract, $instance );

	/**
	 * Extend a binding.
	 *
	 * The extension closure receives the resolved value and container and
	 * should return the extended or decorated value.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string  $abstract The key, interface, or abstract class name.
	 * @param  Closure $closure  The closure used to extend the binding.
	 * @return void
	 */
	public function extend( string $abstract, Closure $closure ): void;

	/**
	 * Create an alias for an abstract type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The original abstract type or binding key.
	 * @param  string $alias    The alias name for the abstract type.
	 * @return void
	 */
	public function alias( string $abstract, string $alias ): void;
}