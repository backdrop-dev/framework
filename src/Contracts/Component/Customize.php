<?php
/**
 * Backdrop Core ( src/Contracts/Component/Customize.php )
 *
 * @package   Backdrop Core
 * @copyright Copyright (C) 2019-2021. Benjamin Lu
 * @license   GNU General PUblic License v2 or later ( https://www.gnu.org/licenses/gpl-2.0.html )
 * @author    Benjamin Lu ( https://benjlu.com )
 */

/**
 * Define namespace
 */
namespace Benlumia007\Backdrop\Contracts\Component;
use Benlumia007\Backdrop\Contracts\Bootable;

/**
 * Customize
 *
 * @since  2.0.0
 * @access public
 */
interface Customize {
	/**
	 * Register panels
	 */
	public function panels( $manager );
	
	/**
	 * Register sections
	 */
	public function sections( $manager );

	/**
	 * Register settings
	 */
	public function settings( $manager );

	/**
	 * Register controls
	 */
	public function controls( $manager );
}