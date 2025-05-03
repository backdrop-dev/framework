<?php
/**
 * Attributes contract.
 *
 * Defines the contract that classes for building HTML attributes must adhere to.
 * Extends the `Renderable` and `Displayable` contracts for handling output.
 * Attributes are meant to be used for HTML elements.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

 namespace Backdrop\Contracts\Attr;

 use Backdrop\Contracts\Displayable;
 use Backdrop\Contracts\Renderable;

/**
 * Attributes interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Attributes extends Renderable, Displayable {

	/**
	 * Returns an array of HTML attributes in name/value pairs. Attributes
	 * are not expected to be escaped. Escaping should be handled on output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function all();

	/**
	 * Returns a single, unescaped attribute's value.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return string
	 */
	public function get( $name );

	/**
	 * Adds custom data to the attribute object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string|array  $name
	 * @param  mixed         $value
	 * @return $this
	 */
	public function with( $key, $value = null );
}
