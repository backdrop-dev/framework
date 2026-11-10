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

use Backdrop\Contracts\Attr\Attributes;
use Backdrop\Contracts\Lang\Language;
use Backdrop\Contracts\Template\Hierarchy;
use Backdrop\Contracts\View\Engine;
use Backdrop\Core\Application;
use Backdrop\Core\ServiceProvider;
use Backdrop\Proxies\Proxy;
use Backdrop\Template\Manager;
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

	public function testBootMarksTheApplicationAsBooted(): void {

		static::$app = static::$app ?: \Backdrop\Tests\app();
		static::$app->boot();

		$this->assertTrue( \Backdrop\booted() );
		$this->assertTrue( static::$app->isBooted() );
		$this->assertTrue( class_exists( 'Backdrop\App', false ) );
		$this->assertSame( static::$app, \Backdrop\App::resolve( 'app' ) );
	}

	public function testBootingTwiceDoesNotRegisterHooksAgain(): void {

		$count = $this->countCallbacks( 'template_include' );

		static::$app->boot();

		$this->assertSame( $count, $this->countCallbacks( 'template_include' ) );
	}

	public function testSecondApplicationDoesNotReplaceTheFirst(): void {

		// A parent and child theme may both create an application. The
		// second one must not redeclare `Backdrop\App` (which would emit
		// a warning and fail this test) or replace the first instance.
		$second = new Application();
		$second->boot();

		$this->assertSame( static::$app, \Backdrop\app() );
	}

	public function testProviderAddedAfterBootIsRegisteredAndBooted(): void {

		CountingProvider::$registered = 0;
		CountingProvider::$booted     = 0;

		static::$app->provider( CountingProvider::class );

		$this->assertSame( 1, CountingProvider::$registered );
		$this->assertSame( 1, CountingProvider::$booted );
		$this->assertSame( 'registered', static::$app->resolve( 'counting' ) );

		// Booting again doesn't boot the provider a second time.
		static::$app->boot();

		$this->assertSame( 1, CountingProvider::$booted );
	}

	public function testDefaultServicesResolve(): void {

		$this->assertInstanceOf( Attributes::class, static::$app->resolve( 'attr', [ 'name' => 'test' ] ) );
		$this->assertInstanceOf( Language::class, static::$app->resolve( 'language' ) );
		$this->assertInstanceOf( Manager::class, static::$app->resolve( 'template/manager' ) );
		$this->assertInstanceOf( Hierarchy::class, static::$app->resolve( 'template/hierarchy' ) );
		$this->assertInstanceOf( Engine::class, static::$app->resolve( 'view/engine' ) );

		// Shared bindings return the same instance.
		$this->assertSame( static::$app->resolve( 'view/engine' ), static::$app->resolve( 'view/engine' ) );
	}

	public function testPathAndVersionHelpers(): void {

		$src = str_replace( '\\', '/', dirname( __DIR__, 2 ) . '/src' );

		$this->assertSame( $src, str_replace( '\\', '/', \Backdrop\path() ) );
		$this->assertSame( $src . '/Core/Application.php', str_replace( '\\', '/', \Backdrop\path( '/Core/Application.php' ) ) );
		$this->assertSame( Application::VERSION, \Backdrop\version() );
	}

	/**
	 * Counts the callbacks registered for a hook.
	 *
	 * @param  string $hook Hook name.
	 * @return int
	 */
	protected function countCallbacks( $hook ) {

		$count = 0;

		foreach ( isset( $GLOBALS['wp_test_hooks'][ $hook ] ) ? $GLOBALS['wp_test_hooks'][ $hook ] : [] as $callbacks ) {
			$count += count( $callbacks );
		}

		return $count;
	}
}

/**
 * Service provider that counts how often it's registered and booted.
 */
class CountingProvider extends ServiceProvider {

	public static $registered = 0;

	public static $booted = 0;

	public function register(): void {

		static::$registered++;

		$this->app->instance( 'counting', 'registered' );
	}

	public function boot(): void {

		static::$booted++;
	}
}
