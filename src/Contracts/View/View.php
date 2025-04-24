<?php
/**
 * View contract.
 *
 * View classes represent a template partial, generally speaking. Their purpose
 * should be to find a template file and render or display the output.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @link      https://github.com/backdrop-dev/framework
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Backdrop\Contracts\View;

use Backdrop\Contracts\Renderable;
use Backdrop\Contracts\Displayable;

/**
 * View interface.
 *
 * @since  1.0.0
 * @access public
 */
interface View extends Renderable, Displayable {

	/**
	 * Returns the array of slugs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function slugs();

	/**
	 * Returns the absolute path to the template file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function template();
}