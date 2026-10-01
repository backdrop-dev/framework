<?php
/**
 * Container class.
 *
 * The `Container` class handles storing objects for later use and
 * handles single instances to avoid globals or singleton.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Core;

use ArrayAccess;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;

/**
 * A simple container for objects.
 *
 * @since  1.0.0
 * @access public
 */
class Container implements ArrayAccess {

    /**
     * Stored definitions of objects.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected array $bindings = [];

    /**
     * Array of aliases for bindings.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected array $aliases = [];

    /**
     * Array of single instance objects.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected array $instances = [];

    /**
     * Array of object extensions.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected array $extensions = [];

    /**
     * Set up a new container.
     *
     * @since  1.0.0
     * @access public
     * @param  array  $definitions
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
     * the abstract.
     *
     * @since  1.0.0
     * @access public
     * @param  string $abstract
     * @param  mixed  $concrete
     * @param  bool   $shared
     * @return void
     */
    public function bind( string $abstract, $concrete = null, bool $shared = false): void {

        // Drop all the stale instances and aliases
        unset( $this->instances[$abstract] );

        /**
         * If no concrete type was given, we will simply set the concrete type to the
         * abstract type. After, the concrete type to be registered as shared without
         * be forced to state their classes in both  of the parameters
         */
        if ( is_null( $concrete ) ) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact( 'concrete', 'shared' );
        $this->extensions[$abstract] = [];
    }

    /**
     * Alias for `bind()`.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @param  mixed   $concrete
     * @param  bool    $shared
     * @return void
     */
    public function add( string $abstract, $concrete = null, bool $shared = false ): void {

        if ( ! $this->bound( $abstract ) ) {
            $this->bind( $abstract, $concrete, $shared );
        }
    }

    /**
     * Remove a binding.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @return void
     */
    public function remove( string $abstract ): void {

        // Allow removing a binding by one of its aliases. The alias itself
        // is kept, so it still works if the abstract is bound again.
        $abstract = $this->getAlias( $abstract );

        if ( $this->bound( $abstract ) ) {
            unset(
                $this->bindings[$abstract],
                $this->instances[$abstract],
                $this->extensions[$abstract]
            );
        }
    }

    /**
     * Resolve and return the binding.
     *
     * @since  1.0.0
     * @access public
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function resolve( string $abstract, array $parameters = [] ) {

        // Let's grab the true abstract name.
        $abstract = $this->getAlias( $abstract );

        /**
         * if an instance of the type is currently being managed as a singleton
         * we'll just return an existing instance instead of instantiating a new
         * instance so the developer can keep using the same objects instance
         * every time.
         */
        // Use `array_key_exists()` so that `null` instances are returned.
        if ( array_key_exists( $abstract, $this->instances ) ) {

            return $this->instances[$abstract];
        }

        // Get the concrete implementation.
        $concrete = $this->getConcrete($abstract);

        // If we can't build an object, assume we should return the value.
        if ( ! $this->isBuildable( $concrete ) ) {

            // If we don't actually have this, return false.
            if ( ! $this->bound( $abstract ) ) {

                return false;
            }

            return $concrete;
        }

        // Build the object.
        $object = $this->build( $concrete, $parameters );

        if ( ! $this->bound( $abstract ) ) {

            return $object;
        }

        // Run through each of the extensions for the object. Extensions
        // are closures that receive the object and the container and
        // return the (possibly decorated) object.
        foreach ( $this->extensions[$abstract] ?? [] as $extension ) {

            $object = $extension( $object, $this );
        }

        // If shared instance, store the final extended object so that we're
        // not creating new objects later.
        if ( $this->bindings[$abstract]['shared'] && ! array_key_exists( $abstract, $this->instances ) ) {

            $this->instances[$abstract] = $object;
        }

