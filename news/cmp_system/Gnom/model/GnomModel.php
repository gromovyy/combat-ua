<?php 
	class GnomModel extends DBProc {

	public function get_gnoms() {
		
		 $params = array ( 'tables' => array('gnm_gnom'),
						  'fields' => array('*')
						);
		
		 $result = $this->Select($params);
		 //print_r($result);
		 return $result['rows'];
		}
		
}