<?php 
	class AttachmentModel extends DBProc {
	
	// Возвращает все задачи заданного проекта
	public function getAttachments($component, $id_object) {
		$params = array ( 'tables' => array('a'=>'attchmnt_attachment'),
						  'fields' => array('a.*'),
						  'where' =>  array (
									'operator'=>'AND',
									array( 
									'field'=>'a.component',
									'operator' => '=',
									'value' => $component),
									array( 
									'field'=>'a.id_object',
									'operator' => '=',
									'value' => $id_object)
						));

		$result_tasks = $this->Select($params);
		return $result_tasks;
	}
}

?>