<?php
/**
 * Proxy class
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019-2023. Benjamin Lu
 * @link      https://github.com/benlumia007/backdrop
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Proxies;

use Backdrop\Core\Container;
use ReflectionException;
use RuntimeException;

/**
 * Base static proxy class.
 *
 * @since  1.0.0
 * @access public
 */
class Proxy {

	/**
	 * The container object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Container
	 */
	protected static $container;

	/**
	 * Returns the name of the accessor for object registered in the container.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	protected static function accessor(): string {

		return '';
	}

	/**
	 * Sets the container object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public static function setContainer( $container ) {

		static::$container = $container;
	}

	/**
	 * Determines whether a container has been set.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return bool
	 */
	public static function hasContainer(): bool {

		return null !== static::$container;
	}

	/**
	 * Returns the instance from the container.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @throws ReflectionException
	 * @throws RuntimeException If no application has been created.
	 * @return object
	 */
	protected static function instance() {

		if ( null === static::$container ) {
			throw new RuntimeException(
				'No Backdrop application has been created. Create one with `new Backdrop\Core\Application()` before using `Backdrop\app()` or a proxy.'
			);
		}

		return static::$container->resolve( static::accessor() );
	}

	/**
	 * Calls the requested method from the object registered with the
	 * container statically.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param string $method
	 * @param array $args
	 * @throws ReflectionException
	 * @return mixed
	 */
	public static function __callStatic( string $method, array $args ) {

		$instance = static::instance();

		return $instance->$method(...$args);
	}
}
