<?php
/**
 * Object templates service provider.
 *
 * Registers and boots the object templates manager.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Core\ServiceProvider;

/**
 * Object templates service provider.
 *
 * @since  1.0.0
 * @access public
 */
class TemplatesServiceProvider extends ServiceProvider {

	/**
	 * Registers the templates manager with the container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register(): void {

		$this->app->singleton(
			Manager::class
		);

		$this->app->alias(
			Manager::class,
			'template/manager'
		);
	}

	/**
	 * Boots the templates manager.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		$this->app->resolve( 'template/manager' )->boot();
	}
}