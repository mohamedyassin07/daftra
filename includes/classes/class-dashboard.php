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
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );

        add_action("wp_ajax_nopriv_daftra_sync_data", array( $this,"daftra_sync_data" ) );
        add_action("wp_ajax_daftra_sync_data", array( $this,"daftra_sync_data" ) );
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
        require_once DAFTRA_PLUGIN_DIR . 'libs/carbon-fields/vendor/autoload.php';
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
        if ( is_admin() && isset($_GET['page']) == 'crb_carbon_fields_container_daftra_sync.php') {

            if( isset( $_POST['carbon_fields_compact_input']['_daftra_sync_users'] ) && $_POST['carbon_fields_compact_input']['_daftra_sync_users'] === 'yes' ) {
                header("location: " . $_SERVER['REQUEST_URI']);
            }
            if( isset( $_POST['carbon_fields_compact_input']['_daftra_sync_products'] ) && $_POST['carbon_fields_compact_input']['_daftra_sync_products'] === 'yes' ) {
                //wp_die('_daftra_sync_products');
            }
            if( isset( $_POST['carbon_fields_compact_input']['_daftra_sync_orders'] ) && $_POST['carbon_fields_compact_input']['_daftra_sync_orders'] === 'yes' ) {
                //wp_die('_daftra_sync_orders');
            }

            $current_page = admin_url("admin.php?page=".$_GET["page"]);
            $html = include_once DAFTRA_PLUGIN_DIR . 'template/daftra-sync.php';

        } else {
            $html = '';
        }
        // Default options page
        $basic_options_container = Container::make( 'theme_options', __( 'Daftra API' , 'daftra') )
        ->add_fields( array(
            Field::make( 'text',  DAFTRA_SLUG . '_api_token' ),
            Field::make( 'text',  DAFTRA_SLUG . '_domain' ),
        ) );

        // Add second options page under 'Basic Options'
        Container::make( 'theme_options', __( 'Daftra Sync' ) )
        ->set_page_parent( $basic_options_container ) // reference to a top level container
        ->add_fields( array(
            // Field::make( 'checkbox',  DAFTRA_SLUG . '_sync_users' ),
            // Field::make( 'checkbox',  DAFTRA_SLUG . '_sync_products' ),
            // Field::make( 'checkbox',  DAFTRA_SLUG . '_sync_orders' ),
            Field::make( 'html', DAFTRA_SLUG . '_sync_now' )
            ->set_html( $html )
        ) );
   
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
    public function enqueue_backend_scripts_and_styles() {
        wp_enqueue_script( 'daftra-scripts', DAFTRA_PLUGIN_URL . 'assets/js/daftra-scripts.js', array(), DAFTRA_VERSION, false );

        $daftra_object = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),		
        );
        wp_localize_script( 'daftra-scripts', 'ajax_wpx', $daftra_object );   
    }
    
    /**
     * daftra_sync_data
     *
     * @return void
     */
    public function daftra_sync_data(){
        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-sync.php';

        if( $_POST['sync_users'] === 'false' && $_POST['sync_products'] === 'false' && $_POST['sync_orders'] === 'false') {
            echo wp_send_json( array(
                'success' => false , 
                'msg' => 'Error : Select One Options To Start Sync' 
                ));
        }

        $sync_number = 10;

        // sync users
        if( isset( $_POST['sync_users'] )  && $_POST['sync_users'] === 'true' ){
            // get 10 user don't have daftra_user_id
            $args = array(
                // 'role' => 'customer',
                'number' => $sync_number,
                'meta_query' => array(
                    array(
                        'key' => 'daftra_user_id',
                        'compare' => 'NOT EXISTS'
                    ),
                  )
            );
            // Get the results
            $authors = get_users( $args );

            if( !empty( $authors ) ){
                // loop through each author
                foreach ($authors as $author){
                    // get all the user's data
                    $sync_user = Daftra_Sync::sync_user( $author->ID );
                }
                
                //not_completed;
                echo wp_send_json( array(
                    'repeat'=> true, 
                    'success' => null , 
                    'msg' => 'Done Sync '. count( $authors ) .' Users',
                    'response' => $sync_user,  
                ) );
            
            } 
            
        }
        
        // sync products
        if( isset( $_POST['sync_products'] )  && $_POST['sync_products'] === 'true' ){

            $product_args = array(
                'post_type' => 'product',
                'posts_per_page' => $sync_number,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'daftra_product_id',
                        'compare' => 'NOT EXISTS'
                    ),
                  )
            );
            $product_args['fields'] = 'ids';
            $product = get_posts( $product_args );

            if( !empty( $product ) ){ 
                foreach ( (array)$product as $product_id ) {
                     $sync_product = Daftra_Sync::sync_product( $product_id ) ;
                }
    
              //not_completed;
              echo wp_send_json( array(
                'repeat'=> true, 
                'success' => null , 
                'msg' => 'Done Sync '. count($product) .' Products',
                'response' => $sync_product,
                 ) );
            } 

           
        }

         // sync orders
        if( isset( $_POST['sync_orders'] )  && $_POST['sync_orders'] === 'true' ){

            $order_args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => $sync_number,
                'post_status' => array ('wc-completed', 'wc-processing'),
                'meta_query' => array(
                    array(
                        'key' => 'daftra_invoice_id',
                        'compare' => 'NOT EXISTS'
                    ),
                  )
            );
            $order_args['fields'] = 'ids';
            $orders = get_posts( $order_args );

            if( !empty( $orders ) ){ 
                foreach ( (array)$orders as $order_id ) {
                    $sync_invoice = Daftra_Sync::ajax_sync_invoice( $order_id );
                }

              //not_completed;
              echo wp_send_json( array(
                'repeat'=> true, 
                'success' => null , 
                'msg' => 'Done Sync '. count($orders) .' invoice',
                'response' => $sync_invoice, 
                ) );
            } 
        }
        $success = true;
        if( $success === true ) { 
            echo wp_send_json( array(
                'success' => $success , 
                'msg' => 'Sync Is Complete' 
                ));
        } 
        die;
    }
}
