<?php
/**
 * Container class.
 *
 * This file maintains the `Container` class, which handles storing objects for
 * later use. It's primarily designed for handling single instances to avoid
 * globals or singletons. This is just a basic container for the purposes of
 * WordPress theme dev and isn't as powerful as some of the more robust
 * containers available in the larger PHP world.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Container;

use ArrayAccess;
use Closure;
use ReflectionClass;
use RuntimeException;
use Backdrop\Contracts\Container\Container as ContainerContract;

/**
 * A simple container for objects.
 *
 * @since  1.0.0
 * @access public
 */
class Container implements ContainerContract, ArrayAccess {

	/**
	 * Stored definitions of objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $bindings = [];

	/**
	 * Array of aliases for bindings.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $aliases = [];

	/**
	 * Array of single instance objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $instances = [];

	/**
	 * Array of object extensions.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $extensions = [];

	/**
	 * Set up a new container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $definitions Initial container definitions.
	 * @return void
	 */
	public function __construct( array $definitions = [] ) {

		foreach ( $definitions as $abstract => $concrete ) {
			$this->add( $abstract, $concrete );
		}
	}

	/**
	 * Add a binding. The abstract should be a key, abstract class name, or
	 * interface name. The concrete should be the concrete implementation of
	 * the abstract. If no concrete is given, it's assumed the abstract
	 * handles the concrete implementation.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $concrete The concrete implementation.
	 * @param  bool   $shared   Whether the binding is shared.
	 * @return void
	 */
	public function bind( string $abstract, $concrete = null, bool $shared = false ): void {

		unset( $this->instances[ $abstract ] );

		if ( is_null( $concrete ) ) {
			$concrete = $abstract;
		}

		$this->bindings[ $abstract ]    = compact( 'concrete', 'shared' );
		$this->extensions[ $abstract ] = [];
	}

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
	public function add( string $abstract, $concrete = null, bool $shared = false ): void {

		$this->bind( $abstract, $concrete, $shared );
	}

	/**
	 * Remove a binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return void
	 */
	public function remove( string $abstract ): void {

		if ( $this->has( $abstract ) ) {
			unset(
				$this->bindings[ $abstract ],
				$this->instances[ $abstract ],
				$this->extensions[ $abstract ]
			);
		}
	}

	/**
	 * Resolve and return the binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract   The key, interface, or abstract class name.
	 * @param  array  $parameters Optional parameters to pass when resolving.
	 * @return mixed
	 */
	public function resolve( string $abstract, array $parameters = [] ) {

		// Get the true abstract name.
		$abstract = $this->getAbstract( $abstract );

		// If this is being managed as an instance and we already have
		// the instance, return it now.
		if ( isset( $this->instances[ $abstract ] ) ) {
			return $this->instances[ $abstract ];
		}

		// Get the concrete implementation.
		$concrete = $this->getConcrete( $abstract );

		// If we can't build an object, assume we should return the value.
		if ( ! $this->isBuildable( $concrete ) ) {

			// If we don't actually have this, return false.
			if ( ! $this->has( $abstract ) ) {
				return false;
			}

			return $concrete;
		}

		// Build the object.
		$object = $this->build( $concrete, $parameters );

		if ( ! $this->has( $abstract ) ) {
			return $object;
		}

		// Run through each of the extensions for the object.
		foreach ( $this->extensions[ $abstract ] as $extension ) {
			$object = $extension( $object, $this );
		}

		// If shared, store the final extended object so future resolutions
		// return the same instance.
		if ( $this->bindings[ $abstract ]['shared'] && ! isset( $this->instances[ $abstract ] ) ) {
			$this->instances[ $abstract ] = $object;
		}

		// Return the object.
		return $object;
	}

	/**
	 * Creates an alias for an abstract. This allows you to add names that
	 * are easy to access without remembering more complex class names.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The original abstract type or binding key.
	 * @param  string $alias    The alias name for the abstract type.
	 * @return void
	 */
	public function alias( string $abstract, string $alias ): void {

		$this->aliases[ $alias ] = $abstract;
	}

