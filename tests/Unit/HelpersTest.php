<?php
/**
 * Helper function tests.
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

use function Backdrop\collect;
use function Backdrop\hex_to_rgb;
use function Backdrop\is_plural;
use function Backdrop\replace_html_class;

class HelpersTest extends TestCase {

	/**
	 * @dataProvider htmlClasses
	 *
	 * @param string $html     Input HTML.
	 * @param string $expected Expected HTML.
	 */
	public function testReplaceHtmlClass( $html, $expected ): void {

		$this->assertSame( $expected, replace_html_class( 'new', $html ) );
	}

	public function htmlClasses(): array {

		return [
			'double quotes' => [ '<a class="old" href="#">', '<a class="new" href="#">' ],
			'single quotes' => [ "<a class='old' href='#'>", "<a class='new' href='#'>" ],
			'empty class'   => [ '<a class="" href="#">', '<a class="new" href="#">' ],
			'apostrophe'    => [ '<a class="it\'s" href="#">', '<a class="new" href="#">' ],
			'first only'    => [ '<a class="a"><b class="b">', '<a class="new"><b class="b">' ],
			'no class'      => [ '<a href="#">', '<a href="#">' ],
		];
	}

	/**
	 * @dataProvider hexColors
	 *
	 * @param string $hex      Hex color.
	 * @param array  $expected Expected RGB.
	 */
	public function testHexToRgb( $hex, array $expected ): void {

		$this->assertSame( $expected, hex_to_rgb( $hex ) );
	}

	public function hexColors(): array {

		return [
			'six digits'   => [ '#ff8000', [ 'r' => 255, 'g' => 128, 'b' => 0 ] ],
			'no hash'      => [ '336699', [ 'r' => 51, 'g' => 102, 'b' => 153 ] ],
			'three digits' => [ '#fff', [ 'r' => 255, 'g' => 255, 'b' => 255 ] ],
			'invalid'      => [ 'zz', [ 'r' => 0, 'g' => 0, 'b' => 0 ] ],
			'empty'        => [ '', [ 'r' => 0, 'g' => 0, 'b' => 0 ] ],
		];
	}

	public function testCollect(): void {

		$collection = collect( [ 'a' => 1 ] );

		$this->assertInstanceOf( Collection::class, $collection );
		$this->assertSame( [ 'a' => 1 ], $collection->all() );
	}

	public function testIsPlural(): void {

		$this->assertFalse( is_plural() );

		wp_test_reset( [ 'conditionals' => [ 'is_search' => true ] ] );

		$this->assertTrue( is_plural() );
	}

	public function testIsClassicPress(): void {

		$this->assertSame( function_exists( 'classicpress_version' ), \Backdrop\is_classicpress() );
	}
}
