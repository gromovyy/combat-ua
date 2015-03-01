<?php
/**
 * Файл содержащий магическую функцию __autoload
 * @package IC-CMS
 * @subpackage Core
 */

/**
 * Подключает (include) требуемый класс при первом обращении к нему
 * @param string $class_name Имя подключаемого класса
 */
function __autoload($class_name)
{
	if (file_exists("cmp_system/" . $class_name . "/" . $class_name . ".php"))
		{include ("cmp_system/" . $class_name . "/" . $class_name . ".php"); return true;}
	if (file_exists("cmp/" . $class_name . "/" . $class_name . ".php"))
		{include ("cmp/" . $class_name . "/" . $class_name . ".php");	return true;}
		
	//	Загрузка класов в соответствии со стандартом PSR-0 	
		$className = ltrim($class_name, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		if (file_exists('lib/php/'.$fileName))
		{include ('lib/php/'.$fileName);	return true;}
		
  // Дополнеине для тех кто не придерживается страндарта
	if (file_exists('lib/php/'.$class_name.'/'.$fileName))
		{include ('lib/php/'.$class_name.'/'.$fileName);	return true;}
	
	return false;
}

?>