	/**
	 * Alias for `resolve()`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return mixed
	 */
	public function get( string $abstract ) {

		return $this->resolve( $abstract );
	}

	/**
	 * Check if a binding exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @return bool
	 */
	public function has( string $abstract ): bool {

		$abstract = $this->getAbstract( $abstract );

		return isset( $this->bindings[ $abstract ] ) || isset( $this->instances[ $abstract ] );
	}

	/**
	 * Add a shared binding.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $concrete The concrete implementation.
	 * @return void
	 */
	public function singleton( string $abstract, $concrete = null ): void {

		$this->add( $abstract, $concrete, true );
	}

	/**
	 * Add an existing instance. This can be an instance of an object or a
	 * single value that should be stored.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $abstract The key, interface, or abstract class name.
	 * @param  mixed  $instance The existing instance to register.
	 * @return mixed
	 */
	public function instance( string $abstract, $instance ) {

		$this->instances[ $abstract ] = $instance;

		return $instance;
	}

	/**
	 * Extend a binding with something like a decorator class. Cannot
	 * extend resolved instances.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string  $abstract The key, interface, or abstract class name.
	 * @param  Closure $closure  The closure used to extend the binding.
	 * @return void
	 */
	public function extend( string $abstract, Closure $closure ): void {

		$abstract = $this->getAbstract( $abstract );

		$this->extensions[ $abstract ][] = $closure;
	}

	/**
	 * Checks if we're dealing with an alias and returns the abstract. If
	 * not an alias, return the abstract passed in.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  string $abstract The abstract type or binding key.
	 * @return string
	 */
	protected function getAbstract( $abstract ) {

		if ( isset( $this->aliases[ $abstract ] ) ) {
			return $this->aliases[ $abstract ];
		}

		return $abstract;
	}

	/**
	 * Gets the concrete of an abstract.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  string $abstract The abstract type or binding key.
	 * @return mixed
	 */
	protected function getConcrete( $abstract ) {

		$concrete = false;
		$abstract = $this->getAbstract( $abstract );

		if ( $this->has( $abstract ) ) {
			$concrete = $this->bindings[ $abstract ]['concrete'];
		}

		return $concrete ?: $abstract;
	}

	/**
	 * Determines if a concrete is buildable. It should either be a closure
	 * or a concrete class.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  mixed $concrete The concrete implementation.
	 * @return bool
	 */
	protected function isBuildable( $concrete ) {

		return $concrete instanceof Closure
		       || ( is_string( $concrete ) && class_exists( $concrete ) );
	}

	/**
	 * Builds the concrete implementation. If a closure, we'll simply return
	 * the closure and pass the included parameters. Otherwise, we'll resolve
	 * the dependencies for the class and return a new object.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  mixed $concrete   The concrete implementation.
	 * @param  array $parameters Optional parameters to pass when resolving.
	 * @return object
	 */
	protected function build( $concrete, array $parameters = [] ) {

		if ( $concrete instanceof Closure ) {
			return $concrete( $this, $parameters );
		}

		$reflect = new ReflectionClass( $concrete );

		$constructor = $reflect->getConstructor();

		if ( ! $constructor ) {
			return new $concrete();
		}

		return $reflect->newInstanceArgs(
			$this->resolveDependencies( $constructor->getParameters(), $parameters )
		);
	}

