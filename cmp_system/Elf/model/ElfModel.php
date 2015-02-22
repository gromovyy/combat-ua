<?php 
	class ElfModel extends DBProc {

	public function get_elfs() {
		
		 $params = array ( 'tables' => array('elf_elf'),
						  'fields' => array('*')
						);
		
		 $result = $this->Select($params);
		 //print_r($result);
		 return $result['rows'];
		}
		
}