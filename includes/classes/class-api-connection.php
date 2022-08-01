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
	 * @var		string
	 * @since   1.0.0
	 */
	protected static $url;

	/**
	 * The API account key
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	protected static $key;

	/**
	 * The API account token
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	protected static $token;

	/**
	 * The API Enviroment Status
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	protected static $sandbox;

		
	/**
	 * do we created instant of the class ?
	 *
	 * @var		string
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
		  'Content-Type' => 'application/x-www-form-urlencoded',
		);
		$response = wp_remote_post( 
		  $url,
		  array(
			'method' => $type,
			'headers' => $headers,
			'timeout' => 10,
			'body' => $body,
		  )
		);
		return json_decode( wp_remote_retrieve_body( $response ), true );

		if( $body['success'] == true && isset($body['checkoutUrl']) ){
			return array( 'id' => $body['checkoutId'], 'url' => $body['checkoutUrl'], 'checkout_id' => $body['checkoutId'] );
		}
		return false;
	}

	private static function get_url(){
		return self::$sandbox === 1 ? 'https://z.daftra.com/api2/' : 'https://z.daftra.com/api2/';
	}


	/**
	 * Get the full URL the connection will use
	 * 
	 * @access	public
	 * @since	1.6.0
	 * @return	string	url to the sandbox/live enviroment and the required end point
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
		if( ! is_a( $response , 'wp_error' ) ){
			return // format data to be clear to be used directly
		}
	}

}