        // Return the object.
        return $object;
    }

    /**
     * Alias for `resolve()`.
     *
     * @since  1.0.0
     * @access public
     * @param string $abstract
     * @return mixed
     */
    public function get( string $abstract ) {

        return $this->resolve( $abstract );
    }

    /**
     * Check if a binding exists.
     *
     * Follows the PSR-11 standard. Do not alter.
     * @link https://www.php-fig.org/psr/psr-11/
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @return bool
     */
    public function bound( string $abstract ): bool {

		return isset( $this->bindings[$abstract] ) || array_key_exists( $abstract, $this->instances );
    }

    /**
     * Add a shared binding.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @param  object  $concrete
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
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return mixed
     */
    public function instance( string $abstract, $instance ) {

        $this->instances[$abstract] = $instance;

		return $instance;
    }

    /**
     * Extend a binding with something like a decorator class. Cannot
     * extend resolved instances.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @param  Closure $closure
     * @return void
     */
    public function extend( string $abstract, Closure $closure ): void {

		$abstract = $this->getAlias( $abstract );

        $this->extensions[$abstract][] = $closure;
    }


    /**
     * Creates an alias for an abstract type. This allows you to add alias
     * names that are easier to remember rather than using full class names.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias( string $abstract, string $alias ): void {

        $this->aliases[$alias] = $abstract;
    }

    /**
     * Checks if a property exists via `ArrayAccess`.
     *
     * @since  1.0.0
     * @access public
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists( $offset ): bool {

        return $this->bound( $offset );
    }

	/**
	 * Returns a property via `ArrayAccess`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param mixed $offset
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {

		return $this->get( $offset );
	}

    /**
     * Sets a property via `ArrayAccess`.
     *
     * @since  1.0.0
     * @access public
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void {

		$this->add( $offset, $value );
    }

    /**
     * Unsets a property via `ArrayAccess`.
     *
     * @since  1.0.0
     * @access public
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset( $offset ): void {

		$this->remove( $offset );
    }

    /**
     * Checks if we're dealing with an alias and returns the abstract. If
     * not an alias, return the abstract passed in.
     *
     * @since  1.0.0
     * @access protected
     * @param  string    $abstract
     * @return string
     */
    protected function getAlias( string $abstract ): string {
        if ( isset( $this->aliases[$abstract] ) ) {

			return $this->aliases[$abstract];
        }

        return $abstract;
    }

    /**
     * Gets the concrete of an abstract.
     *
     * @since  1.0.0
     * @access protected
     * @param  string    $abstract
     * @return mixed
     */
    protected function getConcrete( string $abstract ) {

        $abstract = $this->getAlias( $abstract );

        // Return the bound concrete as-is, so that falsy values such as
        // `0`, `''`, `false`, and `[]` can be stored and resolved.
        if ( isset( $this->bindings[$abstract] ) ) {

			return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Determines if a concrete is buildable. It should either be a closure
     * or a concrete class.
     *
     * @since  1.0.0
     * @access protected
     * @param  mixed    $concrete
     * @return bool
     */
    protected function isBuildable( $concrete ): bool {

        if ( $concrete instanceof Closure ) {
            return true;
        }

        if ( ! is_string( $concrete ) || ! class_exists( $concrete ) ) {
            return false;
        }

        // Abstract classes and classes with non-public constructors can't
        // be instantiated.
        return ( new ReflectionClass( $concrete ) )->isInstantiable();
    }

    /**
     * Builds the concrete implementation. If a closure, we'll simply return
     * the closure and pass the included parameters. Otherwise, we'll resolve
     * the dependencies for the class and return a new object.
     *
     * @since  1.0.0
     * @access protected
     * @param mixed $concrete
     * @param array $parameters
     * @return mixed
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

        return $reflect->newInstanceArgs( $this->resolveDependencies( $constructor->getParameters(), $parameters ) );
    }

    /**
     * Resolves the dependencies for a method's parameters.
     *
     * Dependencies are resolved in the following order:
     *
     * 1. Explicit parameters passed to the container.
     * 2. The first class or interface type that the container can resolve.
     * 3. Default parameter values.
     * 4. Null for nullable parameters.
     *
     * If a required dependency can't be resolved, an exception is thrown
     * instead of silently skipping the parameter, which would shift the
     * remaining arguments out of position.
     *
     * @since  1.0.0
     * @access protected
     * @param array $dependencies
     * @param array $parameters
     * @return array
     * @throws ReflectionException
     * @throws RuntimeException If a required dependency can't be resolved.
     */
    protected function resolveDependencies( array $dependencies, array $parameters ): array {

		$args = [];

        foreach ( $dependencies as $dependency ) {

            // If a dependency is set via the parameters passed in, use it.
            if ( array_key_exists( $dependency->getName(), $parameters ) ) {

				$args[] = $parameters[ $dependency->getName() ];
                continue;
            }

            // If the parameter has a class or interface type, use the first
            // type that the container can resolve.
            foreach ( $this->getReflectionTypes( $dependency ) as $type ) {

                // Only named, non-built-in types can be resolved. This skips
                // types like `string` and PHP 8.1+ intersection types.
                if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin() ) {
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

            // Else, use the default parameter value.
            if ( $dependency->isDefaultValueAvailable() ) {

				$args[] = $dependency->getDefaultValue();
                continue;
            }

            // Else, use null if the parameter allows it.
            if ( $dependency->allowsNull() ) {

                $args[] = null;
                continue;
            }

            throw new RuntimeException(
                sprintf(
                    'Unable to resolve dependency [%s] for parameter [$%s] of [%s].',
                    $this->getDependencyTypeName( $dependency ),
                    $dependency->getName(),
                    $dependency->getDeclaringClass() ? $dependency->getDeclaringClass()->getName() : 'unknown'
                )
            );
        }

        return $args;
    }

    /**
     * Returns a readable type name for a dependency, used in error messages.
     *
     * @since  2.0.0
     * @access protected
     * @param  object $dependency Reflection parameter.
     * @return string
     */
    protected function getDependencyTypeName( object $dependency ): string {

        $types = $this->getReflectionTypes( $dependency );

        if ( ! $types ) {
            return 'untyped';
        }

        // Casting a type to a string is deprecated on PHP 7.4, so use the
        // name of named types. Other types (PHP 8.1+) can be cast safely.
        return implode( '|', array_map( function( $type ) {
            return $type instanceof ReflectionNamedType ? $type->getName() : (string) $type;
        }, $types ) );
    }

    /**
     * `ReflectionParameter::getType()` in PHP may return an instance of
     * `ReflectionNamedType` or an `ReflectionUnionType`.  The latter class's
     * `getTypes()` method returns and array of the former objects. This
     * method ensures that we always get an array of `ReflectionNamedType`
     * objects.
     *
     * @since  1.0.0
     * @access protected
     * @param  object    $dependency
     * @return array
     */
    protected function getReflectionTypes( object $dependency ): array {

        $types = $dependency->getType();

        if ( !$types ) {

			return [];
        } elseif ( class_exists( 'ReflectionUnionType' ) && $types instanceof \ReflectionUnionType ) {

			return $types->getTypes();
        }

        return [$types];
    }

    /**
     * Magic method when trying to set a property.
     *
     * @since  1.0.0
     * @access public
     * @param  string  $name
     * @param  mixed   $value
     * @return void
     */
    public function __set( string $name, $value ): void {

		$this->add($name, $value);
    }


	/**
	 * Magic method when trying to unset a property.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function __unset( string $name ): void {

		$this->remove( $name );
	}

	/**
	 * Magic method when trying to check if a property exists.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function __isset( string $name ): bool {

		return $this->bound( $name );
	}

	/**
	* Magic method when trying to get a property.
	*
	 * @since  1.0.0
	 * @access public
	 * @param string $name
	 * @return mixed
	*/
	public function __get( string $name ) {

		return $this->get( $name );
	}
}
