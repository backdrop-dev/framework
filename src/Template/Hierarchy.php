<?php
/**
 * Template hierarchy class.
 *
 * The framework has its own template hierarchy that can be used instead of the
 * default WordPress template hierarchy. It is not much different than the
 * default. It was built to extend the default by making it smarter and more
 * flexible. The goal is to give theme developers and end users an
 * easy-to-override system that doesn't involve massive amounts of conditional
 * tags within files.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use WP_User;
use Backdrop\Contracts\Template\Hierarchy as TemplateHierarchy;

/**
 * Overrides the core WordPress template hierarchy.
 *
 * @since  1.0.0
 * @access public
 */
class Hierarchy implements TemplateHierarchy {

	/**
	 * Array of template types in WordPress.
	 *
	 * @link   https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $types = [
		'index',
		'404',
		'archive',
		'author',
		'category',
		'tag',
		'taxonomy',
		'date',
		'embed',
		'home',
		'frontpage',
		'page',
		'paged',
		'search',
		'single',
		'singular',
		'attachment'
	];

	/**
	 * Located template found while processing the template hierarchy.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $located = '';

	/**
	 * Entire template hierarchy for the current page view.
	 *
	 * Template names are stored without the `.php` file extension.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $hierarchy = [];

	/**
	 * Sets up template hierarchy filters.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		add_filter( 'frontpage_template_hierarchy', [ $this, 'frontPage' ], 5 );

		add_filter( 'single_template_hierarchy', [ $this, 'single' ], 5 );
		add_filter( 'page_template_hierarchy', [ $this, 'single' ], 5 );
		add_filter( 'attachment_template_hierarchy', [ $this, 'single' ], 5 );

		add_filter( 'taxonomy_template_hierarchy', [ $this, 'taxonomy' ], 5 );
		add_filter( 'category_template_hierarchy', [ $this, 'taxonomy' ], 5 );
		add_filter( 'tag_template_hierarchy', [ $this, 'taxonomy' ], 5 );

		add_filter( 'author_template_hierarchy', [ $this, 'author' ], 5 );

		add_filter( 'date_template_hierarchy', [ $this, 'date' ], 5 );

		foreach ( $this->types as $type ) {
			add_filter(
				"{$type}_template_hierarchy",
				[ $this, 'templateHierarchy' ],
				PHP_INT_MAX
			);

			add_filter(
				"{$type}_template",
				[ $this, 'template' ],
				PHP_INT_MAX
			);
		}

		add_filter(
			'template_include',
			[ $this, 'templateInclude' ],
			PHP_INT_MAX
		);
	}

	/**
	 * Returns the full template hierarchy for the current page load.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function hierarchy() {

		return $this->hierarchy;
	}

	/**
	 * Filters the front page template hierarchy.
	 *
	 * This disables the `front-page.php` template when posts are displayed on
	 * the front page. It also allows a custom page template selected by the
	 * user to take precedence.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function frontPage( $templates ) {

		$templates = [];

		if ( ! is_home() ) {
			$custom = get_page_template_slug( get_queried_object_id() );

			if ( $custom ) {
				$templates[] = $custom;
			}

			$templates[] = 'front-page.php';
		}

		return $templates;
	}

	/**
	 * Filters the singular post template hierarchy.
	 *
	 * Handles all singular post types, including pages and attachments.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function single( $templates ) {

		$templates = [];

		$post = get_queried_object();

		if ( ! $post ) {
			return $templates;
		}

		$name = urldecode( $post->post_name );

		$custom = get_page_template_slug( $post->ID );

		if ( $custom ) {
			$templates[] = $custom;
		}

		if ( is_attachment() ) {
			$type    = get_post_mime_type( $post );
			$subtype = '';

			if ( false !== strpos( $type, '/' ) ) {
				list( $type, $subtype ) = explode( '/', $type, 2 );
			}

			if ( $subtype ) {
				$templates[] = "attachment-{$type}-{$subtype}.php";
				$templates[] = "attachment-{$subtype}.php";
			}

			$templates[] = "attachment-{$type}.php";
		} else {
			$templates[] = "single-{$post->post_type}-{$post->ID}.php";
			$templates[] = "{$post->post_type}-{$post->ID}.php";

			$templates[] = "single-{$post->post_type}-{$name}.php";
			$templates[] = "{$post->post_type}-{$name}.php";
		}

		$templates[] = "single-{$post->post_type}.php";
		$templates[] = "{$post->post_type}.php";

		$templates[] = 'single.php';

		return $templates;
	}

	/**
	 * Filters taxonomy archive template hierarchies.
	 *
	 * Categories and post tags are handled the same way as other taxonomies.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function taxonomy( $templates ) {

		$templates = [];

		$term = get_queried_object();

		if ( ! $term ) {
			return $templates;
		}

		$slug = urldecode( $term->slug );

		if ( 'post_format' === $term->taxonomy ) {
			$slug = str_replace( 'post-format-', '', $slug );
		}

		$templates[] = "taxonomy-{$term->taxonomy}-{$slug}.php";
		$templates[] = "taxonomy-{$term->taxonomy}.php";
		$templates[] = 'taxonomy.php';

		return $templates;
	}

	/**
	 * Filters the author archive template hierarchy.
	 *
	 * Allows templates for specific authors and user roles.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function author( $templates ) {

		$templates = [];

		$author_id = absint( get_query_var( 'author' ) );
		$name      = get_the_author_meta( 'user_nicename', $author_id );
		$user      = new WP_User( $author_id );

		if ( $name ) {
			$templates[] = "user-{$name}.php";
		}

		if ( is_array( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				$templates[] = "user-role-{$role}.php";
			}
		}

		$templates[] = 'user.php';
		$templates[] = 'author.php';

		return $templates;
	}

	/**
	 * Filters the date archive template hierarchy.
	 *
	 * Adds templates for minute, hour, day, week, month, and year archives.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function date( $templates ) {

		$templates = [];

		if ( is_time() ) {
			if ( get_query_var( 'minute' ) ) {
				$templates[] = 'minute.php';
			} elseif ( get_query_var( 'hour' ) ) {
				$templates[] = 'hour.php';
			}

			$templates[] = 'time.php';
		} elseif ( is_day() ) {
			$templates[] = 'day.php';
		} elseif ( get_query_var( 'w' ) ) {
			$templates[] = 'week.php';
		} elseif ( is_month() ) {
			$templates[] = 'month.php';
		} elseif ( is_year() ) {
			$templates[] = 'year.php';
		}

		$templates[] = 'date.php';

		return $templates;
	}

	/**
	 * Filters a queried template hierarchy and prefixes templates with the
	 * configured template path.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $templates Template hierarchy.
	 * @return array
	 */
	public function templateHierarchy( $templates ) {

		if (
			function_exists( 'is_woocommerce' )
			&& is_woocommerce()
			&& ! in_array( 'woocommerce.php', $templates, true )
		) {
			$templates = array_merge(
				[ 'woocommerce.php' ],
				$templates
			);
		}

		$this->hierarchy = array_merge(
			$this->hierarchy,
			array_map(
				function( $template ) {

					return pathinfo(
						$template,
						PATHINFO_FILENAME
					);
				},
				$templates
			)
		);

		$this->hierarchy = array_values(
			array_unique( $this->hierarchy )
		);

		return filter_templates( $templates );
	}

	/**
	 * Captures the first located template.
	 *
	 * Returning an empty string allows the core template hierarchy to continue
	 * processing so that the framework can capture the complete hierarchy.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $template Located template.
	 * @return string
	 */
	public function template( $template ) {

		if ( ! $this->located && $template ) {
			$this->located = $template;
		}

		return '';
	}

	/**
	 * Returns the template that should ultimately be included.
	 *
	 * If another component supplies a non-string value, it is returned
	 * unchanged. Otherwise, the current template is preferred and the
	 * previously located template is used as a fallback.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $template Template to include.
	 * @return mixed
	 */
	public function templateInclude( $template ) {

		if ( ! is_string( $template ) ) {
			return $template;
		}

		return $template ?: $this->located;
	}
}