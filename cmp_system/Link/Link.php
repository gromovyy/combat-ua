<?php
class Link extends Contenter{
	
	// Создаём новую ссылку
	function e_Insert(){
		// Проверка прав доступа на операцию вставки
		if (!$this->is_insert()) return false;

		$id_row = $this->create_row("lnk_link");	
		return $id_row;
	}
	
	// Удаление сссылки
	function e_Delete($id_link){
			// Проверка прав доступа на операцию
			if (!$this->is_delete(null, $id_link)) return false;

			$this->delete_row("lnk_link",$id_link);
			return true;
	}
	
	// Вывод всех ссылок списком
	function e_List(){
		$data['list'] = $whis->DBProc->list();
		
		
		$this->loadView('list',$data);
	}
}
?>