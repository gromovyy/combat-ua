<?php
/**
 * Файл запускающий работу IC-CMS
 *
 * Стартует сессию, создает обект класса сайт и запускает {@link Site::Dispatch}
 * @package IC-CMS
 * @subpackage Core
 */

 /**
 * Если существует файл установки - запускаем установку 
 */
header("Content-Type: text/html; charset=utf-8");
// Устанавливаем время по Киеву
date_default_timezone_set("Europe/Kiev");
if (!file_exists("config/config.php"))	{
	
	define('INSTALL','1');
	include ("lib/php/install.php");
	die();
}
  
/**
 * Подключает глобальные конфигурационные опции
 */
require ('config/config.php');

/**
 * Подключает магическую функцию {@link __autoload}
 */
require ('lib/php/autoloader.php');


error_reporting(E_ERROR | E_WARNING | E_PARSE);

$site = new Site();
$site->Dispatch();
?>