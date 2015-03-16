<?php

define('TSR_READER',0);
define('TSR_ADMIN',3);
/*************************************************************************************  
* Класс Tester обеспечивает единый механизм тестирования компонентов:
* - тестирование комплектности
* - функциональное тестирование
* - тестирование каналов связи
***************************************************************************************/

class Tester extends Viewer{

	/*************************************************************************************  
	*  Конструктор класса
	***************************************************************************************/
	
	public function Tester($role = TSR_READER){	
		
		$this->component 	= "Tester";
		$this->version 		= "1.0";
		if (empty($role)) $role = TSR_READER;		
		$this->role = $role;		
	}

	/*************************************************************************************  
	*  Обработка входящих событий
	***************************************************************************************/
	public function Dispatch() {
		$cmp = $_GET['cmp'];
		$evt = $_GET['evt'];
		
		if ($cmp == "Tester")
			switch ($evt) {
				
				case 'loadFileResult': 	
				case 'loadDBResult': 	
				// Загрузка результатов тестирования
				case 'delete_method_result':
								$this->deleteCorrectMethodResult($_POST['component'], $_POST['file_name']);
								break;
				case 'load_method_result': 	
								$this->loadMethodResult($_POST['component'], $_POST['file_name']);
								break;
				case 'save_method_result':
								$this->saveMethodResult($_POST['component'], $_POST['file_name']);
								break;
				case 'load_db_check_result':
								$this->loadDBResult($_POST['component'], $_POST['file_name']);
								break;
				case 'update_db_structure':
								$this->restoreComponentDBStructure($_POST['component']);
								break;
				case 'load_filelist_result': 	
								$this->loadFileListResult($_POST['component']);
								break;
				case 'save_filelist_result':
								$this->saveFileList($_POST['component']);
								break;
				case 'delete_filelist_result':
								$this->deleteCorrectFileList($_POST['component']);
								break;
								
				default    : 	echo "Parent::Dispatch";
								parent::Dispatch();
								break;
			}
	}
	
	/*************************************************************************************  
	*  Функция получает текущий список файлов и сохраняет список в папке компонента
	* \test\correct\filelist\filelist.txt
	***************************************************************************************/
 	public function saveFileList($component){
		
		// Создаем дерево файлов для текущегшо компонента
		$dir_tree_current = array();
		$this->getDirTree("cmp/$component", $dir_tree_current);
		
		// Удаляем из дерева папку test
		unset($dir_tree_current["test"]);	
		
		//Сохраняем строку в заархивированном виде в файл
		if (!is_dir("cmp/$component/test/filelist/")) 
			mkdir("cmp/$component/test/filelist/",0,true);
		$this->saveArrayToFile("cmp/$component/test/filelist/correct_filelist.txt", $dir_tree_current);
	}
	
