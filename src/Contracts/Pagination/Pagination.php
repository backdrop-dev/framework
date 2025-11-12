<?php
/**
 * Pagination interface.
 *
 * Defines the interface that pagination classes must use.
 *
 * Compatible with PHP 8.0+ (uses return type declarations).
 * - `self` and interface return types supported since PHP 7.0
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
	 * Builds the pagination instance.
	 *
	 * Compatible with PHP 8.0+ (typed return value).
	 * - Interface return types supported since PHP 7.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Pagination
	 */
	public function make(): Pagination;
}
