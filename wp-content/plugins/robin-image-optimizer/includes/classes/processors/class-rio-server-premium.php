<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для оптимизации изображений через API сервиса Resmush.
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 2018, Webcraftic
 */
class WIO_Image_Processor_Premium extends WIO_Image_Processor_Abstract {

	/**
	 * @var string
	 */
	protected $api_url;

	/**
	 * @var string Имя сервера
	 */
	protected $server_name = 'server_5';

	/**
	 * Инициализация
	 *
	 * @return void
	 */
	public function __construct() {
		// Получаем ссылку на сервер 5
		$this->api_url = wrio_get_server_url( $this->server_name );
	}

	public function howareyou()
    {
        return false;
    }

    /**
	 * Оптимизация изображения
	 *
	 * @param array $params   входные параметры оптимизации изображения
	 *
	 * @return array|WP_Error {
	 *      Результаты оптимизации
	 *
	 *      {type} string $optimized_img_url УРЛ оптимизированного изображения на сервере оптимизации
	 *      {type} int $src_size размер исходного изображения в байтах
	 *      {type} int $optimized_size размер оптимизированного изображения в байтах
	 *      {type} int $optimized_percent На сколько процентов уменьшилось изображение
	 * }
	 */
	public function process( $settings ) {

		$settings = wp_parse_args( $settings, [
			'image_url' => '',
			'quality'   => 100,
			'save_exif' => false,
		] );

		$query_args = [
			'quality'     => $settings['quality'],
			'progressive' => true
		];

		if ( $settings['save_exif'] ) {
			$query_args['strip-exif'] = true;
		}

		$file = wp_normalize_path( $settings['image_path'] );

		if ( ! file_exists( $file ) ) {
			return new WP_Error( 'http_request_failed', sprintf( "File %s isn't exists.", $file ) );
		}

		WRIO_Plugin::app()->logger->info( sprintf( "Preparing to upload a file (%s) to a remote server (%s).", $settings['image_path'], $this->server_name ) );

		$boundary = wp_generate_password( 24 ); // Just a random string, use something better than wp_generate_password() though.
		$headers  = [
			'Authorization' => 'Bearer ' . base64_encode( wrio_get_license_key() ),
			'PluginId'      => wrio_get_freemius_plugin_id(),
			'content-type'  => 'multipart/form-data; boundary=' . $boundary
		];

		$payload = '';

		// First, add the standard POST fields:
		foreach ( $query_args as $name => $value ) {
			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
			$payload .= $value;
			$payload .= "\r\n";
		}
		// Upload the file
		if ( $file ) {
			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="file"; filename="' . basename( $file ) . '"' . "\r\n";
			//$payload .= 'Content-Type: image/jpeg' . "\r\n"; // If you know the mime-type
			$payload .= "\r\n";
			$payload .= @file_get_contents( $file );
			$payload .= "\r\n";
		}

		$payload .= '--' . $boundary . '--';

		$error_message = sprintf( 'Failed to get content of URL: %s as wp_remote_request()', $this->api_url );

		wp_raise_memory_limit( 'image' );

		$response = wp_remote_request( $this->api_url, [
			'method'  => 'POST',
			'headers' => $headers,
			'body'    => $payload,
			'timeout' => 150 // it make take some time for large images and slow Internet connections
		] );

		if ( is_wp_error( $response ) ) {
			WRIO_Plugin::app()->logger->error( sprintf( '%s returned error (%s).', $error_message, $response->get_error_message() ) );

			return $response;
		}

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
	        WRIO_Plugin::app()->logger->error( sprintf( '%s, responded Http error (%s)', $error_message, $response_code ) );

            return new WP_Error('http_request_failed', sprintf("Server responded an HTTP error %s", $response_code));
        }

        $response_text = wp_remote_retrieve_body($response);
        $data = @json_decode($response_text);
        if (!isset($data->status)) {
	        WRIO_Plugin::app()->logger->error( sprintf( '%s responded an empty request body.', $error_message ) );

            return new WP_Error('http_request_failed', "Server responded an empty request body.");
        }

        if ($data->status != 'ok') {
	        WRIO_Plugin::app()->logger->error( sprintf( "Pending status \"ok\", bot received \"%s\"", $data->status ) );

            if(isset($data->error) && is_string($data->error)) {
                return new WP_Error( 'http_request_failed', $data->error );
            }

            return new WP_Error('http_request_failed', sprintf("Server responded an %s status", $response_code));
        }

        WRIO_Plugin::app()->updateOption('current_quota', (int) $data->response->quota);

        return [
            'optimized_img_url' => $data->response->dest,
            'src_size'          => $data->response->src_size,
            'optimized_size'    => $data->response->dest_size,
            'optimized_percent' => $data->response->percent,
            'not_need_download' => false
        ];
	}

	/**
	 * Качество изображения
	 * Метод конвертирует качество из настроек плагина в формат сервиса resmush
	 *
	 * @param mixed $quality   качество
	 *
	 * @return int
	 */
	public function quality( $quality = 100 ) {
		if ( is_numeric( $quality ) ) {
			if ( $quality >= 1 && $quality <= 100 ) {
				return $quality;
			}
		}

		switch( $quality ) {
            case 'normal':
                return 90;

            case 'aggresive':
                return 75;

            case 'ultra':
                return 50;

            case 'googlepage':
                return 30;

            default:
                return 100;
        }
	}
}
