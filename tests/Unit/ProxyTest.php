<?php
/**
 * Proxy and helper tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Core\ServiceProvider;
use Backdrop\Proxies\App;
use Backdrop\Proxies\Proxy;
use Backdrop\Tests\TestCase;
use RuntimeException;

use function Backdrop\Tests\app;

class ProxyTest extends TestCase {

	public function testProxyResolvesFromTheApplication(): void {

		$app = app();
		$app->instance( 'proxy.test', 'value' );

		$this->assertTrue( Proxy::hasContainer() );
		$this->assertSame( $app, App::resolve( 'app' ) );
		$this->assertSame( 'value', \Backdrop\app( 'proxy.test' ) );
	}

	public function testClearErrorWhenNoApplicationExists(): void {

		$container = $this->swapContainer( null );

		try {
			$this->assertFalse( Proxy::hasContainer() );

			$this->expectException( RuntimeException::class );
			$this->expectExceptionMessage( 'No Backdrop application has been created.' );

			\Backdrop\app();
		} finally {
			$this->swapContainer( $container );
		}
	}

	public function testServiceProviderDefaults(): void {

		$provider = new class( app() ) extends ServiceProvider {};

		// The default `register()` and `boot()` methods do nothing.
		$provider->register();
		$provider->boot();

		$this->assertTrue( true );
	}

	/**
	 * Replaces the proxy's container and returns the previous one.
	 *
	 * @param  mixed $container New container.
	 * @return mixed
	 */
	protected function swapContainer( $container ) {

		$swap = \Closure::bind( function( $container ) {
			$previous          = static::$container;
			static::$container = $container;
			return $previous;
		}, null, Proxy::class );

		return $swap( $container );
	}
}
