<?php
/**
 * Pagination functions.
 *
 * Helper functions and template tags related to pagination.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Pagination;

/**
 * Outputs the pagination output.
 *
 * @since  1.0.0
 * @access public
 * @param  string $context
 * @param  array  $args
 * @return object
 */
function display( $context = 'posts', array $args = [] ) {

	( new Pagination( $context, $args ) )->make()->display();
}

/**
 * Returns the pagination output.
 *
 * @since  1.0.0
 * @access public
 * @param  string $context
 * @param  array  $args
 * @return object
 */
function render( $context = 'posts', array $args = [] ) {

	return ( new Pagination( $context, $args ) )->make()->render();
}