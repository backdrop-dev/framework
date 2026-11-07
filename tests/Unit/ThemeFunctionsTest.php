<?php
/**
 * Tests for the theme template functions and filters.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Tests\TestCase;
use WP_Error;

class ThemeFunctionsTest extends TestCase {

	public function testCommentsLinkIsHiddenWhenThereAreNoCommentsAndCommentsAreClosed(): void {

		// `get_comments_number()` returns a numeric string in WordPress.
		wp_test_reset( [ 'comments_num' => '0' ] );

		$this->assertSame( '', \Backdrop\Post\render_comments_link() );
	}

	public function testCommentsLinkShowsWhenCommentsAreOpen(): void {

		wp_test_reset( [ 'comments_num' => '0', 'comments_open' => true ] );

		$this->assertSame(
			'<a class="entry__comments" href="https://example.com/hello-world/#comments">0 Comments</a>',
			\Backdrop\Post\render_comments_link()
		);
	}

	public function testIsApprovedChecksTheGivenComment(): void {

		wp_test_reset( [
			'comments' => [
				1 => (object) [ 'comment_ID' => 1, 'status' => 'approved' ],
				2 => (object) [ 'comment_ID' => 2, 'status' => 'unapproved' ],
			],
			'comment'  => 1,
		] );

		$this->assertTrue( \Backdrop\Comment\is_approved() );
		$this->assertFalse( \Backdrop\Comment\is_approved( 2 ) );
		$this->assertFalse( \Backdrop\Comment\is_approved( 99 ) );
	}

	/**
	 * @dataProvider replyLinks
	 *
	 * @param string $link     Reply link from WordPress.
	 * @param string $expected Expected link.
	 */
	public function testReplyLinkClassIsReplaced( $link, $expected ): void {

		wp_test_reset( [
			'options'    => [ 'thread_comments' => 1, 'thread_comments_depth' => 5 ],
			'reply_link' => $link,
		] );

		$this->assertSame( $expected, \Backdrop\Comment\render_reply_link() );
	}

	public function replyLinks(): array {

		return [
			'single quotes' => [
				"<a rel='nofollow' class='comment-reply-link' href='#'>Reply</a>",
				"<a rel='nofollow' class='comment-reply comment-reply-link' href='#'>Reply</a>",
			],
			'empty class'   => [
				'<a class="" href="#">Reply</a>',
				'<a class="comment-reply comment-reply-link" href="#">Reply</a>',
			],
		];
	}

	public function testReplyLinkIsHiddenForPingbacks(): void {

		wp_test_reset( [
			'options'      => [ 'thread_comments' => 1 ],
			'comment_type' => 'pingback',
			'reply_link'   => '<a class="x" href="#">Reply</a>',
		] );

		$this->assertSame( '', \Backdrop\Comment\render_reply_link() );
	}

	/**
	 * @dataProvider locales
	 *
	 * @param string $locale   Locale.
	 * @param bool   $rtl      Whether the locale is right-to-left.
	 * @param array  $expected Expected hierarchy.
	 */
	public function testLanguageHierarchyHasNoDuplicates( $locale, $rtl, array $expected ): void {

		wp_test_reset( [ 'locale' => $locale, 'is_rtl' => $rtl ] );

		$this->assertSame( $expected, \Backdrop\Lang\hierarchy() );
	}

	public function locales(): array {

		return [
			'en_US' => [ 'en_US', false, [ 'en-us', 'us', 'en', 'ltr' ] ],
			'fr_FR' => [ 'fr_FR', false, [ 'fr-fr', 'fr', 'ltr' ] ],
			'he_IL' => [ 'he_IL', true, [ 'he-il', 'il', 'he', 'rtl' ] ],
		];
	}

	public function testArchiveDescriptionIgnoresErrors(): void {

		wp_test_reset( [
			'conditionals' => [ 'is_category' => true ],
			'term_field'   => new WP_Error( 'invalid_term', 'Empty Term.' ),
		] );

		$this->assertSame( 'Fallback', \Backdrop\archive_description_filter( 'Fallback' ) );
	}

	public function testArchiveDescriptionUsesTheTermDescription(): void {

		wp_test_reset( [
			'conditionals' => [ 'is_category' => true ],
			'term_field'   => 'Category description',
		] );

		$this->assertSame( 'Category description', \Backdrop\archive_description_filter( 'Fallback' ) );
	}

	public function testExcerptMoreWrapsTheTextInALink(): void {

		$this->assertSame(
			' <a href="https://example.com/hello-world/" class="entry__more-link">Read more</a>',
			\Backdrop\excerpt_more( " Read more\n" )
		);

		$this->assertSame( '<a href="#">More</a>', \Backdrop\excerpt_more( '<a href="#">More</a>' ) );
	}

	/**
	 * @dataProvider monthTitles
	 *
	 * @param mixed  $title    What `single_month_title()` returns.
	 * @param string $expected Expected title.
	 */
	public function testMonthTitleHasNoLeadingSpace( $title, $expected ): void {

		wp_test_reset( [ 'single_month_title' => $title ] );

		$this->assertSame( $expected, \Backdrop\Util\Title::month() );
	}

	public function monthTitles(): array {

		return [
			'month archive'     => [ ' March 2026', 'March 2026' ],
			'not a month query' => [ false, '' ],
		];
	}

	/**
	 * @dataProvider generatorThemes
	 *
	 * @param array  $headers  Theme headers.
	 * @param string $expected Expected meta tag.
	 */
	public function testGeneratorMetaTag( array $headers, $expected ): void {

		wp_test_reset( [ 'theme_headers' => $headers ] );

		$this->expectOutputString( $expected . "\n" );

		\Backdrop\meta_generator();
	}

	public function generatorThemes(): array {

		return [
			'with version'    => [ [ 'Name' => 'My Theme', 'Version' => '1.2.0' ], '<meta name="generator" content="My Theme 1.2.0" />' ],
			'without version' => [ [ 'Name' => 'My Theme', 'Version' => '' ], '<meta name="generator" content="My Theme" />' ],
		];
	}

	/**
	 * @dataProvider menuItems
	 *
	 * @param array $classes  Classes from WordPress.
	 * @param mixed $custom   The `_menu_item_classes` meta value.
	 * @param array $expected Expected classes.
	 */
	public function testMenuItemClasses( array $classes, $custom, array $expected ): void {

		wp_test_reset( [ 'post_meta' => [ 5 => [ '_menu_item_classes' => $custom ] ] ] );

		$this->assertSame( $expected, array_values( \Backdrop\nav_menu_css_class( $classes, (object) [ 'ID' => 5, 'type' => 'custom', 'object' => 'custom' ] ) ) );
	}

	public function menuItems(): array {

		return [
			// WordPress stores `['']` when no custom classes were added.
			'no custom classes' => [ [ 'menu-item' ], [ '' ], [ 'menu__item' ] ],
			'custom classes'    => [ [ 'menu-item' ], [ 'one', ' ', 'two' ], [ 'menu__item', 'one', 'two' ] ],
			'current item'      => [ [ 'menu-item', 'current-menu-item' ], [ '' ], [ 'menu__item', 'menu__item--current' ] ],
			'parent with child' => [ [ 'current-menu-parent', 'menu-item-has-children' ], '', [ 'menu__item', 'menu__item--parent', 'has-children' ] ],
		];
	}
}
