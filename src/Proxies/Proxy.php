<?php
/**
 * Static proxy class.
 *
 * The base static proxy class. This allows us to create easy-to-use static
 * classes around objects registered with the container.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Proxies;

use Backdrop\Contracts\Container\Container;
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
	 *
	 * @var Container|null
	 */
	protected static $container;

	/**
	 * Returns the name of the accessor for the object registered in the
	 * container.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected static function accessor() {

		return '';
	}

	/**
	 * Sets the container object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  Container $container Container instance.
	 * @return void
	 */
	public static function setContainer( Container $container ) {

		static::$container = $container;
	}

	/**
	 * Returns the instance from the container.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return mixed
	 *
	 * @throws RuntimeException If the container has not been set.
	 */
	protected static function instance() {

		if ( ! static::$container ) {
			throw new RuntimeException(
				'The container has not been set on the proxy.'
			);
		}

		return static::$container->resolve( static::accessor() );
	}

	/**
	 * Calls the requested method on the object registered with the container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $method Method name.
	 * @param  array  $args   Method arguments.
	 * @return mixed
	 */
	public static function __callStatic( $method, $args ) {

		$instance = static::instance();

		return $instance ? $instance->$method( ...$args ) : null;
	}
}