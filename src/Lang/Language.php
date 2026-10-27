<?php
/**
 * Language class.
 *
 * This file holds the `Language` class, which translates the framework's
 * strings with the theme's translations and loads locale-specific function
 * files.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Lang;

use Backdrop\Contracts\Lang\Language as LanguageContract;

/**
 * Language class.
 *
 * @since  1.0.0
 * @access public
 */
class Language implements LanguageContract {

	/**
	 * The parent theme's textdomain.
	 *
	 * Gets set to the value of the `Text Domain` header in `style.css`.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $parent_textdomain = '';

	/**
	 * The child theme's textdomain.
	 *
	 * Gets set to the value of the `Text Domain` header in `style.css`.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $child_textdomain = '';

	/**
	 * Absolute path to the parent theme's language directory.
	 *
	 * Theme authors should set the relative path via the `Domain Path` header
	 * in `style.css`. Like WordPress, it defaults to `/languages`.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $parent_path = '';

	/**
	 * Absolute path to the child theme's language directory.
	 *
	 * Theme authors should set the relative path via the `Domain Path` header
	 * in `style.css`. Like WordPress, it defaults to `/languages`.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $child_path = '';

	/**
	 * Stores the language-related theme information.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

		$theme = wp_get_theme( get_template() );

		$this->parent_textdomain = (string) $theme->get( 'TextDomain' );
		$this->parent_path       = trailingslashit( get_template_directory() )
			. $this->domainPath( $theme );

		if ( is_child_theme() ) {
			$child = wp_get_theme();

			$this->child_textdomain = (string) $child->get( 'TextDomain' );
			$this->child_path       = trailingslashit( get_stylesheet_directory() )
				. $this->domainPath( $child );
		}
	}

	/**
	 * Returns a theme's relative language directory.
	 *
	 * Uses the `Domain Path` header and, like WordPress, falls back to
	 * `languages` when the header isn't set.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  object $theme Theme object (`WP_Theme`).
	 * @return string
	 */
	protected function domainPath( $theme ): string {

		$path = trim( (string) $theme->get( 'DomainPath' ), '/' );

		return '' !== $path ? $path : 'languages';
	}

	/**
	 * Adds the class actions and filters.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		// Load the locale functions files.
		add_action( 'after_setup_theme', [ $this, 'loadLocaleFunctions' ], ~PHP_INT_MAX );

		// Translate framework strings with the parent theme's translations.
		add_filter( 'gettext_backdrop', [ $this, 'gettext' ], 10, 3 );
		add_filter( 'gettext_with_context_backdrop', [ $this, 'gettextWithContext' ], 10, 4 );
		add_filter( 'ngettext_backdrop', [ $this, 'ngettext' ], 10, 5 );
		add_filter( 'ngettext_with_context_backdrop', [ $this, 'ngettextWithContext' ], 10, 6 );

		// Allow child themes to load parent theme translations.
		add_filter( 'load_textdomain_mofile', [ $this, 'loadTextdomainMofile' ], 10, 2 );
	}

	/**
	 * Gets the parent theme textdomain.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function parentTextdomain(): string {

		return $this->parent_textdomain;
	}

	/**
	 * Gets the child theme textdomain.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function childTextdomain(): string {

		return $this->child_textdomain;
	}

	/**
	 * Returns the parent theme language directory path.
	 *
	 * No trailing slash is included unless a file is appended.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $file Optional file path.
	 * @return string
	 */
	public function parentPath( $file = '' ): string {

		$file = ltrim( $file, '/' );

		$path = untrailingslashit( $this->parent_path );

		return $file ? "{$path}/{$file}" : $path;
	}

	/**
	 * Returns the child theme language directory path.
	 *
	 * No trailing slash is included unless a file is appended.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $file Optional file path.
	 * @return string
	 */
	public function childPath( $file = '' ): string {

		$file = ltrim( $file, '/' );

		$path = untrailingslashit( $this->child_path );

		return $file ? "{$path}/{$file}" : $path;
	}

	/**
	 * Loads locale-specific function files.
	 *
	 * Locale filenames should be lowercase and hyphenated. For example,
	 * `en_US` becomes `en-us.php`. Child theme locale files are loaded before
	 * parent theme locale files.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function loadLocaleFunctions(): void {

		$locale = is_admin() ? get_user_locale() : get_locale();
		$locale = strtolower( str_replace( '_', '-', $locale ) );

		$child_func = $this->childPath( "{$locale}.php" );
		$theme_func = $this->parentPath( "{$locale}.php" );

		if ( is_child_theme() && file_exists( $child_func ) ) {
			require_once $child_func;
		}

		if ( file_exists( $theme_func ) ) {
			require_once $theme_func;
		}
	}

	/**
	 * Translates a framework string with the parent theme's translations.
	 *
	 * Framework strings use the `backdrop` textdomain. Instead of shipping
	 * their own translation files, they're translated through the parent
	 * theme's textdomain, so theme authors include the framework's strings
	 * in their theme's translation files.
	 *
	 * This uses the `gettext_backdrop` filter, which works with WordPress's
	 * just-in-time translation loading (WordPress 6.7+), `switch_to_locale()`,
	 * and ClassicPress.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $translation Translated text.
	 * @param  string $text        Text to translate.
	 * @param  string $domain      Textdomain (`backdrop`).
	 * @return string
	 */
	public function gettext( $translation, $text, $domain ) {

		if ( ! $this->shouldForward() ) {
			return $translation;
		}

		return translate( $text, $this->parent_textdomain );
	}

