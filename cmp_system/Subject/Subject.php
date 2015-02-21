<?php
	// Общий класс для копонентов, которые зависят от других объектов.
	// Особенностью этих компонентов есть то, что их редактирование зависит от  
	// возможности редактирования родительских объектов, в которые они внедрены
	class Subject extends Contenter {
	
	// Переопределяем функцию іs_update базового класса Base 

	function is_update($id_owner = null, $id_row = null, $column = null, $table = null) {
		// Если текущий пользователь имеет Администраторкие права на обновление, положительный результат
		if ($this->getR("update")==1) return true;
		// Если не задан идентификатор ряда, отрицательный результат
		if (empty($id_row)) return false;
		// Если не задан идентификатор пользователя, получаем его.
		if (empty($id_owner))
			   $id_owner = $this->get_owner($id_row, $table);
		$row = $this->get_row($id_row, $table);
		// Если приложения не найдено, отрицательный результат
		if (empty($row)) return false;
		
		$component = $row["component"];
		// Если объект не привязан ни к какому компоненту, проверяем права как для обычных компонентов
		if (empty($component) and ($this->getR("update")==2) and ($this->User->getId() == $id_owner)) return true;
		
		// Если родительский компонент задан, проверям, можно ли его редактировать. 
		$table = $this->$component->getTableByObject($row["object"]);
		if (($this->getR("update")==2) and 
		    ($this->User->getId() == $id_owner) and 
			 $this->$component->is_update($id_owner, $row["id_object"], null, $table))
			return true;
		return false;
	}

// Переопределяем функцию іs_delete базового класса Base 
// В этой версии функция также осуществляет проверку возможности обновления по конкретному ряду.

	public function is_delete($id_owner=null, $id_row=null, $table = null) {
		// Если текущий пользователь имеет Администраторкие права на обновление, положительный результат
		if ($this->getR("delete")==1) return true;
		// Если не задан идентификатор ряда, отрицательный результат
		if (empty($id_row)) return false;
		// Если не задан идентификатор пользователя, получаем его.
		if (empty($id_owner))
			   $id_owner = $this->get_owner($id_row, $table);
		$row = $this->get_row($id_row, $table);
		// Если приложения не найдено, отрицательный результат
		if (empty($row)) return false;
		
		$component = $row["component"];
		// Если объект не привязан ни к какому родительскому компоненту, проверяем права как для обычных компонентов
		if (empty($component) and ($this->getR("delete")==2) and ($this->User->getId() == $id_owner)) return true;
		
		// Если родительский компонент задан, проверям, можно ли его редактировать. 
		//echo $component;
		$table = $this->$component->getTableByObject($row["object"]);
		if (($this->getR("delete")==2) and 
		    ($this->User->getId() == $id_owner) and 
			 $this->$component->is_update($id_owner, $row["id_object"], null, $table))
			return true;
		return false;
	}
	
	}
?>