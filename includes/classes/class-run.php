<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Daftra_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		DAFTRA
 * @subpackage	Classes/Daftra_Run
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Daftra_Run{

	/**
	 * Our Daftra_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugin_action_links_' . DAFTRA_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links )
	{
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=crb_carbon_fields_container_daftra.php' ) . '" aria-label="' . esc_attr__( 'View Daftra settings', 'daftra' ) . '">' . esc_html__( 'Settings', 'daftra' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles()
	{
		wp_enqueue_style( 'daftra-backend-styles', DAFTRA_PLUGIN_URL . 'assets/css/backend-styles.css', array(), DAFTRA_VERSION, 'all' );
		wp_enqueue_script( 'daftra-backend-scripts', DAFTRA_PLUGIN_URL . 'assets/js/backend-scripts.js', array(), DAFTRA_VERSION, false );
		wp_localize_script( 'daftra-backend-scripts', 'daftra', array(
			'plugin_name'   	=> __( DAFTRA_NAME, 'daftra' ),
		));
	}

}
