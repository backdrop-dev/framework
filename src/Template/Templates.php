<?php
/**
 * Templates collection.
 *
 * This class extends the base collection and ensures that values added to the
 * collection are stored as Template objects.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Tools\Collection;

/**
 * Template collection class.
 *
 * @since  1.0.0
 * @access public
 */
class Templates extends Collection {

	/**
	 * Adds a new template to the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name  Template filename.
	 * @param  array  $value Template arguments.
	 * @return void
	 */
	public function add( $name, $value ) {

		parent::add(
			$name,
			new Template( $name, $value )
		);
	}
}