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
class WRIO_Media_Processing extends WRIO_Processing {

	/**
	 * @param array $attachment_ids
	 *
	 * @return int Count of pushed queue
	 */
	public function push_items( $attachment_ids = [] ) {
		if ( empty( $attachment_ids ) && $this->scope === 'media-library' ) {
			$media_library  = WRIO_Media_Library::get_instance();
			$attachment_ids = $media_library->getUnoptimizedImages();
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
			WRIO_Plugin::app()->logger->info( sprintf( "Start optimize attachment: %s", $image ) );

			if ( $this->scope === 'media-library' ) {
				$media_library = WRIO_Media_Library::get_instance();
				$result        = $media_library->optimizeAttachment( $image );
			}

			WRIO_Plugin::app()->logger->info( sprintf( "End optimize attachment: %s", $image ) );
		}

		return false;
	}
}
