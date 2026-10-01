<?php
/**
 * View engine class.
 *
 * Provides a wrapper around the View contract for quickly creating, displaying,
 * and rendering views without manually resolving view objects. The engine is
 * also passed to each view so that views can render other views directly.
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
 * View engine class.
 *
 * @since  1.0.0
 * @access public
 */
class Engine {

	/**
	 * Creates and returns a view object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string           $name  View name.
	 * @param  array|string     $slugs Optional view slugs.
	 * @param  array|Collection $data  Data passed to the view.
	 * @return View
	 */
	public function view( $name, $slugs = [], $data = [] ) {

		if ( ! $data instanceof Collection ) {
			$data = new Collection( (array) $data );
		}

		// Pass the engine to the view so that it can be used to render
		// additional views.
		$data->add( 'engine', $this );

		return App::resolve(
			View::class,
			compact( 'name', 'slugs', 'data' )
		);
	}

	/**
	 * Outputs a view.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string           $name  View name.
	 * @param  array|string     $slugs Optional view slugs.
	 * @param  array|Collection $data  Data passed to the view.
	 * @return void
	 */
	public function display( $name, $slugs = [], $data = [] ) {

		$this->view( $name, $slugs, $data )->display();
	}

	/**
	 * Renders and returns a view as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string           $name  View name.
	 * @param  array|string     $slugs Optional view slugs.
	 * @param  array|Collection $data  Data passed to the view.
	 * @return string
	 */
	public function render( $name, $slugs = [], $data = [] ) {

		return $this->view( $name, $slugs, $data )->render();
	}
}