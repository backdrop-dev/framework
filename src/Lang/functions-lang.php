<?php
/**
 * Language-related functions.
 *
 * Internationalization and translation functions that are mostly useful in the
 * framework but might be needed for themes.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Lang;

/**
 * Returns a hierarchy based on the locale, language, region, and text direction.
 * This can be useful for loading functions, files, scripts, or styles based on
 * the site's locale. Note that the locale is all lowercase and hyphenated (for
 * example, `en_US` becomes `en-us`).
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	$locale = strtolower( str_replace( '_', '-', is_admin() ? get_user_locale() : get_locale() ) );
	$lang   = strtolower( language() );
	$region = strtolower( region() );

	$hier = [ $locale ];

	if ( $region !== $locale ) {
		$hier[] = $region;
	}

	if ( $lang !== $locale ) {
		$hier[] = $lang;
	}

	$hier[] = is_rtl() ? 'rtl' : 'ltr';

	return apply_filters( 'backdrop/lang/hierarchy', $hier );
}

/**
 * Gets the language for the currently-viewed page.  It strips the region from
 * the locale if needed and just returns the language code.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function language( $locale = '' ) {

	if ( ! $locale ) {
		$locale = is_admin() ? get_user_locale() : get_locale();
	}

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page.  It strips the language from
 * the locale if needed.  Note that not all locales will have a region, so this
 * might actually return the same thing as `language()`.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function region( $locale = '' ) {

	if ( ! $locale ) {
		$locale = is_admin() ? get_user_locale() : get_locale();
	}

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}