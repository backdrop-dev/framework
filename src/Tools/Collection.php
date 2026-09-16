<?php
/**
 * Collection class.
 *
 * This file houses the Collection class, which is used for storing collections
 * of data as key/value pairs. Values may contain any type of data. Named keys
 * are recommended when items need to be accessed individually.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tools;

use ArrayObject;

/**
 * Collection class.
 *
 * @since  1.0.0
 * @access public
 */
class Collection extends ArrayObject {

	/**
	 * Adds an item to the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name  Item name.
	 * @param  mixed  $value Item value.
	 * @return void
	 */
	public function add( $name, $value ) {

		$this->offsetSet( $name, $value );
	}

	/**
	 * Removes an item from the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return void
	 */
	public function remove( $name ) {

		$this->offsetUnset( $name );
	}

	/**
	 * Checks whether an item exists in the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return bool
	 */
	public function has( $name ) {

		return $this->offsetExists( $name );
	}

	/**
	 * Returns an item from the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return mixed
	 */
	public function get( $name ) {

		return $this->offsetGet( $name );
	}

	/**
	 * Returns all items in the collection.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function all() {

		return $this->getArrayCopy();
	}

	/**
	 * Adds an item to the collection using property syntax.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name  Item name.
	 * @param  mixed  $value Item value.
	 * @return void
	 */
	public function __set( $name, $value ) {

		$this->offsetSet( $name, $value );
	}

	/**
	 * Removes an item from the collection using property syntax.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return void
	 */
	public function __unset( $name ) {

		$this->offsetUnset( $name );
	}

	/**
	 * Checks whether an item exists using property syntax.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return bool
	 */
	public function __isset( $name ) {

		return $this->offsetExists( $name );
	}

	/**
	 * Returns an item from the collection using property syntax.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $name Item name.
	 * @return mixed
	 */
	public function __get( $name ) {

		return $this->offsetGet( $name );
	}
}