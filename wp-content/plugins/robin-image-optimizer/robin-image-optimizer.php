<?php
/**
 * Plugin Name: Robin image optimizer
 * Plugin URI: https://robinoptimizer.com
 * Description: Optimize images without losing quality, speed up your website load, improve SEO and save money on server and CDN bandwidth.
 * Author: Creative Motion <info@cm-wp.com>
 * Version: 1.5.8
 * Text Domain: robin-image-optimizer
 * Domain Path: /languages/
 * Author URI: https://cm-wp.com
 * Framework Version: FACTORY_458_VERSION
 * Requires at least: 4.8
 * Requires PHP: 7.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once dirname( __FILE__ ) . '/libs/factory/core/includes/class-factory-requirements.php';

// @formatter:off
$plugin_info = [
	'prefix'               => 'wbcr_io_',
	'plugin_name'          => 'wbcr_image_optimizer',
	'plugin_title'         => __( 'Robin image optimizer', 'robin-image-optimizer' ),

	// PLUGIN SUPPORT
	'support_details'      => [
		'url'       => 'https://robinoptimizer.com',
		'pages_map' => [
			'features' => 'premium-features',  // {site}/premium-features
			'pricing'  => 'pricing',           // {site}/prices
			'support'  => 'support',           // {site}/support
			'docs'     => 'docs',               // {site}/docs
		],
	],

	// PLUGIN UPDATED SETTINGS
	'has_updates'          => false,
	'updates_settings'     => [
		'repository'        => 'wordpress',
		'slug'              => 'robin-image-optimizer',
		'maybe_rollback'    => true,
		'rollback_settings' => [
			'prev_stable_version' => '0.0.0',
		],
	],

	// PLUGIN PREMIUM SETTINGS
	'has_premium'          => true,
	'license_settings'     => [
		'provider'         => 'freemius',
		'slug'             => 'robin-image-optimizer',
		'plugin_id'        => '3464',
		'public_key'       => 'pk_cafff5a51bd5fcf09c6bde806956d',
		'price'            => 39.99,
		'has_updates'      => false,
		'updates_settings' => [
			'maybe_rollback'    => true,
			'rollback_settings' => [
				'prev_stable_version' => '0.0.0',
			],
		],
	],

	// PLUGIN SUBSCRIBE FORM
	'subscribe_widget'     => true,
	'subscribe_settings'   => [ 'group_id' => '105407128' ],

	// PLUGIN ADVERTS
	'render_adverts'       => true,
	'adverts_settings'     => [
		'dashboard_widget' => true, // show dashboard widget (default: false)
		'right_sidebar'    => true, // show adverts sidebar (default: false)
		'notice'           => true, // show notice message (default: false)
	],

	// FRAMEWORK MODULES
	'load_factory_modules' => [
		[ 'libs/factory/bootstrap', 'factory_bootstrap_459', 'admin' ],
		[ 'libs/factory/forms', 'factory_forms_455', 'admin' ],
		[ 'libs/factory/pages', 'factory_pages_457', 'admin' ],
		[ 'libs/factory/templates', 'factory_templates_110', 'all' ],
		[ 'libs/factory/freemius', 'factory_freemius_145', 'all' ],
		[ 'libs/factory/adverts', 'factory_adverts_135', 'admin' ],
		[ 'libs/factory/feedback', 'factory_feedback_118', 'admin' ],
		[ 'libs/factory/logger', 'factory_logger_123', 'all' ],
		[ 'libs/factory/processing', 'factory_processing_103', 'all' ],
	],
];

$wrio_compatibility = new Wbcr_Factory458_Requirements( __FILE__, array_merge( $plugin_info, [
	'plugin_already_activate'          => defined( 'WRIO_PLUGIN_ACTIVE' ),
	'required_php_version'             => '7.0',
	'required_wp_version'              => '4.8.0',
	'required_clearfy_check_component' => false,
] ) );

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if ( ! $wrio_compatibility->check() ) {
	return;
}

/**
 * -----------------------------------------------------------------------------
 * CONSTANTS
 * Install frequently used constants and constants for debugging, which will be
 * removed after compiling the plugin.
 * -----------------------------------------------------------------------------
 */

// This plugin is activated
/**
 *
 */
define( 'WRIO_PLUGIN_ACTIVE', true );

// todo: remove after few releases. For compatibility with Clearfy
define( 'WIO_PLUGIN_ACTIVE', true );

// Plugin version
define( 'WRIO_PLUGIN_VERSION', $wrio_compatibility->get_plugin_version() );

// Директория плагина
define( 'WRIO_PLUGIN_DIR', dirname( __FILE__ ) );

// Относительный путь к плагину
define( 'WRIO_PLUGIN_BASE', plugin_basename( __FILE__ ) );

// Ссылка к директории плагина
define( 'WRIO_PLUGIN_URL', plugins_url( null, __FILE__ ) );



/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */

require_once WRIO_PLUGIN_DIR . '/libs/factory/core/boot.php';
require_once WRIO_PLUGIN_DIR . '/includes/class-rio-plugin.php';

try {
	new WRIO_Plugin( __FILE__, array_merge( $plugin_info, [
		'plugin_version'     => WRIO_PLUGIN_VERSION,
		'plugin_text_domain' => $wrio_compatibility->get_text_domain(),
	] ) );
} catch ( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define( 'WRIO_PLUGIN_THROW_ERROR', true );

	$wrio_plugin_error_func = function () use ( $e ) {
		$error = sprintf( 'The %s plugin has stopped. <b>Error:</b> %s Code: %s', 'Robin image optimizer', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $wrio_plugin_error_func );
	add_action( 'network_admin_notices', $wrio_plugin_error_func );
}
// @formatter:on
