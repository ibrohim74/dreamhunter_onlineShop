<?php

use WBCR\Factory_Processing_103\WP_Background_Process;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для работы оптимизации в фоне
 *
 * @author        Artem Prikhodko <webtemyk@yandex.ru>
 * @copyright (c) 2021, Webcraftic
 * @version       1.0
 */
class WRIO_Media_Processing_Webp extends WRIO_Processing {

	/**
	 * @var string
	 */
	protected $action = 'convert_process';

	/**
	 * @return int Count of pushed queue
	 */
	public function push_items() {
		$attachment_ids = [];
		if ( $this->scope === 'media-library_webp' ) {
			$media_library  = WRIO_Media_Library::get_instance();
			$attachment_ids = $media_library->getUnconvertedImages();
		}

		foreach ( $attachment_ids as $attachment_id ) {
			$this->push_to_queue( $attachment_id );
		}

		return $this->count_queue();
	}

	/**
	 * Метод оптимизирует изображения при выполнении задачи
	 *
	 * @param int $image
	 *
	 * @return bool
	 */
	protected function task( $image ) {
		if ( $image ) {
			WRIO_Plugin::app()->logger->info( sprintf( "Start convert attachment: %s", $image ) );

			if ( $this->scope === 'media-library_webp' ) {
				$media_library = WRIO_Media_Library::get_instance();
				//$result        = $media_library->optimizeAttachment( $image );
				$media_library->webpConvertAttachment( $image );
			}

			WRIO_Plugin::app()->logger->info( sprintf( "End convert attachment: %s", $image ) );
		}

		return false;
	}

	/**
	 * Fire after complete handle
	 */
	protected function handle_after_complete() {
		$scope = $this->scope . "_webp";
		WRIO_Plugin::app()->updatePopulateOption( "{$scope}_process_running", false );

	}

}
