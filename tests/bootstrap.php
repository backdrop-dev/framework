<?php
/**
 * PHPUnit bootstrap.
 *
 * Loads the WordPress stubs and the framework, then provides a shared base
 * test case and a helper for getting the application instance.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace {

	define( 'BACKDROP_TEST_THEME_DIR', __DIR__ . '/fixtures/theme' );

	require_once __DIR__ . '/wordpress-stubs.php';

	wp_test_reset();

	// Use Composer's autoloader when it's available so the `autoload`
	// configuration in `composer.json` is tested too.
	if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
		require_once dirname( __DIR__ ) . '/vendor/autoload.php';
	} else {
		spl_autoload_register( function( $class ) {

			if ( 0 !== strpos( $class, 'Backdrop\\' ) ) {
				return;
			}

			$file = dirname( __DIR__ ) . '/src/' . str_replace( '\\', '/', substr( $class, 9 ) ) . '.php';

			if ( file_exists( $file ) ) {
				require $file;
			}
		} );

		require_once dirname( __DIR__ ) . '/src/bootstrap-backdrop.php';
	}
}

namespace Backdrop\Tests {

	use Backdrop\Core\Application;
	use Backdrop\Proxies\Proxy;
	use PHPUnit\Framework\TestCase as BaseTestCase;

	/**
	 * Returns the shared application, creating and booting one if needed.
	 *
	 * `ApplicationTest` runs first and creates the application itself, so
	 * that it can test the state before booting. Other tests use this helper
	 * so they also work when run on their own.
	 *
	 * @return Application
	 */
	function app() {

		if ( ! Proxy::hasContainer() ) {
			( new Application() )->boot();
		}

		return \Backdrop\app();
	}

	/**
	 * Base test case that resets the WordPress test state.
	 */
	abstract class TestCase extends BaseTestCase {

		protected function setUp(): void {

			parent::setUp();

			wp_test_reset();
		}
	}
}
