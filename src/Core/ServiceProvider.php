<?php
/**
 * Base service provider.
 *
 * This is the base service provider class. This is an abstract class that must
 * be extended to create new service providers for the application.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Core;

use Backdrop\Contracts\Core\Application;

/**
 * Service provider class.
 *
 * @since  1.0.0
 * @access public
 */
abstract class ServiceProvider {

	/**
	 * Application instance.
	 *
	 * Subclasses should use this property to access the application container
	 * to add, remove, or resolve bindings.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var Application
	 */
	protected $app;

	/**
	 * Accepts the application and sets it to the `$app` property.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  Application $app Application instance.
	 * @return void
	 */
	public function __construct( Application $app ) {

		$this->app = $app;
	}

	/**
	 * Callback executed when the application registers providers.
	 *
	 * Subclasses may override this method to register bindings, singletons,
	 * aliases, or other services with the application container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register(): void {}

	/**
	 * Callback executed after all service providers have been registered.
	 *
	 * Subclasses may override this method to perform initialization that
	 * depends on services registered by other providers.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {}
}