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
class WRIO_Folder_Processing extends WRIO_Processing {

	/**
	 * @return int Count of pushed queue
	 */
	public function push_items() {
		$attachment_ids = [];
		if ( $this->scope === 'custom-folders' ) {
			$cf             = WRIO_Custom_Folders::get_instance();
			$attachment_ids = $cf->getUnoptimizedImages();
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
			WRIO_Plugin::app()->logger->info( sprintf( "Start optimize custom folder image: %s", $image ) );

			if ( $this->scope === 'custom-folders' ) {
				$cf     = WRIO_Custom_Folders::get_instance();
				$result = $cf->optimizeImage( $image );
			}

			WRIO_Plugin::app()->logger->info( sprintf( "End optimize custom folder image: %s", $image ) );
		}

		return false;
	}
}
