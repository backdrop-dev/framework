<?php
/**
 * Bas customize control.
 *
 * This is a base customize control class for our other controls to extend.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Customize\Controls;

use WP_Customize_Control;

/**
 * Multiple checkbox customize control class.
 *
 * @since  1.0.0
 * @access public
 */
abstract class Control extends WP_Customize_Control {

    /**
     * This is the PHP callback for rendering the control content. JS-based
     * controls require this method to be empty. Because most of our classes
     * utilize JS templates, we're defining this in the base class to not
     * worry about it in our subclasses.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    protected function render_content() {}
}