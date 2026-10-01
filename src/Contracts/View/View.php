<?php
/**
 * View contract.
 *
 * Defines the interface for view objects that locate, render, and display
 * template files.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Contracts\View;

use Backdrop\Contracts\Displayable;
use Backdrop\Contracts\Renderable;

/**
 * View interface.
 *
 * @since  1.0.0
 * @access public
 */
interface View extends Renderable, Displayable {

	/**
	 * Returns the view slugs.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function slugs();

	/**
	 * Returns the absolute path to the template file.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function template();
}