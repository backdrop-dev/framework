<?php
/**
 * View service provider.
 *
 * This is the service provider for the view system. The primary purpose of
 * this is to use the container as a factory for creating views. By adding this
 * to the container, it also allows the view implementation to be overwritten.
 * That way, any custom functions will utilize the new class.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\View;

use Backdrop\Tools\ServiceProvider;
use Backdrop\Contracts\View\Engine as EngineContract;
use Backdrop\Contracts\View\View   as ViewContract;

/**
 * View provider class.
 *
 * @since  1.0.0
 * @access public
 */
class ViewServiceProvider extends ServiceProvider {

	/**
	 * Binds the implementation of the view contract to the container.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		// Bind the view contract.
		$this->app->bind( ViewContract::class, View::class );

		// Bind a single instance of the engine contract.
		$this->app->singleton( EngineContract::class, Engine::class );

		// Create aliases for the view and engine.
		$this->app->alias( ViewContract::class,   'view'        );
		$this->app->alias( EngineContract::class, 'view/engine' );
	}
}