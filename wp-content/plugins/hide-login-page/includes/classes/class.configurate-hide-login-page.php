<?php
/**
 * This class configures hide login page
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 2017 Webraftic Ltd
 * @version 1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WHLP_ConfigHideLoginPage {

	/**
	 * @var bool
	 */
	private $wp_login_php;

	/**
	 * @var bool
	 */
	private $disable_wp_admin;

	/**
	 * @var bool
	 */
	private $disable_wp_login;

	/**
	 * @var string
	 */
	private $login_path;

	/**
	 * @param Wbcr_Factory465_Plugin $plugin
	 */
	public function __construct($plugin)
	{
		$this->plugin = $plugin;
		$this->registerActionsAndFilters();
	}


	public function registerActionsAndFilters()
	{
		$this->disable_wp_admin = WHLP_Plugin::app()->getPopulateOption('hide_wp_admin');
		$this->disable_wp_login = WHLP_Plugin::app()->getPopulateOption('hide_login_path');
		$this->login_path = WHLP_Plugin::app()->getPopulateOption('login_path');

		add_filter('init', array($this, 'init'));

		if( $this->login_path ) {
			add_action('plugins_loaded', array($this, 'pluginsLoaded'), 9999);
			add_action('wp_loaded', array($this, 'wpLoaded'));

			add_filter('network_site_url', array($this, 'siteUrl'), 10, 3);
			add_filter('site_url', array($this, 'siteUrl'), 10, 3);

			add_filter('wp_redirect', array($this, 'wpRedirect'), 10, 2);
			add_filter('site_option_welcome_email', array($this, 'welcomeEmail'));
			add_action('template_redirect', array($this, 'redirectPageEmailNotifWoocommerce'));
			add_action('setup_theme', array($this, 'setupTheme'), 1);
		}
	}

	/**
	 * Checks that the requested page is wp-admin when this page is hide and when login page is changed
	 *
	 * @return bool
	 */
	private function isAdminWhenThisIsBlocked()
	{
		return $this->disable_wp_admin && $this->disable_wp_login && $this->login_path && \WBCR\Factory_Templates_115\Helpers::strContains(rawurldecode($_SERVER['REQUEST_URI']), '/wp-admin/');
	}

	public function init()
	{
		if( $this->disable_wp_admin ) {
			remove_action('template_redirect', 'wp_redirect_admin_locations', 9999);
		}

		//check for recovery link run
		if( !empty($this->login_path) && isset($_GET['wbcr_hlp_login_recovery']) ) {
			$user_recovery_code = sanitize_text_field($_GET['wbcr_hlp_login_recovery']);
			$plugin_recovery_code = $this->plugin->getPopulateOption('login_recovery_code');

			if( empty($plugin_recovery_code) || empty($user_recovery_code) || $user_recovery_code !== $plugin_recovery_code ) {
				return;
			}

			$this->plugin->deletePopulateOption('hide_wp_admin');
			$this->plugin->deletePopulateOption('login_path');
			$this->plugin->deletePopulateOption('hide_login_path');
			$this->plugin->deletePopulateOption('old_login_path');
			$this->plugin->deletePopulateOption('login_recovery_code');

			$this->login_path = null;
			$this->disable_wp_login = null;
			$this->disable_wp_admin = null;
			$this->wp_login_php = false;

			wp_safe_redirect(admin_url());
			exit;
		}
	}

	public function pluginsLoaded()
	{
		global $pagenow;

		$request = parse_url($_SERVER['REQUEST_URI']);

		$denied_slugs = array('wp-login', 'login', 'wp-activate', 'wp-register');

		if( !is_multisite() ) {
			$denied_slugs[] = 'wp-signup';
		}

		$denied_slugs_to_regex = implode('|', $denied_slugs);

		$is_wp_login = preg_match('#^\/(' . $denied_slugs_to_regex . ')(\.php)?$#i', untrailingslashit($request['path']));

		if( $is_wp_login && !is_admin() ) {
			$this->wp_login_php = true;
			$pagenow = 'index.php';
		} elseif( (untrailingslashit($request['path']) === home_url($this->login_path, 'relative')) || (!get_option('permalink_structure') && isset($_GET[$this->login_path]) && empty($_GET[$this->login_path])) ) {
			$pagenow = 'wp-login.php';
		}
	}

	public function wpLoaded()
	{
		global $pagenow;

		if( is_admin() && !is_user_logged_in() && !defined('DOING_AJAX') && $pagenow !== 'admin-post.php' ) {
			$ddisable_wp_admin = WHLP_Plugin::app()->getPopulateOption('hide_wp_admin');

			if( !$ddisable_wp_admin ) {
				$redirect_uri = untrailingslashit(home_url($this->login_path));

				if( !get_option('permalink_structure') ) {
					$redirect_uri = add_query_arg(array(
						$this->login_path => ''
					), home_url());
				}

				wp_safe_redirect($redirect_uri);
				die();
			}

			$this->setAccessError();
		}

		$request = parse_url($_SERVER['REQUEST_URI']);

		if( $pagenow === 'wp-login.php' && $request['path'] !== \WBCR\Factory_Templates_115\Helpers::userTrailingslashit($request['path']) && get_option('permalink_structure') ) {
			$query_string = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

			wp_safe_redirect(\WBCR\Factory_Templates_115\Helpers::userTrailingslashit($this->login_path) . $query_string);
			die();
		} elseif( $this->wp_login_php ) {
			$new_login_redirect = false;
			$referer = wp_get_referer();
			$parse_referer = parse_url($referer);

			if( $referer && \WBCR\Factory_Templates_115\Helpers::strContains($referer, 'wp-activate.php') && $parse_referer && !empty($parse_referer['query']) ) {

				parse_str($parse_referer['query'], $parse_referer);

				if( !empty($parse_referer['key']) && ($result = wpmu_activate_signup($parse_referer['key'])) && is_wp_error($result) && ($result->get_error_code() === 'already_active' || $result->get_error_code() === 'blog_taken') ) {
					$new_login_redirect = true;
				}
			}

			if( !$this->disable_wp_login || $new_login_redirect ) {
				$query_string = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

				if( \WBCR\Factory_Templates_115\Helpers::isPermalink() ) {
					$redirect_uri = $this->login_path . $query_string;
				} else {
					$redirect_uri = home_url() . '/' . add_query_arg(array(
							$this->login_path => ''
						), $query_string);
				}

				if( \WBCR\Factory_Templates_115\Helpers::strContains($_SERVER['REQUEST_URI'], 'wp-signup') ) {
					$redirect_uri = add_query_arg(array(
						'action' => 'register'
					), $redirect_uri);
				}

				wp_safe_redirect($redirect_uri);
				die();
			}

			$this->setAccessError();
		} elseif( $pagenow === 'wp-login.php' ) {
			if( is_user_logged_in() && !isset($_REQUEST['action']) ) {
				wp_safe_redirect(admin_url());
				die();
			}

			if( !defined('DONOTCACHEPAGE') ) {
				define('DONOTCACHEPAGE', true);
			}

			@require_once ABSPATH . 'wp-login.php';

			die();
		}
	}

	public function setAccessError()
	{
		if( $this->plugin->getPopulateOption('wpadmin_and_login_access_error') == "redirect_to" && $this->isAdminWhenThisIsBlocked() ) {
			$access_redirect_to = $this->plugin->getPopulateOption('wpadmin_and_login_access_redirect', site_url() . '/404');
			wp_safe_redirect($access_redirect_to);
			die();
		} else if( $this->plugin->getPopulateOption('wpadmin_and_login_access_error') == "forbidden" && function_exists('status_header') ) {
			status_header('403');
			nocache_headers();
			die();
		} else {
			\WBCR\Factory_Templates_115\Helpers::setError404();
		}
	}

	public function siteUrl($url, $path, $scheme)
	{
		return $this->filterWpLoginPhp($url, $scheme);
	}

	public function wpRedirect($location, $status)
	{
		return $this->filterWpLoginPhp($location);
	}

	public function filterWpLoginPhp($url, $scheme = null)
	{
		if( strpos($url, 'wp-login.php') !== false ) {
			if( is_ssl() ) {
				$scheme = 'https';
			}

			$args = explode('?', $url);

			if( isset($args[1]) ) {
				parse_str($args[1], $args);
				$url = add_query_arg($args, $this->newLoginUrl($scheme));
			} else {
				$url = $this->newLoginUrl($scheme);
			}
		}

		return $url;
	}

	public function welcomeEmail($value)
	{
		return $value = str_replace('wp-login.php', \WBCR\Factory_Templates_115\Helpers::userTrailingslashit($this->login_path), $value);
	}

	public function newLoginUrl($scheme = null)
	{
		if( \WBCR\Factory_Templates_115\Helpers::isPermalink() ) {
			return \WBCR\Factory_Templates_115\Helpers::userTrailingslashit(home_url('/', $scheme) . $this->login_path);
		} else {
			return home_url('/', $scheme) . '?' . $this->login_path;
		}
	}

	/**
	 * Update redirect for Woocommerce email notification
	 */
	public function redirectPageEmailNotifWoocommerce()
	{
		if( !class_exists('WC_Form_Handler') ) {
			return false;
		}

		if( !empty($_GET) && isset($_GET['action']) && 'rp' === $_GET['action'] && isset($_GET['key']) && isset($_GET['login']) ) {
			wp_redirect($this->newLoginUrl());
			exit();
		}
	}

	public function setupTheme()
	{
		global $pagenow;

		if( !is_user_logged_in() && 'customize.php' === $pagenow ) {
			wp_die(__('This has been disabled', 'hide-login-page'), 403);
		}
	}
}