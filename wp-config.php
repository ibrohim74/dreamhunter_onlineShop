<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'dreamhunter' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'root' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', 'root' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '_H=w]CG-CifGaIMgc4kgwItf:|awPTQ--zNo7U[h*;&uHtn-X.>`J7o6iV%5e.L5' );
define( 'SECURE_AUTH_KEY',  'Ii`sAYm9giOj6`Bd[1hG*J]*[[D<t;b|RMEM|qL&(Djs U:lo>.nT1LoDm/Z)SEm' );
define( 'LOGGED_IN_KEY',    'FUGm-1b@~?7(*.4-evdR(uRwOh^_`)s,7CT)]^(p!)dSBE9y>gT*_Q96LO=cI0|L' );
define( 'NONCE_KEY',        '5UZ=&Q?*y8|dFnR!74HUI>,~]b46j2t>Z0Tj[Wu|w=1wTx><)O1|=oMQZ4fAU<$Y' );
define( 'AUTH_SALT',        'tJD`x>:=Wu:m[Xd]>@-^i&[`cxX(O[|Sl4ut&K}mXbuN%i0o~T[XR%//1_2uD9U3' );
define( 'SECURE_AUTH_SALT', ',]!Ywx:IoNWF+`Z-/LM@&>/Mg9.X6mZPxhioH*^PP7)LklT8fSrL#g0B]C&tL@8O' );
define( 'LOGGED_IN_SALT',   '1o~g9mBC3P[Eygs0f(kgP6TBLs;`yDlV/#1}yY,dfK5x,}Gn[Pj}[kM{-vAW8KXZ' );
define( 'NONCE_SALT',       '2Cs7^f05AEImsh~@Fj~@dlu /TiyhNv7-xhiM?n>&1v(Acq_;b[XOc[8%Oz(-$wK' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
