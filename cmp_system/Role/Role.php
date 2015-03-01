<?php 
	class Role extends Contenter {
	
	function getRoles() {
		return $this->DBProc->list();
	}
	function e_List() {
			$data['roles'] = $this->DBProc->list();
			$this->loadView('list',$data);
		}
	function e_Delete_role($id_role) {
			$r = $this->DBProc->role($id_role);
			$role=$r[0]["role"];
			$this->delete_row("rl_role",$id_role);
			$rs = $this->Rule->e_rules();
			foreach ($rs as $row) 
				$rules[$row["component"]][$row["view"]]=$row;
			if(isset($rules)) {
				foreach ($rules as $component => $views) {
					foreach ($views as $view => $d) {
						if($d["role"]==$role) {
						$this->delete_row("rl_view_rule",$d["id_view_rule"]);}
					}
				}
			}
		}
	function e_Insert_role() {
			$id_row = $this->create_row("rl_role");
			$r = $this->DBProc->role($id_row);
			$role=$r[0]["role"];
			$rs = $this->Rule->e_rules();
			foreach ($rs as $row) 
				$rules[$row["component"]][$row["view"]]=0;
			if(isset($rules)) {
				foreach ($rules as $component => $views) {
					foreach ($views as $view => $d) {
						$id_row = $this->create_row("rl_view_rule");
						$this->set_cell("rl_view_rule","role",$id_row,$role);
						$this->set_cell("rl_view_rule","access",$id_row,$d);
						$this->set_cell("rl_view_rule","component",$id_row,$component);
						$this->set_cell("rl_view_rule","view",$id_row,$view);
					}
				}
			}
		}
	}
?>