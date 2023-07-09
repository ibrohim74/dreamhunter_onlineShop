<?php
/**
 * Admin boot
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright Webcraftic 25.05.2017
 * @version 1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * @return array
 */
function wbcr_hlp_install_conflict_plugins()
{
	// fist array item 0 - conflict
	// fist array item 1 - maybe conflict
	$install_plugins = array();

	if( is_plugin_active('hide-my-wp/index.php') ) {
		$install_plugins[] = array(0, 'Hide My WP');
	}
	if( is_plugin_active('clearfy/hide-my-wp.php') ) {
		$install_plugins[] = array(1, 'Hide My WP');
	}
	if( is_plugin_active('rename-wp-login/rename-wp-login.php') ) {
		$install_plugins[] = array(0, 'Rename wp-login.php');
	}
	if( is_plugin_active('wps-hide-login/wps-hide-login.php') ) {
		$install_plugins[] = array(0, 'WPS Hide Login');
	}
	if( is_plugin_active('wp-cerber/wp-cerber.php') ) {
		$install_plugins[] = array(1, 'WP Cerber Security & Antispam');
	}
	if( is_plugin_active('all-in-one-wp-security-and-firewall/wp-security.php') ) {
		$install_plugins[] = array(1, 'All In One WP Security');
	}
	if( is_plugin_active('wp-hide-security-enhancer/wp-hide.php') ) {
		$install_plugins[] = array(1, 'WP Hide & Security Enhancer');
	}

	return $install_plugins;
}

/**
 *
 * @param Wbcr_Factory465_Plugin $plugin
 * @param Wbcr_FactoryPages465_ImpressiveThemplate $page
 *
 */
function wbcr_hlp_get_conflict_notices_error($plugin, $page)
{
	if( !is_admin() ) {
		return;
	}
	$pages = array("hlp_hide_login", "defence");

	if( $plugin->getPluginName() == WHLP_Plugin::app()->getPluginName() && in_array($page->id, $pages) ) {

		$install_conflict_plugins = wbcr_hlp_install_conflict_plugins();

		if( !empty($install_conflict_plugins) ) {
			foreach((array)$install_conflict_plugins as $plugin) {
				if( sizeof($plugin) == 2 ) {
					if( $plugin[0] === 0 ) {
						$page->printWarningNotice(sprintf(__("We found that you are use the (%s) plugin to change wp-login.php page address. Please delete it, because Clearfy already contains these functions and you do not need to use two plugins. If you do not want to remove (%s) plugin for some reason, please do not use wp-login.php page address change feature in the Clearfy plugin, to avoid conflicts.", 'hide-login-page'), $plugin[1], $plugin[1]));
					} else {
						$page->printWarningNotice(sprintf(__("We found that you are use the (%s) plugin. Please do not use its wp-login.php page address change and the same feature in the Clearfy plugin, to avoid conflicts.", 'hide-login-page'), $plugin[1], $plugin[1]));
					}
				}
			}
		}
	}
}

add_filter('wbcr/factory/pages/impressive/print_all_notices', 'wbcr_hlp_get_conflict_notices_error', 10, 2);

/**
 * Виджет отзывов
 *
 * @param string $page_url
 * @param string $plugin_name
 * @return string
 */
function wbcr_hlp_rating_widget_url($page_url, $plugin_name)
{
	if( $plugin_name == WHLP_Plugin::app()->getPluginName() ) {
		return 'https://goo.gl/ecaj2V';
	}

	return $page_url;
}

add_filter('wbcr_factory_pages_465_imppage_rating_widget_url', 'wbcr_hlp_rating_widget_url', 10, 2);

function wbcr_hlp_group_options($options)
{
	$options[] = array(
		'name' => 'hide_wp_admin',
		'title' => __('Hide wp-admin', 'hide-login-page'),
		'tags' => array()
	);
	$options[] = array(
		'name' => 'hide_login_path',
		'title' => __('Hide Login Page', 'hide-login-page'),
		'tags' => array()
	);
	$options[] = array(
		'name' => 'login_path',
		'title' => __('New login page', 'hide-login-page'),
		'tags' => array()
	);

	return $options;
}

add_filter("wbcr_clearfy_group_options", 'wbcr_hlp_group_options');

/**
 * It is not possible to create a page with the same slugs as the login page
 *
 * @param string $slug
 * @param int $post_ID
 * @param string $post_status
 * @param string $post_type
 * @return string
 */
function wbcr_hlp_login_path_noconflict($slug, $post_ID, $post_status, $post_type)
{
	if( in_array($post_type, array('post', 'page', 'attachment')) ) {
		$login_path = WHLP_Plugin::app()->getOption('login_path');

		if( !empty($login_path) ) {
			if( $slug == trim($login_path) ) {
				$slug = $slug . rand(10, 99);
			}
		}
	}

	return $slug;
}

add_filter('wp_unique_post_slug', 'wbcr_hlp_login_path_noconflict', 10, 4);


