<?php

use WRIO\Paths\phpUri;

/**
 * Checks if the current request is a WP REST API request.
 *
 * Case #1: After WP_REST_Request initialisation
 * Case #2: Support "plain" permalink settings
 * Case #3: URL Path begins with wp-json/ (your REST prefix)
 *          Also supports WP installations in subfolders
 *
 * @author matzeeable https://wordpress.stackexchange.com/questions/221202/does-something-like-is-rest-exist
 * @since  1.3.6
 * @return boolean
 */
function wrio_doing_rest_api() {
	$prefix     = rest_get_url_prefix();
	$rest_route = WRIO_Plugin::app()->request->get( 'rest_route', null );
	if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) // (#1)
		 || ( ! is_null( $rest_route ) // (#2)
			  && strpos( trim( $rest_route, '\\/' ), $prefix, 0 ) === 0 ) ) {
		return true;
	}

	// (#3)
	$rest_url    = wp_parse_url( site_url( $prefix ) );
	$current_url = wp_parse_url( add_query_arg( [] ) );

	return strpos( $current_url['path'] ?? '/', $rest_url['path'], 0 ) === 0;
}

/**
 * @return bool
 * @since  1.3.6
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_doing_ajax() {
	if ( function_exists( 'wp_doing_ajax' ) ) {
		return wp_doing_ajax();
	}

	return defined( 'DOING_AJAX' ) && DOING_AJAX;
}

/**
 * @return bool
 * @since  1.3.6
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_doing_cron() {
	if ( function_exists( 'wp_doing_cron' ) ) {
		return wp_doing_cron();
	}

	return defined( 'DOING_CRON' ) && DOING_CRON;
}

/**
 * Convert full URL paths to absolute paths.
 *
 * @param string $url abs url https://site.com/wp-conent/uploads/10/05/image.jpeg
 *
 * @return string|null abs path var/site.com/www/wp-conent/uploads/10/05/image.jpeg, if failure null
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @since  1.4.0
 *
 */
function wrio_url_to_abs_path( $url ) {
	if ( empty( $url ) ) {
		return null;
	}

	if ( strpos( $url, '?' ) !== false ) {
		$url_parts = explode( '?', $url );

		if ( 2 == sizeof( $url_parts ) ) {
			$url = $url_parts[0];
		}
	}

	$url = rtrim( $url, '/' );

	# todo: if the external site, then it will not work
	return str_replace( get_site_url(), untrailingslashit( wp_normalize_path( ABSPATH ) ), $url );
}

/**
 * Convert relative urls to absolute
 *
 * @param string $url relative url /wp-conent/uploads/10/05/image.jpeg
 *
 * @return string abs url https://site.com/wp-conent/uploads/10/05/image.jpeg
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @since  1.4.0
 *
 */
function wrio_rel_to_abs_url( $url ) {
	require_once WRIO_PLUGIN_DIR . '/libs/class-rio-relative-to-abs-uri.php';

	return WRIO\Paths\phpUri::parse( get_site_url() )->join( $url );
}

/**
 * Converts relative urls to absolute paths
 *
 * @param string $url relative url /wp-conent/uploads/10/05/image.jpeg
 *
 * @return string abs path var/site.com/www/wp-conent/uploads/10/05/image.jpeg
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @since  1.4.0
 *
 */
function wrio_rel_url_to_abs_path( $url ) {
	$abs_url = wrio_rel_to_abs_url( $url );

	return wrio_url_to_abs_path( $abs_url );
}

/**
 * @param      $string
 * @param bool $capitalize_first_character
 *
 * @return mixed|string
 * @since  1.1
 *
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_dashes_to_camel_case( $string, $capitalize_first_character = false ) {

	$str = str_replace( '-', '_', ucwords( $string, '-' ) );

	if ( ! $capitalize_first_character ) {
		$str = lcfirst( $str );
	}

	return $str;
}

/**
 * Alternative php functions basename. Our function works with сyrillic file names.
 *
 * @param string $str file path
 *
 * @return string|string[]|null
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @since  1.3.0
 *
 */
/*function wrio_basename( $str ) {
	return preg_replace( '/^.+[\\\\\\/]/', '', $str );
}*/

/**
 * @return bool
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_is_active_nextgen_gallery() {
	return is_plugin_active( 'nextgen-gallery/nggallery.php' );
}

/**
 * @param string $dir
 *
 * @return bool
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 * @since  1.1
 *
 */
