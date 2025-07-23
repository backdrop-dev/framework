<?php
/**
 * Customize class.
 *
 * Registers JS-based panel, section, and/or control types if booted. Otherwise,
 * theme authors must manually register these.
 *
 * @package   Backdrop Customize
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/customize
 */

namespace Backdrop\Customize;

use Backdrop\Contracts\Bootable;
use Backdrop\Customize\Controls\RadioImage;
use WP_Customize_Manager;
use Backdrop\App;

/**
 * Customize class.
 */
class Component implements Bootable {

    /**
     * Array of `Customizable` components bound to the container.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected $components = [];

    /**
     * Sets up initial object properties.
     *
     * @since  1.0.0
     * @access public
     * @param  array  $components  Array `Customizable` component names.
     * @return void
     */
    public function __construct( array $components = [] ) {

        $this->components = $components;
    }

    /**
     * Adds our customizer-related actions to the appropriate hooks.
     *
     * @since  1.0.0
     * @return void
     *
     * @access public
     */
    public function boot(): void {

		array_map( function( $callback ) {

            // Register panels, sections, settings, controls, and partials.
			add_action( 'customize_register', [ $this, $callback ] );
		}, [
			'registerPanels',
			'registerSections',
			'registerSettings',
			'registerControls',
			'registerPartials',
		] );
	}

    /**
     * Callback for registering panels.
     *
     * @link   https://developer.wordpress.org/themes/customize-api/customizer-objects/#panels
     * @since  1.0.0
     * @access public
     * @param  WP_Customize_Manager  $manager  Instance of the customize manager.
     * @return void
     */
    public function registerPanels( WP_Customize_Manager $manager ) {
		$panels = [
				'theme_global'  => esc_html__( 'Theme: Global',  'backdrop' ),
				'theme_header'  => esc_html__( 'Theme: Header',  'backdrop' ),
				'theme_content' => esc_html__( 'Theme: Content', 'backdrop' ),
				'theme_footer'  => esc_html__( 'Theme: Footer',  'backdrop' )
		];

		foreach ( $panels as $panel => $label ) {
				$manager->add_panel( $panel, [
						'title'    => $label,
						'priority' => 100
				] );
		}

		foreach ( $this->components as $component ) {

			App::resolve( $component )->registerPanels( $manager );
		}
    }

	/**
	 * Callback for registering sections.
	 *
	 * @link   https://developer.wordpress.org/themes/customize-api/customizer-objects/#sections
	 * @since  1.0.0
	 * @access public
	 * @param  WP_Customize_Manager  $manager  Instance of the customize manager.
	 * @return void
	 */
    public function registerSections( WP_Customize_Manager $manager ) {

        $manager->get_section( 'custom_css' )->panel = 'theme_global';
        $manager->get_section( 'title_tagline' )->panel = 'theme_header';
        $manager->get_section( 'title_tagline' )->title = esc_html__( 'Branding', 'backdrop' );
        $manager->get_section( 'static_front_page' )->panel = 'theme_content';
        $manager->get_section( 'static_front_page' )->priority = '5';
        $manager->remove_section( 'colors' );
        $manager->get_section( 'header_image' )->panel = 'theme_header';


		foreach ( $this->components as $component ) {

			App::resolve( $component )->registerSections( $manager );
		}
    }

	/**
	 * Callback for registering controls.
	 *
	 * @link   https://developer.wordpress.org/themes/customize-api/customizer-objects/#controls
	 * @since  1.0.0
	 * @access public
	 * @param  WP_Customize_Manager  $manager  Instance of the customize manager.
	 * @return void
	 */
    public function registerSettings( WP_Customize_Manager $manager ) {

		foreach ( $this->components as $component ) {

			App::resolve( $component )->registerSettings( $manager );
		}
	}

    /**
     * Registers our JS-based custom control types with WordPress.
     *
     * @param object $manager
     * @return void
     */
    public function registerControls( WP_Customize_Manager $manager ) {

        $controls = [
            RadioImage::class,
        ];

        array_map( static function ( $control ) use ( $manager ) {
            $manager->register_control_type( $control );
        }, $controls );

		foreach ( $this->components as $component ) {

			App::resolve( $component )->registerControls( $manager );
		}
    }

	/**
	 * Registers customizer partials.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  WP_Customize_Manager  $manager
	 * @return void
	 */
	public function registerPartials( WP_Customize_Manager $manager ) {

		foreach ( $this->components as $component ) {

			App::resolve( $component )->registerPartials( $manager );
		}
	}
}