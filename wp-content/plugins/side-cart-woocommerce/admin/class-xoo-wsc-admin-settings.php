<?php

class Xoo_Wsc_Admin_Settings{

	protected static $_instance = null;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){
		$this->hooks();
	}


	public function hooks(){
		if( current_user_can( 'manage_options' ) ){
			add_action( 'init', array( $this, 'generate_settings' ), 0 );
			add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		}
		add_action( 'xoo_as_enqueue_scripts', array( $this, 'enqueue_custom_scripts' ) );
		add_action( 'xoo_tab_page_end', array( $this, 'tab_html' ), 10, 2 );
		add_filter( 'plugin_action_links_' . XOO_WSC_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
	}



	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings' 	=> '<a href="' . admin_url( 'admin.php?page=side-cart-woocommerce-settings' ) . '">Settings</a>',
			'support' 	=> '<a href="https://xootix.com/contact" target="__blank">Support</a>',
			'upgrade' 	=> '<a href="https://xootix.com/plugins/side-cart-for-woocommerce" target="__blank">Upgrade</a>',
		);

		return array_merge( $action_links, $links );
	}



	public function enqueue_custom_scripts( $slug ){
		if( $slug !== 'side-cart-woocommerce' ) return;
		wp_enqueue_style( 'xoo-wsc-admin-fonts', XOO_WSC_URL . '/assets/css/xoo-wsc-fonts.css', array(), XOO_WSC_VERSION );
		wp_enqueue_style( 'xoo-wsc-admin-style', XOO_WSC_URL . '/admin/assets/xoo-wsc-admin-style.css', array(), XOO_WSC_VERSION );
	}


	public function generate_settings(){
		xoo_wsc_helper()->admin->auto_generate_settings();
	}



	public function add_menu_pages(){

		$args = array(
			'menu_title' 	=> 'Side Cart',
			'icon' 			=> 'dashicons-cart',
		);

		xoo_wsc_helper()->admin->register_menu_page( $args );

	}


	public function tab_html( $tab_id, $tab_data ){
		
		if( $tab_id === 'pro' ){
			xoo_wsc_helper()->get_template( 'xoo-wsc-tab-pro.php', array(), XOO_WSC_PATH.'/admin/templates/' );
		}

		if( $tab_id === 'info' ){
			xoo_wsc_helper()->get_template( 'xoo-wsc-tab-info.php', array(), XOO_WSC_PATH.'/admin/templates/' );
		}
		
	}

}

function xoo_wsc_admin_settings(){
	return Xoo_Wsc_Admin_Settings::get_instance();
}
xoo_wsc_admin_settings();