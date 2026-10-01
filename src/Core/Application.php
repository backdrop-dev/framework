<?php
/**
 * Application class.
 *
 * This class is essentially a wrapper around the `Container` class that's
 * specific to the framework. This class is meant to be used as the single,
 * one-true instance of the framework. It's used to load up service providers
 * that interact with the container.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Core;

use Backdrop\Attr\AttrServiceProvider;
use Backdrop\Container\Container;
use Backdrop\Contracts\Bootable;
use Backdrop\Contracts\Core\Application as ApplicationContract;
use Backdrop\Lang\LanguageServiceProvider;
use Backdrop\Proxies\App;
use Backdrop\Proxies\Proxy;
use Backdrop\Template\HierarchyServiceProvider;
use Backdrop\Template\TemplatesServiceProvider;
use Backdrop\View\ViewServiceProvider;

/**
 * Application class.
 *
 * @since  1.0.0
 * @access public
 */
class Application extends Container implements ApplicationContract, Bootable {

	/**
	 * The current version of the framework.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Array of service provider objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $providers = [];

	/**
	 * Array of static proxy classes and aliases.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $proxies = [];

	/**
	 * Whether the application has been booted.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * Array of booted service provider objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $booted_providers = [];

	/**
	 * Registers the default bindings, providers, and proxies for the
	 * framework.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {

		$this->registerDefaultBindings();
		$this->registerDefaultProviders();
		$this->registerDefaultProxies();

		// Make the application available to `Backdrop\app()` and the other
		// helper functions as soon as it is created. If another application
		// already exists, it remains the one that the helpers resolve from.
		if ( ! Proxy::hasContainer() ) {
			Proxy::setContainer( $this );
		}

		$this->bootstrapFilters();
	}

	/**
	 * Calls the functions to register and boot providers and proxies.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		// Only boot the application once.
		if ( $this->booted ) {
			return;
		}

		$this->registerProviders();
		$this->bootProviders();
		$this->registerProxies();

		$this->booted = true;

		if ( ! defined( 'BACKDROP_BOOTED' ) ) {
			define( 'BACKDROP_BOOTED', true );
		}
	}

	/**
	 * Determines whether the application has been booted.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function isBooted(): bool {

		return $this->booted;
	}

	/**
	 * Registers the default bindings we need to run the framework.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function registerDefaultBindings() {

		// Add the instance of this application.
		$this->instance( 'app', $this );

		// Add the directory path for the framework.
		$this->instance( 'path', untrailingslashit( BACKDROP_DIR ) );

		// Add the version for the framework.
		$this->instance( 'version', static::VERSION );
	}

	/**
	 * Adds the default service providers for the framework.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function registerDefaultProviders() {

		array_map( function( $provider ) {
			$this->provider( $provider );
		}, [
			AttrServiceProvider::class,
			LanguageServiceProvider::class,
			TemplatesServiceProvider::class,
			HierarchyServiceProvider::class,
			ViewServiceProvider::class
		] );
	}

	/**
	 * Adds the default static proxy classes.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function registerDefaultProxies() {

		$this->proxy( App::class, 'Backdrop\App' );
	}

	/**
	 * Bootstrap action/filter hook calls.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function bootstrapFilters() {

		require_once( $this->path . '/bootstrap-filters.php' );
	}

	/**
	 * Adds a service provider.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string|object $provider Service provider class name or object.
	 * @return void
	 */
	public function provider( $provider ): void {

		if ( is_string( $provider ) ) {
			$provider = $this->resolveProvider( $provider );
		}

		$this->providers[] = $provider;

		// Providers added after the application has booted would never
		// be registered or booted, so handle them immediately.
		if ( $this->booted ) {
			$this->registerProvider( $provider );
			$this->bootProvider( $provider );
		}
	}

	/**
	 * Creates a new instance of a service provider class.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  string $provider Service provider class name.
	 * @return object
	 */
	protected function resolveProvider( $provider ) {

		return new $provider( $this );
	}

	/**
	 * Calls a service provider's `register()` method if it exists.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  object $provider Service provider object.
	 * @return void
	 */
	protected function registerProvider( $provider ) {

		if ( method_exists( $provider, 'register' ) ) {
			$provider->register();
		}
	}

	/**
	 * Calls a service provider's `boot()` method if it exists.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  object $provider Service provider object.
	 * @return void
	 */
	protected function bootProvider( $provider ) {

		// Bail if this provider has already been booted.
		if ( in_array( $provider, $this->booted_providers, true ) ) {
			return;
		}

		if ( method_exists( $provider, 'boot' ) ) {
			$provider->boot();
		}

		$this->booted_providers[] = $provider;
	}

	/**
	 * Returns an array of service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function getProviders() {

		return $this->providers;
	}

	/**
	 * Calls the `register()` method of all the available service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function registerProviders() {

		foreach ( $this->getProviders() as $provider ) {
			$this->registerProvider( $provider );
		}
	}

	/**
	 * Calls the `boot()` method of all the registered service providers.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function bootProviders() {

		foreach ( $this->getProviders() as $provider ) {
			$this->bootProvider( $provider );
		}
	}

	/**
	 * Adds a static proxy alias. Developers must pass in a fully qualified
	 * class name and an alias class name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $class_name The fully qualified class name.
	 * @param  string $alias      The alias class name.
	 * @return void
	 */
	public function proxy( string $class_name, string $alias ): void {

		$this->proxies[ $class_name ] = $alias;
	}

	/**
	 * Registers the static proxy classes.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function registerProxies() {

		if ( ! Proxy::hasContainer() ) {
			Proxy::setContainer( $this );
		}

		foreach ( $this->proxies as $class => $alias ) {

			// Aliases are global, so skip any that already exist (for
			// example, when a parent and child theme both create an
			// application).
			$alias = ltrim( $alias, '\\' );

			if ( ! class_exists( $alias, false ) ) {
				class_alias( $class, $alias );
			}
		}
	}
}