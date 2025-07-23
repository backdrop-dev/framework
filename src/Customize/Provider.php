<?php
/**
 * Customize service provider.
 *
 * This is the service provider for the customization API integration. It binds
 * an instance of the frameworks `Customize` class to the container.
 *
 * @package   Backdrop Customize
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/customize
 */

namespace Backdrop\Customize;

use Backdrop\Core\ServiceProvider;

/**
 * Customize provider.
 */
class Provider extends ServiceProvider {

    /**
     * Registration callback that adds a single instance of the customize
     * object to the container.
     *
     * @return void
     */
    public function register(): void {
        $this->app->singleton( Component::class );
    }

    /**
     * Boots the customize component by firing its hooks in the `boot()` method.
     *
     * @return void
     */
    public function boot(): void {
        $this->app->resolve( Component::class )->boot();
    }

}