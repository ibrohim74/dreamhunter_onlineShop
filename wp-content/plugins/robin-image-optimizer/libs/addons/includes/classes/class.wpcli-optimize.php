<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP CLI commands for optimize
 *
 * @author        Artem Prikhodko <webtemyk@yandex.ru>
 * @copyright (c) 2021, Webcraftic
 * @version       1.0
 */
class WRIO_CLI_Commands {

	/**
	 * Start optimization
	 *
	 * ## OPTIONS
	 *
	 * <scope>
	 * : What to optimize?
	 *
	 * ## EXAMPLES
	 *
	 *     wp robin optimize media-library
	 *     wp robin optimize custom-folders
	 *     wp robin optimize nextgen
	 *
	 * @when after_wp_load
	 */
	public function optimize( $args, $assoc_args ) {
		list( $scope ) = $args;
		$process_running = WRIO_Plugin::app()->getPopulateOption( 'process_running', $scope );

		if ( ! $process_running ) {
			$processing = wrio_get_processing_class( $scope );
			if ( $scope && is_object( $processing ) ) {
				WP_CLI::log( "Scope: {$scope}" );
				$push_items = $processing->push_items();
				if ( $push_items ) {
					WP_CLI::log( "Items pushed: {$push_items}" );
					WRIO_Plugin::app()->updatePopulateOption( 'process_running', $scope );
					$processing->save()->dispatch();
					WP_CLI::log( "Start optimize" );
				}
			} else {
				WP_CLI::error( "Undefined scope" );
			}
		} else {
			WP_CLI::error( "Optimize already running!" );
		}

	}

	/**
	 * Stop optimization
	 *
	 * ## OPTIONS
	 *
	 * <scope>
	 * : What to stop to optimize?
	 *
	 * ## EXAMPLES
	 *
	 *     wp robin stop media-library
	 *     wp robin stop custom-folders
	 *     wp robin stop nextgen
	 *
	 * @when after_wp_load
	 */
	public function stop( $args, $assoc_args ) {
		list( $scope ) = $args;

		$process_running = WRIO_Plugin::app()->getPopulateOption( 'process_running', $scope );

		if ( $process_running ) {
			$processing = wrio_get_processing_class( $scope );
			if ( $scope && is_object( $processing ) ) {
				WP_CLI::log( "Current scope: {$scope}" );
				$processing = wrio_get_processing_class( $scope );
				WRIO_Plugin::app()->updatePopulateOption( 'process_running', false );
				$processing->cancel_process();
				WP_CLI::log( "Processing scope '{$scope}' is canceled!" );
			} else {
				WP_CLI::error( "Undefined scope" );
			}
		} else {
			WP_CLI::error( "Optimize not running!" );
		}
	}

	/**
	 * Start optimization
	 *
	 * ## OPTIONS
	 *
	 * <scope>
	 * : What to show status?
	 *
	 * ## EXAMPLES
	 *
	 *     wp robin status media-library
	 *     wp robin status custom-folders
	 *     wp robin status nextgen
	 *
	 * @when after_wp_load
	 */
	public function status( $args, $assoc_args ) {
		list( $scope ) = $args;

		$process_running = WRIO_Plugin::app()->getPopulateOption( 'process_running', false );

		if ( $scope ) {
			$unoptimized = '';
			switch ( $scope ) {
				case 'media-library':
					$unoptimized = WRIO_Image_Statistic::get_unoptimized_count();
					break;
				case 'custom-folders':
					$unoptimized = WRIO_Image_Statistic_Folders::get_unoptimized_count();
					break;
				case 'nextgen':
					$unoptimized = WRIO_Image_Statistic_Nextgen::get_unoptimized_count();
					break;
			}

			WP_CLI::log( "Scope: {$scope}" );
			WP_CLI::log( "Status: " . ($process_running ? "running" : "stopped") );
			WP_CLI::log( "Remains to optimize: {$unoptimized}" );

		} else {
			WP_CLI::error( "Undefined scope" );
		}
	}

}

WP_CLI::add_command( 'robin', 'WRIO_CLI_Commands' );