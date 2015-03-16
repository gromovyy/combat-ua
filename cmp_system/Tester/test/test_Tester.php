<?php

/*************************************************************************************  
* Класс Tester обеспечивает единый механизм тестирования компонентов:
* - тестирование комплектности
* - функциональное тестирование
* - тестирование каналов связи
***************************************************************************************/

	echo "<h1>Тестируем функции saveArrayToFile и loadArrayFromFile</h1>";
	/*************************************************************************************  
	*  Функция saveArrayToFile сохраняет массив в файл
	*  $filename - имя файла
	*  $array - массив
	***************************************************************************************/
	$array = array("hello"=>1, "b"=>"new", "c"=>array(1,2,3),"buka");
	echo "Исходный массив:<br>";
	print_r($array);
	
	$this->saveArrayToFile("test_saveArray.txt", $array);
	
	$file_content = file_get_contents("test_saveArray.txt");
	echo "<br>Сохраненный массив:<br>";
	echo $file_content;
	
	/*************************************************************************************  
	*  Функция для загрузки массива из файла
	*  $filename - имя файла
	***************************************************************************************/
	$array_loaded = $this->loadArrayFromFile("test_saveArray.txt");
	echo "<br>Загруженный массив:<br>";
	print_r($array_loaded);
	
	echo "<h1>Тестируем функцию getDirTree</h1>";
	/*************************************************************************************  
	*  Рекурсивная функция для формирования дерева файлов и каталогов в указанной папке
	*  $dir - папка, в которой происходит формирование дерева каталогов.
	*  &$dir_array - cсылка на массив, в котором будет формироваться дерево каталогов.
	***************************************************************************************/
	$dir_array = array();
	$this->getDirTree("cmp/Eventer", $dir_array);
	print_r($dir_array);
	
	echo "<br>Тестируем сохранение списка папок в файл и загрузку из файла. Результат загрузки:<br>";
	$this->saveArrayToFile("test_saveDirTree.txt", $dir_array);
	$dir_array_from_file = $this->loadArrayFromFile("test_saveDirTree.txt");
	print_r($dir_array_from_file);
	
	
	echo "<h1>Тестируем функцию compareDirTree</h1>";
	/*************************************************************************************  
	*  Функция для сравнения двух многоуровневых массивов и нахождения разницы
	*  Если деревья идентичны - возврат TRUE
	*  Если деревья отличаются - возврат сообщений где найдены отличия и FALSE
	***************************************************************************************/
	
	// Сравниваем одинаковые папки
	$this->getDirTree("cmp/Eventer", $dir_array);
	$this->saveArrayToFile("test_saveDirTree.txt", $dir_array);
	$dir_array_from_file = $this->loadArrayFromFile("test_saveDirTree.txt");
	echo "Дерево файлов по адрессу cmp/Eventer<br>";
	print_r($dir_array);
	echo "<br>Дерево файлов из файла <br>";
	print_r($dir_array_from_file);
	
	echo "<br>Результат сравнения<br>";
	$this->compareDirTree($dir_array, $dir_array_from_file);
		
	// Сравниваем разные папки cmp/Eventer
	$this->getDirTree("cmp/Eventer", $dir_array_correct);
	// В файле находится папка cmp/Tester
	$this->getDirTree("cmp/Tester", $dir_array_current);
	
	echo "Дерево файлов по адрессу cmp/Eventer<br>";
	print_r($dir_array_correct);
	echo "<br>Дерево файлов по адрессу cmp/Tester<br>";
	print_r($dir_array_current);
	
	echo "<br>Результат сравнения<br>";
	$this->compareDirTree($dir_array_correct, $dir_array_current);

	
	echo "<h1>Тестируем функции  saveFileList и checkFileList</h1>";
	/*************************************************************************************  
	*  Функция получает текущий список файлов и сохраняет список в папке компонента
	* \test\correct\filelist\filelist.txt
	***************************************************************************************/
	echo "<br>Сначала сохраняем дерево папок, а потом сразу сравниваем с исходником<br>";

	$this->saveFileList("Tester");
 	$this->checkFileList("Tester");
		
	echo "<br>Создаем новый файл test_additional.txt<br>";
	file_put_contents("cmp/Tester/test_additional.txt","Тестовый файл");
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Удаляем существующий файл test_integrity.txt<br>";
	unlink("cmp/Tester/test_integrity.txt");
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Возвращаем файлы на место<br>";
	file_put_contents("cmp/Tester/test_integrity.txt","Тестовый файл");
	unlink("cmp/Tester/test_additional.txt");
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Создаем новую папку test_additional<br>";
	mkdir("cmp/Tester/test_additional",0,true);
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Удаляем папку test_additional и test_integrity<br>";
	rmdir("cmp/Tester/test_additional");
	rmdir("cmp/Tester/test_integrity");
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Возвращаем папку test_integrity на место<br>";
	mkdir("cmp/Tester/test_integrity");
	
	echo "<br>Проверяем целостность<br>";
	$this->checkFileList("Tester");
	
	echo "<br>Тестируем компонент DB<br>";

	$this->saveFileList("DB");
 	$this->checkFileList("DB");

	echo "<h1>Тестируем функции getDbStructureFromFile,  getDbStructureFromDb, и checkDBStructure</h1>";
	echo "<br>Тестируем функцию getTableListFromFile<br>";
	$bd_structure_from_file = $this->getDbStructureFromFile("cmp/Base/db/Base.sql");
	print_r($sql_structure_from_file);
	echo "<br>Тестируем функцию getTableListFromDB<br>";
	$bd_structure_from_bd = $this->getDbStructureFromDb();
	print_r($bd_structure_from_bd);
	echo "<br>Тестируем функцию checkDBStructure<br>";
	
	/*************************************************************************************  
	*  Функция берет структуру базы данных, расположенную в папке компонента по адресу 
	* db/Имя_компонента.sql  и сравнивает её со структурой базы данных в MySQL
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	$this->checkDBStructure("Base");
	$this->checkDBStructure("Viewer");
	$this->checkDBStructure("Tester");
	
	/*************************************************************************************  
	*  Функция создает в базе данных отсутствующий объект типа таблица, вид или процедура. 
	*  из папки test\methods\current\$file_name в папку test\methods\correct\$file_name 
	***************************************************************************************/
	$this->createDBObject("DB", "tables", "db_column_access");
	$this->createDBObject("Base", "views", "insert_event");
	$this->createDBObject("Base", "procedures", "insert_event");
	
	$this->restoreComponentDBStructure("Base");
	$this->restoreComponentDBStructure("DB");
	$this->restoreComponentDBStructure("Viewer");
	
	
	/*************************************************************************************  
	*  Функция сохраняет правильный результат выполнения метода с внешнего файла
	*  в папку test\methods\correct\$file_name 
	***************************************************************************************/
	
