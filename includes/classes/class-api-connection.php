<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Daftra_API
 *
 * @package		daftra
 * @subpackage	Classes/Daftra_API
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Daftra_API{

	/**
	 * The API URL base
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $url;

	/**
	 * The API URL sup domain
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $domain;

	/**
	 * The API account key
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $key;

	/**
	 * The API account token
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $token;

	/**
	 * The API Enviroment Status
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $sandbox;

		
	/**
	 * do we created instant of the class ?
	 *
	 * @var		
	 * @since   1.0.0
	 */
	protected static $instant;

	
	/**
	 * Our Daftra_API constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		$this->set_base_connection_settings();
	}

	private static function set_base_connection_settings()
	{
		self::$sandbox	= false;
		self::$domain   = carbon_get_theme_option( DAFTRA_SLUG . '_domain' );
		self::$key		= carbon_get_theme_option( DAFTRA_SLUG . '_api_key' );
		self::$token	= carbon_get_theme_option( DAFTRA_SLUG . '_api_token' );
		self::$instant 	= true;
	}

	/**
	 * Execute a connection to Payzaty API
	 * @access	public
	 * @since	1.6.0
	 * @return	array	needed data from the connection
	 */
	public static function create_connection( $url, $type = 'POST', $body = array() )
	{
		if( self::$instant !== true ){
			self::set_base_connection_settings();
		}

		$headers = array(
		  'X-Source' => 8, // 8:WooCommerce
		  'X-Build' => 1,
		  'X-Version' => 1,
		  'X-Language' => 'ar',
		  'X-MerchantNo' => self::$key,
		  'X-SecretKey' => self::$token , 
		  'Content-Type' => 'application/json',
		  'APIKEY' => self::$token
		);
		$response = wp_remote_post( 
		  $url,
		  array(
			'method' => $type,
			'headers' => $headers,
			'timeout'  => 45,
            'blocking' => true, 
            'sslverify' => false,
            'httpversion' => '1.0',
            'redirection' => 5,
			'body' => $body,
		  )
		);

		return json_decode( wp_remote_retrieve_body( $response ) ) ;

	}

	private static function get_url(){
		$domain   = carbon_get_theme_option( DAFTRA_SLUG . '_domain' );
		return self::$sandbox === 1 ? 'https://'. $domain .'.daftra.com/api2/ ': 'https://'. $domain .'.daftra.com/api2/';
	}


	/**
	 * Get the full URL the connection will use
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return		url to the sandbox/live enviroment and the required end point
	 */
	public static function get_endpoitn_url( $endpoint_base = '' )
	{
		return self::get_url() . trim( $endpoint_base ) . '.json';
	}

	/**
	 * Get result from api
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	array	paymanet process status
	 */
	public static function get_all_invoices()
	{
		$url = self::get_endpoitn_url( 'invoices' );
		$response = self::create_connection( $url, 'GET' );
		// format data to be clear to be used directly
		return $response;
	}

	/**
	 * Get result from api
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	array	paymanet process status
	 */
	public static function add_invoice( $order_id, $daftra_invoice_id = '', $paid = false ){
		if( empty ( $daftra_invoice_id ) ) {
			$method = 'POST' ;
			$url = self::get_endpoitn_url( 'invoices' );
		}else{
			$method = 'PUT' ;
			$url = self::get_endpoitn_url( 'invoices/'.$daftra_invoice_id );
		}
		$order = wc_get_order( $order_id );
		$client_id = get_user_meta( $order->get_user_id(), 'daftra_user_id', true );

		if( empty( $client_id ) ) {
			$client_id = $order->get_user_id();
		}
		$total_paid = '';
		if( $paid === true ){
			$total_paid = $order->get_total();
		}

		if( $order ) {
			// Get and Loop Over Order Items
			foreach ( $order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$variation_id = $item->get_variation_id();
				$product = $item->get_product(); // see link above to get $product info
				$product_name = $item->get_name();
				$quantity = $item->get_quantity();
				$subtotal = $item->get_subtotal();
				$total = $item->get_total();
				$tax = $item->get_subtotal_tax();
				$tax_class = $item->get_tax_class();
				$tax_status = $item->get_tax_status();
				$allmeta = $item->get_meta_data();
				$somemeta = $item->get_meta( '_whatever', true );
				$item_type = $item->get_type(); // e.g. "line_item"
				$product = wc_get_product( $product_id );
				$regular_price = $product->get_regular_price();
				$sale_price = $product->get_sale_price();
				$price = $product->get_price();
				$description = $product->description;
				$InvoiceItem[] = [
					"invoice_id"=> $order->get_id(),
					"item"=> $product_name,
					"description"=> $description,
					"unit_price"=> $price,
					"quantity"=> $quantity,
					"product_id"=> $product_id,
				];
			}
			$InvoiceItem = array_values( $InvoiceItem ); 
			$currency_code = $order->get_currency();
            $currency_symbol = get_woocommerce_currency_symbol( $currency_code );
			$date_modified = $order->get_date_modified();
			$date = $date_modified->date("Y-m-d, g:i:s A T");
			$body_json = [
				"Invoice"=> [
					"staff_id"=> 0,
					"subscription_id"=> null,
					"store_id"=> 0,
					"no"=> "",
					"po_number"=> $order->get_order_number(),
					"name"=> $product_name,
					"client_id"=> $client_id,
					"is_offline"=> true,
					"currency_code"=> $currency_code,
					"client_business_name"=> $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
					"client_first_name"=> $order->get_billing_first_name(),
					"client_last_name"=> $order->get_billing_last_name(),
					"client_email"=> $order->get_billing_email(),
					"client_address1"=> $order->get_billing_address_1(),
					"client_address2"=> $order->get_billing_address_2(),
					"client_postal_code"=> $order->get_billing_postcode(),
					"client_city"=> $order->get_billing_city(),
					"client_state"=> $order->get_billing_state(),
					"client_country_code"=> $order->get_billing_country(),
					"date"=> $date,
					"draft"=> "0",
					"discount"=> "",
					"discount_amount"=> $order->get_discount_total(),
					"deposit"=> 0,
					"deposit_type"=> 0,
					"notes"=> $order->get_customer_note(),
					"html_notes"=> "",
					"invoice_layout_id"=> 1,
					"estimate_id"=> 0,
					"shipping_options"=> "",
					"shipping_amount"=> null,
					"client_active_secondary_address"=> false,
					"client_secondary_name"=> "",
					"client_secondary_address1"=> "",
					"client_secondary_address2"=> "",
					"client_secondary_city"=> "",
					"client_secondary_state"=> "",
					"client_secondary_postal_code"=> "",
					"client_secondary_country_code"=> "",
					"follow_up_status"=> null,
					"work_order_id"=> null,
					"requisition_delivery_status"=> null,
					"pos_shift_id"=> null
				],
				"InvoiceItem"=> $InvoiceItem ,
				"Payment"=> [
				  [
					"payment_method"=> $order->get_payment_method_title(),
					"amount"=> $total_paid,
					"transaction_id"=> $order->get_transaction_id(),
					"date"=> $date,
					"staff_id"=> 0
				  ]
				],
				// "InvoiceCustomField"=> [],
				// "Deposit"=> [],
				// "InvoiceReminder"=> [],
				// "Document"=> [],
				// "DocumentTitle"=> []
			  ];

			// return $body_json;

			$daftra_invoice = self::create_connection( $url, $method, json_encode( $body_json ) );

			if( !empty( $daftra_invoice->code ) && $daftra_invoice->code  === 202 ) {
				$daftra_invoice_id = $daftra_invoice->id;
				update_post_meta( $order_id, 'daftra_invoice_id', $daftra_invoice_id );
			}
		
		 return $daftra_invoice;
		}
	}
	
	/**
	 * delete_invoice
	 *
	 * @param  mixed $order_id
	 * @param  mixed $daftra_invoice_id
	 * @return void
	 */
	public function delete_invoice( $order_id, $daftra_invoice_id ){
        
		if( !empty( $daftra_invoice_id ) ) { 
			$method = 'DELETE' ;
			$url = self::get_endpoitn_url( 'invoices/'.$daftra_invoice_id );
			$daftra_invoice = self::create_connection( $url, $method  );
			return $daftra_invoice;
		}
		
	}

	/**
	 * Get result from api
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	array	get all staff
	 */
	public static function get_all_clients()
	{
		$url = self::get_endpoitn_url( 'clients' );
		$body = [
          'limit' => 10,
          'page' => 1,
		];
		$response = self::create_connection( $url, 'GET', $body );
		// format data to be clear to be used directly
		return $response;
	}

	/**
	 * Get result from api
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	int/client_id
	 */
	public static function add_new_client( $user_id ){
		
		
		
		$url = self::get_endpoitn_url( 'clients' );
		
		$first_name = !empty(get_user_meta( $user_id, 'billing_first_name', true )) ? get_user_meta( $user_id, 'billing_first_name', true ) : get_user_meta( $user_id, 'first_name', true );
		$last_name  = !empty(get_user_meta( $user_id, 'billing_last_name', true )) ? get_user_meta( $user_id, 'billing_last_name', true ) : get_user_meta( $user_id, 'last_name', true );
		
		if( empty( $first_name ) && empty( $last_name )  ){
			$business_name = 'client_'.$user_id ;
		}else{
			$business_name = $first_name . ' ' . $last_name ;
		}
		$user_info = get_userdata( $user_id );
		$user_email = $user_info->user_email ;
		$email = !empty(get_user_meta( $user_id, 'billing_email', true )) ? get_user_meta( $user_id, 'billing_email', true ) : $user_email ;

		$body['Client'] = [ 
			"is_offline"=> true,
			"client_number"=> $user_id,
			"staff_id"=> 0,
			"business_name"=> $business_name,
			"first_name"=> $first_name,
			"last_name"=> $last_name,
			"email"=> $email,
			"password"=> wp_generate_password( 8, false ),
			"address1"=> get_user_meta( $user_id, 'billing_address_1', true ),
			"address2"=> get_user_meta( $user_id, 'billing_address_2', true ),
			"city"=> get_user_meta( $user_id, 'billing_city', true ),
			"state"=> get_user_meta( $user_id, 'billing_state', true ),
			"postal_code"=> get_user_meta( $user_id, 'billing_postcode', true ),
			"phone1"=> get_user_meta( $user_id, 'billing_phone', true ),
			"phone2"=> "",
			"country_code"=> get_user_meta( $user_id, 'billing_country', true ),
			"notes"=> "",
			"active_secondary_address"=> false,
			"secondary_name"=> "",
			"secondary_address1"=> "",
			"secondary_address2"=> "",
			"secondary_city"=> "",
			"secondary_state"=> "",
			"secondary_postal_code"=> "",
			"secondary_country_code"=> "",
			"default_currency_code"=> "",
			"follow_up_status"=> null,
			"category"=> "",
			"group_price_id"=> 0,
			"timezone"=> 0,
			"bn1"=> "",
			"bn1_label"=> "",
			"bn2_label"=> "",
			"bn2"=> "",
			"starting_balance"=> null,
			"type"=> 2,
			"birth_date"=> "",
			"gender"=> "",
			"map_location"=> "",
			"credit_limit"=> 0,
			"credit_period"=> 0
		];

		$client_id = self::create_connection( $url, 'POST', json_encode( $body ) );
        
		// update user meta daftra user id .
        if( !empty( $client_id->code ) && $client_id->code  === 202 ) {
			update_user_meta( $user_id, 'daftra_user_id', $client_id->id );
		}	
		return $client_id;
	}

	/**
	 * Get result from api
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	int/client_id
	 */
	public static function edit_client( $user_id, $daftra_user_id ){
			
		$url = self::get_endpoitn_url( 'clients/'.$daftra_user_id );
		$user_info  = get_userdata( $user_id );
		$user_email = $user_info->user_email ;
		$first_name = !empty(get_user_meta( $user_id, 'billing_first_name', true )) ? get_user_meta( $user_id, 'billing_first_name', true ) : get_user_meta( $user_id, 'first_name', true );
		$last_name  = !empty(get_user_meta( $user_id, 'billing_last_name', true )) ? get_user_meta( $user_id, 'billing_last_name', true ) : get_user_meta( $user_id, 'last_name', true );
		
		if( empty( $first_name ) && empty( $last_name )  ){
			$business_name = 'client_'.$user_id ;
		}else{
			$business_name = $first_name . ' ' . $last_name ;
		}
		
		$email = !empty(get_user_meta( $user_id, 'billing_email', true )) ? get_user_meta( $user_id, 'billing_email', true ) : $user_email ;

		$body['Client'] = [ 
			"is_offline"=> true,
			"client_number"=> $user_id,
			"staff_id"=> 0,
			"business_name"=> $business_name,
			"first_name"=> $first_name,
			"last_name"=> $last_name,
			"email"=> $email,
			"password"=> wp_generate_password( 8, false ),
			"address1"=> get_user_meta( $user_id, 'billing_address_1', true ),
			"address2"=> get_user_meta( $user_id, 'billing_address_2', true ),
			"city"=> get_user_meta( $user_id, 'billing_city', true ),
			"state"=> get_user_meta( $user_id, 'billing_state', true ),
			"postal_code"=> get_user_meta( $user_id, 'billing_postcode', true ),
			"phone1"=> get_user_meta( $user_id, 'billing_phone', true ),
			"phone2"=> "",
			"country_code"=> get_user_meta( $user_id, 'billing_country', true ),
			"notes"=> "",
			"active_secondary_address"=> false,
			"secondary_name"=> "",
			"secondary_address1"=> "",
			"secondary_address2"=> "",
			"secondary_city"=> "",
			"secondary_state"=> "",
			"secondary_postal_code"=> "",
			"secondary_country_code"=> "",
			"default_currency_code"=> "",
			"follow_up_status"=> null,
			"category"=> "",
			"group_price_id"=> 0,
			"timezone"=> 0,
			"bn1"=> "",
			"bn1_label"=> "",
			"bn2_label"=> "",
			"bn2"=> "",
			"starting_balance"=> null,
			"type"=> 2,
			"birth_date"=> "",
			"gender"=> "",
			"map_location"=> "",
			"credit_limit"=> 0,
			"credit_period"=> 0
		];
		
        // wp_die( prr( $body  ) );
		$client_id = self::create_connection( $url, 'PUT', json_encode( $body ) );
        
		// update user meta daftra user id .
        if( !empty( $client_id->code ) && $client_id->code  === 202 ) {
			$daftra_user_id = $client_id->id;
			update_user_meta( $user_id, 'daftra_user_id', $daftra_user_id );
		}	
		return $client_id;
	}
	
	/**
	 * add_edit_product
	 *
	 * @param  mixed $product_id
	 * @param  mixed $daftra_product_id
	 * @return void
	 */
	public static function add_edit_product( $product_id, $daftra_product_id = '' ){

        if( empty ( $daftra_product_id ) ) {
			$method = 'POST' ;
			$url = self::get_endpoitn_url( 'products' );
		}else{
			$method = 'PUT' ;
			$url = self::get_endpoitn_url( 'products/'.$daftra_product_id );
		}
		$product = wc_get_product( $product_id );
	    $category = get_term_by( 'id', $product->get_category_ids()[0], 'product_cat' ) ;

		$body['Product'] = [
			"staff_id"=> 0,
			"name"=> get_the_title($product_id),
			"description"=> $product->post->post_excerpt,
			"unit_price"=> $product->get_price(),
			"tax1"=> 0,
			"tax2"=> 0,
			"supplier_id"=> 0,
			"brand"=> "",
			"category"=> $category->name,
			"tags"=> "",
			"buy_price"=> 0,
			"product_code"=> $product->get_sku(),
			"track_stock"=> 0,
			"stock_balance"=> "{$product->get_stock_quantity()}",
			"low_stock_thershold"=> 1,
			"barcode"=> "",
			"notes"=> "",
			"deactivate"=> 0,
			"follow_up_status"=> null,
			"updated_price"=> true,
			"average_price"=> 0,
			"type"=> null,
			"minimum_price"=> null,
			"profit_margin"=> null,
			"discount"=> null,
			"discount_type"=> null,
			"duration_minutes"=> 0,
			"availabe_online"=> 0,
			"raw_store_id"=> 0
		];

		// wp_die( prr ( $body ) );

		// return $url;
		$daftra_product = self::create_connection( $url, $method, json_encode( $body ) );

		// update user meta daftra user id .
        if( !empty( $daftra_product->code ) && $daftra_product->code  === 202 ) {
			$daftra_product_id = $daftra_product->id;
			update_post_meta( $product_id, 'daftra_product_id', $daftra_product_id );
		}	
		return $daftra_product;
		
	}


}
