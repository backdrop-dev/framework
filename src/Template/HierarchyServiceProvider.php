<?php
/**
 * Template hierarchy service provider.
 *
 * Registers the template hierarchy with the application container and boots
 * the hierarchy when the application is booted.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Template;

use Backdrop\Contracts\Template\Hierarchy as TemplateHierarchy;
use Backdrop\Core\ServiceProvider;

/**
 * Template hierarchy service provider.
 *
 * @since  1.0.0
 * @access public
 */
class HierarchyServiceProvider extends ServiceProvider {

	/**
	 * Registers the template hierarchy with the container.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register(): void {

		$this->app->singleton(
			TemplateHierarchy::class,
			Hierarchy::class
		);

		$this->app->alias(
			TemplateHierarchy::class,
			'template/hierarchy'
		);
	}

	/**
	 * Boots the template hierarchy.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function boot(): void {

		$this->app->resolve( 'template/hierarchy' )->boot();
	}
}