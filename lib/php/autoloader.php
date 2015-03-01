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
	// Определяем, это класс модели или обычного компонента. Класс модели заканчивается на имя Model
	$count = 0;
	$component_class = preg_replace('/Model$/','', $class_name, -1, $count); 
	
	// Если это класс модели - ищем его в папке model компонента
	if ($count > 0)
		{
		if (file_exists("cmp/" . $component_class . "/model/" . $class_name . ".php"))
			{include ("cmp/" . $component_class . "/model/" . $class_name . ".php"); return true;}
		if (file_exists("cmp_system/" . $component_class . "/model/" . $class_name . ".php"))
			{include ("cmp_system/" . $component_class . "/model/" . $class_name . ".php");	return true;}
		}
	// Если не модель - ищем в основных классах компонентов
	else {
	
		if (file_exists("cmp/" . $class_name . "/" . $class_name . ".php"))
			{include ("cmp/" . $class_name . "/" . $class_name . ".php"); return true;}
		if (file_exists("cmp_system/" . $class_name . "/" . $class_name . ".php"))
			{include ("cmp_system/" . $class_name . "/" . $class_name . ".php");	return true;}
	}
		
	//	Загрузка класcов в соответствии со стандартом PSR-0 и установленных через Composer 	
		$className = ltrim($class_name, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		if (file_exists('vendor/'.$fileName))
		{include ('vendor/'.$fileName);	return true;}
		
  // Дополнеине для тех кто не придерживается страндарта
	if (file_exists('lib/php/'.$class_name.'/'.$fileName))
		{include ('lib/php/'.$class_name.'/'.$fileName);	return true;}
	
	return false;
}

?>