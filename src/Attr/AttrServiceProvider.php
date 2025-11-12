<?php
/**
 * Attributes service provider.
 *
 * This is the service provider for the attributes system. The primary purpose
 * of this is to use the container as a factory for creating attributes. By
 * adding this to the container, it also allows the implementation to be
 * overwritten. That way, any custom functions will utilize the new class.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Attr;

use Backdrop\Core\ServiceProvider;
use Backdrop\Contracts\Attr\Attributes;

/**
 * Attr provider class.
 *
 * @since  1.0.0
 * @access public
 */
class AttrServiceProvider extends ServiceProvider {

	/**
	 * Binds the implementation of the attributes contract to the container.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		$this->app->bind( Attributes::class, Attr::class );

		$this->app->alias( Attributes::class, 'attr' );
	}
}