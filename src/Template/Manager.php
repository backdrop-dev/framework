<?php
/**
 * Template manager.
 *
 * Handles template registration and the action and filter hooks used by the
 * template system.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Contracts\Bootable;

/**
 * Template manager class.
 *
 * @since  1.0.0
 * @access public
 */
class Manager implements Bootable {

	/**
	 * Templates collection.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var Templates
	 */
	protected $templates;

	/**
	 * Creates the template manager.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  Templates $templates Templates collection.
	 * @return void
	 */
	public function __construct( Templates $templates ) {

		$this->templates = $templates;
	}

	/**
	 * Sets up the template manager actions and filters.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		add_action( 'init', [ $this, 'register' ], 95 );

		add_filter(
			'theme_templates',
			[ $this, 'postTemplates' ],
			5,
			4
		);
	}

	/**
	 * Fires the template registration action.
	 *
	 * Themes should register their templates on this hook.
	 *
	 * This method is public because it is used as a WordPress hook callback.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register(): void {

		do_action(
			'backdrop/templates/register',
			$this->templates
		);
	}

	/**
	 * Adds registered templates to the theme template list.
	 *
	 * This method is public because it is used as a WordPress hook callback.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array  $templates Existing theme templates.
	 * @param  object $theme     Theme object.
	 * @param  object $post      Post object.
	 * @param  string $post_type Post type.
	 * @return array
	 */
	public function postTemplates( $templates, $theme, $post, $post_type ) {

		foreach ( $this->templates->all() as $template ) {
			if ( $template->forPostType( $post_type ) ) {
				$templates[ $template->filename() ] = esc_html(
					$template->label()
				);
			}
		}

		return $templates;
	}
}