	/*************************************************************************************  
	*  Функция сравнивает текущий список файлов с списком файлов по адресу
	* \test\filelist\filelist.txt
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
 	public function checkFileList($component){
		
		// Создаем дерево файлов для текущегшо компонента
		$dir_tree_current = array();
		if (!$this->getDirTree("cmp/$component", $dir_tree_current)) return FALSE;
		
		// Удаляем из дерева папку test
		unset($dir_tree_current["test"]);	
			
		// Загружаем корректную структуру файлов
		$dir_tree_correct = $this->loadArrayFromFile("cmp/$component/test/filelist/correct_filelist.txt");
		
		//заблокируем выдачу информации в бразуер
		ob_start();
		//Сравниваем  деревья файлов			
		// Проверка на наличие необходимых файлов
		$result = $this->checkNecessaryFiles(&$dir_tree_correct, &$dir_tree_current);
		
		// Проверка на наличие дополнительных файлов и папок
		$result_additional = $this->checkAdditionalFiles(&$dir_tree_correct, &$dir_tree_current);
		
		// Очищаем буффер вывода
		ob_get_clean();
		
		// Выводим результаты проверки.
		$this->loadView("check_filelist_result",array("component"=>$component, "result"=>$result, "result_additional"=>$result_additional),"Tester");
		
		return ($result AND $result_additional);
	}
	
	/*************************************************************************************  
	*  Функция сравнивает текущий список файлов с списком файлов по адресу
	* \test\filelist\filelist.txt
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
 	public function loadFileListResult($component){
		
		//Создаем массив для передачи входных параметров в вид load_filelist_result.php
		$data = array();
		$data["component"] =  $component;
		$data["class_correct_filelist"] = "correct";
		// Создаем дерево файлов для текущегшо компонента
		$dir_tree_current = array();

		//Вывод результатов сравнения сохраняем в буффер
		ob_start();
		
		if (!$this->getDirTree("cmp/$component", $dir_tree_current)) {
			$data["class_current_filelist"] = "error";
		}
			
		// Удаляем из дерева папку test
		unset($dir_tree_current["test"]);	
			
		
		if (!file_exists("cmp/$component/test/filelist/correct_filelist.txt")) {
			$data["class_correct_filelist"] = "absent";
			}
		else {
			// Загружаем корректную структуру файлов
			$dir_tree_correct = $this->loadArrayFromFile("cmp/$component/test/filelist/correct_filelist.txt");

			// Проверка на наличие необходимых файлов
			$result_necessary = $this->checkNecessaryFiles(&$dir_tree_correct, &$dir_tree_current);
			// Проверка на наличие дополнительных файлов и папок
			$result_additional = $this->checkAdditionalFiles(&$dir_tree_correct, &$dir_tree_current);
		}
		
		$data["description"] = ob_get_clean();
//		echo "----------------------".$data["description"];
		
		
		if ($result_necessary and $result_additional) 
			$data["class_current_filelist"] = "correct";
		else
			$data["class_current_filelist"] = "error";
		$data["current_filelist"] = preg_replace("!(array[\s]+\()|(=> 'file',)|(=> NULL,)|(\),)|(\))!i","",var_export($dir_tree_current, true));
		$data["correct_filelist"] = preg_replace("!(array[\s]+\()|(=> 'file',)|(=> NULL,)|(\),)|(\))!i","",var_export($dir_tree_correct, true));
		
		// Выводим результаты проверки.
		$this->loadView("load_filelist_result",$data,"Tester");
		
		return ($result_necessary AND $result_additional);
	}
	
	/*************************************************************************************  
	*  Функция разбирает структуру базы данных, расположенную в папке компонента по адресу 
	* db/Имя_компонента.sql  и сравнивает её со структурой базы данных в MySQL
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	public function checkDBStructure($component){
		
		$result = TRUE;
		
		if (file_exists("cmp/$component/db/$component.sql")) {
			// Разбираем структуру таблиц, видов и процедур из файла db/$component.sql в папке компонента.
			// В этом файле по умолчанию хранится структура БД компонента
			$db_structure_from_file = $this->getDbStructureFromFile("cmp/$component/db/$component.sql");
			// Получаем фактическую структуру БД компонента из базы данных
			$db_structure_from_db = $this->getDbStructureFromDb();
			
			// Сравниваем эти структуры. Если все объекты из файла найдены в структуре БД - результат TRUE. Иначе - FALSE
			$result = $this->compareDBStructure($db_structure_from_file, $db_structure_from_db);		
		}	
		
		// Загрузка вида check_method_result.php
		$this->loadView("check_db_result", array("component"=>$component, "result"=>$result), "Tester");	
		
		return $result;
	}
	
	/*************************************************************************************  
	*  Функция создает в базе данных отсутствующий объект типа таблица, вид или процедура. 
	*  из папки test\methods\current\$file_name в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function createDBObject($component, $object_type, $object_name){
		
		$result = TRUE;

		echo "<h3>Создание в БД объекта $object_type $object_name компонента $component</h3>";
		
		if (file_exists("cmp/$component/db/$component.sql")) {
			
			// Разбираем структуру таблиц, видов и процедур из файла db/$component.sql в папке компонента.
			// В этом файле по умолчанию хранится структура БД компонента
			$db_structure_from_file = $this->getDbStructureFromFile("cmp/$component/db/$component.sql");
			
			// Получаем фактическую структуру БД компонента из базы данных
			$db_structure_from_db = $this->getDbStructureFromDb();
			
			// Если указанного объекта нет в исходном файле или объект уже создан в БД - результат FALSE.
			// Иначе - попытка создать объект в БД. Если объект создан - вывод успешного результата TRUE 
			// Если объект так и не создался - вывод сообщения и результат FALSE
//			print_r($db_structure_from_file);
			if (!isset($db_structure_from_file[$object_type][$object_name])) {
				$result = FALSE;
				echo "<font color='red'>CREATING BD OBJECT FAIL: Объект $object_type с именем $object_name в исходном файле компонента $component не найден!</font><br>";
				}
			else if (is_array($db_structure_from_db[$object_type]) and array_key_exists($object_name, $db_structure_from_db[$object_type])){
				$result = FALSE;
				echo "<font color='red'>CREATING BD OBJECT FAIL: Объект $object_type с именем $object_name уже существует в БД компонента $component!</font><br>";			
				}
			else {
				$this->DB->sqlQuery($db_structure_from_file[$object_type][$object_name]);
				$db_structure_from_db = $this->getDbStructureFromDb();
				if ((!is_array($db_structure_from_db[$object_type])) or 
				    (!array_key_exists($object_name, $db_structure_from_db[$object_type]))) {
						$result = FALSE;
						echo "<font color='red'>CREATING BD OBJECT FAIL: Попытка создать объект $object_type с именем $object_name компонента $component не была успешна. Проверьте корректность скрипта создания объекта.</font><br>";			
						}
				else {
						echo "<font color='green'>CREATING BD OBJECT SUCCESS: объект $object_type с именем $object_name компонента $component создан.</font><br>";
					}
				}
			}
			// Если файла с sql кодом компонента не существует - ошибка.
			else {
				$result = FALSE;
				echo "<font color='red'>CREATING BD OBJECT FAIL: Объект $object_type с именем $object_name в БД компонента $component не найден!</font><br>";
			}
			
		return $result;		
	}
	
//	public function renewDBStructure($component){
//		
//	}
	
	/*************************************************************************************  
	*  Функция переписывает результат выполнения метода, который находится в файле $file_name  
	*  из папки test\methods\current\$file_name в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function saveMethodResult($component, $file_name){
		$current_file = "cmp/$component/test/methods/current/$file_name";
		$correct_file = "cmp/$component/test/methods/correct/$file_name";
		
		if (!file_exists($current_file)) { 
			echo "<font color = 'red'>Ошибка. Файл $file_name с результатами тестирования метода компонента $component не найден.</font>";
			return FALSE;
			}
		
		// Если не существует - создаем папку для хранения корректных результатов
		if (!is_dir("cmp/$component/test/methods/correct")) 
			mkdir("cmp/$component/test/methods/correct",0,true);
		
		// Удаляем файл с корректными результатами, если он существует
		if (file_exists($correct_file))
			unlink($correct_file);
		$result = copy($current_file, $correct_file);
		
		if ($result) {
			echo "<font color = 'green'>Результаты сохранены</font>";
			return TRUE;
			}
		else {
			echo "<font color = 'red'>Ошибка. Файл $file_name с результатами тестирования метода компонента $component не удалось сохранить.</font>";
			return FALSE;
		}
	}
	
	/*************************************************************************************  
	*  Функция удаляет правильные результаты тестируемого метода
	*  из папки test\methods\current\$file_name в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function deleteCorrectMethodResult($component, $file_name){
		
		$correct_file = "cmp/$component/test/methods/correct/$file_name";
		
		if (!file_exists($correct_file)) {
			echo "<font color = 'red'>Ошибка. Файл $file_name с результатами тестирования метода компонента $component не найден.</font>";
			return FALSE;
			}
		else {
			$result = unlink($correct_file);
			if (!$result) {
				echo "<font color = 'red'>Ошибка. Файл $file_name с результатами тестирования метода компонента $component удалить не удалось.</font>";
				return FALSE;
				}
			else {
				echo "Правильные результаты тестирования очищены.";
				return TRUE;
			}
		}
	}
	
	/*************************************************************************************  
	*  Функция удаляет сохраненную структуру файлов
	*  из папки test\methods\current\$file_name в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function deleteCorrectFileList($component){
		
		$correct_file = "cmp/$component/test/filelist/correct_filelist.txt";
		
		if (!file_exists($correct_file)) {
			echo "<font color = 'red'>Ошибка. Файл с сохраненной структурой файлов и папок компонента $component не найден.</font>";
			return FALSE;
			}
		else {
			$result = unlink($correct_file);
			if (!$result) {
				echo "<font color = 'red'>Ошибка. Файл с сохраненной структурой файлов и папок компонента $component удалить не удалось.</font>";
				return FALSE;
				}
			else {
				echo "Файл с сохраненной структурой файлов и папок компонента $component успешно удален.";
				return TRUE;
			}
		}
	}
	
	/*************************************************************************************  
	*  Функция загружает правильный результат выполнения метода с внешнего файла
	*  в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function loadMethodResult($component, $file_name){
		// Массив для хранения значений переменных для вида.
		$data = array();

		$data["file_name"] = $file_name;
		$data["component"] = $component;
		
		//Если файл с результатом корректного тестирования  существует, загружаем файл и сравниваем его содержимое с текущими результатами.
		if (!file_exists("cmp/$component/test/methods/correct/".$file_name)) {
				$data["class_function_correct"] = "absent";
				$data["result_function_correct"] = "";
				$data["class_browser_correct"] = "absent";
				$data["result_browser_correct"] = "";
				$data["class_bd_correct"] = "absent";
				$data["result_bd_correct"] = "";
			}
		else {			
			$correct_result = $this->loadArrayFromFile("cmp/$component/test/methods/correct/".$file_name);
				$data["class_function_correct"] = "correct";
				$data["result_function_correct"] = $correct_result["function_return"];
				$data["class_browser_correct"] = "correct";
				$data["result_browser_correct"] = $correct_result["browser_return"];
				$data["class_bd_correct"] = "correct";
				$data["result_bd_correct"] = $correct_result["sql_return"];
		}
				
			//Если файл с результатом корректного тестирования  существует, загружаем файл и сравниваем его содержимое с текущими результатами.
		if (!file_exists("cmp/$component/test/methods/current/".$file_name)) {
				$data["class_function_current"] = "absent";
				$data["result_function_current"] = "";
				$data["class_browser_current"] = "absent";
				$data["result_browser_current"] = "";
				$data["class_bd_current"] = "absent";
				$data["result_bd_current"] = "";
			}
		else {	
				$current_result = $this->loadArrayFromFile("cmp/$component/test/methods/current/".$file_name);
				$data["result_function_current"] = $current_result["function_return"];
				$data["result_browser_current"] = $current_result["browser_return"];
				$data["result_bd_current"] = $current_result["sql_return"];
		}
		
		//Если правильный результат не найден, но есть текущий результат - подсвечиваем все оранжевым и устанавливаем статус warning
		if (!isset($correct_result) and isset($current_result)){
				$data["class_function_current"] = "warning";
				$data["class_browser_current"] = "warning";
				$data["class_bd_current"] = "warning";
				$result = FALSE;
		}
		
		// Проверка по полям - если поля не совпадают- поле подсвечивается как ошибка, иначе - успех
		else if (isset($correct_result) and isset($current_result)) {
				if ($correct_result["function_return"] != $current_result["function_return"]) 
					$data["class_function_current"] = "error";
				else
					$data["class_function_current"] = "correct";
				
				if ($correct_result["browser_return"] != $current_result["browser_return"]) 
					$data["class_browser_current"] = "error";
				else
					$data["class_browser_current"] = "correct";
				
				if ($correct_result["sql_return"] != $current_result["sql_return"]) 
					$data["class_bd_current"] = "error";
				else
					$data["class_bd_current"] = "correct";
		}
		
		$this->loadView("load_method_result", $data, "Tester");

	}
	
	/*************************************************************************************  
	*  Функция загружает результат сравнения структур баз дынных
	*  в папку test\methods\correct\$file_name 
	***************************************************************************************/
	public function loadDBResult($component){
		// Массив для хранения значений переменных для вида.
		$data = array();

		$data["component"] = $component;
		
		// Устанавливаем начальные значения состояния всех колонок
		$data["class_file_tables"] 		= "correct";
		$data["class_db_tables"] 		= "correct";
		$data["class_file_views"] 		= "correct";
		$data["class_db_views"] 		= "correct";
		$data["class_file_procedures"] 	= "correct";
		$data["class_db_procedures"] 	= "correct";
		// Устанавливаем начальные значения для списков таблиц, видов и процедур
		$data["file_tables"] 			= "";
		$data["db_tables"] 				= "";
		$data["file_views"] 			= "";
		$data["db_views"] 				= "";
		$data["file_procedures"]		= "";
		$data["db_procedures"] 			= "";
		
		//Если файл с результатом корректного тестирования  существует, загружаем файл и сравниваем его содержимое с текущими результатами.
		if (!file_exists("cmp/$component/db/$component.sql")) {
				$data["class_file_tables"] 		= "absent";
				$data["class_db_tables"] 		= "absent";
				$data["class_file_views"] 		= "absent";
				$data["class_db_views"] 		= "absent";
				$data["class_file_procedures"] 	= "absent";
				$data["class_db_procedures"] 	= "absent";
			}
		else {			
			// Разбираем структуру таблиц, видов и процедур из файла db/$component.sql в папке компонента.
			// В этом файле по умолчанию хранится структура БД компонента
			$db_structure_from_file = $this->getDbStructureFromFile("cmp/$component/db/$component.sql");
			// Получаем фактическую структуру БД компонента из базы данных
			$db_structure_from_db = $this->getDbStructureFromDb();
			
			if (isset($db_structure_from_file["tables"])) 
				foreach($db_structure_from_file["tables"] as $table => $script) 
					if (!is_array($db_structure_from_db["tables"]) or !array_key_exists($table, $db_structure_from_db["tables"])) {				
						$data["class_db_tables"] 		= "error";
						$data["file_tables"] .= "<p class='absent_object'>$table</p>";
					}
					else {
						$data["file_tables"] .= "<p class='present_object'>$table</p>";
						$data["db_tables"] .= "<p class='present_object'>$table</p>";
					}
			if (isset($db_structure_from_file["views"])) 
				foreach($db_structure_from_file["views"] as $view => $script) 
					if (!is_array($db_structure_from_db["views"]) or !array_key_exists($view, $db_structure_from_db["views"])) {				
						$data["class_db_views"] 		= "error";
						$data["file_views"] .= "<p class='absent_object'>$view</p>";
					}
					else {
						$data["file_views"] .= "<p class='present_object'>$view</p>";
						$data["db_views"] .= "<p class='present_object'>$view</p>";
					}
				
			if (isset($db_structure_from_file["procedures"])) 
				foreach($db_structure_from_file["procedures"] as $procedure => $script) 
					if (!is_array($db_structure_from_db["procedures"]) or !array_key_exists($procedure, $db_structure_from_db["procedures"])) {				
						$data["class_db_procedures"] 		= "error";
						$data["file_procedures"] .= "<p class='absent_object'>$procedure</p>";
					}
					else {
						$data["file_procedures"] .= "<p class='present_object'>$procedure</p>";
						$data["db_procedures"] .= "<p class='present_object'>$procedure</p>";
					}
			}
		
				
		$this->loadView("load_db_check_result", $data, "Tester");

	}
	