	/**
	 * Translates a framework string with context through the parent theme's
	 * translations.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $translation Translated text.
	 * @param  string $text        Text to translate.
	 * @param  string $context     Context information for the translators.
	 * @param  string $domain      Textdomain (`backdrop`).
	 * @return string
	 */
	public function gettextWithContext( $translation, $text, $context, $domain ) {

		if ( ! $this->shouldForward() ) {
			return $translation;
		}

		return translate_with_gettext_context( $text, $context, $this->parent_textdomain );
	}

	/**
	 * Translates a singular or plural framework string through the parent
	 * theme's translations.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $translation Translated text.
	 * @param  string $single      The text for a single item.
	 * @param  string $plural      The text for multiple items.
	 * @param  int    $number      The number used to choose the form.
	 * @param  string $domain      Textdomain (`backdrop`).
	 * @return string
	 */
	public function ngettext( $translation, $single, $plural, $number, $domain ) {

		if ( ! $this->shouldForward() ) {
			return $translation;
		}

		return _n( $single, $plural, $number, $this->parent_textdomain ); // phpcs:ignore WordPress.WP.I18n
	}

	/**
	 * Translates a singular or plural framework string with context through
	 * the parent theme's translations.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $translation Translated text.
	 * @param  string $single      The text for a single item.
	 * @param  string $plural      The text for multiple items.
	 * @param  int    $number      The number used to choose the form.
	 * @param  string $context     Context information for the translators.
	 * @param  string $domain      Textdomain (`backdrop`).
	 * @return string
	 */
	public function ngettextWithContext( $translation, $single, $plural, $number, $context, $domain ) {

		if ( ! $this->shouldForward() ) {
			return $translation;
		}

		return _nx( $single, $plural, $number, $context, $this->parent_textdomain ); // phpcs:ignore WordPress.WP.I18n
	}

	/**
	 * Determines whether framework strings should be translated through the
	 * parent theme's textdomain.
	 *
	 * They aren't when the theme has no textdomain, when the theme's
	 * textdomain is `backdrop` itself (which would loop), or when a
	 * dedicated `backdrop` translation file has been loaded.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function shouldForward(): bool {

		return '' !== $this->parent_textdomain
			&& 'backdrop' !== $this->parent_textdomain
			&& ! is_textdomain_loaded( 'backdrop' );
	}

	/**
	 * Loads the framework textdomain.
	 *
	 * @deprecated No longer hooked. Since WordPress 6.7 loads theme
	 *             translations just in time, there are no translations to
	 *             copy when this runs. Framework strings are now translated
	 *             with the `gettext_backdrop` filters instead.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function loadTextdomain(): void {

		load_textdomain( 'backdrop', '' );
	}

	/**
	 * Overrides textdomain loading for the framework domain.
	 *
	 * @deprecated No longer hooked. See `loadTextdomain()`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @global array $l10n
	 *
	 * @param  bool   $override Whether to override textdomain loading.
	 * @param  string $domain   Textdomain being loaded.
	 * @param  string $mofile   Path to the MO file.
	 * @return bool
	 */
	public function overrideLoadTextdomain( $override, $domain, $mofile ) {

		global $l10n;

		if ( 'backdrop' === $domain ) {
			$theme_textdomain = $this->parentTextdomain();

			if ( $theme_textdomain && isset( $l10n[ $theme_textdomain ] ) ) {
				$l10n[ $domain ] = $l10n[ $theme_textdomain ];
			}

			$override = true;
		}

		return $override;
	}

	/**
	 * Filters the textdomain MO file path.
	 *
	 * This allows a child theme to contain parent theme translations without
	 * those files being overwritten when the parent theme is updated. A
	 * `{$domain}-{$locale}.mo` file in the child theme's language directory
	 * replaces the parent theme's file for that locale, so it needs to
	 * contain every string, including the framework's.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $mofile Path to the MO file.
	 * @param  string $domain Textdomain currently being filtered.
	 * @return string
	 */
	public function loadTextdomainMofile( $mofile, $domain ) {

		if (
			$domain === $this->parentTextdomain()
			|| $domain === $this->childTextdomain()
		) {
			$locale = is_admin() ? get_user_locale() : get_locale();

			$child_mofile = $this->childPath( "{$domain}-{$locale}.mo" );
			$theme_mofile = $this->parentPath( "{$domain}-{$locale}.mo" );

			if ( is_child_theme() && file_exists( $child_mofile ) ) {
				$mofile = $child_mofile;
			} elseif ( file_exists( $theme_mofile ) ) {
				$mofile = $theme_mofile;
			}
		}

		return $mofile;
	}
}