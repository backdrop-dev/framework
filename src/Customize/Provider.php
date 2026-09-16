<?php
/**
 * Customize service provider.
 *
 * This is the service provider for the customization API integration. It binds
 * an instance of the framework's `Component` class to the container.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Customize;

use Backdrop\Core\ServiceProvider;

/**
 * Customize service provider.
 *
 * @since  1.0.0
 * @access public
 */
class Provider extends ServiceProvider {

	/**
	 * Register the customize component with the container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register(): void {

		$this->app->singleton( Component::class );
	}

	/**
	 * Boot the customize component.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		$this->app->resolve( Component::class )->boot();
	}
}