	/*************************************************************************************  
	// В результате выполнения функции могут меняться следующие объекты:
			//1. Вывод в браузер пользователю 
			//2. Запросы в БД
			//3. Возвращаемое значение функции
			//4. Файловая система
			//5. Состояние объекта
			//6. Входные параметры
			//7. Глобальные переменные
	Данная функция проверяет 1, 2 и 3 параметр
	*  Функция сравнивает результат корректный результат, который находится по адресу test\methods\correct\$file_name
	*  с результатом выполнения входной функции
          *  Параметры:
	*  $component - имя компонента
	*  $method - имя метода
	*  $params[0][1]- двухмерный массив с параметрами метода $method. Каждая строка массива содержит параметры для одного вызова метода
	*  таким образом за один вызов функции checkMethod вызовов метода $method будет столько, сколько строк в массиве $param
	* Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	public function checkMethod($component, $method, $params){
		
		// Массив для хранения результатов
		$data = array();
		
		// Проверяем - существует ли папка компонента. Если не существует - вовращаем FALSE и сообщение об ошибке
		if (!is_dir("cmp/$component")) {		
				echo "<font color='red'>CHECK METHOD FAIL: Компонент $component не найден!</font><br>";
				return FALSE;
		}
		
		// Проверяем - существуют ли папки для хранения текущих и корректных результатов тестирования.
		// Если не существуют - создаем их.
		if (!is_dir("cmp/$component/test/methods/current")) 
			mkdir("cmp/$component/test/methods/current",0,true);
		
		if (!is_dir("cmp/$component/test/methods/correct")) 
			mkdir("cmp/$component/test/methods/correct",0,true);
		
		// Для каждого значения входных параметров запускаем тестируемую функцию и сравниваем её с корректными результатами
		foreach ($params as $test_number => $input_params) {
	
			$method_result = "success";
			// В соответствии с входными параметрами, определяем имя файла, в котором должен хранится результат проверки
			$file_name = $this->generateFileNameByMethod($method, $input_params);
			
			// Запускаем режим буфферизации выходного потока пользователю
			ob_start();
			// Запускаем режим буфферизации sql команд на СУБД
			$this->DB->StartLog();
			// выполняем тестируемый метод и сохраняем результат в переменную $current_result.
			$func_result = call_user_func_array(array($this->$component, $method), $input_params);
			
			//Если результат - массив - преобразуем его в строку 
			//if (is_array($func_result)) 
				$current_result["function_return"] = var_export($func_result, true);
				
			//else 
			//	$current_result["function_return"] = $func_result;
			// Завершаем режим буфферизации выходных данных и присоединяем результат выполнения в переменную $current_result
			$current_result["browser_return"] = ob_get_clean();
			
			//Завершаем режим логирования запросов в БД и сохраняем результат в переменную $current_result
			$current_result["sql_return"]= $this->DB->GetAndCleanDBLog();
						
			// Преобразование массива в строку.
			$str_current_result = var_export($current_result, true);
			
			// Сохраняем результат тестирования в текущую папку.
			file_put_contents("cmp/$component/test/methods/current/".$file_name, $str_current_result);
			
			//$this->compareMethodResult($component, $file_name);
			//Если файл с результатом корректного тестирования  не существует, возвращаем ошибку и FALSE
			if (!file_exists("cmp/$component/test/methods/correct/".$file_name)) 
				$method_result = "correct_file_absent";
			else {	
				//Если файл с результатом корректного тестирования  существует, загружаем файл и сравниваем его содержимое с текущими результатами.
				$correct_result = file_get_contents("cmp/$component/test/methods/correct/".$file_name);
				if (strcmp($str_current_result, $correct_result) != 0)
						$method_result = "fail";				
			}
			
			// Формируем массив с данными для вывода результатов в вид
			$data[$test_number]["test_number"] = $test_number;
			$data[$test_number]["file_name"] = $file_name;
			$data[$test_number]["params"] = preg_replace("![\s]+[\d]+[\s]+=>[\s]+!i", " ", var_export($input_params, true));
			
			switch ($method_result){
				case "correct_file_absent"	: 	$data[$test_number]["method_result"] = "warning";
												$data[$test_number]["method_result_description"] = "WARNING: Файл с корректными результатами не обнаружен";
												break;
				case "fail"					:	$data[$test_number]["method_result"] = "error";
												$data[$test_number]["method_result_description"] = "FAIL: Результаты выполнения не совпадают с ожидаемым результатом";
												break;
				case "success"				:	$data[$test_number]["method_result"] = "correct";
												$data[$test_number]["method_result_description"] = "SUCCESS: Результаты совпали";
												break;
			}
			
			// Устанавливаем общий результат
			if ($method_result != "success") $result = FALSE;
		}

		// Загрузка вида check_method_result.php
		$this->loadView("check_method_result", array("component"=>$component, "method"=>$method, "check_method_result"=>$data), "Tester");
		
		return $result;
	}
	
	/*************************************************************************************  
	*  Функция сравнивает два результата выполнения метода - корректный и текущий.
	*  Если корректного результата не существует - функция выводит предупреждение и вопрос о ручном подтверждении корректности
	*  Если файлы не совпадают - функция предоставляет анализ - в каких элементах была разбежность - в выводе в браузер, воз
	*  1. Вывод в браузер пользователю 
	* 2. Запросы в БД
	* 3. Возвращаемое значение функции
	* Также функция дает возможность просмотреть каждый из трех результатов деятельности
	*  Параметры:
	*  $component - имя компонента
	*  $method - имя метода
	*  $params - двухмерный массив с параметрами метода $method. 
	***************************************************************************************/
	public function compareMethodResult($component, $file_name) {
		
	
	}
	
