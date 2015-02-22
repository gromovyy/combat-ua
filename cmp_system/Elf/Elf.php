<?php
class Elf extends Contenter {
	public function e_Test(){
		$data['elfs'] = $this->Model->get_elfs();
		$this->loadView('elf_list', $data);
	}
	
}
?>