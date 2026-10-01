<?php
/**
 * Template tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Template\Hierarchy;
use Backdrop\Template\Manager;
use Backdrop\Template\Template;
use Backdrop\Template\Templates;
use Backdrop\Tests\TestCase;

use function Backdrop\Template\filter_templates;
use function Backdrop\Template\locate;
use function Backdrop\Template\path;

class TemplateTest extends TestCase {

	public function testTemplatesWithoutSubtypesApplyToAllPostTypes(): void {

		$template = new Template( 'templates/wide.php', [ 'label' => 'Wide' ] );

		$this->assertSame( 'templates/wide.php', $template->filename() );
		$this->assertSame( 'templates/wide.php', (string) $template );
		$this->assertSame( 'Wide', $template->label() );
		$this->assertTrue( $template->forPostType( 'post' ) );
		$this->assertTrue( $template->forPostType( 'page' ) );
	}

	public function testPostTypesLimitTheTemplate(): void {

		$template = new Template( 'templates/landing.php', [ 'post_types' => 'page' ] );

		$this->assertTrue( $template->forPostType( 'page' ) );
		$this->assertFalse( $template->forPostType( 'post' ) );
	}

	public function testManagerAddsTemplatesForThePostType(): void {

		$templates = new Templates();
		$templates->add( 'templates/landing.php', [ 'label' => 'Landing <b>', 'subtype' => [ 'page' ] ] );

		$manager = new Manager( $templates );

		$this->assertSame(
			[ 'templates/landing.php' => 'Landing &lt;b&gt;' ],
			$manager->postTemplates( [], null, null, 'page' )
		);

		$this->assertSame( [], $manager->postTemplates( [], null, null, 'post' ) );
	}

	public function testPathCanBeFiltered(): void {

		$this->assertSame( 'resources/views', path() );
		$this->assertSame( 'resources/views/entry.php', path( '/entry.php' ) );

		$callback = function() {
			return 'views';
		};

		add_filter( 'backdrop/template/path', $callback );

		try {
			$this->assertSame( 'views/entry.php', path( 'entry.php' ) );
		} finally {
			remove_filter( 'backdrop/template/path', $callback );
		}
	}

	public function testLocateReturnsTheFirstExistingTemplate(): void {

		$this->assertSame(
			BACKDROP_TEST_THEME_DIR . '/resources/views/entry/featured.php',
			locate( [ '', 'entry/missing.php', '/entry/featured.php', 'entry/default.php' ] )
		);

		$this->assertSame( '', locate( 'entry/missing.php' ) );
	}

	public function testFilterTemplatesAddsTheViewsPath(): void {

		$this->assertSame(
			[ 'resources/views/single.php', 'resources/views/page.php' ],
			filter_templates( [ 'single.php', 'resources/views/page.php' ] )
		);
	}

	public function testHierarchyRegistersEveryTemplateType(): void {

		$hierarchy = new Hierarchy();
		$hierarchy->boot();

		foreach ( [ 'index', '404', 'single', 'page', 'privacypolicy', 'frontpage', 'attachment' ] as $type ) {
			$this->assertTrue(
				$this->isRegistered( "{$type}_template_hierarchy", [ $hierarchy, 'templateHierarchy' ] ),
				"The {$type} template hierarchy is not filtered."
			);
		}
	}

	public function testHierarchyRecordsAndPrefixesTemplates(): void {

		$hierarchy = new Hierarchy();

		$this->assertSame(
			[ 'resources/views/single-post.php', 'resources/views/single.php' ],
			$hierarchy->templateHierarchy( [ 'single-post.php', 'single.php' ] )
		);

		$hierarchy->templateHierarchy( [ 'single.php', 'index.php' ] );

		$this->assertSame( [ 'single-post', 'single', 'index' ], $hierarchy->hierarchy() );
	}

	public function testLocatedTemplateIsUsedForTheInclude(): void {

		$hierarchy = new Hierarchy();

		$this->assertSame( '', $hierarchy->template( '/theme/resources/views/single.php' ) );
		$this->assertSame( '', $hierarchy->template( '/theme/resources/views/index.php' ) );

		$this->assertSame( '/theme/resources/views/single.php', $hierarchy->templateInclude( '' ) );
		$this->assertSame( '/custom.php', $hierarchy->templateInclude( '/custom.php' ) );
	}

	/**
	 * Checks whether a callback is registered for a hook.
	 *
	 * @param  string   $hook     Hook name.
	 * @param  callable $callback Callback.
	 * @return bool
	 */
	protected function isRegistered( $hook, $callback ) {

		foreach ( isset( $GLOBALS['wp_test_hooks'][ $hook ] ) ? $GLOBALS['wp_test_hooks'][ $hook ] : [] as $callbacks ) {
			foreach ( $callbacks as $registered ) {
				if ( $registered[0] === $callback ) {
					return true;
				}
			}
		}

		return false;
	}
}