	/*************************************************************************************  
	*  Функция  генерирует имя файла по следующему шаблону: ИмяМетода_К-воПараметров_CRCсуммаСтрокиПараметров.txt
	*  Параметры:
	*  $component - имя компонента
	*  $method - имя метода
	*  $params - двухмерный массив с параметрами метода $method. 
	***************************************************************************************/
	public function generateFileNameByMethod($method, $params=0){
		$result = $method;
		$params_count = count($params);
		$result .= "_".$params_count;
		if ($params_count != 0) {
			$result .= "_".crc32(var_export($params, true));
		}
//		$result .=".txt";
		return $result;
	}
	
	/*************************************************************************************  
	*  Функция парсит вид компонента сохраняет список форм и переменных формы вида в файл test/viewvars/view.txt
	*  $component - имя компонента
	*  $view - имя вида
	***************************************************************************************/
	public function saveViewVars ($component, $view){
	
	}
	
	/*************************************************************************************  
	*  Функция парсит вид компонента  и сравнивает  полученный список форм и переменных   
	*  с "правильным" списком из файла test/viewvars/view.txt
	*  $component - имя компонента
	*  $view - имя вида
	*  Если результат сравнения положительный - возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	public function checkViewVars($component, $view){
	
	}

	/*************************************************************************************  
	*  Функция проходит по всем видам компонента, находит все попадания функций includeCSS и запускает их. 
	*  Если в результате выполнения все функции includeCSS вернули TRUE, 
	*  значит все файлы для подключения найдены и функция возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	public function checkCssFiles($component){
	
	}
	
	/*************************************************************************************  
	*  Функция проходит по всем видам компонента, находит все попадания функций includeJS и запускает их. 
	*  Если в результате выполнения все функции includeJS вернули TRUE, 
	*  значит все файлы для подключения найдены и функция возвращает TRUE. Иначе - FALSE
	***************************************************************************************/
	public function checkJsFiles($component){
	
	}
	
