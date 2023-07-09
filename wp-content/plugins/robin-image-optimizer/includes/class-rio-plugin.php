<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Основной класс плагина
 *
 * @author        Webcraftic <WordPress.webraftic@gmail.com>
 * @copyright (c) 19.02.2018, Webcraftic
 * @version       1.0
 */
class WRIO_Plugin extends Wbcr_Factory458_Plugin {

	/**
	 * @see self::app()
	 * @var Wbcr_Factory458_Plugin
	 */
	private static $app;

	/**
	 * @since  3.1.0
	 * @var array
	 */
	private $plugin_data;

	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array $data
	 *
	 * @throws \Exception
	 */
	public function __construct( $plugin_path, $data ) {
		parent::__construct( $plugin_path, $data );

		self::$app         = $this;
		$this->plugin_data = $data;

		$this->includes();

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			// Processing
			if ( wrio_is_license_activate() ) {
				require_once WRIO_PLUGIN_DIR . '/includes/classes/processing/class-rio-processing.php';
				require_once WRIO_PLUGIN_DIR . '/includes/classes/processing/class-rio-media-processing.php';
				require_once WRIO_PLUGIN_DIR . '/includes/classes/processing/class-rio-folder-processing.php';
				require_once WRIO_PLUGIN_DIR . '/includes/classes/processing/class-rio-nextgen-processing.php';

				require_once WRIO_PLUGIN_DIR . '/includes/classes/processing/class-rio-media-processing-webp.php';
			}
		}

		if ( is_admin() ) {
			$this->initActivation();

			// completely disable image size threshold
			add_filter( 'big_image_size_threshold', '__return_false' );

			if ( wrio_is_license_activate() ) {
				if ( ! defined( 'FACTORY_ADVERTS_BLOCK' ) ) {
					define( 'FACTORY_ADVERTS_BLOCK', true );
				}
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				// Ajax files
				require_once WRIO_PLUGIN_DIR . '/admin/ajax/backup.php';
				require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-bulk-optimization.php';
				new WRIO_Bulk_Optimization();

				//require_once( WRIO_PLUGIN_DIR . '/admin/ajax/logs.php' );

				// Not under AJAX logical operator above on purpose to have helpers available to find out whether
				// metas were migrated or not
				require_once WRIO_PLUGIN_DIR . '/admin/ajax/meta-migrations.php';
			}
		}

		add_action( 'plugins_loaded', [ $this, 'pluginsLoaded' ] );
	}

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * Позволяет разработчику глобально получить доступ к экземпляру класса плагина в любом месте
	 * плагина, но при этом разработчик не может вносить изменения в основной класс плагина.
	 *
	 * Используется для получения настроек плагина, информации о плагине, для доступа к вспомогательным
	 * классам.
	 *
	 * @return \Wbcr_Factory458_Plugin|\WRIO_Plugin
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * Подключаем функции бекенда
	 *
	 * @throws Exception
	 */
	public function pluginsLoaded() {
		if ( is_admin() || wrio_doing_cron() || wrio_doing_rest_api() ) {
			$media_library = WRIO_Media_Library::get_instance();
			$media_library->initHooks();
		}

		if ( is_admin() ) {
			require_once WRIO_PLUGIN_DIR . '/admin/boot.php';
			//require_once( WRIO_PLUGIN_DIR . '/admin/includes/classes/class-rio-nextgen-landing.php' );

			$this->registerPages();
		}

		if ( wrio_doing_cron() || wrio_doing_rest_api() ) {
			$media_library = WRIO_Media_Library::get_instance();
			$media_library->initHooks();
		}

		if ( wrio_is_license_activate() ) {
			require_once WRIO_PLUGIN_DIR . '/libs/addons/robin-image-optimizer-premium.php';
			wrio_premium_load();
		}
	}

	/**
	 * Подключаем модули классы и функции
	 */
	protected function includes() {

		require_once WRIO_PLUGIN_DIR . '/includes/functions.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-views.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-attachment.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-media-library.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/processors/class-rio-server-abstract.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-image-statistic.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-backup.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-optimization-tools.php';

		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-base-helper.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-base-object.php'; // Base object

		// Database related models
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-base-active-record.php';
		// Base class
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-base-extra-data.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-attachment-extra-data.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class.webp-extra-data.php';
		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-server-smushit-extra-data.php';

		require_once WRIO_PLUGIN_DIR . '/includes/classes/models/class-rio-process-queue-table.php'; // Processing queue model

		// Cron
		// ----------------
		require_once WRIO_PLUGIN_DIR . '/includes/classes/class-rio-cron.php';
		new WRIO_Cron();
	}

	/**
	 * Инициализируем активацию плагина
	 */
	protected function initActivation() {
		include_once WRIO_PLUGIN_DIR . '/admin/activation.php';
		self::app()->registerActivation( 'WIO_Activation' );
	}

	/**
	 * Регистрируем страницы плагина
	 *
	 * @throws Exception
	 */
	private function registerPages() {
		$admin_path = WRIO_PLUGIN_DIR . '/admin/pages/';

		// Parent page class
		require_once $admin_path . '/class-rio-page.php';

		if ( ! wrio_is_clearfy_license_activate() ) {
			self::app()->registerPage( 'WRIO_License_Page', $admin_path . '/class-rio-license.php' );
		}

		self::app()->registerPage( 'WRIO_SettingsPage', $admin_path . '/class-rio-settings.php' );
		self::app()->registerPage( 'WRIO_StatisticPage', $admin_path . '/class-rio-statistic.php' );

		if ( self::app()->getPopulateOption( 'error_log', false ) ) {
			self::app()->registerPage( 'WRIO_LogPage', $admin_path . '/class-rio-log.php' );
		}
	}

	/**
	 * Option enables error logging on frontend. If for some reason webp images are not displayed on the front-end, you can use
	 * this option to catch errors and send this report to the plugin support service.
	 *
	 * @return int
	 * @since  1.3.6
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function is_keep_error_log_on_frontend() {
		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			return false;
		}

		return (int) $this->getPopulateOption( 'keep_error_log_on_frontend', 0 );
	}
}

