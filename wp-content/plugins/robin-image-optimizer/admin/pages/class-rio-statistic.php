<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WRIO_StatisticPage
 * Класс отвечает за работу страницы статистики
 *
 * @author        Eugene Jokerov <jokerov@gmail.com>
 * @copyright (c) 2018, Webcraftic
 */
class WRIO_StatisticPage extends WRIO_Page {

	/**
	 * {@inheritdoc}
	 */
	public $id = 'rio_general';

	/**
	 * {@inheritdoc}
	 */
	public $type = 'page';

	/**
	 * {@inheritdoc}
	 */
	public $plugin;

	/**
	 * {@inheritdoc}
	 */
	public $page_menu_position = 20;

	/**
	 * {@inheritdoc}
	 */
	public $page_menu_dashicon = 'dashicons-chart-line';

	/**
	 * @var string
	 */
	public $menu_target = 'options-general.php';

	/**
	 * @var bool
	 */
	public $internal = false;

	/**
	 * @var bool
	 */
	public $add_link_to_plugin_actions = true;

	/**
	 * Page type
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.3.0
	 * @var string
	 */
	protected $scope = 'media-library';


	/**
	 * @param WRIO_Plugin $plugin
	 */
	public function __construct( WRIO_Plugin $plugin ) {
		$this->menu_title                  = __( 'Robin image optimizer', 'robin-image-optimizer' );
		$this->page_menu_short_description = __( 'Compress bulk of images', 'robin-image-optimizer' );
		$this->plugin                      = $plugin;

		parent::__construct( $plugin );

		add_action( 'admin_enqueue_scripts', [ $this, 'print_i18n' ] );

		add_filter( 'wbcr/factory/pages/impressive/print_all_notices', [ $this, 'register_limit_notice' ], 10, 2 );
	}

	/**
	 * @param $plugin
	 * @param $obj
	 *
	 * @return void|bool
	 */
	public function register_limit_notice( $plugin, $obj ) {
		if ( ( $this->plugin->getPluginName() !== $plugin->getPluginName() ) || ( 'rio_general' !== $obj->id ) ) {
			return false;
		}
	}

