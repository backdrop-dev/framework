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
 * Locale variants such as `de_DE_formal` also include their base locale, so
 * the hierarchy for `de_DE_formal` is `de-de-formal`, `de-de`, `de`, `ltr`.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function hierarchy() {

	$current = is_admin() ? get_user_locale() : get_locale();
	$locale  = strtolower( str_replace( '_', '-', $current ) );
	$lang    = strtolower( language( $current ) );
	$region  = strtolower( region( $current ) );

	$hier = [ $locale ];

	// Locale variants (e.g., `de_DE_formal`) fall back to their base locale.
	$parts = explode( '_', $current );

	if ( 2 < count( $parts ) ) {
		$hier[] = strtolower( "{$parts[0]}-{$parts[1]}" );
	}

	if ( $region !== $locale ) {
		$hier[] = $region;
	}

	if ( $lang !== $locale ) {
		$hier[] = $lang;
	}

	$hier[] = is_rtl() ? 'rtl' : 'ltr';

	// The region and language can match (e.g., `fr_FR`), so remove duplicates.
	return apply_filters( 'backdrop/lang/hierarchy', array_values( array_unique( $hier ) ) );
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

	// The language is the first part of the locale (e.g., `de` in `de_DE_formal`).
	$parts = explode( '_', $locale );

	return sanitize_key( $parts[0] );
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

	// The region is the second part of the locale (e.g., `DE` in `de_DE_formal`).
	// Locales without a region (e.g., `ja`) return the language.
	$parts = explode( '_', $locale );

	return sanitize_key( isset( $parts[1] ) ? $parts[1] : $parts[0] );
}