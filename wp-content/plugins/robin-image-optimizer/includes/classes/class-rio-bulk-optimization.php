<?php

use WBCR\Factory_Processing_103\WP_Background_Process;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для AJAX массовой оптимизации
 *
 * @author        Artem Prikhodko <webtemyk@yandex.ru>
 * @copyright (c) 2021, Webcraftic
 * @version       1.0
 */
class WRIO_Bulk_Optimization {

	public $processing;

	public function __construct() {
		$image_optimization_type = WRIO_Plugin::app()->getOption( 'image_optimization_type', '' );
		if ( wrio_is_license_activate() && $image_optimization_type === 'background' ) {
			$scope            = WRIO_Plugin::app()->request->request( 'scope', null, true );
			$this->processing = $scope ? wrio_get_processing_class( $scope ) : $scope;

			add_action( 'wp_ajax_wrio-cron-start', [ $this, 'processing_start' ] );
			add_action( 'wp_ajax_wrio-cron-stop', [ $this, 'processing_stop' ] );

			add_action( 'wp_ajax_wrio-webp-cron-start', [ $this, 'webp_processing_start' ] );
			add_action( 'wp_ajax_wrio-webp-cron-stop', [ $this, 'webp_processing_stop' ] );
		} else {
			add_action( 'wp_ajax_wrio-cron-start', [ $this, 'cron_start' ] );
			add_action( 'wp_ajax_wrio-cron-stop', [ $this, 'cron_stop' ] );

			add_action( 'wp_ajax_wrio-webp-cron-start', [ $this, 'webp_cron_start' ] );
			add_action( 'wp_ajax_wrio-webp-cron-stop', [ $this, 'webp_cron_stop' ] );
		}

		add_action( 'wp_ajax_wrio-bulk-optimization-process', [ $this, 'bulk_optimization_process' ] );
		add_action( 'wp_ajax_wrio-bulk-conversion-process', [ $this, 'bulk_conversion_process' ] );
		add_action( 'wp_ajax_wio_reoptimize_image', [ $this, 'reoptimize_image' ] );
		add_action( 'wp_ajax_wio_convert_image', [ $this, 'convert_image' ] );
		add_action( 'wp_ajax_wio_restore_image', [ $this, 'restore_image' ] );

		add_action( 'wp_ajax_wbcr-rio-check-servers-status', [ $this, 'check_servers_status' ] );
		add_action( 'wp_ajax_wbcr-rio-check-user-balance', [ $this, 'check_user_balance' ] );

		add_action( 'wp_ajax_wbcr-rio-calculate-total-images', [ $this, 'calculate_total_images' ] );

	}

	public function cron_start() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		// where was runned cron
		$cron_running_place = WRIO_Plugin::app()->getPopulateOption( 'cron_running', false );

		if ( $scope == $cron_running_place ) {
			wp_send_json_success();
		}

		WRIO_Plugin::app()->updatePopulateOption( 'cron_running', $scope );
		WRIO_Cron::start();