	/**
	 * Подменяем простраинство имен для меню плагина, если активирован плагин Clearfy
	 * Меню текущего плагина будет добавлено в общее меню Clearfy
	 *
	 * @return string
	 */
	public function getMenuScope() {
		if ( $this->clearfy_collaboration ) {
			//$this->internal = true;

			return 'wbcr_clearfy';
		}

		return $this->plugin->getPluginName();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMenuTitle() {
		return $this->clearfy_collaboration ? __( 'Robin Image Optimizer', 'robin-image-optimizer' ) : __( 'Robin image optimizer', 'robin-image-optimizer' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPageTitle() {
		return $this->clearfy_collaboration ? __( 'Image optimizer', 'robin-image-optimizer' ) : __( 'Bulk optimization', 'robin-image-optimizer' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function assets( $scripts, $styles ) {
		parent::assets( $scripts, $styles );

		$this->styles->add( WRIO_PLUGIN_URL . '/admin/assets/css/base-statistic.css' );

		$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/sweetalert2.js' );
		$this->styles->add( WRIO_PLUGIN_URL . '/admin/assets/css/sweetalert2.css' );
		$this->styles->add( WRIO_PLUGIN_URL . '/admin/assets/css/sweetalert-custom.css' );

		$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/Chart.min.js' );
		//$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/statistic.js' );

		$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/modals.js', [ 'jquery' ], 'wrio-modals' );
		$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/bulk-optimization.js', [
				'jquery',
				'wrio-modals',
		] );
		if ( wrio_is_license_activate() ) {
			$this->scripts->add( WRIO_PLUGIN_URL . '/admin/assets/js/bulk-conversion.js', [
					'jquery',
					'wrio-modals',
			] );

		}

		// Add Clearfy styles for HMWP pages
		if ( defined( 'WBCR_CLEARFY_PLUGIN_ACTIVE' ) ) {
			$this->styles->add( WCL_PLUGIN_URL . '/admin/assets/css/general.css' );
		}
	}

	/**
	 * Print localization only current page
	 *
	 * @throws \Exception
	 * @since  1.3.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function print_i18n() {
		$page = $this->plugin->request->get( 'page', null );

		if ( $page !== $this->getResultId() ) {
			return;
		}

		$backup = new WIO_Backup();

		wp_enqueue_script( 'wio-statistic-page', WRIO_PLUGIN_URL . '/admin/assets/js/statistic.js', [ 'jquery' ], WRIO_Plugin::app()->getPluginVersion(), true );
		wp_localize_script( 'wio-statistic-page', 'wrio_l18n_bulk_page', $this->get_i18n() );

		wp_localize_script( 'wio-statistic-page', 'wrio_settings_bulk_page', [
				'is_premium'             => wrio_is_license_activate(),
				'is_network_admin'       => WRIO_Plugin::app()->isNetworkAdmin() ? 1 : 0,
				'is_writable_backup_dir' => $backup->isBackupWritable() ? 1 : 0,
				'images_backup'          => WRIO_Plugin::app()->getPopulateOption( 'backup_origin_images', false ) ? 1 : 0,
				'need_migration'         => wbcr_rio_has_meta_to_migrate() ? 1 : 0,
				'scope'                  => $this->scope,
				'optimization_nonce'     => wp_create_nonce( 'bulk_optimization' ),
				'conversion_nonce'       => wp_create_nonce( 'bulk_conversion' ),
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function showPageContent() {
		$is_premium = wrio_is_license_activate();
		$statistics = $this->get_statisctic_data();

		$template_data = [
				'is_premium' => $is_premium,
				'scope'      => $this->scope,
		];

		//do_action( 'wbcr/rio/multisite_current_blog' );

		// Page header
		$this->view->print_template( 'part-page-header', [
				'title'       => __( 'Image optimization dashboard', 'robin-image-optimizer' ),
				'description' => __( 'Monitor image optimization statistics and run on demand or scheduled optimization.', 'robin-image-optimizer' ),
		], $this );

		// Page tabs
		$this->view->print_template( 'part-bulk-optimization-tabs', $template_data, $this );

		?>
		<div class="wbcr-factory-page-group-body" style="padding:0; border-top: 1px solid #d4d4d4;">
			<?php
			// Servers
			$this->view->print_template( 'part-bulk-optimization-servers', $template_data, $this );

			// Total
			$this->view->print_template( 'part-bulk-optimization-total', $template_data, $this );

			// Statistic
			$this->view->print_template( 'part-bulk-optimization-statistic', array_merge( $template_data, [
					'stats' => $statistics->get(),
			] ), $this );

			// Optimization log
			$this->view->print_template( 'part-bulk-optimization-log', array_merge( $template_data, [
					'process_log' => $statistics->get_last_optimized_images(),
			] ), $this );
			?>
		</div>
		<script type="text/html" id="wrio-tmpl-bulk-optimization">
			<?php $this->view->print_template( 'modal-bulk-optimization' ); ?>
		</script>
		<?php if ( wrio_is_license_activate() ): ?>
			<script type="text/html" id="wrio-tmpl-webp-conversion">
				<?php $this->view->print_template( 'modal-webp-conversion' ); ?>
			</script>
		<?php endif; ?>
		<?php
		//do_action( 'wbcr/rio/multisite_restore_blog' );
	}

	/**
	 * @return object|\WRIO_Image_Statistic
	 * @since  1.3.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function get_statisctic_data() {
		return WRIO_Image_Statistic::get_instance();
	}

	/**
	 * @return array
	 * @since  1.3.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	protected function get_i18n() {
		$modal_optimization_cron_button      = __( 'Scheduled optimization', 'robin-image-optimizer' );
		$modal_conversion_cron_button        = __( 'Scheduled conversion', 'robin-image-optimizer' );
		$modal_optimization_cron_button_stop = __( 'Stop schedule optimization', 'robin-image-optimizer' );
		$modal_conversion_cron_button_stop   = __( 'Stop schedule conversion', 'robin-image-optimizer' );

		$optimize_type = WRIO_Plugin::app()->getOption( 'image_optimization_type', 'schedule' );
		if ( wrio_is_license_activate() && $optimize_type === 'background' ) {
			$modal_optimization_cron_button      = __( 'Background optimization', 'robin-image-optimizer' );
			$modal_conversion_cron_button        = __( 'Background conversion', 'robin-image-optimizer' );
			$modal_optimization_cron_button_stop = __( 'Stop background optimization', 'robin-image-optimizer' );
			$modal_conversion_cron_button_stop   = __( 'Stop background conversion', 'robin-image-optimizer' );
		}

		return [
				'premium_server_disabled'      => __( 'You cannot use the premium server on a free plan. You must activate the license to use all the features of the premium version.', 'robin-image-optimizer' ),
				'webp_premium_server_disabled' => __( "You can't convert to WebP through a free optimization server. Select the premium optimization server.", 'robin-image-optimizer' ),
				'server_down_warning'          => __( 'Your selected optimization server is down. This means that you cannot optimize images through this server. Try selecting another optimization server.', 'robin-image-optimizer' ),
				'server_status_down'           => __( 'down', 'robin-image-optimizer' ),
				'server_status_stable'         => __( 'stable', 'robin-image-optimizer' ),
				'modal_error'                  => __( 'Error', 'robin-image-optimizer' ),
				'modal_cancel'                 => __( 'Cancel', 'robin-image-optimizer' ),
				'modal_confirm'                => __( 'Confirm', 'robin-image-optimizer' ),

				'modal_optimization_title'            => __( 'Select optimization way', 'robin-image-optimizer' ),
				'modal_optimization_manual_button'    => __( 'Optimize now', 'robin-image-optimizer' ),
				'modal_optimization_cron_button'      => $modal_optimization_cron_button,
				'modal_optimization_cron_button_stop' => $modal_optimization_cron_button_stop,
				'optimization_complete'               => __( 'All images from the media library are optimized.', 'robin-image-optimizer' ),
				'optimization_inprogress'             => __( 'Optimization in progress. Remained <span id="wio-total-unoptimized">%s</span> images.', 'robin-image-optimizer' ),
				'modal_conversion_title'              => __( 'Select conversion way', 'robin-image-optimizer' ),
				'modal_conversion_manual_button'      => __( 'Convert now', 'robin-image-optimizer' ),
				'modal_conversion_cron_button'        => $modal_conversion_cron_button,
				'modal_conversion_cron_button_stop'   => $modal_conversion_cron_button_stop,
				'conversion_complete'                 => __( 'All images from the media library are optimized.', 'robin-image-optimizer' ),
				'conversion_inprogress'               => __( 'Conversion in progress. Remained <span id="wio-total-unoptimized">%s</span> images.', 'robin-image-optimizer' ),
				'webp_button_start'                   => __( 'Convert to WebP', 'robin-image-optimizer' ),

				'need_migrations'        => __( 'To start optimizing, you must complete migration from old plugin version.', 'robin-image-optimizer' ),
				'leave_page_warning'     => __( 'Are you sure that you want to leave the page? The optimization process is not over yet, stay on the page until the end of the optimization process.', 'robin-image-optimizer' ),
				'process_without_backup' => __( 'Do you want to start optimization without backup?', 'robin-image-optimizer' ),
				'button_resume'          => __( 'Resume', 'robin-image-optimizer' ),
				'button_completed'       => __( 'Completed', 'robin-image-optimizer' ),
				'button_start'           => __( 'Optimize', 'robin-image-optimizer' ),
				'button_stop'            => __( 'Stop', 'robin-image-optimizer' ),
			//Don't Need a Parachute?
			//If you keep this option deactivated, you won't be able to re-optimize your images to another compression level and restore your original images in case of need.
		];
	}
}
