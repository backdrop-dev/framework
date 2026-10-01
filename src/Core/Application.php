<?php
/**
 * Create a new Application.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019-2023. Benjamin Lu
 * @link      https://github.com/benlumia007/backdrop
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Core;

use Backdrop\Contracts\Bootable;
use Backdrop\Proxies\App;
use Backdrop\Proxies\Proxy;

/**
 * Application class.
 *
 * @since  1.0.0
 * @access public
 */
class Application extends Container implements Bootable {

	/**
	 * The current version of the framework.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	const VERSION = '1.0.0';

	/**
	 * Array of service provider objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected array $providers = [];

	/**
	 * Array of static proxy classes and aliases.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected array $proxies = [];

	/**
	 * Array of booted service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected array $booted_providers = [];

	/**
	 * Array of registered proxies.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected array $registered_proxies = [];

	/**
	 * Registers the default bindings, providers, and proxies for the
	 * framework.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->registerDefaultBindings();
		$this->registerDefaultProxies();

		// Make the application available to `Backdrop\app()` as soon as it's
		// created, so it can be used before booting (for example, in a
		// service provider's `register()` method). If an application
		// already exists (e.g., a parent theme's), it stays the one that
		// the helpers resolve from.
		if ( ! Proxy::hasContainer() ) {
			Proxy::setContainer( $this );
		}
	}

	/**
	 * Calls the functions to register and boot providers and proxies.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot(): void {

		$this->bootProviders();
		$this->registerProxies();

		if ( ! defined( 'BACKDROP_BOOTED' ) ) {
			define( 'BACKDROP_BOOTED', true );
		}
	}

	/**
	 * Registers the default bindings we need to run the framework.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerDefaultBindings(): void {

		// Add the instance of this application.
		$this->instance( 'app', $this );

		// Add the version for the framework.
		$this->instance( 'version', static::VERSION );
	}

	/**
	 * Adds the default static proxy classes.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerDefaultProxies(): void {

		// Makes the `Backdrop\App` class an alias for the app.
		$this->proxy( App::class, 'Backdrop\App' );
	}

	/**
	 * Adds a service provider.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  ServiceProvider|string  $provider
	 * @return void
	 */
	public function provider( $provider ): void {

		// If passed a class name, resolve provider.
		if ( is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		// Register the provider.
		$this->registerProvider( $provider );

		// Store the provider.
		$this->providers[] = $provider;
	}

	/**
	 * Creates a new instance of a service provider class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $provider
	 * @return object
	 */
	protected function resolveProvider( string $provider ): object {

		return new $provider( $this );
	}

	/**
	 * Calls a service provider's `register()` method if it exists.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param object $provider
	 * @return void
	 */
	protected function registerProvider( object $provider ): void {

		if ( method_exists( $provider, 'register' ) ) {

			$provider->register();
		}
	}

	/**
	 * Calls a service provider's `boot()` method if it exists.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  object	$provider
	 * @return void
	 */
	protected function bootProvider( object $provider ): void {

		$class_name = get_class( $provider );

		// Bail if the provider has already been booted.
		if ( in_array( $class_name, $this->booted_providers ) ) {
			return;
		}

		if ( method_exists( $provider, 'boot' ) ) {
			$provider->boot();
			$this->booted_providers[] = $class_name;
		}
	}

	/**
	 * Returns an array of service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function getProviders(): array {

		return $this->providers;
	}

	/**
	 * Calls the `boot()` method of all the registered service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function bootProviders(): void {

		foreach ( $this->getProviders() as $provider ) {

			$this->bootProvider( $provider );
		}
	}

	/**
	 * Adds a static proxy alias. Developers must pass in fully-qualified
	 * class name and alias class name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $class_name
	 * @param  string  $alias
	 * @return void
	 */
	public function proxy( string $class_name, string $alias ): void {

		$this->proxies[ $class_name ] = $alias;
	}

	/**
	 * Registers a static proxy class alias.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $class
	 * @param  string  $alias
	 * @return void
	 */
	protected function registerProxy( string $class, string $alias ): void {

		if ( ! class_exists( $alias ) ) {
			class_alias( $class, $alias );
		}

		$this->registered_proxies[] = $alias;
	}

	/**
	 * Registers the static proxy classes.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerProxies(): void {

		// Set the container if no application has set one yet. A second
		// application must not replace the first one.
		if ( ! Proxy::hasContainer() ) {
			Proxy::setContainer( $this );
		}

		foreach ( $this->proxies as $class => $alias ) {

			// Register proxy if not already registered.
			if ( ! in_array( $alias, $this->registered_proxies ) ) {
				$this->registerProxy( $class, $alias );
			}
		}
	}
}
