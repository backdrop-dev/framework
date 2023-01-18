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
 * @copyright 2019-2023. Benjamin Lu
 * @link      https://github.com/benlumia007/backdrop-template-view
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Template\View;

use Backdrop\Contracts\Template\Engine as EngineContract;
use Backdrop\Contracts\Template\View as ViewContract;
use Backdrop\Template\View\Engine\Component as Engine;
use Backdrop\Template\View\View\Component as View;
use Backdrop\Tools\ServiceProvider;

/**
 * View provider class.
 *
 * @since  2.0.0
 * @access public
 */
class Provider extends ServiceProvider {

	/**
	 * Binds the implementation of the view contract to the container.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function register(): void {

		// Bind the view contract.
		$this->app->bind( ViewContract::class, View::class );

		// Bind a single instance of the engine contract.
		$this->app->singleton( EngineContract::class, Engine::class );

		// Create aliases for the view and engine.
		$this->app->alias( ViewContract::class,   'view'        );
		$this->app->alias( EngineContract::class, 'view/engine' );
	}
}