		wp_send_json_success();
	}

	public function cron_stop() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		WRIO_Plugin::app()->updatePopulateOption( 'cron_running', false );
		WRIO_Cron::stop();

		wp_send_json_success();
	}

	public function webp_cron_start() {
		check_ajax_referer( 'bulk_conversion' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		$type = 'conversion';

		// where was runned cron
		$cron_running_place = WRIO_Plugin::app()->getPopulateOption( "{$type}_cron_running", false );

		if ( $scope == $cron_running_place ) {
			wp_send_json_success();
		}

		WRIO_Plugin::app()->updatePopulateOption( "{$type}_cron_running", $scope );
		WRIO_Cron::start( $type );

		wp_send_json_success();
	}

	public function webp_cron_stop() {
		check_ajax_referer( 'bulk_conversion' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$type = 'conversion';

		WRIO_Plugin::app()->updatePopulateOption( "{$type}_cron_running", false );
		WRIO_Cron::stop( $type );

		wp_send_json_success();
	}

	public function processing_start() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		// where was runned
		$process_running_place = WRIO_Plugin::app()->getPopulateOption( 'process_running', false );

		if ( $scope == $process_running_place ) {
			wp_send_json_success();
		}

		WRIO_Plugin::app()->updatePopulateOption( 'process_running', $scope );

		$processing = wrio_get_processing_class( $scope );
		if ( $processing->push_items() ) {
			$processing->save()->dispatch();
		} else {
			//WRIO_Plugin::app()->updatePopulateOption( 'process_running', false );
			wp_send_json_success( [
				'stop' => true
			] );
		}

		wp_send_json_success();
	}

	public function processing_stop() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );
		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		WRIO_Plugin::app()->updatePopulateOption( 'process_running', false );
		$processing = wrio_get_processing_class( $scope );
		$processing->cancel_process();

		wp_send_json_success();
	}

	public function webp_processing_start() {
		check_ajax_referer( 'bulk_conversion' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		$scope = $scope . "_webp";

		// where was runned
		$process_running_place = WRIO_Plugin::app()->getPopulateOption( "{$scope}_process_running", false );

		if ( $scope == $process_running_place ) {
			wp_send_json_success();
		}

		WRIO_Plugin::app()->updatePopulateOption( "{$scope}_process_running", $scope );

		$processing = wrio_get_processing_class( $scope );
		if ( $processing->push_items() ) {
			$processing->save()->dispatch();
		} else {
			//WRIO_Plugin::app()->updatePopulateOption( 'process_running', false );
			wp_send_json_success( [
				'stop' => true
			] );
		}

		wp_send_json_success();
	}

	public function webp_processing_stop() {
		check_ajax_referer( 'bulk_conversion' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$scope = WRIO_Plugin::app()->request->request( 'scope', null, true );
		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		$scope = $scope . "_webp";

		WRIO_Plugin::app()->updatePopulateOption( "{$scope}_process_running", false );
		$processing = wrio_get_processing_class( $scope );
		$processing->cancel_process();

		wp_send_json_success();
	}

	public function bulk_optimization_process() {
		check_admin_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$reset_current_error = (bool) WRIO_Plugin::app()->request->request( 'reset_current_errors' );
		$scope               = WRIO_Plugin::app()->request->request( 'scope', null, true );

		WRIO_Plugin::app()->logger->info( sprintf( 'Start bulk optimization process! Scope: %s', $scope ) );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		// Context class name. If plugin expands with add-ons
		$class_name = 'WRIO_' . wrio_dashes_to_camel_case( $scope, true );

		if ( ! class_exists( $class_name ) ) {
			WRIO_Plugin::app()->logger->error( sprintf( 'Bulk optimization error: Context class (%s) not found.', $class_name ) );

			//todo: Temporary bug fix.
			if ( 'media-library' === $scope ) {
				$class_name = 'WRIO_Media_Library';
			} else if ( 'custom-folders' === $scope ) {
				$class_name = 'WRIO_Custom_Folders';
			} else if ( 'nextgen-gallery' == $scope ) {
				$class_name = 'WRIO_Nextgen_Gallery';
			}

			if ( ! class_exists( $class_name ) ) {
				wp_send_json_error( [ 'error_message' => 'Context class not found.' ] );
			}
		}

		/**
		 * Create an instance of the class depending on the context in which scope user
		 * has runned optimization.
		 *
		 * @see WRIO_Media_Library
		 * @see WRIO_Custom_Folders
		 * @see WRIO_Nextgen_Gallery
		 */
		$optimizer = new $class_name();

		// в ajax запросе мы не знаем, получен ли он из мультиадминки или из обычной. Поэтому проверяем параметр, полученный из frontend
		/*if ( isset( $_POST['multisite'] ) && (bool) $_POST['multisite'] ) {
			$multisite = new WIO_Multisite;
			$multisite->initHooks();
		}*/

		if ( $reset_current_error ) {
			$optimizer->resetCurrentErrors(); // сбрасываем текущие ошибки оптимизации
		}

		$result = $optimizer->processUnoptimizedImages( 1 );

		if ( is_wp_error( $result ) ) {
			$error_massage = $result->get_error_message();

			if ( empty( $error_massage ) ) {
				$error_massage = __( "Unknown error. Enable error log on the plugin's settings page, then check the error report on the Error Log page. You can export the error report and send it to the support service of the plugin.", "robin-image-optimizer" );
			}

			WRIO_Plugin::app()->logger->error( sprintf( 'Bulk optimization error: %s.', $result->get_error_message() ) );

			wp_send_json_error( [ 'error_message' => $error_massage ] );
		}

		// если изображения закончились - посылаем команду завершения
		if ( $result['remain'] <= 0 ) {
			$result['end'] = true;
		}

		WRIO_Plugin::app()->logger->info( sprintf( 'End bulk optimization process! Scope: %s. Remain: %d', $scope, $result['remain'] ) );

		wp_send_json_success( $result );
	}

	public function bulk_conversion_process() {
		check_admin_referer( 'bulk_conversion' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$reset_current_error = (bool) WRIO_Plugin::app()->request->request( 'reset_current_errors' );
		$scope               = WRIO_Plugin::app()->request->request( 'scope', null, true );

		WRIO_Plugin::app()->logger->info( sprintf( 'Start bulk conversion process! Scope: %s', $scope ) );

		if ( empty( $scope ) ) {
			wp_die( - 1 );
		}

		// Context class name. If plugin expands with add-ons
		$class_name = 'WRIO_' . wrio_dashes_to_camel_case( $scope, true );

		if ( ! class_exists( $class_name ) ) {
			WRIO_Plugin::app()->logger->error( sprintf( 'Bulk conversion error: Context class (%s) not found.', $class_name ) );

			//todo: Temporary bug fix.
			if ( 'media-library' === $scope ) {
				$class_name = 'WRIO_Media_Library';
			} else if ( 'custom-folders' === $scope ) {
				$class_name = 'WRIO_Custom_Folders';
			} else if ( 'nextgen-gallery' == $scope ) {
				$class_name = 'WRIO_Nextgen_Gallery';
			}

			if ( ! class_exists( $class_name ) ) {
				wp_send_json_error( [ 'error_message' => 'Context class not found.' ] );
			}
		}

		/**
		 * Create an instance of the class depending on the context in which scope user
		 * has runned optimization.
		 *
		 * @see WRIO_Media_Library
		 * @see WRIO_Custom_Folders
		 * @see WRIO_Nextgen_Gallery
		 */
		$optimizer = new $class_name();

		if ( $reset_current_error ) {
			$optimizer->resetCurrentErrors(); // сбрасываем текущие ошибки оптимизации
		}

		$result = $optimizer->webpUnoptimizedImages( 1 );

		if ( is_wp_error( $result ) ) {
			$error_massage = $result->get_error_message();

			if ( empty( $error_massage ) ) {
				$error_massage = __( "Unknown error. Enable error log on the plugin's settings page, then check the error report on the Error Log page. You can export the error report and send it to the support service of the plugin.", "robin-image-optimizer" );
			}

			WRIO_Plugin::app()->logger->error( sprintf( 'Bulk conversion error: %s.', $result->get_error_message() ) );

			wp_send_json_error( [ 'error_message' => $error_massage ] );
		}

		// если изображения закончились - посылаем команду завершения
		if ( $result['remain'] <= 0 ) {
			$result['end'] = true;
		}

		WRIO_Plugin::app()->logger->info( sprintf( 'End bulk conversion process! Scope: %s. Remain: %d', $scope, $result['remain'] ) );

		wp_send_json_success( $result );
	}

	public function reoptimize_image() {
		check_admin_referer( 'reoptimize' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$default_level = WRIO_Plugin::app()->getPopulateOption( 'image_optimization_level', 'normal' );

		$attachment_id = (int) WRIO_Plugin::app()->request->post( 'id' );
		$level         = WRIO_Plugin::app()->request->post( 'level', $default_level, true );

		$backup               = WIO_Backup::get_instance();
		$media_library        = WRIO_Media_Library::get_instance();
		$backup_origin_images = WRIO_Plugin::app()->getPopulateOption( 'backup_origin_images', false );

		if ( $backup_origin_images && ! $backup->isBackupWritable() ) {
			echo $media_library->getMediaColumnContent( $attachment_id );
			die();
		}

		$optimized_data = $media_library->optimizeAttachment( $attachment_id, $level );

		if ( $optimized_data && isset( $optimized_data['processing'] ) ) {
			echo 'processing';
			die();
		}

		echo $media_library->getMediaColumnContent( $attachment_id );
		die();
	}

	public function convert_image() {
		check_admin_referer( 'convert' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$attachment_id = (int) WRIO_Plugin::app()->request->post( 'id' );
		$media_library = WRIO_Media_Library::get_instance();

		$media_library->webpConvertAttachment( $attachment_id );

		echo $media_library->getMediaColumnContent( $attachment_id );
		die();
	}

	public function restore_image() {
		check_admin_referer( 'restore' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$attachment_id = (int) WRIO_Plugin::app()->request->post( 'id' );

		$media_library  = WRIO_Media_Library::get_instance();
		$wio_attachment = $media_library->getAttachment( $attachment_id );

		if ( $wio_attachment->isOptimized() ) {
			$media_library->restoreAttachment( $attachment_id );
		}

		echo $media_library->getMediaColumnContent( $attachment_id );
		die();
	}

	public function check_servers_status() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$server_name = WRIO_Plugin::app()->request->post( 'server_name' );

		if ( empty( $server_name ) || ! in_array( $server_name, [
				'server_1',
				'server_2',
				'server_5'
			] ) ) {
			wp_send_json_error( [ 'error' => __( 'Server name is empty!', 'robin-image-optimizer' ) ] );
		}

		// Позволяем выбрать сервер, даже если он недоступен.
		WRIO_Plugin::app()->updatePopulateOption( 'image_optimization_server', $server_name );

		// Проверяем доступность сервер
		// --------------------------------------------------------------------
		$return_data = [ 'server_name' => $server_name ];

		$server_url = wrio_get_server_url( $server_name );
		$headers    = [];

		$method = 'POST';

		if ( $server_name == 'server_2' ) {
			$api_url                  = "https://dev.robinoptimizer.com/v1/free/license/check";
			$method                   = 'GET';
			$host                     = get_option( 'siteurl' );
			$headers['Authorization'] = 'Bearer ' . base64_encode( $host );
		} else if ( $server_name == 'server_5' ) {
			$api_url                  = "https://dashboard.robinoptimizer.com/v1/license/check";
			$method                   = 'GET';
			$headers['Authorization'] = 'Bearer ' . base64_encode( wrio_get_license_key() );
			$headers['PluginId']      = wrio_get_freemius_plugin_id();
		} else {
			$api_url = $server_url;
		}

		$request = wp_remote_request( $api_url, [
			'method'  => $method,
			'headers' => $headers
		] );

		if ( is_wp_error( $request ) ) {
			$er_msg = $request->get_error_message();

			$return_data['error'] = $er_msg;
			wp_send_json_error( $return_data );
		}

		$response_code = wp_remote_retrieve_response_code( $request );

		if ( $response_code != 200 ) {
			$return_data['error'] = 'Server response ' . $response_code;
			wp_send_json_error( $return_data );
		}

		wp_send_json_success( $return_data );
	}

	public function check_user_balance() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$optimization_server = $server_name = WRIO_Plugin::app()->request->post( 'server_name' );
		if ( $optimization_server !== 'server_5' && $optimization_server !== 'server_2' ) {
			$processor = WIO_OptimizationTools::getImageProcessor();

			$processor->checkLimits( false );

			$usage     = (int) WRIO_Plugin::app()->getPopulateOption( $processor->getUsageOptionName(), 0 );
			$remaining = $processor->iamokay() - $usage;
			wp_send_json_success( [
				'balance' => $remaining,
			] );
		}

		if ( $optimization_server == 'server_2' ) {
			$api_url                  = "https://dev.robinoptimizer.com/v1/free/license/remaining";
			$host                     = get_option( 'siteurl' );
			$headers['Authorization'] = 'Bearer ' . base64_encode( $host );
		} elseif ( $optimization_server == 'server_5' ) {
			$api_url                  = 'https://dashboard.robinoptimizer.com/v1/license/remaining';
			$headers['Authorization'] = 'Bearer ' . base64_encode( wrio_get_license_key() );
			$headers['PluginId']      = wrio_get_freemius_plugin_id();
		}


		$request = wp_remote_request( $api_url, [
			'method'  => 'GET',
			'headers' => $headers
		] );

		if ( is_wp_error( $request ) ) {
			$error_msg = $request->get_error_message();

			$return_data['error'] = $error_msg;
			wp_send_json_error( $return_data );
		}

		$response_code = wp_remote_retrieve_response_code( $request );
		$response_body = wp_remote_retrieve_body( $request );

		if ( $response_code != 200 ) {
			$return_data['error'] = 'Server response ' . $response_code;
			if ( $response_code === 401 ) {
				$error_data           = @json_decode( $response_body );
				$return_data['error'] = $error_data->message;
			}
			wp_send_json_error( $return_data );
		}

		if ( empty( $response_body ) ) {
			$return_data['error'] = "Server responded an empty request body!";
			wp_send_json_error( $return_data );
		}

		$data = @json_decode( $response_body );

		if ( ! isset( $data->status ) || $data->status != 'ok' ) {
			$return_data['error'] = "Server responded an fail status";
			wp_send_json_error( $return_data );
		}

		$current_quota = (int) $data->response->quota;
		WRIO_Plugin::app()->app()->updateOption( 'current_quota', $current_quota );

		$output = [ 'balance' => $current_quota ];

		if ( $optimization_server == 'server_5' ) {
			$reset_at           = (int) $data->response->reset_at;
			$reset_at           += (int) get_option( 'gmt_offset', 0 );
			$output['reset_at'] = date( 'd-m-Y H:i', $reset_at );
		}

		wp_send_json_success( $output );
	}

	public function calculate_total_images() {
		check_ajax_referer( 'bulk_optimization' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		global $wpdb;
		$db_table         = RIO_Process_Queue::table_name();
		$sql              = $wpdb->prepare( "SELECT *	FROM {$db_table}					
					WHERE item_type = 'attachment' AND result_status IN (%s, %s)
					ORDER BY id DESC;", RIO_Process_Queue::STATUS_SUCCESS, RIO_Process_Queue::STATUS_ERROR );
		$optimized_images = $wpdb->get_results( $sql, ARRAY_A );

		$count = 0;
		if ( ! empty( $optimized_images ) ) {
			foreach ( $optimized_images as $row ) {
				$item  = new RIO_Process_Queue( $row );
				$count = $count + 1 + (int) $item->get_extra_data()->get_thumbnails_count();
			}
		}

		$allowed_formats_sql = wrio_get_allowed_formats( true );
		$sql                 = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit' AND post_mime_type IN ( {$allowed_formats_sql} );";
		$attachments         = $wpdb->get_results( $sql );

		$allowed_sizes = explode( ',', WRIO_Plugin::app()->getPopulateOption( 'allowed_sizes_thumbnail', '' ) );
		$total_images  = 0;
		$upload        = wp_upload_dir();
		$upload        = $upload['basedir'];
		foreach ( $attachments as $attachment ) {
			$meta = wp_get_attachment_metadata( $attachment->ID );
			if ( $meta ) {
				if ( isset( $meta['file'] ) && file_exists( "{$upload}/{$meta['file']}" ) ) {
					$total_images ++;
				}

				foreach ( $meta['sizes'] as $k => $value ) {
					if ( in_array( $k, $allowed_sizes ) ) {
						$total_images ++;
					}
				}
			}
		}
		$result_total = $total_images - $count;
		wp_send_json_success( [
			'total' => $result_total >= 0 ? $result_total : 0,
		] );
	}

}