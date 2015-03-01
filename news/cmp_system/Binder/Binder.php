<?php
	// Общий класс для копонентов, которым необходима связь многие ко многим с другими объектами.
	// Особенностью этих компонентов есть то, что их редактирование зависит от  
	// возможности редактирования родительских объектов, в которые они внедрены
	// В этих компонентах должна быть предусмотрена таблица c префиксом bind,
	// Которая содержит поля родительского объекта $component, $object, $id_object
class Binder extends Contenter {

	// Возваращает имя таблицы связи
	private function getBindTable($object=null) {
		return $this->getTableByObject($object)."_bind";
	}
	
	// Функция формирует Toolbox для работы с компонентами
	// $component_parent, $object_parent, $id_parent - название компонента, объекта и идентификатора экземпляра
	// $tool_list - список кнопок на панели инструментов.
	// Возможные значение массива $tool_list :(delete, edit, visible, invisible, down, up, left, right, add, select)
	
	protected function loadToolBoxBind($component_bind, $object_bind='', $id_object_bind, $tool_list, $update_link, $id_bind=NULL){
		$data['component'] = $this->component;
		$data['component_bind'] = $component_bind;
		$data['object_bind'] = $object_bind;
		$data['id_object_bind'] = $id_object_bind;
		$data['id_bind'] = $id_bind;
		$data['tool_list'] = $tool_list;
		$data['update_link'] = $update_link;
		// получаем айди ячейки используя айди в кеше		
		$this->loadView("toolbox_bind", $data, "Binder");
	}
	
	// Функція додавання нового зв'язку
	public function e_InsertBind($component_bind, $id_object_bind, $object_bind='', $id_object_array=Array() , $extra = null){
		
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		
		// Проверяем - если нужна пустая связь без ссылки на объект, создаем её
		if (empty($id_object_array)) {
			// Создаем новую запись в таблице связи
			$id_row = $this->create_row($bind_table);

			$this->set_cell($bind_table,"component", $id_row, $component_bind);
			$this->set_cell($bind_table,"id_object",$id_row,$id_object_bind);
			$this->set_cell($bind_table,"object", $id_row, strtolower($object_bind));
		}
		// Если нужна связь со ссылкой на объект, или список объектов, создаем её
		else {
			$component = strtolower($this->component);
			// Если в $id_object_array поступает одно значение, преобразуем его в массив
			if (!is_array($id_object_array)) $id_object_array = array($id_object_array);
			
			// Поочередно связываем все идентификаторы детей с родителем.
			foreach ($id_object_array as $id_object) {
			
				// Проверяем, существует ли уже такая связь между этими объектами. 
				// Если существует - завершаем процедуру связывания.
				$bind = $this->DBProc->get_bind($bind_table, $component_bind, $object_bind, $id_object_bind, "id_$component", $id_object);
				if (!empty($bind)) continue;
				// Если такой связи нет - создаем её
        
        $row = $this->get_row($id_object);
        // Если указаного обекста не существует создаем новый и делаем связь с ним
        if (empty($row)){
        	$id_object = $this->create_row($this->getTableByObject());
        }
				$id_row = $this->create_row($bind_table);
				$this->set_cell($bind_table,"component", $id_row, $component_bind);
				$this->set_cell($bind_table,"id_object",$id_row,$id_object_bind);
				$this->set_cell($bind_table,"object", $id_row, strtolower($object_bind));
				$this->set_cell($bind_table,"id_$component", $id_row, $id_object);
			}
		}
		
		// Возвращаем последний связанный объект		
		return $id_row;
	}
	
	// Функція видалення об'єкту
	public function e_DeleteBind($id_row){
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		//if (!$this->is_delete(null, $id_row)) return false;

		$bind_object = $this->get_row($id_row, $bind_table);
		if (!empty($bind_object)) 
				$this->delete_row($bind_table,$id_row);
		return true;
	}
	
	// Функція зміни видимості об'єкту
	public function e_ShowBind($id_bind){
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		//if (!$this->is_delete(null, $id_row)) return false;

		$bind_object = $this->get_row($id_bind, $bind_table);
		if (!empty($bind_object)) {
				if (empty($bind_object['is_visible'])) 
					$this->set_cell($bind_table,"is_visible", $id_bind, 1);
				else 
					$this->set_cell($bind_table,"is_visible", $id_bind, 0);
				return true;
			}
		return "В таблице $bind_table связи с идентификатором $id_bind не существует";
	}
	
