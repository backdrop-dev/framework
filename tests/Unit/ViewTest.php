<?php
/**
 * View tests.
 *
 * Templates live in `tests/fixtures/theme/resources/views`.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Contracts\View\Engine;
use Backdrop\Tests\TestCase;
use Backdrop\View\View;

use function Backdrop\Tests\app;

class ViewTest extends TestCase {

	/**
	 * @var Engine
	 */
	protected $engine;

	protected function setUp(): void {

		parent::setUp();

		$this->engine = app()->resolve( Engine::class );
	}

	public function testRendersTheFirstMatchingSlug(): void {

		$this->assertSame(
			'featured:Hello|data|view',
			trim( $this->engine->render( 'entry', [ 'missing', 'featured' ], [ 'title' => 'Hello' ] ) )
		);
	}

	public function testFallsBackToTheDefaultTemplate(): void {

		$this->assertSame(
			'default:Hello',
			trim( $this->engine->render( 'entry', [ 'missing' ], [ 'title' => 'Hello' ] ) )
		);
	}

	public function testFallsBackToTheNameTemplate(): void {

		$this->assertSame( 'footer', trim( \Backdrop\View\render( 'footer' ) ) );
	}

	public function testEmptySlugsAreSkipped(): void {

		$view = $this->engine->view( 'entry', [ '', 'featured' ] );

		$hierarchy = ( function() {
			return $this->hierarchy();
		} )->call( $view );

		$this->assertNotContains( 'entry/.php', $hierarchy );
		$this->assertSame( [ 'entry/featured.php', 'entry/default.php', 'entry.php' ], $hierarchy );
	}

	public function testMissingTemplatesRenderNothing(): void {

		$view = $this->engine->view( 'missing', [ 'slug' ] );

		$this->assertInstanceOf( View::class, $view );
		$this->assertSame( '', $view->template() );
		$this->assertSame( '', $view->render() );
	}

	public function testViewsCanBeCreatedWithoutData(): void {

		$view = new View( 'footer' );

		$this->assertSame( 'footer', trim( (string) $view ) );
	}

	public function testDisplayFiresTemplatePartHooks(): void {

		$fired = [];

		$callback = function( $name, $slug ) use ( &$fired ) {
			$fired[] = [ $name, $slug ];
		};

		add_action( 'get_template_part_entry', $callback, 10, 2 );

		try {
			$this->engine->render( 'entry', [ 'featured' ], [ 'title' => 'Hi' ] );
		} finally {
			remove_action( 'get_template_part_entry', $callback );
		}

		$this->assertSame( [ [ 'entry', 'featured' ] ], $fired );
	}
}
