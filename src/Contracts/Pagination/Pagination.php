<?php
/**
 * Pagination interface.
 *
 * Defines the interface that pagination classes must use.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\Pagination;

use Backdrop\Contracts\Renderable;
use Backdrop\Contracts\Displayable;

/**
 * Pagination interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Pagination extends Renderable, Displayable {

	/**
	 * Builds the pagination.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Pagination
	 */
	public function make();
}