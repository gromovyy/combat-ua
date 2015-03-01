<?php 
	class Menu extends Contenter {
	
		//Переменные для хранения активных пунктов меню
		public $active_parent_id = 0;
		public $active_child_id = 0;
		
		// Функция входной ссылке получает текущие активные пункты меню и записывает их в переменные $active_parent_id и $active_child_id
		function setActiveMenu($path_query) {
//			echo $path_query;
			if (empty($path_query)) {
//				$this->active_parent_id = 1;
				return;
			}
			
			$data = $this->getList('menu');
			
			foreach($data as $key =>$row) {
				if (!empty($row["id_parent_menu"]) and (strpos($row["url"],$path_query)!==FALSE)) { 
					$this->active_child_id = $row["id_menu"];
					$this->active_parent_id = $row["id_parent_menu"];
					break;
				}
			}
			if (empty($active_parent_id)) {
				foreach($data as $key =>$row) 
					if (empty($row["id_parent_menu"]) and (strpos($row["url"],$path_query)!==FALSE)) { 			
						$this->active_parent_id = $row["id_menu"];
						break;
					}	
			}
			if ($this->active_parent_id == 0) {
				$this->active_parent_id = 1;
			}

		}
		
		// Функция для отображения главного меню
		function e_ShowMenu($viewName = 'main_menu',$data = null ) {
			// Если указан вид и данные то загружаем сразу 
			if(!empty($data)){
				$this->loadView($viewName, array("main_menu" => $data));
			return;
			}
			
			// Получаем данные для меню.
			$menu = $this->getList('menu');
			// Вырезание невидимых елементов
			foreach ($menu as $key=>$menuitem){
				// Перебираем все елементы, есливыполняется условие что обьект видим или пользователь видит скрытые обьекты. Вырезаем обьект.
				if(!($menuitem['is_visible'] or $this->is_visible())){
					unset($menu[$key]);
				} 
			}
			$menu1 = $menu;
			$menu2 = $menu;
			// Выбираем только главные пункты меню
			foreach ($menu as $key => $row) {
			
				if (empty($row["id_parent_menu"])) {
					$sub_menu1 = Array();
					// Для каждого главного пункта мени ищем его подменю
					foreach ($menu1 as $key_sub1 =>$row_sub1) {
						if ($row_sub1["id_parent_menu"] == $row["id_menu"] ) {
							$sub_menu2 = Array();
							// Для каждого подменю ищем его подменю третьего уровня
							foreach ($menu2 as $key_sub2 =>$row_sub2) {
								if ($row_sub2["id_parent_menu"] == $row_sub1["id_menu"] ) 
								$sub_menu2[] = $row_sub2;
							}
							// Если подменю третьего уровня существует - добавляем его
							if (!empty($sub_menu2)) {
								$row_sub1["sub_menu"] = $sub_menu2;
							}
							$sub_menu1[] = $row_sub1;
						}
					}
					// Если подменю второго уровня существует - добавляем его
					if (!empty($sub_menu1)) {
						$row["sub_menu"] = $sub_menu1;
					}
					$main_menu[] = $row;
				}
			}
			
			// Запускаем отображение меню.
			$this->loadView($viewName, array("main_menu" => $main_menu));
		}
		
		// Отображает диалог для правки меню
		// Отличается от стандартного тем что дополнительно получает параметры для вложености меню
		function e_EditDlg($object,$id_menu) {
			$data["menu"] = $this->get_row($id_menu);
			if (!$this->is_update($data["menu"]["id_owner"])) return false;
			
			$data["combo_menu_list"] = $this->getComboMenu(2);
			//print_r($data["combo_menu_list"]);
			$this->loadView("edit_menu_dlg", $data);
		}
		
		// Отображает диалог для удаления меню
		function e_DeleteDlg($object,$id_menu) {			
			$data["menu"] = $this->get_row($id_menu);
			if (!$this->is_delete($data["menu"]["id_owner"])) return false;
			//print_r($data["menu"]);
			$this->loadView("delete_menu_dlg", $data);
		}
		
		// Возвращает список первого и второго уровня меню в формате для выпадающего списка.
		function getComboMenu($level = 3) {
			// Получаем данные для меню.

			$menu = $this->getList('menu');
			$menu1 = $menu;
			$menu2 = $menu;
			// Выбираем только главные пункты меню
			foreach ($menu as $key => $row) {
				if (empty($row["id_parent_menu"])) {
					$sub_menu1 = Array();
					// Для каждого главного пункта мени ищем его подменю
					foreach ($menu1 as $key_sub1 =>$row_sub1) {
						if ($row_sub1["id_parent_menu"] == $row["id_menu"] ) {
							$sub_menu2 = Array();
							// Для каждого подменю ищем его подменю третьего уровня
							foreach ($menu2 as $key_sub2 =>$row_sub2) {
								if ($row_sub2["id_parent_menu"] == $row_sub1["id_menu"] ) 
								$sub_menu2[] = $row_sub2;
							}
							// Если подменю третьего уровня существует - сортируем его и выводим
							if (!empty($sub_menu2)) {
								$row_sub1["sub_menu"] = $sub_menu2;
							}
							$sub_menu1[] = $row_sub1;
						}
					}
					// Если подменю второго уровня существует - сортируем его и выводим
					if (!empty($sub_menu1)) {
						$row["sub_menu"] = $sub_menu1;
					}
					$main_menu[] = $row;
				}
			}
			
			// Преобразовываем массив в формат выпадающего списка в соответствии с необходимым уровнем списка
			$i = 1;
			foreach ($main_menu as $menu) {
				$combo_menu[$i]["id"]= $menu["id_menu"];
				$combo_menu[$i]["v"]= $menu["menu"];
				$i++;
				if ($level > 1 and !empty($menu["sub_menu"])){
					foreach ($menu["sub_menu"] as $sub_menu1) {
						$combo_menu[$i]["id"]= $sub_menu1["id_menu"];
						$combo_menu[$i]["v"]= '&nbsp;&nbsp;&nbsp;'.$sub_menu1["menu"];
						$i++;
						if ($level > 2 and !empty($sub_menu1["sub_menu"])){
							foreach ($sub_menu1["sub_menu"] as $sub_menu2) {
								$combo_menu[$i]["id"]= $sub_menu2["id_menu"];
								$combo_menu[$i]["v"]= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sub_menu2["menu"];
								$i++;
							}
						}
					}
				}
			}
			return $combo_menu;
		}
		
	//url_title
	//приводит имя в пригодное для использования в ссылках
	//на русском языке
}
?>