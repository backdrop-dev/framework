<?php
/**
 * Language class.
 *
 * This file holds the `Language` class, which deals with loading textdomains
 * and locale-specific function files.
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
	 * in `style.css`.
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
	 * in `style.css`.
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

		$this->parent_textdomain = $theme->get( 'TextDomain' );
		$this->parent_path       = trailingslashit( get_template_directory() )
			. trim( $theme->get( 'DomainPath' ), '/' );

		if ( is_child_theme() ) {
			$child = wp_get_theme();

			$this->child_textdomain = $child->get( 'TextDomain' );
			$this->child_path       = trailingslashit( get_stylesheet_directory() )
				. trim( $child->get( 'DomainPath' ), '/' );
		}
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

		// Load the framework textdomain.
		add_action( 'after_setup_theme', [ $this, 'loadTextdomain' ], 95 );

		// Override textdomain loading for the framework domain.
		add_filter( 'override_load_textdomain', [ $this, 'overrideLoadTextdomain' ], 5, 3 );

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

		return $file ? "{$this->parent_path}/{$file}" : $this->parent_path;
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

		return $file ? "{$this->child_path}/{$file}" : $this->child_path;
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
	 * Loads the framework textdomain.
	 *
	 * An empty MO file path is intentionally passed because loading is
	 * overridden by `overrideLoadTextdomain()`.
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
	 * Framework strings use the parent theme's translations so that the same
	 * translation catalog does not need to be loaded multiple times.
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
	 * those files being overwritten when the parent theme is updated.
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