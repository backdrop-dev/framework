<?php
/**
 * App static proxy class.
 *
 * Static proxy class for the application instance.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Proxies;

/**
 * App static proxy class.
 *
 * @since  5.0.0
 * @access public
 */
class App extends Proxy {

	/**
	 * Returns the name of the accessor for object registered in the container.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return string
	 */
	protected static function accessor() {

		return 'app';
	}
}