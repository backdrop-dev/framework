<?php
/**
 * Sidebar functions.
 *
 * Helper functions and template tags related to sidebars.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Sidebar;

/**
 * Outputs a sidebar name.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $sidebar_id
 * @return void
 */
function display_name( $sidebar_id ) {

	echo esc_html( render_name( $sidebar_id ) );
}

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since  1.0.0
 * @access public
 * @global array   $wp_registered_sidebars
 * @param  string  $sidebar_id
 * @return string
 */
function render_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	return isset( $wp_registered_sidebars[ $sidebar_id ] )
	       ? $wp_registered_sidebars[ $sidebar_id ]['name']
	       : '';
}