<?php
/**
 * Framework bootstrap process.
 *
 * This file bootstraps parts of the framework that can't be autoloaded. We
 * define any global constants here and load any additional function files.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

/**
 * Define the directory path to the framework.
 */
if ( ! defined( 'BACKDROP_DIR' ) ) {
	define( 'BACKDROP_DIR', __DIR__ );
}

/**
 * Bootstrap the framework.
 *
 * Load the framework functions and define the bootstrap constant so the
 * bootstrap process only runs once.
 */
if ( ! defined( 'BACKDROP_BOOTSTRAPPED' ) ) {
	require_once __DIR__ . '/bootstrap-functions.php';

	define( 'BACKDROP_BOOTSTRAPPED', true );
}