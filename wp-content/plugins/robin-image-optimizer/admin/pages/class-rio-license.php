<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WRIOP_License
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 */
class WRIO_License_Page extends WBCR\Factory_Templates_110\Pages\License {

	/**
	 * {@inheritdoc}
	 */
	public $id = 'rio_license';

	/**
	 * {@inheritdoc}
	 */
	public $page_parent_page = null;

	/**
	 * {@inheritdoc}
	 */
	public $available_for_multisite = true;

	/**
	 * {@inheritdoc}
	 */
	public $clearfy_collaboration = false;

	/**
	 * {@inheritdoc}
	 */
	public $show_right_sidebar_in_options = true;

	/**
	 * {@inheritdoc}
	 */
	public $page_menu_position = 0;

	/**
	 * {@inheritdoc}
	 * @param Wbcr_Factory458_Plugin $plugin
	 */
	public function __construct( Wbcr_Factory458_Plugin $plugin ) {
		$this->menu_title                  = __( 'License', 'robin-image-optimizer' );
		$this->page_menu_short_description = __( 'Product activation', 'robin-image-optimizer' );

		$this->plan_name = __( 'Robin image optimizer Premium', 'robin-image-optimizer' );

		if ( is_multisite() && defined( 'WCL_PLUGIN_ACTIVE' ) ) {
			if ( WRIO_Plugin::app()->isNetworkActive() && WCL_Plugin::app()->isNetworkActive() ) {
				$this->clearfy_collaboration = true;
			}
		} else if ( defined( 'WCL_PLUGIN_ACTIVE' ) ) {
			$this->clearfy_collaboration = true;
		}

		parent::__construct( $plugin );

		/**
		 * Adds a new plugin card to license components page
		 *
		 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
		 * @since  1.3.0
		 */
		add_filter( 'wbcr/clearfy/license/list_components', function ( $components ) {
			$title       = 'Free';
			$icon        = 'rio-premium-icon-256x256--lock.png';
			$description = "";

			if ( wrio_is_license_activate() ) {
				$title = 'Premium';
				$icon  = 'rio-premium-icon-256x256--default.png';
				//$description = "Key: " . wrio_get_license_key();
			}

			$components[] = [
				'name'            => 'robin_image_optimizer',
				'title'           => sprintf( __( 'Robin image optimizer [%s]', 'clearfy' ), $title ),
				'url'             => 'https://wordpress.org/plugins/robin-image-optimizer/',
				'type'            => 'wordpress',
				'build'           => $this->is_premium ? 'premium' : 'free',
				'key'             => $this->get_hidden_license_key(),
				'plan'            => $this->get_plan(),
				'expiration_days' => $this->get_expiration_days(),
				'quota'           => $this->is_premium ? $this->premium_license->get_count_active_sites() . ' ' . __( 'of', 'clearfy' ) . ' ' . $this->premium_license->get_sites_quota() : null,
				'subscription'    => $this->is_premium && $this->premium_has_subscription ? sprintf( __( 'Automatic renewal, every %s', '' ), esc_attr( $this->get_billing_cycle_readable() ) ) : null,
				'base_path'       => 'robin-image-optimizer/robin-image-optimizer.php',
				'icon'            => WCL_PLUGIN_URL . '/admin/assets/img/' . $icon,
				'description'     => $description . __( 'Public License is a GPLv3 compatible license allowing you to change and use this version of the plugin for free. Please keep in mind this license covers only free edition of the plugin. Premium versions are distributed with other type of a license.', 'clearfy' ),
				'license_page_id' => 'rio_license'
			];

			return $components;
		} );
	}

	/**
	 * Подменяем простраинство имен для меню плагина, если активирован плагин Clearfy
	 * Меню текущего плагина будет добавлено в общее меню Clearfy
	 *
	 * @return string
	 */
	public function getMenuScope() {
		if ( $this->clearfy_collaboration ) {
			//$this->page_parent_page = 'rio_general';
			$this->page_parent_page = 'none';

			return 'wbcr_clearfy';
		}

		return $this->plugin->getPluginName();
	}

	public function get_plan_description() {
		//$paragraf1 = sprintf( __( '<b>%s</b> is a premium image optimization plugin for WordPress. ', 'robin-image-optimizer' ), $this->plan_name ) . '</p>';
		//$paragraf2 = '<p style="font-size: 16px;">' . __( 'Paid license guarantees that you can optimize images under better conditions and WebP support.', 'robin-image-optimizer' );
		//return '<p style="font-size: 16px;">' . $paragraf1 . '</p><p style="font-size: 16px;">' . $paragraf2 . '</p>';

		//return '<p style="font-size: 16px;">' . $paragraf1 . '</p>';
	}
}