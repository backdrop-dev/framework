<?php
/**
 * Application tests.
 *
 * These tests run first (see `phpunit.xml.dist`) because they check the state
 * before and after the first application boots. The methods run in order and
 * share the same application.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Contracts\Bootable;
use Backdrop\Core\Application;
use Backdrop\Core\ServiceProvider;
use Backdrop\Proxies\Proxy;
use Backdrop\Tests\TestCase;

class ApplicationTest extends TestCase {

	/**
	 * The application shared by the tests in this class.
	 *
	 * @var Application
	 */
	protected static $app;

	public function testHelpersWorkBeforeBoot(): void {

		if ( Proxy::hasContainer() ) {
			$this->markTestSkipped( 'An application already exists. Run the full suite to test the first boot.' );
		}

		static::$app = new Application();

		$this->assertSame( static::$app, \Backdrop\app() );
		$this->assertFalse( \Backdrop\booted() );
		$this->assertFalse( static::$app->isBooted() );
	}

	public function testProvidersCanUseTheAppHelperWhenRegistering(): void {

		static::$app = static::$app ?: \Backdrop\Tests\app();

		static::$app->provider( UsesAppProvider::class );

		$this->assertSame( static::$app, UsesAppProvider::$seen );
	}

	public function testBootMarksTheApplicationAsBooted(): void {

		static::$app->boot();

		$this->assertTrue( \Backdrop\booted() );
		$this->assertTrue( static::$app->isBooted() );
		$this->assertTrue( class_exists( 'Backdrop\App', false ) );
		$this->assertSame( static::$app, \Backdrop\App::resolve( 'app' ) );
	}

	public function testApplicationIsBootable(): void {

		$this->assertInstanceOf( Bootable::class, static::$app );
	}

	public function testSecondApplicationDoesNotReplaceTheFirst(): void {

		// A parent and child theme may both create an application. The
		// second one must not redeclare `Backdrop\App` (which would emit
		// a warning and fail this test) or replace the first instance.
		$second = new Application();
		$second->boot();

		$this->assertSame( static::$app, \Backdrop\app() );
	}

	public function testReusingTheBootedApplicationAsDocumented(): void {

		$app = \Backdrop\booted() ? \Backdrop\app() : new Application();

		$this->assertSame( static::$app, $app );
	}

	public function testProviderAddedAfterBootIsBootedOnce(): void {

		CountingProvider::reset();

		static::$app->provider( CountingProvider::class );

		$this->assertSame( 1, CountingProvider::$registered );
		$this->assertSame( 1, CountingProvider::$booted );
		$this->assertSame( 'registered', static::$app->resolve( 'counting' ) );

		// Booting again doesn't boot the provider a second time.
		static::$app->boot();

		$this->assertSame( 1, CountingProvider::$booted );
	}

	public function testEachProviderInstanceIsBooted(): void {

		CountingProvider::reset();

		$app = new Application();
		$app->provider( new CountingProvider( $app ) );
		$app->provider( new CountingProvider( $app ) );
		$app->boot();

		$this->assertSame( 2, CountingProvider::$booted );
	}

	public function testVersion(): void {

		$this->assertSame( '2.0.0', Application::VERSION );
		$this->assertSame( Application::VERSION, static::$app->resolve( 'version' ) );
	}
}

/**
 * Service provider that counts how often it's registered and booted.
 */
class CountingProvider extends ServiceProvider {

	public static $registered = 0;

	public static $booted = 0;

	public static function reset() {

		static::$registered = 0;
		static::$booted     = 0;
	}

	public function register(): void {

		static::$registered++;

		$this->app->instance( 'counting', 'registered' );
	}

	public function boot(): void {

		static::$booted++;
	}
}

/**
 * Service provider that uses `Backdrop\app()` while registering.
 */
class UsesAppProvider extends ServiceProvider {

	public static $seen;

	public function register(): void {

		static::$seen = \Backdrop\app();
	}
}