	/*************************************************************************************  
	*  Функция для загрузки массива из файла
	*  $filename - имя файла
	***************************************************************************************/
	public function loadArrayFromFile($filename) {
		if (!file_exists($filename)) return NULL;
		$file_content = file_get_contents($filename);	
		
	//	Если  данные хранятся  заархивированными, раскоментировать следующую строку
	//	$file_content = gzuncompress($file_content);
	
		eval('$result = '.$file_content.';');
		return $result;
	}

	/*************************************************************************************  
	*  Функция для сохранения массива в файл
	*  $filename - имя файла
	*  $array - массив
	***************************************************************************************/
	public function saveArrayToFile($filename, $array) {
	
		$file_content = var_export($array, true);
		
	//	Если необходимо, чтобы данные сохранялись сжатыми и заархивированными, раскоментировать следующую строку
	//	$file_content = gzcompress($file_content, 7);
	
		file_put_contents($filename, $file_content);
	}
	
	/*************************************************************************************  
	*  Рекурсивная функция для формирования дерева файлов и каталогов в указанной папке
	*  $dir - папка, в которой происходит формирование дерева каталогов.
	*  &$dir_array - cсылка на массив, в котором будет формироваться дерево каталогов.
	***************************************************************************************/
	public function getDirTree($dir, &$dir_array) {
		
		// Проверяем папку на существование
		if (!is_dir($dir)) {
			echo "<font color = 'red'>ERROR: Папка $dir не найдена</font><br>";
			return FALSE;
			}
		// Открываем папку
		$folder_handle = opendir($dir);
		
		// Пока есть файл или папка - повторяем цикл
		while (false !== ($filename = readdir($folder_handle))) {
			//	Если имя файла - точка или две точки берем следующий объект.
			if (($filename == ".") or ($filename == "..")) continue;
			
			// Если это папка - рекурсивно запускаем функцию. Если файл - сохраняем в массиве.
			if (is_dir("$dir/$filename")) {
				$this->getDirTree("$dir/$filename", $dir_array[$filename]);
				}
			else
				$dir_array[$filename] = "file";
        }
		// Закрываем папку
		closedir($folder_handle);
		return TRUE;
    }
	
	
	/*************************************************************************************  
	*  Рекурсивная функция для проверки на наличие необходимых файлов
	*  Если деревья отличаются - возврат сообщений где найдены отличия и FALSE
	* $dir_tree_correct - корректное дерево фаойлов
	* $dir_tree_current - дерево файлов для проверки
	***************************************************************************************/
	public function checkNecessaryFiles(&$dir_tree_correct, &$dir_tree_current) {
		$result = TRUE;
		// Проверка на наличие всех  файлов
		foreach ($dir_tree_correct as $key=>$value) {
			// Проверка на наличие папки с файлами
			if (is_array($value)) { 
				if (!array_key_exists($key, $dir_tree_current)) {
					$result = FALSE;
					echo "<font color='red'>FAIL: Отсутствует папка с файлами $key</font><br>";
				}
				else 
					$result = $this->checkNecessaryFiles($dir_tree_correct[$key], $dir_tree_current[$key]);
				}
			// Проверка на наличие пустых папок
			else if ($value =="") {
					if (!array_key_exists($key, $dir_tree_current) or $dir_tree_current[$key]=="file") {
						$result = FALSE;
						echo "<font color='red'>FAIL: Отсутствует пустая папка $key</font><br>";
					}	
				}
			// Проверка на наличие файлов
			else if ($value =="file"){ 
					if ($dir_tree_current[$key]!="file") {
						$result = FALSE;
						echo "<font color='red'>FAIL: Отсутствует файл $key</font><br>";
					}
				}
			// Недопустимый формат входного массива
			else {
				$result = FALSE;
				echo "<font color='red'>FAIL: Недопустимый формат масива для сравнения</font><br>";
			}
		}
		
		return $result;
	}
	