function wrio_rmdir( $dir ) {
	if ( is_dir( $dir ) ) {
		$scn = scandir( $dir );

		foreach ( $scn as $files ) {
			if ( $files !== '.' ) {
				if ( $files !== '..' ) {
					if ( ! is_dir( $dir . '/' . $files ) ) {
						@unlink( $dir . '/' . $files );
					} else {
						wrio_rmdir( $dir . '/' . $files );
						if ( is_dir( $dir . '/' . $files ) ) {
							@rmdir( $dir . '/' . $files );
						}
					}
				}
			}
		}
		@rmdir( $dir );

		return true;
	}

	return false;
}

/**
 * Пересчёт размера файла в байтах на человекопонятный вид
 *
 * Пример: вводим 67894 байт, получаем 67.8 KB
 * Пример: вводим 6789477 байт, получаем 6.7 MB
 *
 * @param int $size размер файла в байтах
 *
 * @return string
 */
function wrio_convert_bytes( $size ) {
	if ( ! $size ) {
		return 0;
	}
	$base   = log( $size ) / log( 1024 );
	$suffix = [ '', 'KB', 'MB', 'GB', 'TB' ];
	$f_base = intval( floor( $base ) );

	return round( pow( 1024, $base - floor( $base ) ), 2 ) . ' ' . $suffix[ $f_base ];
}

/**
 * Генерирует хеш строку
 *
 * @param int $length
 *
 * @return string
 */
function wrio_generate_random_string( $length = 10 ) {
	$characters       = '0123456789abcdefghiklmnopqrstuvwxyz';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

/**
 * Checks whether the license is activated for the plugin or not. If the Clearfy plugin is installed
 * in priorities checks its license.
 *
 * @return bool
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_is_license_activate() {
	return wrio_is_clearfy_license_activate() || ( WRIO_Plugin::app()->premium->is_activate() && WRIO_Plugin::app()->premium->is_active() );
}

/**
 * Checks whether the license is activated for Clearfy plugin.
 *
 * @return bool
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_is_clearfy_license_activate() {
	if ( class_exists( 'WCL_Plugin' ) ) {
		if ( version_compare( WCL_PLUGIN_VERSION, '1.6.3', '>=' ) ) {
			if ( WCL_Plugin::app()->premium->is_activate() ) {
				$plan_id = WCL_Plugin::app()->premium->get_license()->get_plan_id();

				// Old plans for the Clearfy plugin allowed the use the Robin Premium plugin under one license.
				// Now for new plans this doesn't work.
				return "4710" === $plan_id || "3530" === $plan_id;
			}
		} else {
			$current_license = WCL_Licensing::instance()->getStorage()->getLicense();

			if ( ! empty( $current_license ) && ! empty( $current_license->id ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Checks active (not expired!) License for plugin or not. If the Clearfy plugin is installed
 * checks its license in priorities.
 *
 * @return bool
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_is_license_active() {
	if ( wrio_is_clearfy_license_activate() ) {
		if ( version_compare( WCL_PLUGIN_VERSION, '1.6.3', '>=' ) ) {
			return WCL_Plugin::app()->premium->is_active();
		} else {
			return WCL_Licensing::instance()->isLicenseValid();
		}
	}

	return WRIO_Plugin::app()->premium->is_activate() && WRIO_Plugin::app()->premium->is_active();
}

/**
 * Allows you to get a license key. If the Clearfy plugin is installed, it will be prioritized
 * return it key.
 *
 * @return string|null
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_get_license_key() {
	if ( ! wrio_is_license_activate() ) {
		return null;
	}

	if ( wrio_is_clearfy_license_activate() ) {
		if ( version_compare( WCL_PLUGIN_VERSION, '1.6.3', '>=' ) ) {
			return WCL_Plugin::app()->premium->get_license()->get_key();
		} else {
			return WCL_Licensing::instance()->getStorage()->getLicense()->secret_key;
		}
	}

	return WRIO_Plugin::app()->premium->get_license()->get_key();
	/*
		if ( WRIO_Plugin::app()->premium->is_activate() ) {
		return WRIO_Plugin::app()->premium->get_license()->get_key();
	} else {
		if ( wrio_is_clearfy_license_activate() ) {
			if ( version_compare( WCL_PLUGIN_VERSION, '1.6.3', '>=' ) ) {
				return WCL_Plugin::app()->premium->get_license()->get_key();
			} else {
				return WCL_Licensing::instance()->getStorage()->getLicense()->secret_key;
			}
		} else {
			return null;
		}
	}
	*/
}

/**
 * @return number|null
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_get_freemius_plugin_id() {
	if ( wrio_is_clearfy_license_activate() ) {
		return WCL_Plugin::app()->getPluginInfoAttr( 'freemius_plugin_id' );
	}

	return WRIO_Plugin::app()->premium->get_setting( 'plugin_id' );
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @return array $sizes Data for all currently-registered image sizes.
 * @uses   get_intermediate_image_sizes()
 * @global $_wp_additional_image_sizes
 */
