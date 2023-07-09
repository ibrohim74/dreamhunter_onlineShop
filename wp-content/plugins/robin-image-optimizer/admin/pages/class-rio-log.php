<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс отвечает за работу страницы логов.
 *
 * @author        Eugene Jokerov <jokerov@gmail.com>
 * @author        Alexander Teshabaev <sasha.tesh@gmail.com>
 * @copyright (c) 2018, Webcraftic
 * @version       1.0
 */
class WRIO_LogPage extends Wbcr_FactoryLogger123_PageBase {

	/**
	 * {@inheritdoc}
	 */
	public $id = 'rio_logs'; // Уникальный идентификатор страницы

	/**
	 * {@inheritdoc}
	 */
	public $page_parent_page = null;

	/**
	 * {@inheritdoc}
	 */
	public $available_for_multisite = false;

	/**
	 * {@inheritdoc}
	 */
	public $clearfy_collaboration = false;

	/**
	 * {@inheritdoc}
	 */
	public $show_right_sidebar_in_options = true;

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.3.0
	 * @var WRIO_Views
	 */
	protected $view;

	/**
	 * Подменяем пространство имен для меню плагина, если активирован плагин Clearfy
	 * Меню текущего плагина будет добавлено в общее меню Clearfy
	 *
	 * @return string
	 */
	public function getMenuScope() {
		if ( $this->clearfy_collaboration ) {
			$this->page_parent_page = 'rio_general';

			return 'wbcr_clearfy';
		}

		return $this->plugin->getPluginName();
	}

	/**
	 * @param WRIO_Plugin $plugin
	 */
	public function __construct( WRIO_Plugin $plugin ) {
		$this->menu_title                  = __( 'Error Log', 'robin-image-optimizer' );
		$this->page_menu_short_description = __( 'Plugin debug report', 'robin-image-optimizer' );

		$this->view = WRIO_Views::get_instance( WRIO_PLUGIN_DIR );
		if ( is_multisite() && defined( 'WCL_PLUGIN_ACTIVE' ) ) {
			if ( WRIO_Plugin::app()->isNetworkActive() && WCL_Plugin::app()->isNetworkActive() ) {
				$this->clearfy_collaboration = true;
			}
		} else if ( defined( 'WCL_PLUGIN_ACTIVE' ) ) {
			$this->clearfy_collaboration = true;
		}

		parent::__construct( $plugin );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function assets( $scripts, $styles ) {
		parent::assets( $scripts, $styles );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMenuTitle() {
		return defined( 'LOADING_ROBIN_IMAGE_OPTIMIZER_AS_ADDON' ) ? __( 'Image optimizer', 'robin-image-optimizer' ) : __( 'Error Log', 'robin-image-optimizer' );
	}
}
