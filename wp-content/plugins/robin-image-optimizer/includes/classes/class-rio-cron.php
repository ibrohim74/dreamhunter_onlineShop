<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для работы оптимизации по расписанию
 *
 * @author        Eugene Jokerov <jokerov@gmail.com>
 * @copyright (c) 2018, Webcraftic
 * @version       1.0
 */
class WRIO_Cron {

	/**
	 * Инициализация оптимизации по расписанию
	 */
	public function __construct() {
		$this->initHooks();
	}

	/**
	 * Подключение хуков
	 */
	public function initHooks() {
		add_action( 'wrio/cron/optimization_process', [ $this, 'optimization_process' ], 10, 1 );
		add_action( 'wrio/cron/conversion_process', [ $this, 'conversion_process' ], 10, 1 );
		add_filter( 'cron_schedules', [ $this, 'intervals' ], 100, 1 );
	}

	/**
	 * Кастомные интервалы выполнения cron задачи
	 *
	 * @param array $intervals Зарегистрированные интервалы
	 *
	 * @return array $intervals Новые интервалы
	 */
	public function intervals( $intervals ) {
		$intervals['wio_1_min']  = [
			'interval' => 60,
			'display'  => __( '1 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_2_min']  = [
			'interval' => 60 * 2,
			'display'  => __( '2 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_5_min']  = [
			'interval' => 60 * 5,
			'display'  => __( '5 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_10_min'] = [
			'interval' => 60 * 10,
			'display'  => __( '10 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_30_min'] = [
			'interval' => 60 * 30,
			'display'  => __( '30 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_hourly'] = [
			'interval' => 60 * 60,
			'display'  => __( '60 min', 'robin-image-optimizer' ),
		];
		$intervals['wio_daily']  = [
			'interval' => 60 * 60 * 24,
			'display'  => __( 'daily', 'robin-image-optimizer' ),
		];

		return $intervals;
	}

	/**
	 * Запуск Cron задачи
	 */
	public static function start_single( $attachment_id ) {
		wp_schedule_single_event( time() + 10, 'wrio/cron/optimization_process', [ $attachment_id ] );
	}

	/**
	 * Запуск Cron задачи
	 */
	public static function start( $type = 'optimization' ) {
		$interval = WRIO_Plugin::app()->getPopulateOption( 'image_autooptimize_shedule_time', 'wio_5_min' );
		if ( ! wp_next_scheduled( "wrio/cron/{$type}_process" ) ) {
			wp_schedule_event( time(), $interval, "wrio/cron/{$type}_process" );
		}
	}

	/**
	 * Остановка Cron задачи
	 */
	public static function stop( $type = 'optimization' ) {
		if ( wp_next_scheduled( "wrio/cron/{$type}_process" ) ) {
			wp_clear_scheduled_hook( "wrio/cron/{$type}_process" );
			WRIO_Plugin::app()->updatePopulateOption( "{$type}_cron_running", false ); // останавливаем крон
		}
	}

	/**
	 * Метод оптимизирует изображения при выполнении cron задачи
	 */
	public function optimization_process( $attachment_id = 0 ) {
		//Optimize single image via cron
		if ( $attachment_id ) {
			WRIO_Plugin::app()->logger->info( sprintf( "START auto optimize cron job. Attachment: %s", $attachment_id ) );
			$media_library = WRIO_Media_Library::get_instance();
			$media_library->optimizeAttachment( $attachment_id );
			WRIO_Plugin::app()->logger->info( sprintf( "END auto optimize cron job. Attachment: %s", $attachment_id ) );

			return;
		}

		$max_process_per_request = WRIO_Plugin::app()->getPopulateOption( 'image_autooptimize_items_number_per_interation', 3 );
		$cron_running_page       = WRIO_Plugin::app()->getPopulateOption( 'cron_running', false );

		if ( ! $cron_running_page ) {
			return;
		}

		WRIO_Plugin::app()->logger->info( sprintf( "Start cron job. Scope: %s", $cron_running_page ) );

		if ( 'media-library' == $cron_running_page ) {
			$media_library = WRIO_Media_Library::get_instance();
			$result        = $media_library->processUnoptimizedImages( $max_process_per_request );
		} else if ( 'nextgen' == $cron_running_page ) {
			$nextgen_gallery = WRIO_Nextgen_Gallery::get_instance();
			$result          = $nextgen_gallery->processUnoptimizedImages( $max_process_per_request );
		} else if ( 'custom-folders' == $cron_running_page ) {
			$cf     = WRIO_Custom_Folders::get_instance();
			$result = $cf->processUnoptimizedImages( $max_process_per_request );
		}

		if ( is_wp_error( $result ) ) {
			WRIO_Plugin::app()->logger->info( sprintf( "Cron job failed. Error: %s", $result->get_error_message() ) );
			WRIO_Plugin::app()->deletePopulateOption( 'cron_running' );

			return;
		}

		if ( $result['remain'] <= 0 ) {
			WRIO_Plugin::app()->deletePopulateOption( 'cron_running' );
		}

		WRIO_Plugin::app()->logger->info( sprintf( "End cron job. Scope: %s", $cron_running_page ) );
	}

	/**
	 * Метод оптимизирует изображения при выполнении cron задачи
	 */
	public function conversion_process( $attachment_id = 0 ) {
		//Optimize single image via cron
		if ( $attachment_id ) {
			WRIO_Plugin::app()->logger->info( sprintf( "START auto optimize cron job. Attachment: %s", $attachment_id ) );
			$media_library = WRIO_Media_Library::get_instance();
			$media_library->optimizeAttachment( $attachment_id );
			WRIO_Plugin::app()->logger->info( sprintf( "END auto optimize cron job. Attachment: %s", $attachment_id ) );

			return;
		}

		$max_process_per_request = WRIO_Plugin::app()->getPopulateOption( 'image_autooptimize_items_number_per_interation', 3 );
		$cron_running_page       = WRIO_Plugin::app()->getPopulateOption( 'conversion_cron_running', false );

		if ( ! $cron_running_page ) {
			return;
		}

		WRIO_Plugin::app()->logger->info( sprintf( "Start cron job. Scope: %s", $cron_running_page ) );

		if ( 'media-library' == $cron_running_page ) {
			$media_library = WRIO_Media_Library::get_instance();
			$result        = $media_library->webpUnoptimizedImages( $max_process_per_request );
		}

		if ( is_wp_error( $result ) ) {
			WRIO_Plugin::app()->logger->info( sprintf( "Cron job failed. Error: %s", $result->get_error_message() ) );
			WRIO_Plugin::app()->deletePopulateOption( 'conversion_cron_running' );

			return;
		}

		if ( $result['remain'] <= 0 ) {
			WRIO_Plugin::app()->deletePopulateOption( 'conversion_cron_running' );
		}

		WRIO_Plugin::app()->logger->info( sprintf( "End cron job. Scope: %s", $cron_running_page ) );
	}

}
