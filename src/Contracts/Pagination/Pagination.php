<?php
/**
 * Pagination interface.
 *
 * Defines the contract that pagination classes must implement.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\Pagination;

use Backdrop\Contracts\Displayable;
use Backdrop\Contracts\Renderable;

/**
 * Pagination interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Pagination extends Renderable, Displayable {

	/**
	 * Builds the pagination instance.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return Pagination
	 */
	public function make(): Pagination;
}