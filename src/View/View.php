<?php
/**
 * View class.
 *
 * This file maintains the View class, which is used for locating and rendering
 * theme template files. Views provide functionality similar to WordPress'
 * `get_template_part()` while supporting template hierarchies and arbitrary
 * data passed directly to templates.
 *
 * Compatibility hooks are provided for WordPress core template functions such
 * as `get_template_part()`, `get_header()`, `get_footer()`, and `get_sidebar()`.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\View;

use Backdrop\Contracts\View\View as ViewContract;
use Backdrop\Tools\Collection;
use function Backdrop\Template\locate as locate_template;

/**
 * View class.
 *
 * @since  1.0.0
 * @access public
 */
class View implements ViewContract {

	/**
	 * View name.
	 *
	 * This is primarily used as the view directory name, but it can also be
	 * used as the final fallback filename.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * View slugs.
	 *
	 * These are used to build the template hierarchy based on the view name,
	 * such as `{$name}/{$slug}.php`. Slugs are checked in the order in which
	 * they are stored.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $slugs = [];

	/**
	 * Data passed to the view template.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var Collection|null
	 */
	protected $data = null;

	/**
	 * Located template filename.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string|null
	 */
	protected $template = null;

	/**
	 * Creates a new view.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string          $name  View name.
	 * @param array|string    $slugs Optional view slugs.
	 * @param Collection|null $data  Data passed to the view.
	 */
	public function __construct( $name, $slugs = [], Collection $data = null ) {

		$this->name  = $name;
		$this->slugs = (array) $slugs;
		$this->data  = $data;

		// Apply filters after all properties have been assigned so that the
		// complete view object is available to callbacks.
		$this->slugs = apply_filters(
			"backdrop/view/{$this->name}/slugs",
			$this->slugs,
			$this
		);

		$this->data = apply_filters(
			"backdrop/view/{$this->name}/data",
			$this->data,
			$this
		);
	}

	/**
	 * Returns the rendered view when the object is used as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function __toString() {

		return $this->render();
	}

	/**
	 * Returns the view slugs.
	 *
	 * @since  5.1.0
	 * @access public
	 *
	 * @return array
	 */
	public function slugs() {

		return (array) $this->slugs;
	}

	/**
	 * Builds the template hierarchy.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function hierarchy() {

		$templates = [];

		// Build the template hierarchy from the configured slugs.
		foreach ( $this->slugs as $slug ) {
			$templates[] = "{$this->name}/{$slug}.php";
		}

		// Add the default template unless it is already represented by a slug.
		if ( ! in_array( 'default', $this->slugs, true ) ) {
			$templates[] = "{$this->name}/default.php";
		}

		// Fall back to `{$name}.php` as a last resort.
		$templates[] = "{$this->name}.php";

		return apply_filters(
			"backdrop/view/{$this->name}/hierarchy",
			$templates,
			$this->slugs
		);
	}

	/**
	 * Locates the view template.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function locate() {

		return locate_template( $this->hierarchy() );
	}

	/**
	 * Returns the located template.
	 *
	 * The template location is cached after the first lookup.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function template() {

		if ( is_null( $this->template ) ) {
			$this->template = $this->locate();
		}

		return $this->template;
	}

	/**
	 * Outputs the view template.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function display(): void {

		// Fire compatibility hooks for WordPress template functions.
		$this->templatePartCompat();

		if ( $this->template() ) {

			// Maybe remove WordPress' attachment content filters.
			$this->maybeShiftAttachment();

			// Extract the collection so each item is available as an
			// individual variable within the template.
			if ( $this->data instanceof Collection ) {
				extract( $this->data->all() );
			}

			// Make the complete data collection and view object available.
			$data = $this->data;
			$view = $this;

			include( $this->template() );
		}
	}

	/**
	 * Renders and returns the view as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function render(): string {

		ob_start();

		$this->display();

		return ob_get_clean();
	}

	/**
	 * Fires WordPress-compatible template part action hooks.
	 *
	 * WordPress uses the terms `$slug` and `$name` differently from this view
	 * system. The first view slug is therefore used as the WordPress template
	 * part name when firing compatibility hooks.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function templatePartCompat() {

		$slug = $this->slugs
			? reset( $this->slugs )
			: null;

		// Compatibility with `get_header()`, `get_footer()`, and
		// `get_sidebar()`.
		if ( in_array( $this->name, [ 'header', 'footer', 'sidebar' ], true ) ) {

			do_action(
				"get_{$this->name}",
				$slug
			);

		// Compatibility with `get_template_part()`.
		} else {

			do_action(
				"get_template_part_{$this->name}",
				$this->name,
				$slug
			);
		}
	}

	/**
	 * Removes WordPress attachment content filters when a theme provides
	 * custom attachment output.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function maybeShiftAttachment() {

		if ( ! in_the_loop() || 'attachment' !== get_post_type() ) {
			return;
		}

		if ( in_array(
			$this->name,
			[ 'entry', 'post', 'entry/archive', 'entry/single' ],
			true
		) ) {

			remove_filter(
				'the_content',
				'prepend_attachment'
			);

		} elseif ( 'embed' === $this->name ) {

			remove_filter(
				'the_content',
				'prepend_attachment'
			);

			remove_filter(
				'the_excerpt_embed',
				'wp_embed_excerpt_attachment'
			);
		}
	}
}