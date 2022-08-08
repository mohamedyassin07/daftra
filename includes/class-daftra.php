<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Daftra' ) ) :

	/**
	 * Main Daftra Class.
	 *
	 * @package		DAFTRA
	 * @subpackage	Classes/Daftra
	 * @since		1.0.0
	 * @author		Mohamed Yassin
	 */
	final class Daftra {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Daftra
		 */
		private static $instance;

		/**
		 * DAFTRA helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Daftra_Helpers
		 */
		public $helpers;

		/**
		 * DAFTRA settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Daftra_Settings
		 */
		public $settings;


		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'daftra' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'daftra' ), '1.0.0' );
		}

		/**
		 * Main Daftra Instance.
		 *
		 * Insures that only one instance of Daftra exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Daftra	The one true Daftra
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Daftra ) ) {
				self::$instance					   = new Daftra;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		   = new Daftra_Helpers();
				self::$instance->settings		   = new Daftra_Settings();
				self::$instance->crb			   = new Daftra_Dashboard();

				//Fire the plugin logic
				new Daftra_Run();
				new Daftra_Sync();
				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'DAFTRA/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once DAFTRA_PLUGIN_DIR . 'includes/classes/class-helpers.php';
			require_once DAFTRA_PLUGIN_DIR . 'includes/classes/class-settings.php';
			require_once DAFTRA_PLUGIN_DIR . 'includes/classes/class-run.php';
			require_once DAFTRA_PLUGIN_DIR . 'includes/classes/class-dashboard.php';
			require_once DAFTRA_PLUGIN_DIR . 'includes/classes/class-sync.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'daftra', FALSE, dirname( plugin_basename( DAFTRA_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.