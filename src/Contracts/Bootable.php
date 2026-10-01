<?php
/**
 * Bootable interface.
 *
 * Defines the contract that bootable classes should utilize. Bootable classes
 * should have a `boot()` method with the singular purpose of booting the action
 * and filter hooks for that class. This keeps action and filter registration
 * out of the class constructor. Most bootable classes are meant to be
 * single-instance classes that get loaded once per page request.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts;

/**
 * Bootable interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Bootable {

	/**
	 * Boots the class.
	 *
	 * Bootable classes should use this method to register their actions,
	 * filters, and other initialization hooks.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void;
}