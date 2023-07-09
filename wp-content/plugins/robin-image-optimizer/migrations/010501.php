<?php

/**
 * Updates for altering the table used to store statistics data.
 * Adds new columns and renames existing ones in order to add support for the new social buttons.
 */
class WIOUpdate010501 extends Wbcr_Factory458_Update {

	/**
	 * {inherit}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.3.9
	 */
	public function install() {
		$old_dir = $this->get_old_dir();
		$new_dir = WRIO_Plugin::app()->logger->get_base_dir();

		$files = array_diff( scandir( $old_dir ), array( '..', '.' ) );
		foreach ( $files as $file ) {
			@copy( $old_dir . $file, $new_dir . $file );
		}
		WRIO_Plugin::app()->logger->info( 'Plugin migration to 1.5.1 was successful!' );
	}

	/**
	 * Get base directory, location of logs.
	 *
	 * @return null|string NULL in case of failure, string on success.
	 */
	public function get_old_dir() {
		$wp_upload_dir = wp_upload_dir();

		if ( isset( $wp_upload_dir['error'] ) && $wp_upload_dir['error'] !== false ) {
			return null;
		}

		$base_path = wp_normalize_path( trailingslashit( $wp_upload_dir['basedir'] ) . 'wrio/' );

		$folders = glob( $base_path . 'logs-*' );

		if ( ! empty( $folders ) ) {
			$exploded_path        = explode( '/', trim( $folders[0] ) );
			$selected_logs_folder = array_pop( $exploded_path );
		} else {
			if ( function_exists( 'wp_salt' ) ) {
				$hash = md5( wp_salt() );
			} else {
				$hash = md5( AUTH_KEY );
			}

			$selected_logs_folder = 'logs-' . $hash;
		}

		$path = $base_path . $selected_logs_folder . '/';

		if ( ! file_exists( $path ) ) {
			@mkdir( $path, 0755, true );
		}

		// Create .htaccess file to protect log files
		$htaccess_path = $path . '.htaccess';

		if ( ! file_exists( $htaccess_path ) ) {
			$htaccess_content = 'deny from all';
			@file_put_contents( $htaccess_path, $htaccess_content );
		}

		// Create index.htm file in case .htaccess is not support as a fallback
		$index_path = $path . 'index.html';

		if ( ! file_exists( $index_path ) ) {
			@file_put_contents( $index_path, '' );
		}

		return $path;
	}

}