	/*************************************************************************************  
	*  Рекурсивная функция для проверки на наличие  новых файлов и папок
	*  Если деревья отличаются - возврат сообщений где найдены отличия и FALSE
	* $dir_tree_correct - корректное дерево фаойлов
	* $dir_tree_current - дерево файлов для проверки
	***************************************************************************************/
	public function checkAdditionalFiles(&$dir_tree_correct, &$dir_tree_current) {
		$result = TRUE;
		// Проверка на наличие всех  файлов
		foreach ($dir_tree_current as $key=>$value) {
			// Проверка на наличие папки с файлами
			if (is_array($value)) { 
				if (!array_key_exists($key, $dir_tree_correct)) {
					$result = FALSE;
					echo "<font color='orange'>WARNING: Обнаружена новая папка с файлами $key</font><br>";
				}
				else 
					$result = $this->checkAdditionalFiles($dir_tree_correct[$key], $dir_tree_current[$key]);
				}
			// Проверка на наличие пустых папок
			else if ($value =="") {
					if (!array_key_exists($key, $dir_tree_correct) or $dir_tree_correct[$key]=="file") {
						$result = FALSE;
						echo "<font color='orange'>WARNING: Обнаружена новая пустая папка $key</font><br>";
					}	
				}
			// Проверка на наличие файлов
			else if ($value =="file"){ 
					if (!array_key_exists($key, $dir_tree_correct) or $dir_tree_correct[$key]!="file") {
						$result = FALSE;
						echo "<font color='orange'>WARNING: Обнаружен новый файл $key</font><br>";
					}
				}
			// Недопустимый формат  текущего массива
			else {
				$result = FALSE;
				echo "<font color='red'>FAIL: Недопустимый формат текущего масива для сравнения</font><br>";
			}
		}
		
		return $result;
	}

