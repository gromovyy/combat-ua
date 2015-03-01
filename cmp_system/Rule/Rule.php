<?php 
	class Rule extends Contenter {
		/** 
			* Переменная задает массив правил для авторизации
		*/
		protected $rule_array;
		
		function getRules($role) {
			$data = $this->DBProc->get_rules($role);
			$rule_array = Array();
			
			foreach($data as $row) {
				$rule_array[$row["component"]][$row["view"]] = $row["access"];
			}
			$this->rule_array = $rule_array;
			
		}
		
		function e_List() {
				$rule = $this->DBProc->rule_table();
				foreach ($rule as $row)
					$rules[$row["component"]][$row["view"]][$row["role"]] = $row;
				$data["rules"] = $rules;
				$this->loadView('list',$data);
			}

		
		function getCmpRules($role) {
			$data = $this->DBProc->get_cmp_rule($role);
			$cmp_rule_array = Array();
			foreach($data as $row) {
				$cmp_rule_array[$row["component"]]["select"]     = $row["select"];
				$cmp_rule_array[$row["component"]]["insert"]     = $row["insert"];
				$cmp_rule_array[$row["component"]]["update"]     = $row["update"];
				$cmp_rule_array[$row["component"]]["delete"]     = $row["delete"];
				$cmp_rule_array[$row["component"]]["visibility"] = $row["visibility"];
			}
			self::$cmp_rules = $cmp_rule_array;
			return true;
		}
		
		function e_ListCmp() {
				$rule = $this->getList('rule');
				foreach ($rule as $row) {
					$cmp_rule_array[$row["component"]][$row["role"]]["select"]      = $row["select"];
					$cmp_rule_array[$row["component"]][$row["role"]]["insert"]      = $row["insert"];
					$cmp_rule_array[$row["component"]][$row["role"]]["update"]      = $row["update"];
					$cmp_rule_array[$row["component"]][$row["role"]]["delete"]      = $row["delete"];
					$cmp_rule_array[$row["component"]][$row["role"]]["visibility"]  = $row["visibility"];
					$cmp_rule_array[$row["component"]][$row["role"]]["id_rule"]     = $row["id_rule"];
				}
					//$rules[$row["component"]][$row["view"]][$row["role"]] = $row;
				// print_r($cmp_rule_array);
				$data["rules"] = $cmp_rule_array;
				$this->loadView('list_cmp',$data);
			}
			

			
		function e_rules() {
				$rules = $this->DBProc->rule_table();
				return $rules;
			}

		function checkView($component, $view) {
			if (!isset($this->rule_array[$component][$view])) {
				$this->e_Insert($component,$view);
				return true;
			}
			if ($this->rule_array[$component][$view] == 0) {
				return false;
			} else {
				return true;
			}
			
		}
		
		function checkAccess($role,$component,$operation) {
			
		}
		function getRuleArray() {
			return $this->rule_array;
		}
		
		function e_Insert($component,$view){
			$roles = $this->Role->getRoles();
			foreach ($roles as $role) {
				$id_row = $this->create_row("rl_view_rule");
				$this->set_cell("rl_view_rule","role", $id_row, $role["role"]);
				$this->set_cell("rl_view_rule","component", $id_row, $component);
				$this->set_cell("rl_view_rule","view", $id_row, $view);	
			}			
		}
		
		// Добавление компонента в таблицу компонентов
		
		function insertCmpRule($component){
			$roles = $this->Role->getRoles();
			foreach ($roles as $role) {
				$id_row = $this->create_row("rl_rule");
				$this->set_cell("rl_rule","role", $id_row, $role["role"]);
				$this->set_cell("rl_rule","component", $id_row, $component);
			}			
		}
	}
?>