<?php
/**
 * Collection tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Tests\TestCase;
use Backdrop\Tools\Collection;

class CollectionTest extends TestCase {

	public function testAddGetHasRemove(): void {

		$collection = new Collection();
		$collection->add( 'a', 1 );

		$this->assertTrue( $collection->has( 'a' ) );
		$this->assertSame( 1, $collection->get( 'a' ) );

		$collection->remove( 'a' );

		$this->assertFalse( $collection->has( 'a' ) );
		$this->assertSame( [], $collection->all() );
	}

	public function testMagicProperties(): void {

		$collection    = new Collection();
		$collection->b = 2;

		$this->assertTrue( isset( $collection->b ) );
		$this->assertSame( 2, $collection->b );
		$this->assertSame( [ 'b' => 2 ], $collection->all() );

		unset( $collection->b );

		$this->assertFalse( isset( $collection->b ) );
	}

	public function testArrayAccessAndCounting(): void {

		$collection = new Collection( [ 'x' => 'y' ] );

		$this->assertSame( 'y', $collection['x'] );
		$this->assertCount( 1, $collection );
	}
}