	/*************************************************************************************  
	*  Функция считывает sql-файл и формирует массив, соответствующий структуре таблиц
	*  $file_sql - имя файла.
	*  Функция возвращает массив следующего вида
	* ("tables"=>(), "views"=>(), "procedures"=>(), "Inserts"=>());
	* Массив Tables состоит из таблиц
	* ("nameTable1" =>"sql_script1_from_file", "nameTable2" =>"sql_script2_from_file", ...)
	* Структура массива Views и Procedure аналогична
	* Структура массива Procedures проще
	* ("procName1" =>"procScript1", "procName2" =>"procScript2", ...)
	* Массив Inserts  содержит только список вставок.
	* ("sql_insert_1", "sql_insert_2", "sql_insert_3",...,"sql_insert_N")
	***************************************************************************************/
	
	public function getDbStructureFromFile($file_sql) {
		
		$result = array();
		// Загружаем файл
		$file_content = file_get_contents($file_sql);		
		
		// Удаляем все коментарии. Строки коментария начинаются с двух дефисов и заканчиваются переносом строки.
		//-- =============================================================================
		$file_content = preg_replace("!--(.*)[\n\r]!i", "", $file_content); 
		
		//Находим все имена таблиц и загружаем их в массив $table_array
		preg_match_all("!CREATE[\s]+TABLE[\s]+`(.+)`.*;!Uis", $file_content, $table_array);

		// Записываем найденные результаты в массив results
		foreach ($table_array[1] as $key => $value){
			$result["tables"][$value] = $table_array[0][$key];
		}
		
		//Находим все имена представлений и загружаем их в массив $view_array
		preg_match_all("!CREATE[\s]+VIEW[\s]+`(.+)`.*;!Uis", $file_content, $view_array);
		
		// Записываем найденные результаты в массив results
		foreach ($view_array[1] as $key => $value){
			$result["views"][$value] = $view_array[0][$key];
		}
			
		//Находим все процедуры и загружаем их в массив $procedure_array
		preg_match_all("!DELIMITER[\s]+([\S]+)[\s]+(CREATE[\s]+PROCEDURE[\s]+`(.+)`.*)\\1!Uis", $file_content, $procedure_array);

//		print_r($procedure_array);
		// Записываем найденные результаты в массив results
		foreach ($procedure_array[3] as $key => $value){
			$result["procedures"][$value] = $procedure_array[2][$key];
		}
		
		return $result;
		//Возвращаем объединенный массив
	}
	
	/*************************************************************************************  
	*  Функция считывает список таблиц из БД и формирует массив, соответствующий структуре таблиц
	*  Возвращает массив следующего вида
	*("tables"=>(), "views"=>(), "procedures"=>(), "Inserts"=>());
	* Массив Tables состоит ассоциативных элементов:
	* ("nameTable1" =>"", "nameTable2" =>"", ...)
	***************************************************************************************/
	public function getDbStructureFromDb() {		
		$result = array();
		
		//Получаем списки таблиц, видов и процедур и записываем их в массив
		$table_array = $this->DB->ShowTables();
		if (is_array($table_array))
			foreach ($table_array as $key => $value){
				$result["tables"][$key] = $value;
			}
		
		$view_array = $this->DB->ShowViews();
		if (is_array($view_array))
			foreach ($view_array as $key => $value){
				$result["views"][$key] = $value;
			}
		
		$procedure_array = $this->DB->ShowProcedures();
		if (is_array($procedure_array))
			foreach ($procedure_array as $key => $value){
				$result["procedures"][$key] = $value;
			}
		
		return $result;
	}
	