	/**
	 * Resolves the dependencies for a method's parameters.
	 *
	 * Dependencies are resolved in the following order:
	 *
	 * 1. Explicit parameters passed to the container.
	 * 2. Class or interface type dependencies.
	 * 3. Default parameter values.
	 * 4. Null for nullable parameters.
	 *
	 * If a required dependency cannot be resolved, an exception is thrown
	 * instead of silently omitting the parameter.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  array $dependencies Method dependencies.
	 * @param  array $parameters   Parameters passed when resolving.
	 * @return array
	 *
	 * @throws RuntimeException If a required dependency cannot be resolved.
	 */
	protected function resolveDependencies( array $dependencies, array $parameters ) {

		$args = [];

		foreach ( $dependencies as $dependency ) {

			// If a dependency is explicitly passed in, use it.
			if ( array_key_exists( $dependency->getName(), $parameters ) ) {
				$args[] = $parameters[ $dependency->getName() ];

				continue;
			}

			// If the parameter has a class or interface type, attempt to
			// resolve the first buildable type.
			$types = $this->getReflectionTypes( $dependency );

			foreach ( $types as $type ) {

				// Built-in types cannot be resolved through the container.
				if ( $type->isBuiltin() ) {
					continue;
				}

				$type_name = $type->getName();

				if ( class_exists( $type_name ) || interface_exists( $type_name ) ) {
					$resolved = $this->resolve( $type_name );

					if ( false !== $resolved ) {
						$args[] = $resolved;

						continue 2;
					}
				}
			}

			// If the parameter has a default value, use it.
			if ( $dependency->isDefaultValueAvailable() ) {
				$args[] = $dependency->getDefaultValue();

				continue;
			}

			// If the parameter allows null, use null.
			if ( $dependency->allowsNull() ) {
				$args[] = null;

				continue;
			}

			// Required dependencies should never be silently omitted because
			// doing so can shift later constructor arguments out of position.
			throw new RuntimeException(
				sprintf(
					'Unable to resolve dependency [%s] for parameter [$%s].',
					$this->getDependencyTypeName( $dependency ),
					$dependency->getName()
				)
			);
		}

		return $args;
	}

	/**
	 * Returns the reflection types for a dependency.
	 *
	 * PHP 7.4 returns a single `ReflectionNamedType`. Later PHP versions may
	 * return a union type, so this method normalizes the result to an array.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  object $dependency Reflection parameter dependency.
	 * @return array
	 */
	protected function getReflectionTypes( $dependency ) {

		$types = $dependency->getType();

		if ( ! $types ) {
			return [];
		}

		if ( class_exists( 'ReflectionUnionType' ) && $types instanceof \ReflectionUnionType ) {
			return $types->getTypes();
		}

		return [ $types ];
	}

	/**
	 * Returns a readable type name for a dependency.
	 *
	 * This is primarily used when reporting dependencies that the container
	 * cannot automatically resolve.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  object $dependency Reflection parameter dependency.
	 * @return string
	 */
	protected function getDependencyTypeName( $dependency ) {

		$types = $this->getReflectionTypes( $dependency );

		if ( ! $types ) {
			return 'untyped';
		}

		$names = [];

		foreach ( $types as $type ) {
			$names[] = $type->getName();
		}

		return implode( '|', $names );
	}

	/**
	 * Sets a property via `ArrayAccess`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $name  Property name.
	 * @param  mixed $value Property value.
	 * @return void
	 */
	public function offsetSet( $name, $value ) {

		$this->add( $name, $value );
	}

	/**
	 * Unsets a property via `ArrayAccess`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $name Property name.
	 * @return void
	 */
	public function offsetUnset( $name ) {

		$this->remove( $name );
	}

	/**
	 * Checks if a property exists via `ArrayAccess`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $name Property name.
	 * @return bool
	 */
	public function offsetExists( $name ) {

		return $this->has( $name );
	}

	/**
	 * Returns a property via `ArrayAccess`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $name Property name.
	 * @return mixed
	 */
	public function offsetGet( $name ) {

		return $this->get( $name );
	}

	/**
	 * Magic method when trying to set a property.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name  Property name.
	 * @param  mixed  $value Property value.
	 * @return void
	 */
	public function __set( $name, $value ) {

		$this->add( $name, $value );
	}

	/**
	 * Magic method when trying to unset a property.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Property name.
	 * @return void
	 */
	public function __unset( $name ) {

		$this->remove( $name );
	}

	/**
	 * Magic method when trying to check if a property exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Property name.
	 * @return bool
	 */
	public function __isset( $name ) {

		return $this->has( $name );
	}

	/**
	 * Magic method when trying to get a property.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Property name.
	 * @return mixed
	 */
	public function __get( $name ) {

		return $this->get( $name );
	}
}