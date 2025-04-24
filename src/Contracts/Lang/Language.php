<?php
/**
 * Language interface.
 *
 * Defines the contract that a language class should use.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\Lang;

use Backdrop\Contracts\Bootable;

/**
 * Language interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Language extends Bootable {

	/**
	 * Returns the parent theme textdomain.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function parentTextdomain();

	/**
	 * Returns the child theme textdomain.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function childTextdomain();

	/**
	 * Returns the full directory path for the parent theme's domain path
	 * and should allow a file/path to be appended.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function parentPath( $file = '' );

	/**
	 * Returns the full directory path for the child theme's domain path
	 * and should allow a file/path to be appended.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $file
	 * @return string
	 */
	public function childPath( $file = '' );
}
