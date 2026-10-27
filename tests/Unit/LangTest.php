<?php
/**
 * Language tests.
 *
 * Translation behavior inside real WordPress is covered by the integration
 * tests in `tests/integration`. These tests cover the logic in isolation.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use Backdrop\Lang\Language;
use Backdrop\Tests\TestCase;

use function Backdrop\Lang\hierarchy;
use function Backdrop\Lang\language;
use function Backdrop\Lang\region;

class LangTest extends TestCase {

	/**
	 * Creates a language object for a theme with the given headers.
	 *
	 * @param  array $headers Theme headers.
	 * @param  array $state   Extra test state.
	 * @return Language
	 */
	protected function language( array $headers = [], array $state = [] ) {

		wp_test_reset( array_merge( [ 'theme_headers' => $headers ], $state ) );

		return new Language();
	}

	public function testFrameworkStringsUseTheThemeTranslations(): void {

		$lang = $this->language( [ 'TextDomain' => 'my-theme' ], [
			'translations' => [ 'my-theme' => [ '(Untitled)' => '(Sans titre)' ] ],
		] );

		$this->assertSame( '(Sans titre)', $lang->gettext( '(Untitled)', '(Untitled)', 'backdrop' ) );
		$this->assertSame( 'Missing', $lang->gettext( 'Missing', 'Missing', 'backdrop' ) );
	}

	public function testFrameworkStringsWithContextUseTheThemeTranslations(): void {

		$lang = $this->language( [ 'TextDomain' => 'my-theme' ], [
			'translations' => [ 'my-theme' => [ "yearly archives date format\004Y" => 'Y (fr)' ] ],
		] );

		$this->assertSame( 'Y (fr)', $lang->gettextWithContext( 'Y', 'Y', 'yearly archives date format', 'backdrop' ) );
	}

	public function testPluralFrameworkStringsUseTheThemeTranslations(): void {

		$lang = $this->language( [ 'TextDomain' => 'my-theme' ], [
			'translations' => [
				'my-theme' => [
					'%s item'           => [ '%s élément', '%s éléments' ],
					"count\004%s reply" => [ '%s réponse', '%s réponses' ],
				],
			],
		] );

		$this->assertSame( '%s élément', $lang->ngettext( '%s items', '%s item', '%s items', 1, 'backdrop' ) );
		$this->assertSame( '%s éléments', $lang->ngettext( '%s items', '%s item', '%s items', 3, 'backdrop' ) );
		$this->assertSame( '%s réponses', $lang->ngettextWithContext( '%s replies', '%s reply', '%s replies', 2, 'count', 'backdrop' ) );
	}

	/**
	 * @dataProvider skippedForwarding
	 *
	 * @param array $headers Theme headers.
	 * @param array $state   Test state.
	 */
	public function testStringsAreLeftAloneWhenForwardingDoesNotApply( array $headers, array $state ): void {

		$lang = $this->language( $headers, $state );

		$this->assertSame( 'original', $lang->gettext( 'original', '(Untitled)', 'backdrop' ) );
		$this->assertSame( 'original', $lang->gettextWithContext( 'original', 'Y', 'context', 'backdrop' ) );
		$this->assertSame( 'original', $lang->ngettext( 'original', 'a', 'b', 2, 'backdrop' ) );
		$this->assertSame( 'original', $lang->ngettextWithContext( 'original', 'a', 'b', 2, 'context', 'backdrop' ) );
	}

	public function skippedForwarding(): array {

		$translations = [ 'translations' => [ 'backdrop' => [ '(Untitled)' => 'loop' ] ] ];

		return [
			'theme without a textdomain'           => [ [ 'TextDomain' => '' ], $translations ],
			// Forwarding to `backdrop` would call the same filter again.
			'theme textdomain is backdrop'         => [ [ 'TextDomain' => 'backdrop' ], $translations ],
			// A dedicated framework translation file takes priority.
			'backdrop translations already loaded' => [ [ 'TextDomain' => 'my-theme' ], [ 'loaded_domains' => [ 'backdrop' ] ] ],
		];
	}

	public function testBootRegistersTheTranslationFilters(): void {

		$lang = $this->language();
		$lang->boot();

		foreach ( [
			'gettext_backdrop'               => 'gettext',
			'gettext_with_context_backdrop'  => 'gettextWithContext',
			'ngettext_backdrop'              => 'ngettext',
			'ngettext_with_context_backdrop' => 'ngettextWithContext',
		] as $hook => $method ) {
			$this->assertTrue( $this->isRegistered( $hook, [ $lang, $method ] ), "{$hook} is not registered." );
		}

		// The old approach copied translations that WordPress 6.7+ hasn't
		// loaded yet, so it's no longer hooked.
		$this->assertFalse( $this->isRegistered( 'after_setup_theme', [ $lang, 'loadTextdomain' ] ) );
		$this->assertFalse( $this->isRegistered( 'override_load_textdomain', [ $lang, 'overrideLoadTextdomain' ] ) );
	}

	public function testLanguageDirectoryDefaultsToLanguages(): void {

		$lang = $this->language( [ 'DomainPath' => '' ] );

		$this->assertSame( BACKDROP_TEST_THEME_DIR . '/languages', $lang->parentPath() );
		$this->assertSame( BACKDROP_TEST_THEME_DIR . '/languages/fr-fr.php', $lang->parentPath( 'fr-fr.php' ) );
	}

	public function testLanguageDirectoryUsesTheDomainPathHeader(): void {

		$lang = $this->language( [ 'DomainPath' => '/lang/' ] );

		$this->assertSame( BACKDROP_TEST_THEME_DIR . '/lang', $lang->parentPath() );
	}

	public function testLocaleFunctionsFileIsLoadedFromTheDefaultDirectory(): void {

		$GLOBALS['backdrop_test_locale_files'] = [];

		$this->language( [ 'DomainPath' => '' ], [ 'locale' => 'fr_FR' ] )->loadLocaleFunctions();

		$this->assertSame( [ 'fr-fr.php' ], $GLOBALS['backdrop_test_locale_files'] );
	}

	public function testThemeTranslationFileInTheLanguageDirectoryIsUsed(): void {

		$lang = $this->language( [ 'TextDomain' => 'backdrop-test' ], [ 'locale' => 'fr_FR' ] );

		$this->assertSame(
			BACKDROP_TEST_THEME_DIR . '/languages/backdrop-test-fr_FR.mo',
			$lang->loadTextdomainMofile( '/wp-content/languages/themes/backdrop-test-fr_FR.mo', 'backdrop-test' )
		);

		// Other textdomains are left alone.
		$this->assertSame(
			'/wp-content/languages/plugins/other-fr_FR.mo',
			$lang->loadTextdomainMofile( '/wp-content/languages/plugins/other-fr_FR.mo', 'other' )
		);
	}

	/**
	 * @dataProvider locales
	 *
	 * @param string $locale    Locale.
	 * @param bool   $rtl       Whether the locale is right-to-left.
	 * @param string $language  Expected language.
	 * @param string $region    Expected region.
	 * @param array  $hierarchy Expected hierarchy.
	 */
	public function testLocaleParsing( $locale, $rtl, $language, $region, array $hierarchy ): void {

		wp_test_reset( [ 'locale' => $locale, 'is_rtl' => $rtl ] );

		$this->assertSame( $language, language() );
		$this->assertSame( $region, region() );
		$this->assertSame( $language, language( $locale ) );
		$this->assertSame( $region, region( $locale ) );
		$this->assertSame( $hierarchy, hierarchy() );
	}

	public function locales(): array {

		return [
			'en_US'          => [ 'en_US', false, 'en', 'us', [ 'en-us', 'us', 'en', 'ltr' ] ],
			'fr_FR'          => [ 'fr_FR', false, 'fr', 'fr', [ 'fr-fr', 'fr', 'ltr' ] ],
			'he_IL'          => [ 'he_IL', true, 'he', 'il', [ 'he-il', 'il', 'he', 'rtl' ] ],
			'ja'             => [ 'ja', false, 'ja', 'ja', [ 'ja', 'ltr' ] ],
			'de_DE_formal'   => [ 'de_DE_formal', false, 'de', 'de', [ 'de-de-formal', 'de-de', 'de', 'ltr' ] ],
			'pt_PT_ao90'     => [ 'pt_PT_ao90', false, 'pt', 'pt', [ 'pt-pt-ao90', 'pt-pt', 'pt', 'ltr' ] ],
			'de_CH_informal' => [ 'de_CH_informal', false, 'de', 'ch', [ 'de-ch-informal', 'de-ch', 'ch', 'de', 'ltr' ] ],
		];
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
