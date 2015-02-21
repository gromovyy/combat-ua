<?php 
class TrackerModel extends DBProc {

	public function getTaskWorks($id_task) {
		$params = array ( 'tables' => array('tr'=>'trckr_tracker'),
						  'fields' => array('tr.*'),
						  'where' => array( 
									'field'=>'tr.id_task',
									'operator' => '=',
									'value' => $id_task)
						);
		return $this->Select($params);
	}
	
}

?>