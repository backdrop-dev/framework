<?php
/**
 * HTML attribute tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Attr\Attr;
use Backdrop\Tests\TestCase;

use function Backdrop\Tests\app;

class AttrTest extends TestCase {

	public function testDefaultClassUsesTheName(): void {

		$this->assertSame( 'class="site-header"', ( new Attr( 'site-header' ) )->render() );
	}

	public function testContextAddsAModifierClass(): void {

		$this->assertSame(
			'class="menu menu--primary"',
			( new Attr( 'menu', 'primary' ) )->render()
		);
	}

	public function testInputClassReplacesTheDefault(): void {

		$this->assertSame(
			'class="custom" id="main"',
			( new Attr( 'content', '', [ 'class' => 'custom', 'id' => 'main' ] ) )->render()
		);
	}

	public function testValuesAreEscaped(): void {

		$attr = new Attr( 'link', '', [
			'href'  => "https://example.com/?a=1&b='2'",
			'title' => '"quoted" <b>',
		] );

		$this->assertSame(
			'class="link" href="https://example.com/?a=1&#038;b=&#039;2&#039;" title="&quot;quoted&quot; &lt;b&gt;"',
			$attr->render()
		);
	}

	public function testArrayValuesAreJoinedAndNullValuesSkipped(): void {

		$attr = new Attr( 'box', '', [
			'data-list' => [ 'a', '', 'b' ],
			'data-none' => null,
		] );

		$this->assertSame( 'class="box" data-list="a b"', $attr->render() );
	}

	/**
	 * Names that match public methods used to be called as compatibility
	 * methods, causing infinite recursion or type errors.
	 *
	 * @dataProvider methodNames
	 *
	 * @param string $name Attribute name.
	 */
	public function testNamesMatchingMethodsAreSafe( $name ): void {

		$this->assertSame( 'class="' . $name . '"', ( new Attr( $name ) )->render() );
	}

	public function methodNames(): array {

		return [
			'render'  => [ 'render' ],
			'all'     => [ 'all' ],
			'get'     => [ 'get' ],
			'display' => [ 'display' ],
			'with'    => [ 'with' ],
		];
	}

	/**
	 * The default class (the attribute name) must not be passed to
	 * WordPress's class functions as an extra class. The stand-in class
	 * functions return `wp` plus the extra classes they receive.
	 *
	 * @dataProvider wordPressClassAttributes
	 *
	 * @param string $name     Attribute name.
	 * @param string $context  Context.
	 * @param array  $input    Input attributes.
	 * @param string $expected Expected rendered attributes.
	 */
	public function testWordPressClassesDoNotIncludeTheName( $name, $context, array $input, $expected ): void {

		$this->assertSame( $expected, ( new Attr( $name, $context, $input ) )->render() );
	}

	public function wordPressClassAttributes(): array {

		return [
			'post'                 => [ 'post', '', [], 'class="wp" id="post-1"' ],
			'entry'                => [ 'entry', '', [], 'class="wp" id="post-1"' ],
			'comment'              => [ 'comment', '', [], 'class="wp" id="comment-1"' ],
			'body'                 => [ 'body', '', [], 'class="wp" dir="ltr"' ],
			'post with context'    => [ 'post', 'archive', [], 'class="wp post--archive" id="post-1"' ],
			'body with context'    => [ 'body', 'home', [], 'class="wp body--home" dir="ltr"' ],
			'post with input'      => [ 'post', '', [ 'class' => 'custom post' ], 'class="wp custom post" id="post-1"' ],
			'comment with input'   => [ 'comment', '', [ 'class' => 'custom' ], 'class="wp custom" id="comment-1"' ],
		];
	}

	public function testGetReturnsAStringValue(): void {

		$attr = new Attr( 'box', '', [ 'id' => 'main', 'data-list' => [ 'a' ] ] );

		$this->assertSame( 'main', $attr->get( 'id' ) );
		$this->assertSame( '', $attr->get( 'missing' ) );
		$this->assertSame( '', $attr->get( 'data-list' ) );
	}

	public function testDisplayEchoesTheAttributes(): void {

		$this->expectOutputString( 'class="box"' );

		( new Attr( 'box' ) )->display();
	}

	public function testFiltersCanChangeTheAttributes(): void {

		$callback = function( $attr ) {
			$attr['data-filtered'] = 'yes';
			return $attr;
		};

		add_filter( 'backdrop/attr/filtered', $callback );

		try {
			$this->assertSame( 'class="filtered" data-filtered="yes"', ( new Attr( 'filtered' ) )->render() );
		} finally {
			remove_filter( 'backdrop/attr/filtered', $callback );
		}
	}

	public function testAttrHelperResolvesFromTheContainer(): void {

		app();

		$this->assertSame(
			'class="sidebar sidebar--primary" id="sidebar"',
			\Backdrop\Attr\render( 'sidebar', 'primary', [ 'id' => 'sidebar' ] )
		);
	}
}
