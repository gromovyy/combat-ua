<?php
/**
 * Файл с глобальными конфигурациями
 * @package IC-CMS
 * @subpackage Core
 * @subpackage Core
 */

/**
 * Имя сайта (title)
 * @global string $GL_SITE_NAME = "TimeTracker";
 * @name $GL_SITE_NAME = "TimeTracker";
 */
$GL_SITE_NAME = "TimeTracker";

/**
 * Язык сайта
 * @global string $GL_LANG = "ru";
 * @name $GL_LANG = "ru";
 */
$GL_LANG = 'ua';

/**
 * E-mail администратора сайта
 * @global string $GL_SITE_NAME = "TimeTracker";
 * @name $GL_SITE_NAME = "TimeTracker";
 */
$GL_EMAIL = "admin@admin.com";


/**
 * Хост БД
 * @global string $GL_HOST = "localhost";
 * @name $GL_HOST = "localhost";
 */
$GL_HOST = "localhost";

/**
 * Имя БД
 * @global string $GL_DB_NAME = "timetracker";
 * @name $GL_DB_NAME = "timetracker";
 */
$GL_DB_NAME = "combat_ua";

/**
 * Пользователь БД
 * @global string $GL_DB_USER = "root";
 * @name $GL_DB_USER = "root";
 */
$GL_DB_USER = "root";

/**
 * Пароль БД
 * @global string $GL_DB_PASSW = "";
 * @name $GL_DB_PASSW = "";
 */
$GL_DB_PASSW = "";

/**
 *
 * @global string $GL_SITE_DIR = "Z:\home\localhost\www\timetracker";
 * @name $GL_SITE_DIR = "Z:\home\localhost\www\timetracker";
 */
$GL_SITE_DIR = 'tracker';

/**
 * Количество сообщений на странице
 * @global int $GL_CONSOLE_MESSAGE_ON_PAGE
 * @name $GL_CONSOLE_MESSAGE_ON_PAGE
 */
$GL_CONSOLE_MESSAGE_ON_PAGE = 50;

///**
// * Масив Регестрации голбальных событий.
// *
// * Глобальные события - те которые могут вызываться из под любого компонента, хотя на самом деле
// * реализуються в одном
// * @global array $GL_GLOBAL_DISPATCH
// * @name $GL_GLOBAL_DISPATCH
// */
//$GL_GLOBAL_DISPATCH = array( 'test' => 'Tester');

// Установка глобальной директории для работы с файлами изображений

$GL_PHOTO_FOLDER = "files/photo";
$GL_ATTACHMENT_FOLDER = "files/attachment";

?>