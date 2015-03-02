<?php
class Bear extends Contenter {
	public function e_Test(){
		$data['bears'] = $this->Model->get_bears();
		$this->loadView('bear_list', $data);
	}
	
}
?>