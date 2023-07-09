<?php
/**
 * Hide login page core class
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 19.02.2018, Webcraftic
 * @version 1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WHLP_Plugin extends Wbcr_Factory465_Plugin {

	/**
	 * @var Wbcr_Factory465_Plugin
	 */
	private static $app;

	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array $data
	 *
	 * @throws Exception
	 */
	public function __construct($plugin_path, $data)
	{
		parent::__construct($plugin_path, $data);
		self::$app = $this;

		$this->global_scripts();

		add_action('plugins_loaded', [$this, 'plugins_loaded']);
	}

	/**
	 * @return Wbcr_Factory465_Plugin
	 */
	public static function app()
	{
		return self::$app;
	}

	/**
	 * @throws \Exception
	 */
	public function plugins_loaded()
	{

		if( is_admin() ) {
			require_once(WHLP_PLUGIN_DIR . '/admin/boot.php');

			$this->register_pages();
		}
	}


	/**
	 * Регистрирует классы страниц в плагине
	 *
	 * Мы указываем плагину, где найти файлы страниц и какое имя у их класса. Чтобы плагин
	 * выполнил подключение классов страниц. После регистрации, страницы будут доступные по url
	 * и в меню боковой панели администратора. Регистрируемые страницы будут связаны с текущим плагином
	 * все операции выполняемые внутри классов страниц, имеют отношение только текущему плагину.
	 *
	 * @throws \Exception
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	private function register_pages()
	{
		self::app()->registerPage('WHLP_HideLoginPage', WHLP_PLUGIN_DIR . '/admin/pages/hide-login.php');
		self::app()->registerPage('WHLP_MoreFeaturesPage', WHLP_PLUGIN_DIR . '/admin/pages/more-features.php');
	}

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.1.0
	 */
	private function global_scripts()
	{
		require_once(WHLP_PLUGIN_DIR . '/includes/classes/class.configurate-hide-login-page.php');
		new WHLP_ConfigHideLoginPage(self::app());
	}
}

