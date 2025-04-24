<?php
/**
 * Engine class.
 *
 * A wrapper around the `View` class with methods for quickly working with views
 * without having to manually instantiate a view object.  It's also useful
 * because it passes an `$engine` variable to all views.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\View;

use Backdrop\Contracts\View\View;
use Backdrop\Proxies\App;
use Backdrop\Tools\Collection;

/**
 * Engine class.
 *
 * @since  1.0.0
 * @access public
 */
class Engine {

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
	public function view( $name, $slugs = [], $data = [] ) {

		if ( ! $data instanceof Collection ) {
			$data = new Collection( (array) $data );
		}

		// Pass the engine itself along so that it can be used directly
		// in views.
		$data->add( 'engine', $this );

		return App::resolve( View::class, compact( 'name', 'slugs', 'data' ) );
	}

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
	public function display( $name, $slugs = [], $data = [] ) {

		$this->view( $name, $slugs, $data )->display();
	}

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
	public function render( $name, $slugs = [], $data = [] ) {

		return $this->view( $name, $slugs, $data )->render();
	}
}