//	$this->saveMethodResult($file_name);
	
	/*************************************************************************************  
	*  Функция загружает правильный результат выполнения метода с внешнего файла
	*  в папку test\methods\correct\$file_name 
	***************************************************************************************/
//	$this->loadMethodResult($file_name);
	
	
	echo "<h1>Тестируем функции checkMethod</h1>";
	
	/*************************************************************************************  
	*  Функция сравнивает результат корректный результат, который находится по адресу test\methods\correct\$file_name
	*  с результатом выполнения входной функции
          *  Параметры:
	*  $component - имя компонента
	*  $method - имя метода
	*  $params[0][1]- двухмерный массив с параметрами метода $method. Каждая строка массива содержит параметры для одного вызова метода
	*  таким образом за один вызов функции checkMethod вызовов метода $method будет столько, сколько строк в массиве $param
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	// Массив параметров 	 1.	Имя функции                                 2. Массив с параметрами
	$params = array(
				array(	"generateFileNameByMethod", array("Hello")					),
				array(  "methodName2", 				array("Test")					),
				array(	"methodName3")
				
				);
	$this->checkMethod("Tester", "generateFileNameByMethod", $params);
	
	/*************************************************************************************  
	*  Функция парсит вид компонента сохраняет список форм и переменных формы вида в файл test/viewvars/view.txt
	*  $component - имя компонента
	*  $view - имя вида
	***************************************************************************************/
//	$this->saveViewVars ($component, $view);
	
	/*************************************************************************************  
	*  Функция парсит вид компонента  и сравнивает  полученный список форм и переменных   
	*  с "правильным" списком из файла test/viewvars/view.txt
	*  $component - имя компонента
	*  $view - имя вида
	*  Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
//	$this->checkViewVars($component, $view);

	/*************************************************************************************  
	*  Функция проходит по всем видам компонента, находит все попадания функций includeCSS и запускает их. 
	*  Если в результате выполнения все функции includeCSS вернули TRUE, 
	*  значит все файлы для подключения найдены и функция возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
//	$this->checkCssFiles($component);
	
	/*************************************************************************************  
	*  Функция проходит по всем видам компонента, находит все попадания функций includeJS и запускает их. 
	*  Если в результате выполнения все функции includeJS вернули TRUE, 
	*  значит все файлы для подключения найдены и функция возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
//	$this->checkJsFiles($component);	
	
	
	/*************************************************************************************  
	*  Функция для сравнения двух многоуровневых массивов и нахождения разницы
	*  Если деревья идентичны - возврат TRUE
	*  Если деревья отличаются - возврат сообщений где найдены отличия и FALSE
	***************************************************************************************/
//	$this->compareDirTree($dir_tree1, $dir_tree2);
//	$this->getTableListFromFile($file_sql); 
	
//	$this->getTableListFromDB();



?>