	//Сдвиг объекта на одну позицию влево
	function e_MoveLeftBind($id_row)
	{
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		//if (!$this->is_delete(null, $id_row)) return false;
		
		// Сдвигаем запись влево
		$this->DBProc->move_left($bind_table, $id_row);
		return true;
	}
	
		
	//Сдвиг объекта на одну позицию вправо	
	function e_MoveRightBind($id_row)
	{
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		//if (!$this->is_delete(null, $id_row)) return false;
		
		// Сдвигаем запись влево
		$this->DBProc->move_right($bind_table, $id_row);
		return true;
	}
	
	// Функция обновляет в связи ссылку на объект 
	// Параметры: 
	// $component_parent, $object_parent, $id_parent - название компонента, объекта и идентификатора родителя
	// $component_child, $object_child - название компонента и объекта ребенка.
	// $id_child - идентификатор ребенка или обычный индексированный массив с идентификаторами детей.
	// $value - значение связи (вес) - необязательно
	// $type - тип связи - необязательно
	public function e_Bind($id_bind, $id_object) {
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		echo $bind_table;
		// Проверяем, есть ли такая связь
		$bind = $this->get_row($id_bind, $bind_table);
		if (!empty($bind)) {
			// Если связь существует, устанавливаем связь между объектами
			$component = $this->component;
			$this->set_cell($bind_table,"id_$component", $id_bind, $id_object);
		}
		return $id_bind;	
	}
	
	// Функция возвращает список всех детей родителя
	// Параметры: 
	// $component_bind, $object_bind, $id_object_bind - название компонента, объекта и идентификатора родителя
	protected function getBindList($component_bind, $object_bind, $id_object_bind) {
		if (empty($id_object_bind) or empty($component_bind)) return false;
		
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		$component_table = $this->getTableByObject();  
		
		//Получаем список
		$bind_list = $this->DBProc->list($bind_table, $component_bind, $object_bind, $id_object_bind, $component_table);
		return $bind_list;
	}
	
	// Функция возвращает запись по связи
	// Параметры: 
	// $component_bind, $object_bind, $id_object_bind - название компонента, объекта и идентификатора родителя
	protected function getBindView($id_bind) {
		if (empty($id_bind)) return false;
		
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
		
		$component_table = $this->getTableByObject(); 
	//	file_put_contents('test.txt', "$bind_table.$component_table", FILE_APPEND);
		//Получаем список
		$bind_view = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($bind_table, $component_table, $id_bind);
		return $bind_view;
	}
	
	// Функция возвращает порядковый номер ребенка родителя
	// Параметры: 
	// $component_parent, $object_parent, $id_parent - название компонента, объекта и идентификатора родителя
	// $component_child, $object_child, $id_child - название компонента, объекта и идентификатора ребенка.
	protected function getBindOrder($id_bind) {
		// Получаем таблицу связи
		$bind_table = $this->getBindTable();
	
		//Получаем порядковый номер
		$child_order = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_bind_order($bind_table, $id_bind);
		return $child_order;
	}
	
	// Функция запускает обновление состояни родительского объекта
	public function updateBindState($id_bind){
			$bind_row = $this->getBindView($id_bind);
			$component = $bind_row['component_bind'];
			$object = $bind_row['object_bind'];
			$id_object = $bind_row['id_object_bind'];
		//	file_put_contents('test.txt', "$component.$object.$id_object", FILE_APPEND);
			$this->$component->setState($object, $id_object);
	}

	public function e_SelectDlg($value='')
	{
		# code...
	}
	public function is ($rule = 'select', $id_owner = null, $id_row = null, $table = null)
	{
		$result = parent::is($rule , $id_owner, $id_row, $table);
		// Если недоступно.
		$id_user = $this->User->getId();
		if (!$result) {
			$function = "is_$rule";
			$binds = $this->getAllBinds($this->getObjectByTable($table),$this->getObjectByTable($table),$id_row);
			if(is_array($binds)){
				foreach ($binds as $row) {
				// Проверяем доступ у родительского компонента.
				if(!empty($row['component'])){
					$result = $this->$row['component']->$function($id_user, $row['id_object'],$this->$row['component']->getTableByObject($row['object']));
				}
				if($result != false) { break; }
				}
			}
		}
		// Проверяем
		return $result;
	}
	public function getAllBinds($object,$column,$id_binded)
	{
		$table = $this->getBindTable($object);
		$column = 'id_'.$this->getObjectByTable($this->getTableByObject($column));
		$id = $id_binded;
		return $this->DBProc->get_all_binds($table,$column,$id);
	}
}
?>