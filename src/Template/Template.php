<?php
/**
 * Object template class.
 *
 * This class allows templates for any object type, including posts, terms,
 * and users. When viewing a single post, term archive, or user/author archive,
 * the template can be used.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Contracts\Template\Template as TemplateContract;

/**
 * Object template class.
 *
 * @since  1.0.0
 * @access public
 */
class Template implements TemplateContract {

	/**
	 * Template type.
	 *
	 * By default, templates are post templates. This can be changed to support
	 * other object types, such as terms or users.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $type = 'post';

	/**
	 * Subtypes supported by the template.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $subtype = [];

	/**
	 * Template filename.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $filename = '';

	/**
	 * Internationalized template label.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Returns the template filename when the object is converted to a string.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function __toString() {

		return $this->filename();
	}

	/**
	 * Creates a new template object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $filename Template filename.
	 * @param  array  $args     Template arguments.
	 * @return void
	 */
	public function __construct( $filename, array $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		if ( isset( $args['subtype'] ) ) {
			$this->subtype = (array) $args['subtype'];
		}

		// Allow `post_types` as an alias for `subtype`.
		if ( isset( $args['post_types'] ) ) {
			$this->subtype = (array) $args['post_types'];
		}

		$this->filename = $filename;
	}

	/**
	 * Returns the filename relative to the template location.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function filename() {

		return $this->filename;
	}

	/**
	 * Returns the internationalized template label.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function label() {

		return $this->label;
	}

	/**
	 * Checks whether the template is of the given type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $type Template type.
	 * @return bool
	 */
	public function isType( $type ) {

		return $type === $this->type;
	}

	/**
	 * Checks whether the template supports a specific subtype.
	 *
	 * An empty subtype collection means that the template supports all
	 * subtypes for its template type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $subtype Template subtype.
	 * @return bool
	 */
	public function hasSubtype( $subtype ) {

		return ! $this->subtype
			|| in_array( $subtype, $this->subtype, true );
	}

	/**
	 * Checks whether the template supports a specific post type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $type Post type.
	 * @return bool
	 */
	public function forPostType( $type ) {

		return $this->isType( 'post' )
			&& $this->hasSubtype( $type );
	}
}