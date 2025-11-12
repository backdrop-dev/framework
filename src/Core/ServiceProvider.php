<?php
/**
 * Base service provider.
 *
 * This is the base service provider class. This is an abstract class that must
 * be extended to create new service providers for the application.
 *
 * Compatible with PHP 8.0+ (typed property and return types).
 * - `void` return type introduced in PHP 7.1
 * - Typed properties introduced in PHP 7.4
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
	 * Application instance. Sub-classes should use this property to access
	 * the application (container) to add, remove, or resolve bindings.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Application
	 */
	protected Application $app;

	/**
	 * Accepts the application and sets it to the `$app` property.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  Application  $app
	 * @return void
	 */
	public function __construct( Application $app ) {
		$this->app = $app;
	}

	/**
	 * Callback executed when the `Application` class registers providers.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register(): void {}

	/**
	 * Callback executed after all the service providers have been registered.
	 * This is particularly useful for single-instance container objects that
	 * only need to be loaded once per page and need to be resolved early.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot(): void {}
}
