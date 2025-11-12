<?php
/**
 * Language service provider.
 *
 * This is the service provider for the language system, which binds an instance
 * of the framework's `Language` class to the container.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Lang;

use Backdrop\Contracts\Lang\Language as LanguageContract;
use Backdrop\Core\ServiceProvider;

/**
 * Language provider.
 *
 * @since  1.0.0
 * @access public
 */
class LanguageServiceProvider extends ServiceProvider {

	/**
	 * Registration callback that adds a single instance of the language
	 * system to the container.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register(): void {

		$this->app->singleton( LanguageContract::class, Language::class );

		$this->app->alias( LanguageContract::class, 'language' );
	}

	/**
	 * Boots the language system by firing its hooks in the `boot()` method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		$this->app->resolve( 'language' )->boot();
	}
}