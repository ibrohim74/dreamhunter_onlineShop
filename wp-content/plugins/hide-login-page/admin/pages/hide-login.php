<?php

/**
 * The page Settings.
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WHLP_HideLoginPage extends WBCR\Factory_Templates_115\Pages\PageBase {

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 * @see FactoryPages465_AdminPage
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $id = "hlp_hide_login";

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-testimonial';

	/**
	 * @var bool
	 */
	public $available_for_multisite = true;

	/**
	 * @var bool
	 */
	public $clearfy_collaboration = false;

	/**
	 * @var bool
	 */
	public $show_right_sidebar_in_options = false;

	/**
	 * Заголовок страницы, также использует в меню, как название закладки
	 *
	 * @var bool
	 */
	public $show_page_title = true;

	/**
	 * @var string
	 */
	public $current_plugin = '';

	/**
	 * @param Wbcr_Factory465_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->menu_title                  = __( 'Hide Login Page', 'hide-login-page' );
		$this->page_title                  = __( 'Hide Login Page', 'hide-login-page' );
		$this->page_menu_short_description = __( 'Login security', 'hide-login-page' );
				
		$this->current_plugin              = $this->detect_plugin();
		if ( 'titan' == $this->current_plugin['plugin'] ) {
			$this->page_parent_page = 'tweaks';
		}

		if ( is_multisite() && defined( 'WBCR_CLEARFY_PLUGIN_ACTIVE' ) ) {
			$clearfy_is_active_for_network = is_plugin_active_for_network( WCL_Plugin::app()->get_paths()->basename );

			if ( WHLP_Plugin::app()->isNetworkActive() && $clearfy_is_active_for_network ) {
				$this->clearfy_collaboration = true;
			}
		} else if ( defined( 'WBCR_CLEARFY_PLUGIN_ACTIVE' ) ) {
			$this->clearfy_collaboration = true;
		}

		//if ( '' == $this->current_plugin['plugin'] ) {
			$this->internal                   = false;
			$this->menu_target                = 'options-general.php';
			$this->add_link_to_plugin_actions = true;
		//}

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	public function getPluginTitle() {
		if ( 'titan' == $this->current_plugin['plugin'] ) { //Titan
			return "<span class='wt-plugin-header-logo'>&nbsp;</span>" . __( 'Titan Anti-spam & Security', 'titan-security' );
		} else if ( '' == $this->current_plugin['plugin'] ) { //Hide login
			return __( 'Webcraftic Hide Login Page', 'hide-login-page' );
		}
		else { //Clearfy
			return parent::getPluginTitle();
		}
	}

	/**
	 * Подменяем простраинство имен для меню плагина, если активирован плагин Clearfy
	 * Меню текущего плагина будет добавлено в общее меню Clearfy
	 * @return string
	 */
	public function getMenuScope() {
		if ( 'titan' == $this->current_plugin['plugin'] ) {
			return $this->current_plugin['plugin_name'];
		}

		if ( 'clearfy' == $this->current_plugin['plugin'] ) {
			if ( $this->clearfy_collaboration ) {
				$this->internal         = true;
				$this->page_parent_page = 'defence';

				return $this->current_plugin['plugin_name'];
			}
		}
		
		// Если пользователь переходит из общего меню, то попадает сначала в Clearfy
		if(class_exists( 'WCL_Plugin' )) {
			return WCL_Plugin::app()->getPluginName();
		}
		
		// Если пользователь переходит из общего меню, то попадает сначала в Titan
		if ( class_exists( '\WBCR\Titan\Plugin' ) ) {
			return \WBCR\Titan\Plugin::app()->getPluginName();
		}
		
		return $this->plugin->getPluginName();
	}

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @see Wbcr_FactoryPages465_AdminPage
	 *
	 */
	public function assets( $scripts, $styles ) {
		parent::assets( $scripts, $styles );

		if ( 'titan' == $this->current_plugin['plugin'] ) {
			$this->styles->add( WTITAN_PLUGIN_URL . '/admin/assets/css/titan-security.css' );
			$this->scripts->add( WTITAN_PLUGIN_URL . '/admin/assets/js/titan-security.js' );
		}
		// Add Clearfy styles for HMWP pages
		if ( defined( 'WBCR_CLEARFY_PLUGIN_ACTIVE' ) && 'clearfy' == $this->current_plugin['plugin'] ) {
			$this->styles->add( WCL_PLUGIN_URL . '/admin/assets/css/general.css' );
		}

		add_filter( 'wbcr/factory/pages/impressive/actions_notice', array( $this, 'actionNotices' ) );
	}

	/**
	 * Определяем плагин, на странице которого мы находимся.
	 *
	 * @return array
	 */
	protected function detect_plugin() {
		$pattern = '/[A-z0-9_-]+%s$/i';
		if ( class_exists( 'WCL_Plugin' ) ) {
			$clearfy_plugin_name = WCL_Plugin::app()->getPluginName();
			$clearfy_pattern     = sprintf( $pattern, $clearfy_plugin_name );

			if ( ! empty( $_GET['page'] ) && preg_match( $clearfy_pattern, $_GET['page'] ) ) {
				return [ 'plugin' => 'clearfy', 'plugin_name' => $clearfy_plugin_name ];
			}
		}

		if ( class_exists( '\WBCR\Titan\Plugin' ) ) {
			$titan_plugin_name = \WBCR\Titan\Plugin::app()->getPluginName();

			if ( ! empty( $_GET['page'] ) && preg_match( sprintf( $pattern, $titan_plugin_name ), $_GET['page'] ) ) {
				return [ 'plugin' => 'titan', 'plugin_name' => $titan_plugin_name ];
			}
		}

		return [ 'plugin' => '', 'plugin_name' => '' ];
	}

	/**
	 * We register notifications for some actions
	 *
	 * @param $notices
	 * @param Wbcr_Factory465_Plugin $plugin
	 *
	 * @return array
	 * @see libs\factory\pages\themplates\FactoryPages465_ImpressiveThemplate
	 */
	public function actionNotices( $notices ) {

		$notices[] = array(
			'conditions' => array(
				'wbcr_hlp_login_path_incorrect' => 1,
			),
			'type'       => 'danger',
			'message'    => __( 'You entered an incorrect part of the path to your login page. The path to the login page can not consist only of digits, at least 3 characters, you must use only the characters [0-9A-z_-]!', 'hide-login-page' )
		);
		$notices[] = array(
			'conditions' => array(
				'wbcr_hlp_login_path_exists' => 1,
			),
			'type'       => 'danger',
			'message'    => __( 'The entered login page name is already used for one of your pages. Try to choose a different login page name!', 'hide-login-page' )
		);

		return $notices;
	}

	/**
	 * Permalinks options.
	 *
	 * @return mixed[]
	 * @since 1.0.0
	 */
	public function getPageOptions() {
		$options = array();

		$options[] = array(
			'type' => 'html',
			'html' => '<div class="wbcr-factory-page-group-header">' . __( '<strong>Protect your admin login</strong>.', 'hide-login-page' ) . '<p>' . __( 'Dozens of bots attack your login page at /wp-login.php and /wp-admin/daily. Bruteforce and want to access your admin panel. Even if you\'re sure that you have created a complex and reliable password, this does not guarantee security and does not relieve your login page load. The easiest way is to protect the login page by simply changing its address to your own and unique.', 'hide-login-page' ) . '</p></div>'
		);

		$eventsOff = array();

		if ( ! WHLP_Plugin::app()->getPopulateOption( 'hide_wp_admin' ) && ! WHLP_Plugin::app()->getPopulateOption( 'hide_login_path' ) ) {
			$eventsOff = array(
				'hide' => '.factory-control-wpadmin_and_login_access_error',
			);
		}

		$options[] = array(
			'type'      => 'checkbox',
			'way'       => 'buttons',
			'name'      => 'hide_wp_admin',
			'title'     => __( 'Hide wp-admin', 'hide-login-page' ),
			'layout'    => array( 'hint-type' => 'icon', 'hint-icon-color' => 'grey' ),
			'hint'      => __( "Hides the /wp-admin directory for unauthorized users. If the option is disabled, when you request the page /wp-admin you will be redirected to the login page, even if you changed its address. Therefore, for protection purposes enable this option.", 'hide-login-page' ),
			'eventsOn'  => array(
				'show' => '.factory-control-wpadmin_and_login_access_error',
			),
			'eventsOff' => $eventsOff
		);

		$options[] = array(
			'type'      => 'checkbox',
			'way'       => 'buttons',
			'name'      => 'hide_login_path',
			'title'     => __( 'Hide Login Page', 'hide-login-page' ),
			'layout'    => array( 'hint-type' => 'icon', 'hint-icon-color' => 'red' ),
			'hint'      => __( "Hides the wp-login.php and wp-signup.php pages.", 'hide-login-page' ) . '<br>--<br><span class="wbcr-factory-light-orange-color">' . __( "Use this option carefully! If you forget the new login page address, you can not get into the admin panel.", 'hide-login-page' ) . '</span>',
			'eventsOn'  => array(
				'show' => '.factory-control-wpadmin_and_login_access_error',
			),
			'eventsOff' => $eventsOff
		);

		$options[] = array(
			'type'    => 'dropdown',
			'way'     => 'buttons',
			'name'    => 'wpadmin_and_login_access_error',
			'title'   => __( 'Access error type', 'hide-login-page' ),
			'data'    => array(
				array( '404', __( 'Page 404', 'hide-login-page' ) ),
				array( 'redirect_to', __( 'Redirect to', 'hide-login-page' ) ),
				array( 'forbidden', __( 'Forbidden 403', 'hide-login-page' ) )
			),
			'layout'  => array( 'hint-type' => 'icon', 'hint-icon-color' => 'grey' ),
			'hint'    => __( "Some Wordpress themes do not have page templates 404, if you see a php error (blank screen, error 500) when accessing the wp-admin page, wp-login.php, try set forbidden 403 error or set  a redirect to your custom page 404.", 'hide-login-page' ),
			'default' => '404',
			'events'  => array(
				'404'         => array(
					'hide' => '.factory-control-wpadmin_and_login_access_redirect'
				),
				'redirect_to' => array(
					'show' => '.factory-control-wpadmin_and_login_access_redirect'
				),
				'forbidden'   => array(
					'hide' => '.factory-control-wpadmin_and_login_access_redirect'
				)
			)
		);

		$options[] = array(
			'type'    => 'textbox',
			'name'    => 'wpadmin_and_login_access_redirect',
			'title'   => __( 'Set custom page 404 url', 'hide-login-page' ),
			'hint'    => __( 'When you try to get to the hidden page wp-admin or wp-login.php, you will be redirected by the link you have set. Redirect will work only if you have chosen the required option above!', 'hide-login-page' ),
			'layout'  => array( 'hint-type' => 'icon', 'hint-icon-color' => 'grey' ),
			'default' => site_url() . '/404'
		);

		$recovery_url  = $this->getRecoveryUrl();
		$recovery_url  = ! empty( $recovery_url ) ? '<br><br>' . sprintf( __( "If unable to access the login/admin section anymore, use the Recovery Link which reset links to default: \n%s", 'hide-login-page' ), $recovery_url ) : '';
		$new_login_url = $this->getNewLoginUrl();

		$options[] = array(
			'type'        => 'textbox',
			'name'        => 'login_path',
			'placeholder' => 'secure/auth.php',
			'title'       => __( 'New login page', 'hide-login-page' ),
			'hint'        => __( 'Set a new login page name without slash. Example: mysecretlogin', 'hide-login-page' ) . '<br><span style="color:red">' . __( "IMPORTANT! Be sure that you wrote down the new login page address", 'hide-login-page' ) . '</span>: <b><a href="' . $new_login_url . '" target="_blank">' . $new_login_url . '</a></b>' . $recovery_url,
			//'units' => '<i class="fa fa-unlock" title="' . __('This option will protect your blog against unauthorized access.', 'hide-login-page') . '"></i>',
			//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'red')
		);

		$formOptions = array();

		$formOptions[] = array(
			'type'  => 'form-group',
			'items' => $options,
			//'cssClass' => 'postbox'
		);

		return apply_filters( 'wbcr_hlp_general_form_options', $formOptions, $this );
	}

	/**
	 * Вызывается после сохранением опций формы, когда выполнен сброс кеша и совершен редирект
	 *
	 * @return void
	 * @since 4.0.0
	 */
	protected function afterFormSave() {
		$pages = array( "hlp_hide_login", "defence" );

		if ( $this->plugin->getPluginName() == WHLP_Plugin::app()->getPluginName() && in_array( $this->id, $pages ) ) {

			$login_path = WHLP_Plugin::app()->getPopulateOption( 'login_path' );
			$valid_path = ! is_numeric( $login_path ) && preg_match( '/^[0-9A-z_-]{3,}$/', $login_path );

			if ( ! empty( $login_path ) ) {
				if ( ! $valid_path ) {
					WHLP_Plugin::app()->deletePopulateOption( 'login_path' );
					WHLP_Plugin::app()->deletePopulateOption( 'hide_login_path' );

					$this->redirectToAction( 'index', array( 'wbcr_hlp_login_path_incorrect' => 1 ) );
				}

				$login_path_exists = false;

				if ( WHLP_Plugin::app()->isNetworkActive() ) {
					foreach ( WHLP_Plugin::app()->getActiveSites() as $site ) {

						switch_to_blog( $site->blog_id );

						if ( $this->loginPathExists( $login_path ) ) {
							$login_path_exists = true;
						}

						restore_current_blog();
					}
				} else {
					$login_path_exists = $this->loginPathExists( $login_path );
				}

				if ( ! empty( $login_path_exists ) ) {
					WHLP_Plugin::app()->deletePopulateOption( 'login_path' );
					WHLP_Plugin::app()->deletePopulateOption( 'hide_login_path' );

					$this->redirectToAction( 'index', array( 'wbcr_hlp_login_path_exists' => 1 ) );
				}

				$old_login_path = WHLP_Plugin::app()->getPopulateOption( 'old_login_path' );

				if ( ! $old_login_path || $login_path != $old_login_path ) {

					$recovery_code = md5( rand( 1, 9999 ) . microtime() );

					$body = __( "Hi,\nThis is %s plugin. Here is your new WordPress login address:\nURL: %s", 'hide-login-page' ) . PHP_EOL . PHP_EOL;
					$body .= __( "IMPORTANT! Be sure that you wrote down the new login page address", 'hide-login-page' ) . '!' . PHP_EOL . PHP_EOL;
					$body .= __( "If unable to access the login/admin section anymore, use the Recovery Link which reset links to default: \n%s", 'hide-login-page' ) . PHP_EOL . PHP_EOL;
					$body .= __( "Best Regards,\n%s", 'hide-login-page' ) . PHP_EOL;

					$new_url = site_url( 'wp-login.php' );

					$body = sprintf( $body, WHLP_Plugin::app()->getPluginTitle(), $new_url, $this->getRecoveryUrl( $recovery_code ), WHLP_Plugin::app()->getPluginTitle() ) . PHP_EOL;

					$subject = sprintf( __( '[%s] Your New WP Login!', 'hide-login-page' ), WHLP_Plugin::app()->getPluginTitle() );

					$admin_email = WHLP_Plugin::app()->isNetworkActive() ? get_site_option( 'admin_email' ) : get_option( 'admin_email' );

					wp_mail( $admin_email, $subject, $body );

					WHLP_Plugin::app()->updatePopulateOption( 'old_login_path', $login_path );
					WHLP_Plugin::app()->updatePopulateOption( 'login_recovery_code', $recovery_code );
				}
			} else {

				// if new login path is empty
				WHLP_Plugin::app()->deletePopulateOption( 'old_login_path' );
				WHLP_Plugin::app()->deletePopulateOption( 'login_recovery_code' );
			}
		}
	}

	protected function loginPathExists( $login_path ) {
		$args = array(
			'name'        => $login_path,
			'post_type'   => array( 'page', 'post' ),
			'numberposts' => 1
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the new address of the login page
	 *
	 * @return string
	 */

	protected function getNewLoginUrl() {
		$login_path = WHLP_Plugin::app()->getPopulateOption( 'login_path' );

		if ( empty( $login_path ) ) {
			return site_url( 'wp-login.php' );
		}

		if ( \WBCR\Factory_Templates_115\Helpers::isPermalink() ) {
			return \WBCR\Factory_Templates_115\Helpers::userTrailingslashit( home_url( '/' ) . $login_path );
		} else {
			return add_query_arg( $login_path, null, site_url() );
		}
	}

	/**
	 * Allows you to get a link to reset settings
	 *
	 * @param string|null $recovery_code
	 *
	 * @return string
	 */
	protected function getRecoveryUrl( $recovery_code = null ) {
		$recovery_code = empty( $recovery_code ) ? WHLP_Plugin::app()->getPopulateOption( 'login_recovery_code' ) : $recovery_code;

		if ( empty( $recovery_code ) ) {
			return '';
		}

		return add_query_arg( 'wbcr_hlp_login_recovery', $recovery_code, site_url() );
	}
}