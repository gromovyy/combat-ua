<?php 
	class Component extends Contenter {
		function e_ListTitle() {
		}
		
		function e_List() {
		}	
		
		
		// Возвращает список
		function combo_component() {
			$user_component = $this->getDirList('cmp');
			$system_component = $this->getDirList('cmp_system');
			$all_component = array_merge($user_component, $system_component);
			foreach($all_component as $key=>$value) {
				$result[$key]['id'] = $value;
				$result[$key]['v'] = $value;
			}
//			print_r ($result);
			return $result;
		}
		
		function combo_view($cmp) {
			if (file_exists("cmp_system/$cmp/vw"))
				$component_view = $this->getFileList("cmp_system/$cmp/vw");
			else if (file_exists("cmp/$cmp/vw"))
				$component_view = $this->getFileList("cmp/$cmp/vw");
			
			foreach($component_view as $key=>$value) {
				$result[$key]['id'] = $value;
				$result[$key]['v'] = $value;
			}
//			print_r ($result);
			return $result;
		}	
		
		public function getDirList($dir) {
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

			// Если это папка - добавляем в массив результатов.
			if (is_dir("$dir/$filename")) $result[] = $filename;
		}
		// Закрываем папку
		closedir($folder_handle);
		
		if (empty($result)) $result = array();
		return $result;
	}
	
	public function getFileList($dir) {

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
			if (!is_dir("$dir/$filename")) {
				$path_parts = pathinfo($filename);
				$result[] = $path_parts['filename'];
			}
		}
		// Закрываем папку
		closedir($folder_handle);
		return $result;
	}
	
	}
?>