	/*****************************************************************************************  
	*  Функция сравнивает два многоуровневых массива, в которых хранятся списки объектов БД
	*  Проверяет на наличие всех необходимых таблиц, видов и процедур. 
	*  Если в файлах найдены отсутствующие в БД объекты - сообщает и возвращает FALSE
	* $db_structure_from_file - список объектов с файла инсталяции
	* $db_structure_from_db - список объектов из БД
	***************************************************************************************/
	public function compareDBStructure (&$db_structure_from_file, &$db_structure_from_db) {
		$result = TRUE;
		// Если в файле есть таблицы - ищем соответствующие таблицы в структуре базы данных. 
		// Если соответствующая таблица в БД отсутствует - выводим сообщение об ошибке и устанавливаем выходной результат в FALSE
		if (isset($db_structure_from_file["tables"])) 
			foreach($db_structure_from_file["tables"] as $table => $script) 
				if (!is_array($db_structure_from_db["tables"]) or !array_key_exists($table, $db_structure_from_db["tables"])) {				
					$result = FALSE;
//					echo "<font color='red'>FAIL: В базе данных отсутствует таблица $table</font><br>";
				}
		if (isset($db_structure_from_file["views"])) 
			foreach($db_structure_from_file["views"] as $view => $script) 
				if (!is_array($db_structure_from_db["views"]) or !array_key_exists($view, $db_structure_from_db["views"])) {				
					$result = FALSE;
//					echo "<font color='red'>FAIL: В базе данных отсутствует вид $view</font><br>";
				}	
		if (isset($db_structure_from_file["procedures"])) 
			foreach($db_structure_from_file["procedures"] as $procedure => $script) 
				if (!is_array($db_structure_from_db["procedures"]) or !array_key_exists($procedure, $db_structure_from_db["procedures"])) {				
					$result = FALSE;
//					echo "<font color='red'>FAIL: В базе данных отсутствует процедура $procedure</font><br>";
				}	
				
		return $result;
	}

	/*****************************************************************************************  
	*  Функция сравнивает два многоуровневых массива, в которых хранятся списки объектов БД
	*  Проверяет на наличие всех необходимых таблиц, видов и процедур. 
	*  Если в файлах найдены отсутствующие в БД объекты - сообщает и возвращает FALSE
	* $db_structure_from_file - список объектов с файла инсталяции
	* $db_structure_from_db - список объектов из БД
	***************************************************************************************/
	public function restoreComponentDBStructure($component) {
		$result = TRUE;
		
		echo "<h3>Восстановление целостности структуры БД компонента $component</h3>";
		if (!file_exists("cmp/$component/db/$component.sql")) {
			echo "<font color='red'>CREATING BD OBJECT FAIL: Файл  cmp/$component/db/$component.sql с структурой БД компонента $component не найден.</font><br>";									
			return FALSE;
		}
		// Разбираем структуру таблиц, видов и процедур из файла db/$component.sql в папке компонента.
		// В этом файле по умолчанию хранится структура БД компонента
		$db_structure_from_file = $this->getDbStructureFromFile("cmp/$component/db/$component.sql");
		// Получаем фактическую структуру БД компонента из базы данных
		$db_structure_from_db = $this->getDbStructureFromDb();
		
		// Сравниваем эти структуры. Если все объекты из файла найдены в структуре БД - результат TRUE. Иначе - FALSE
		$result_compare = $this->compareDBStructure($db_structure_from_file, $db_structure_from_db);		
		if ($result_compare) {
			$result = TRUE;
			echo "<font color='green'>DB SUCCESS: Структура БД компонента $component в обновлении не нуждается.</font><br>";
			return $result;				
			}
		
		// Если в файле есть таблицы - ищем соответствующие таблицы в структуре базы данных. 
		// Если соответствующая таблица в БД отсутствует - выводим сообщение об ошибке и устанавливаем выходной результат в FALSE
		$object_types = array("tables", "views", "procedures");
		foreach ($object_types as $object_type) 
			if (isset($db_structure_from_file[$object_type])) 
				foreach($db_structure_from_file[$object_type] as $object_name => $script) 
					if (!is_array($db_structure_from_db[$object_type]) or 
						!array_key_exists($object_name, $db_structure_from_db[$object_type])) {				
							$is_success = $this->DB->query($script);
							if ($is_success) {
								echo "<font color='green'>CREATING BD OBJECT SUCCESS: объект $object_type с именем $object_name компонента $component создан.</font><br>";
								}
							else {
								$result = FALSE; 
								echo "<font color='red'>CREATING BD OBJECT FAIL: Попытка создать объект $object_type с именем $object_name компонента $component не была успешна. Проверьте корректность скрипта создания объекта.</font><br>";									
								}
					}
		
		if ($result)
				echo "<font color='green'>DB RESTORE SUCCESS: Структура БД компонента $component успешно обновлена.</font><br>";
			else
				echo "<font color='red'>DB RESTORE FAIL: Структуру БД компонента $component обновить не удалось.</font><br>";
				
		return $result;
	}
}

?>
