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
	 * - `array` return type hint introduced in PHP 7.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function all(): array;


	/**
	 * Returns a single, unescaped attribute's value.
	 *
	 * - `string` type hint introduced in PHP 7.0
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name  The name of the attribute.
	 * @return string
	 */
	public function get( string $name ): string;


	/**
	 * Adds custom data to the attribute object.
	 *
	 * - `string` and `array` type hints introduced in PHP 7.0
	 * - Return type `$this` (fluent interface) supported since PHP 5.0
	 * - `mixed` type not available until PHP 8.0, so only documented here
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string|array  $key    The attribute key or an array of key/value pairs.
	 * @param  mixed         $value  The value to assign (optional).
	 * @return static
	 */
	public function with( string|array $key, $value = null ): static;

}
