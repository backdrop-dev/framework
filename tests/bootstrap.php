<?php
/**
 * PHPUnit bootstrap.
 *
 * Loads Composer's autoloader (which also loads `backdrop-dev/contracts`) and
 * provides a shared base test case.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace {

	if ( ! file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
		fwrite( STDERR, "Run `composer install` before running the tests.\n" );
		exit( 1 );
	}

	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
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
	 * Base test case.
	 */
	abstract class TestCase extends BaseTestCase {}
}
