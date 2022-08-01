<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Daftra_Dashboard
 *
 * This class contains all of the plugin dashaboard.
 *
 * @package		DAFTRA
 * @subpackage	Classes/Daftra_Dashboard
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Daftra_Dashboard{

	/**
	 * Our Daftra_Dashboard constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'load_carbon_fields' ) );
        add_action( 'carbon_fields_register_fields', array( $this, 'register_carbon_fields' ) );
    }

    /**
     * Include carbon fields as a library
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    public function load_carbon_fields()
    {
        require_once DGENNY_PLUGIN_DIR . 'libs/carbon-fields/vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }

    /**
     * Set dashboard page
     *
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    public function register_carbon_fields()
    {
        Container::make( 'theme_options', __( 'Daftra' , 'daftra') )
            ->add_fields( 
                array(
                    Field::make( 'text',  DAFTRA_SLUG . '_api_key' ),
					Field::make( 'text',  DAFTRA_SLUG . '_api_token' ),
				),
            );
    }
}
