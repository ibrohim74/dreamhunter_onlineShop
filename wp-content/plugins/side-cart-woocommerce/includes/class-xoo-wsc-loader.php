<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Xoo_Wsc_Loader{

	protected static $_instance = null;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	public function __construct(){
		$this->set_constants();
		$this->includes();
		$this->hooks();
	}


	public function set_constants(){

		$this->define( "XOO_WSC_PATH", plugin_dir_path( XOO_WSC_PLUGIN_FILE ) ); // Plugin path
		$this->define( "XOO_WSC_PLUGIN_BASENAME", plugin_basename( XOO_WSC_PLUGIN_FILE ) );
		$this->define( "XOO_WSC_URL", untrailingslashit( plugins_url( '/', XOO_WSC_PLUGIN_FILE ) ) ); // plugin url
		$this->define( "XOO_WSC_VERSION", "2.2" ); //Plugin version
		$this->define( "XOO_WSC_LITE", true );
	}


	public function define( $constant_name, $constant_value ){
		if( !defined( $constant_name ) ){
			define( $constant_name, $constant_value );
		}
	}

	/**
	 * File Includes
	*/
	public function includes(){

		//xootix framework
		require_once XOO_WSC_PATH.'/includes/xoo-framework/xoo-framework.php';
		require_once XOO_WSC_PATH.'/includes/class-xoo-wsc-helper.php';
		require_once XOO_WSC_PATH.'/includes/xoo-wsc-functions.php';
		require_once XOO_WSC_PATH.'/includes/class-xoo-wsc-template-args.php';

		if( $this->is_request( 'frontend' ) ){
			require_once XOO_WSC_PATH.'/includes/class-xoo-wsc-frontend.php';
		}
		
		if( $this->is_request( 'admin' ) ) {
			require_once XOO_WSC_PATH.'/admin/class-xoo-wsc-admin-settings.php';
		}

		require_once XOO_WSC_PATH.'/includes/class-xoo-wsc-cart.php';

	}


	/**
	 * Hooks
	*/
	public function hooks(){
		$this->on_install();
	}


	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}


	/**
	* On install
	*/
	public function on_install(){

		$version_option = 'xoo-wsc-version';
		$db_version 	= get_option( $version_option );


		//If first time installed
		if( !$db_version ){

			if( get_option( 'xoo-wsc-gl-options' ) === false ) return;

			//2.0 and lower

			//Map old values to new option
			$oldValues = (array) include XOO_WSC_PATH.'/admin/views/oldtonew.php';

			foreach ( $oldValues as $keyData ) {

				$oldKeyValue = (array) get_option( $keyData['oldkey'] );

				$newKeyValue = (array) get_option( $keyData['newkey'] );

				if( $oldKeyValue === false ) continue;

				foreach ( $keyData['values'] as $oldsubkey => $newsubkey ) {
					if( !isset( $oldKeyValue[ $oldsubkey ] ) ) continue;
					$newKeyValue[ $newsubkey ] = $oldKeyValue[ $oldsubkey ];
				}

				//Clean values
				foreach ($newKeyValue as $key => $value) {
					if( $value == 'false' ){
						$value = 'no';
					}
					elseif ( $value == '1' ) {
						$value = 'yes';
					}
					$newKeyValue[ $key ] = $value;
				}

				update_option( $keyData['newkey'], $newKeyValue );
			}

			$style = (array) get_option( 'xoo-wsc-sy-options' );

			$style['scbp-delpos'] 		= 'image';
			$style['scbp-deltype'] 		= 'text';

			update_option( 'xoo-wsc-sy-options', $style );

		}
		elseif ( $db_version == '2.1' ){

			//2.1 to 2.2
			$gl = (array) get_option( 'xoo-wsc-gl-options' );
			$gl['sct-footer'] = '';
			update_option( 'xoo-wsc-gl-options', $gl );
		}

		//Update to current version
		update_option( $version_option, XOO_WSC_VERSION);

		
	}


	public function isSideCartPage(){

		if( isset( $this->isSideCartPage ) ){
			return $this->isSideCartPage;
		}

		if( !trim(xoo_wsc_helper()->get_general_option('m-hide-cart')) ){
			$hidePages = array();
		}
		else{
			$hidePages = array_map( 'trim', explode( ',', xoo_wsc_helper()->get_general_option('m-hide-cart') ) );
		}

		$this->isSideCartPage = !( !empty( $hidePages ) && ( ( in_array( 'no-woocommerce', $hidePages )  && !is_woocommerce() && !is_cart() && !is_checkout() ) || is_page( $hidePages ) ) || ( is_product() && in_array( get_the_id() , $hidePages ) ) );

		foreach ( $hidePages as $page_id ) {
			if( is_single( $page_id ) ){
				$this->isSideCartPage = false;
				break;
			}
		}
		

		return apply_filters( 'xoo_wsc_is_sidecart_page', $this->isSideCartPage, $hidePages );
	}

}

?>