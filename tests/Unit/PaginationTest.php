<?php
/**
 * Pagination tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Pagination\Pagination;
use Backdrop\Tests\TestCase;

class PaginationTest extends TestCase {

	/**
	 * Sets up a posts query.
	 *
	 * @param int    $total   Total pages.
	 * @param int    $current Current page.
	 * @param string $link    Current page link.
	 */
	protected function query( $total, $current, $link = 'https://example.com/' ) {

		wp_test_reset( [
			'query_vars'   => [ 'paged' => $current ],
			'pagenum_link' => $link,
		] );

		$GLOBALS['wp_query']->max_num_pages = $total;
	}

	public function testNothingRendersForASinglePage(): void {

		$this->query( 1, 1 );

		$this->assertSame( '', \Backdrop\Pagination\render() );
	}

	public function testRendersPagesWithDefaultPreviousAndNextText(): void {

		$this->query( 3, 2 );

		$html = \Backdrop\Pagination\render();

		$this->assertStringContainsString( '<nav class="pagination pagination--posts" role="navigation">', $html );
		$this->assertStringContainsString( '>Previous</a>', $html );
		$this->assertStringContainsString( '>Next</a>', $html );
		$this->assertStringContainsString( '<span class="pagination__anchor pagination__anchor--current" aria-current="page">2</span>', $html );
		$this->assertStringContainsString( 'href="https://example.com/?paged=3"', $html );

		// Page one links to the base URL rather than `?paged=1`.
		$this->assertStringContainsString( 'href="https://example.com/"', $html );
		$this->assertStringNotContainsString( 'paged=1', $html );
	}

	public function testFirstAndLastPagesHaveNoPreviousOrNextLinks(): void {

		$this->query( 3, 1 );
		$this->assertStringNotContainsString( 'Previous', \Backdrop\Pagination\render() );

		$this->query( 3, 3 );
		$this->assertStringNotContainsString( 'Next', \Backdrop\Pagination\render() );
	}

	public function testLongPaginationUsesDots(): void {

		$this->query( 10, 5 );

		$items = $this->items( ( new Pagination() )->make() );

		$this->assertSame(
			[ 'prev', '1', 'dots', '4', 'current', '6', 'dots', '10', 'next' ],
			$items
		);
	}

	public function testShowAllListsEveryPage(): void {

		$this->query( 6, 3 );

		$items = $this->items( ( new Pagination( 'posts', [ 'show_all' => true, 'prev_next' => false ] ) )->make() );

		$this->assertSame( [ '1', '2', 'current', '4', '5', '6' ], $items );
	}

	public function testExistingQueryArgsAreKeptAndDecodedTheSameOnEveryPhpVersion(): void {

		// The page link is HTML-encoded. On PHP 7.4, `html_entity_decode()`
		// left `&#039;` encoded unless flags were passed explicitly.
		$this->query( 2, 1, 'https://example.com/?s=it&#039;s&amp;cat=1' );

		$html = \Backdrop\Pagination\render();

		$this->assertStringContainsString( 's=it%27s', $html );
		$this->assertStringContainsString( 'cat=1', $html );
		$this->assertStringNotContainsString( '%23039', $html );
	}

	public function testCustomArgsOverrideDefaults(): void {

		$this->query( 2, 1 );

		$html = \Backdrop\Pagination\render( 'posts', [
			'next_text'     => 'Older',
			'title_text'    => 'Posts navigation',
			'container_tag' => 'div',
		] );

		$this->assertStringContainsString( '<div class="pagination pagination--posts"', $html );
		$this->assertStringContainsString( '<h2 class="pagination__title screen-reader-text">Posts navigation</h2>', $html );
		$this->assertStringContainsString( '>Older</a>', $html );
	}

	/**
	 * Returns a compact list of the pagination items.
	 *
	 * @param  Pagination $pagination Pagination object.
	 * @return array
	 */
	protected function items( Pagination $pagination ) {

		$items = ( function() {
			return $this->items;
		} )->call( $pagination );

		return array_map( function( $item ) {
			return 'number' === $item['type'] ? (string) $item['content'] : $item['type'];
		}, $items );
	}
}
