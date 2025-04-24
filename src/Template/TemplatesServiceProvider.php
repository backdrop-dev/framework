<?php
/**
 * Object templates service provider.
 *
 * This is the service provider for the object templates system, which binds an
 * empty collection to the container that can later be used to register templates.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Tools\ServiceProvider;

/**
 * Object templates provider class.
 *
 * @since  1.0.0
 * @access public
 */
class TemplatesServiceProvider extends ServiceProvider {

	/**
	 * Registers the templates collection and manager.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->singleton( Manager::class );

		$this->app->alias( Manager::class, 'template/manager' );
	}

	/**
	 * Boots the manager by firing its hooks in the `boot()` method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		$this->app->resolve( 'template/manager' )->boot();
	}
}