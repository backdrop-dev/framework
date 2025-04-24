<?php
/**
 * Framwork bootstrap process.
 *
 * This file bootstraps parts of the framework that can't be autoloaded. We
 * define any global constants here and load any additional functions files.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

# Define the directory path to the framework. This shouldn't need changing
# unless doing something really out there or just for clarity.
if ( ! defined( 'BACKDROP_DIR' ) ) {

	define( 'BACKDROP_DIR', __DIR__ );
}

# Check if the framework has been bootstrapped. If not, load the bootstrap files
# and get the framework set up.
if ( ! defined( 'BACKDROP_BOOTSTRAPPED' ) ) {

	require_once( 'bootstrap-functions.php' );

	define( 'BACKDROP_BOOTSTRAPPED', true );
}