function wrio_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = [];

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, [ 'thumbnail', 'medium', 'medium_large', 'large' ] ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} else if ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = [
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			];
		}
	}

	return $sizes;
}

/**
 * Возвращает URL сервера оптимизации
 *
 * @param string $server_name имя сервера
 *
 * @return string
 * @since  1.2.0
 *
 */
function wrio_get_server_url( $server_name ) {

	$use_http = WRIO_Plugin::app()->getPopulateOption( 'use_http' );

	$servers = [
		'server_1' => 'http://api.resmush.it/ws.php',
		'server_2' => 'https://dev.robinoptimizer.com/v1/free/image/optimize',
		'server_5' => 'https://dashboard.robinoptimizer.com/v1/tariff/optimize',
	];

	$servers = apply_filters( 'wbcr/rio/allow_servers', $servers );

	if ( isset( $servers[ $server_name ] ) ) {
		return $servers[ $server_name ];
	}

	return null;
}

/**
 * Check whether there are some migrations left to be processed.
 *
 * @return bool
 * @throws Exception
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @since  1.3.0
 */
function wbcr_rio_has_meta_to_migrate() {

	$db_version = RIO_Process_Queue::get_db_version();

	if ( 2 === $db_version ) {
		return false;
	}

	// Low number to limit resources consumption
	$attachments = wbcr_rio_get_meta_to_migrate( 5 );

	if ( isset( $attachments->posts ) && count( $attachments->posts ) > 0 ) {
		return true;
	}

	if ( 1 === $db_version ) {
		RIO_Process_Queue::update_db_version( 2 );
	}

	return false;
}

/**
 * Get list of meta to migrate.
 *
 * @param int $limit Attachment limit per page.
 *
 * @return WP_Query
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @since  1.3.0
 *
 */
function wbcr_rio_get_meta_to_migrate( $limit = 0 ) {
	$args = [
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_mime_type' => [ 'image/jpeg', 'image/gif', 'image/png' ],
		'posts_per_page' => - 1,
		'meta_query'     => [
			[
				'key'     => 'wio_optimized',
				'compare' => 'EXISTS',
			],
		],
	];

	if ( $limit ) {
		$args['posts_per_page'] = $limit;
	}

	return new WP_Query( $args );
}

/**
 * @return string
 * @since  1.3.0
 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
 */
function wrio_get_meta_migration_notice_text() {
	$nonce = wp_create_nonce( 'wrio-meta-migrations' );

	return sprintf( __( 'There were big changes in database schema. Please <a href="#" id="wbcr-wio-meta-migration-action" class="button button-default" data-nonce="%s">click here</a> to upgrade it to the latest version', 'robin-image-optimizer' ), $nonce );
}

/**
 * @param string $scope
 *
 * @return WRIO_Folder_Processing|WRIO_Media_Processing|WRIO_Nextgen_Processing|null
 */
function wrio_get_processing_class( $scope ) {
	$object = null;
	switch ( $scope ) {
		case 'media-library':
			if ( class_exists( 'WRIO_Media_Processing' ) ) {
				$object = new WRIO_Media_Processing( $scope );
			}
			break;
		case 'media-library_webp':
			if ( class_exists( 'WRIO_Media_Processing_Webp' ) ) {
				$object = new WRIO_Media_Processing_Webp( $scope );
			}
			break;
		case 'custom-folders':
			if ( class_exists( 'WRIO_Folder_Processing' ) ) {
				$object = new WRIO_Folder_Processing( $scope );
			}
			break;
		case 'nextgen':
			if ( class_exists( 'WRIO_Nextgen_Processing' ) ) {
				$object = new WRIO_Nextgen_Processing( $scope );
			}
			break;
	}

	return $object;
}

/**
 * @param bool $for_sql
 *
 * @return string|array
 */
function wrio_get_allowed_formats( $for_sql = false ) {
	$allowed_formats     = explode( ',', WRIO_Plugin::app()->getOption( 'allowed_formats', "image/jpeg,image/png,image/gif" ) );
	$allowed_formats_sql = [];
	foreach ( $allowed_formats as $k => $format ) {
		$format = esc_sql( $format );

		$allowed_formats_sql[ $k ] = "'{$format}'";
	}
	$allowed_formats_sql = implode( ',', $allowed_formats_sql );

	return $for_sql ? $allowed_formats_sql : $allowed_formats;
}
