<?php
/**
 * Plugin Name: Webcraftic Hide login page
 * Plugin URI: https://wordpress.org/plugins/hide-login-page/
 * Description: Hide wp-login.php login page and close wp-admin access to avoid hacker attacks and brute force.
 * Author: Webcraftic <wordpress.webraftic@gmail.com>
 * Version: 1.1.7
 * Text Domain: hide-login-page
 * Domain Path: /languages/
 * Author URI: http://clearfy.pro
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Developers who contributions in the development plugin:
 *
 * Alexander Kovalev
 * ---------------------------------------------------------------------------------
 * Full plugin development.
 *
 * Email:         alex.kovalevv@gmail.com
 * Personal card: https://alexkovalevv.github.io
 * Personal repo: https://github.com/alexkovalevv
 * ---------------------------------------------------------------------------------
 */

/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once(dirname(__FILE__) . '/libs/factory/core/includes/class-factory-requirements.php');

// @formatter:off
$whlp_plugin_info = array(
	'prefix' => 'wbcr_hlp_',
	'plugin_name' => 'wbcr_hide_login_page',
	'plugin_title' => __('Webcraftic Hide login page', 'hide-login-page'),

	// PLUGIN SUPPORT
	'support_details' => array(
		'url' => 'https://clearfy.pro',
		'pages_map' => array(
			'support' => 'support',           // {site}/support
			'docs' => 'docs'               // {site}/docs
		)
	),

	// PLUGIN ADVERTS
	'render_adverts' => true,
	'adverts_settings' => array(
		'dashboard_widget' => true, // show dashboard widget (default: false)
		'right_sidebar' => true, // show adverts sidebar (default: false)
		'notice' => true, // show notice message (default: false)
	),

	// FRAMEWORK MODULES
	'load_factory_modules' => array(
		array('libs/factory/bootstrap', 'factory_bootstrap_466', 'admin'),
		array('libs/factory/forms', 'factory_forms_462', 'admin'),
		array('libs/factory/pages', 'factory_pages_465', 'admin'),
		array('libs/factory/templates', 'factory_templates_115', 'all'),
		array('libs/factory/adverts', 'factory_adverts_142', 'admin')
	)
);

$whlp_compatibility = new Wbcr_Factory465_Requirements(__FILE__, array_merge($whlp_plugin_info, array(
	'plugin_already_activate' => defined('WHLP_PLUGIN_ACTIVE'),
	'required_php_version' => '5.4',
	'required_wp_version' => '4.2.0',
	'required_clearfy_check_component' => false
)));

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if( !$whlp_compatibility->check() ) {
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
define('WHLP_PLUGIN_ACTIVE', true);
define('WHLP_PLUGIN_VERSION', $whlp_compatibility->get_plugin_version());
define('WHLP_PLUGIN_DIR', dirname(__FILE__));
define('WHLP_PLUGIN_BASE', plugin_basename(__FILE__));
define('WHLP_PLUGIN_URL', plugins_url(null, __FILE__));



/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */

require_once(WHLP_PLUGIN_DIR . '/libs/factory/core/boot.php');
require_once(WHLP_PLUGIN_DIR . '/includes/class-plugin.php');

try {
	new WHLP_Plugin(__FILE__, array_merge($whlp_plugin_info, array(
		'plugin_version' => WHLP_PLUGIN_VERSION,
		'plugin_text_domain' => $whlp_compatibility->get_text_domain(),
	)));
} catch( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define('WHLP_PLUGIN_THROW_ERROR', true);

	$whlp_plugin_error_func = function () use ($e) {
		$error = sprintf("The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Webcraftic Hide Login Page', $e->getMessage(), $e->getCode());
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action('admin_notices', $whlp_plugin_error_func);
	add_action('network_admin_notices', $whlp_plugin_error_func);
}

/*
if(defined('WTITAN_PLUGIN_ACTIVE')){
	require_once( WHLP_PLUGIN_DIR . '/includes/3rd-party/class-titan-plugin.php' );
}

if(class_exists( 'WBCR\Titan\Plugin')) {
	try {
		//define('LOADING_HIDE_LOGIN_PAGE_AS_ADDON', true);
		new WHLP_Titan_Plugin( WBCR\Titan\Plugin::app() );
	} catch ( Exception $e ) {
		$whlp_plugin_error_func = function () use ( $e )
		{
			$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Webcraftic Hide Login Page', $e->getMessage(), $e->getCode() );
			echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
		};

		add_action( 'admin_notices', $whlp_plugin_error_func );
		add_action( 'network_admin_notices', $whlp_plugin_error_func );
	}
}
*/
// @formatter:on
