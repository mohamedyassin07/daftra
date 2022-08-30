<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Daftra_Sync
 *
 *
 * @package		DAFTRA
 * @subpackage	Classes/Daftra_Sync
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Daftra_Sync{

    /**
	 * Our Daftra_Sync constructor 
	 * to run the Woocommerce Hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct(){


        add_action( 'woocommerce_order_status_changed', array( $this, 'sync_invoice' ), 10, 4 );
        //add_action( 'woocommerce_order_refunded', array( $this, 'daftra_woocommerce_order_refunded' ), 10, 2 ); 
        //add_action( 'woocommerce_order_partially_refunded', array( $this, 'daftra_woocommerce_order_refunded' ), 10, 2 );
        
        add_action( 'profile_update', array( $this, 'profile_update' ) );
        add_action( 'save_post', array( $this, 'sync_product' ) );

	}
    
    /**
     * add_new_client
     *
     * @param  mixed $user_id
     * @return void
     */
    public static function profile_update( $user_id ){
        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';

        $user_info = get_userdata( $user_id );
        $daftra_user_id = get_user_meta( $user_id, 'daftra_user_id', true );
        if( !empty( $user_id )  &&  $user_info->roles[0] === 'customer' ) {
            if( !empty( $daftra_user_id ) ) {
                Daftra_API::edit_client( $user_id, $daftra_user_id );
            }else {
                Daftra_API::add_new_client( $user_id );
            }
            
        }  

        return $user_id;
    }

     /**
     * add_new_client
     *
     * @param  mixed $user_id
     * @return void
     */
    public static function sync_user( $user_id ){
        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';

        $response = __( 'No Customer Found', 'daftra' );
        
        $user_info = get_userdata( $user_id );
        $daftra_user_id = get_user_meta( $user_id, 'daftra_user_id', true );
        if( !empty( $user_id )  ) {
            if( !empty( $daftra_user_id ) ) {
                $response = Daftra_API::edit_client( $user_id, $daftra_user_id );
            }else {
                $response = Daftra_API::add_new_client( $user_id );
            }
            
        }  

        return $response;
    }
    
    /**
     * sync_product
     *
     * @param  mixed $post_id
     * @return void
     */
    public static function sync_product( $post_id ){
        $post_type = get_post_type( $post_id );

        if( $post_type === 'product' ) {
            include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';
            $daftra_product_id = get_post_meta( $post_id, 'daftra_product_id', true );
            $response = Daftra_API::add_edit_product( $post_id, $daftra_product_id );  
        }  

        if( $post_type === 'shop_order' ) {
            $order_id = $post_id;
            $order = wc_get_order( $order_id );
            include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';
            $daftra_invoice_id = get_post_meta( $post_id, 'daftra_invoice_id', true );
            if( $order->get_status() === "completed"  ) {
                $response = Daftra_API::add_invoice( $order_id, $daftra_invoice_id, true );
                return $post_id; 
            }
            if(  $order->get_status() === "processing" ) {
                $response = Daftra_API::add_invoice( $order_id, $daftra_invoice_id, false );
                return $post_id; 
            }

            if( $order->get_status() === "cancelled" ) {
                // $response = Daftra_API::delete_invoice( $order_id, $daftra_invoice_id );
                // wp_die(prr($response));
                // return $post_id;           
            }
        }
        return $post_id; 
    }
    
        
    /**
     * sync_invoice
     *
     * @param  mixed $order_id
     * @param  mixed $old_status
     * @param  mixed $new_status
     * @param  mixed $order
     * @return void
     */
    public static function sync_invoice( $order_id, $old_status, $new_status, $order ){

        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';
        $daftra_invoice_id = get_post_meta( $order_id, 'daftra_invoice_id', true );
        if( $new_status === "completed" ) {
            $response = Daftra_API::add_invoice( $order_id, $daftra_invoice_id, true );
        }  
        if( $new_status === "processing" ) {
            $response  =  Daftra_API::add_invoice( $order_id, $daftra_invoice_id, false );
        }

        if( $new_status === "cancelled" ) {
            $response = Daftra_API::delete_invoice( $order_id, $daftra_invoice_id );
        }     
    }

    /**
     * sync_invoice
     *
     * @param  mixed $order_id
     * @param  mixed $old_status
     * @param  mixed $new_status
     * @param  mixed $order
     * @return void
     */
    public static function ajax_sync_invoice( $order_id ){

        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';
        $order = wc_get_order( $order_id );

        $daftra_invoice_id = get_post_meta( $order_id, 'daftra_invoice_id', true );
        if( $order->get_status() === "completed"  ) {
            $response = Daftra_API::add_invoice( $order_id, $daftra_invoice_id, true );
        }
        if(  $order->get_status() === "processing" ) {
            $response = Daftra_API::add_invoice( $order_id, $daftra_invoice_id, false );
        }
         
    }
            

    
    /**
     * daftra_woocommerce_order_refunded
     *
     * @param  mixed $order_id
     * @param  mixed $refund_id
     * @return void
     */
    public function daftra_woocommerce_order_refunded( $order_id, $refund_id ){

        include_once DAFTRA_PLUGIN_DIR. 'includes/classes/class-api-connection.php';
        $order = wc_get_order( $order_id );

        $daftra_invoice_id = get_post_meta( $order_id, 'daftra_invoice_id', true );
        if(!empty($daftra_invoice_id)){
            $response = Daftra_API::refund_receipts( $order_id, $daftra_invoice_id, $refund_id ); 
        }

        return $response;

    }

}