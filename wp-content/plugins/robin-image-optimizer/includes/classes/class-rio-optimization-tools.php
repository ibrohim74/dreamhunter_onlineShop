<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Инструменты для оптмизации изображений
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 22.09.2018, Webcraftic
 * @version       1.0
 */
class WIO_OptimizationTools {

	/**
	 * @var WIO_Image_Processor_Abstract Объект оптимизатор
	 */
	private static $image_processor;

	/**
	 * Метод возвращает объект, отвечающий за оптимизацию изображений через API сторонних сервисов
	 *
	 * @return WIO_Image_Processor_Abstract
	 */
	public static function getImageProcessor() {
		if ( self::$image_processor ) {
			return self::$image_processor;
		}

		$server = WRIO_Plugin::app()->getPopulateOption( 'image_optimization_server', 'server_1' );

		switch ( $server ) {
			case 'server_1':
				require_once( WRIO_PLUGIN_DIR . '/includes/classes/processors/class-rio-server-resmush.php' ); // resmush api
				self::$image_processor = new WIO_Image_Processor_Resmush();
				break;
			case 'server_2':
				require_once( WRIO_PLUGIN_DIR . '/includes/classes/processors/class-rio-server-robin.php' ); // robin api
				self::$image_processor = new WIO_Image_Processor_Robin();
				break;
			case 'server_5':
				require_once( WRIO_PLUGIN_DIR . '/includes/classes/processors/class-rio-server-premium.php' ); // webcraftic api
				self::$image_processor = new WIO_Image_Processor_Premium();
				break;
			default:
				require_once( WRIO_PLUGIN_DIR . '/includes/classes/processors/class-rio-server-resmush.php' ); // resmush api
				self::$image_processor = new WIO_Image_Processor_Resmush();
		}

		return self::$image_processor;
	}
}
