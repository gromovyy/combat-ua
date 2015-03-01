<?php 
	class BearModel extends DBProc {

	public function get_bears() {
		
		 $params = array ( 'tables' => array('br_bear'),
						  'fields' => array('*')
						);
		
		 $result = $this->Select($params);
		 //print_r($result);
		 return $result['rows'];
		}
		
}