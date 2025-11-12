<?php
/**
 * Container contract.
 *
 * Container classes should be used for storing, retrieving, and resolving
 * classes/objects passed into them.
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
	 * Add a binding. The abstract should be a key, abstract class name, or
	 * interface name. The concrete should be the concrete implementation of
	 * the abstract.
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` type).
	 * - `string` and `bool` type hints introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 * - `mixed` type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @param  mixed   $concrete  The concrete implementation.
	 * @param  bool    $shared    Whether the binding is shared (singleton).
	 * @return void
	 */
	public function bind( string $abstract, mixed $concrete = null, bool $shared = false ): void;

	/**
	 * Alias for `bind()`.
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` type).
	 * - `string` and `bool` type hints introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 * - `mixed` type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @param  mixed   $concrete  The concrete implementation.
	 * @param  bool    $shared    Whether the binding is shared (singleton).
	 * @return void
	 */
	public function add( string $abstract, mixed $concrete = null, bool $shared = false ): void;

	/**
	 * Remove a binding.
	 *
	 * Compatible with PHP 8.0+ (scalar type hints and `void` return type).
	 * - `string` type hint introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @return void
	 */
	public function remove( string $abstract ): void;

	/**
	 * Resolve and return the binding.
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` return type).
	 * - `string` and `array` type hints introduced in PHP 7.0
	 * - `mixed` return type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract    The key, interface, or abstract class name.
	 * @param  array   $parameters  Optional parameters to pass when resolving.
	 * @return mixed
	 */
	public function resolve( string $abstract, array $parameters = [] ): mixed;

	/**
	 * Alias for `resolve()`.
	 *
	 * Follows the PSR-11 standard. Do not alter.
	 * @link https://www.php-fig.org/psr/psr-11/
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` return type).
	 * - `string` type hint introduced in PHP 7.0
	 * - `mixed` return type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @return mixed
	 */
	public function get( string $abstract ): mixed;

	/**
	 * Check if a binding exists.
	 *
	 * Follows the PSR-11 standard. Do not alter.
	 * @link https://www.php-fig.org/psr/psr-11/
	 *
	 * Compatible with PHP 8.0+ (scalar type hints and `bool` return type).
	 * - `string` and `bool` type hints introduced in PHP 7.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @return bool
	 */
	public function has( string $abstract ): bool;

	/**
	 * Add a shared binding.
	 *
	 * Compatible with PHP 8.0+ (uses nullable and typed parameters).
	 * - `string` type hint introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 * - `object` type hint introduced in PHP 7.2
	 * - Nullable types (`?type`) introduced in PHP 7.1
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string       $abstract  The key, interface, or abstract class name.
	 * @param  ?object|null $concrete  The concrete implementation (optional).
	 * @return void
	 */
	public function singleton( string $abstract, ?object $concrete = null ): void;

	/**
	 * Add an existing instance.
	 *
	 * Compatible with PHP 8.0+ (uses `mixed` type).
	 * - `string` type hint introduced in PHP 7.0
	 * - `mixed` type introduced in PHP 8.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The key, interface, or abstract class name.
	 * @param  mixed   $instance  The existing instance to register.
	 * @return mixed
	 */
	public function instance( string $abstract, mixed $instance ): mixed;

	/**
	 * Extend a binding.
	 *
	 * Compatible with PHP 8.0+ (uses scalar and `void` type hints).
	 * - `string` type hint introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 * - `Closure` type hint available since PHP 5.4 (from global namespace)
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string   $abstract  The key, interface, or abstract class name.
	 * @param  \Closure $closure   The closure used to extend the binding.
	 * @return void
	 */
	public function extend( string $abstract, \Closure $closure ): void;

	/**
	 * Create an alias for an abstract type.
	 *
	 * Compatible with PHP 8.0+ (uses scalar and `void` type hints).
	 * - `string` type hint introduced in PHP 7.0
	 * - `void` return type introduced in PHP 7.1
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $abstract  The original abstract type or binding key.
	 * @param  string  $alias     The alias name for the abstract type.
	 * @return void
	 */
	public function alias( string $abstract, string $alias ): void;
}