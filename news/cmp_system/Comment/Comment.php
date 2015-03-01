<?php
      class Comment extends Contenter {
      
	  function e_List($component,$id_object){
          $data['component']=$component;
          $data['id_object']=$id_object;
          $data['comments'] = $this->DBProc->list($component,$id_object);
          $this->loadView('list',$data);

      }
	  
	  function e_CommentList($component,$id_object){
          $data['component']=$component;
          $data['id_object']=$id_object;
          $data['comments'] = $this->DBProc->list($component,$id_object);
          $this->loadView('comment_list',$data);

    }
		
		function e_ModuleComments($id_module){
          $data['id_module']=$id_module;
					$comments = array();
					$result = $this->DBProc->module_list($id_module);
					///echo'<pre>';
					//print_r($result);
					// Формируем список родительских коментариев
					foreach ($result as $comment){
						if ($comment['id_parent'] == 0){
							$comments[$comment['id_comment']] = $comment;
						}
					} 
					// Добавляем к родительским елементам детишек
					foreach($comments as $key=>$value){
						foreach ($result as $comment){
							if ($comment['id_parent'] == $key){
								$comments[$key]['children'][] = $comment;
							}
						}
						if(!empty($comments[$key]['children'])){krsort($comments[$key]['children']);}
					}
					$data['students'] = $this->Module->getList('module_result',null,'Module',$id_module);
					$data['comments'] = $comments;
					
          $this->loadView('comment_list_teacher',$data);
		}		
		function e_StudentComments($id_module_result){
					$module_result_row = $this->get_row($id_module_result,'mdl_module_result');
					$id_module = $data['id_module'] = $module_result_row['id_module'];
					$id_user = $data['id_user'] = $module_result_row['id_owner'];
					$comments = array();
					$data['id_module_result'] = $id_module_result;
					$result = $this->DBProc->list_user('Module',$id_module,$id_user);
					// Формируем список родительских коментариев
					foreach ($result as $comment){
						if ($comment['id_parent'] == 0){
							$comments[$comment['id_comment']] = $comment;
						}
					} 
					// Добавляем к родительским елементам детишек
					foreach($comments as $key=>$value){
						foreach ($result as $comment){
							if ($comment['id_parent'] == $key){
								$comments[$key]['children'][] = $comment;
							}
						}
						if(!empty($comments[$key]['children'])){krsort($comments[$key]['children']);}
						
					}
					$data['comments'] = $comments;
          $this->loadView('comment_list_student',$data);
		}
		
		
    // Модификация для it-university
		// @param $user - какому пользователю предназначен коментарий
		// для студента его собственный id для учителя id ученика которому он отвечает
 	  function e_Insert($component, $id_object, $id_user, $id_parent = 0) {
		  $text  = strip_tags($_POST['text']);
          if(!$this->is_insert()) return;
					if($id_user == 'all'){
						$this->Send($component, $id_object,$text);
						return;
					}
		  if (empty($component) or empty($id_object)) return;
			
				$id_row = $this->create_row("cmmnt_comment");
          $this->set_cell("cmmnt_comment","text",$id_row,$text);
          $this->set_cell("cmmnt_comment","id_user",$id_row,$id_user);
          $this->set_cell("cmmnt_comment","component",$id_row,$component);
          $this->set_cell("cmmnt_comment","id_object",$id_row,$id_object);
          $this->set_cell("cmmnt_comment","id_parent",$id_row,$id_parent);
					// Обновим родительский елемент
					if(!empty($id_parent)){
						$this->set_cell("cmmnt_comment","update_date",$id_parent,date("Y-m-d H:i:s"));
					}
      } 
			
		function Send($component, $id_object,$text){
			$students = $this->Module->getList('module_result',null,'Module',$id_object);
			foreach($students as $student){
			
				$id_row = $this->create_row("cmmnt_comment");
        $this->set_cell("cmmnt_comment","text",$id_row,$text);
        $this->set_cell("cmmnt_comment","id_user",$id_row,$student['id_owner']);
        $this->set_cell("cmmnt_comment","component",$id_row,$component);
        $this->set_cell("cmmnt_comment","id_object",$id_row,$id_object);
			}
		}
		
		function e_AllComments(){
			$result = $this->getList('comment');
			foreach ($result as $comment){
				if ($comment['id_parent'] == 0){
					$comments[$comment['id_comment']] = $comment;
				}
			} 
					// Добавляем к родительским елементам детишек
					foreach($comments as $key=>$value){
						foreach ($result as $comment){
							if ($comment['id_parent'] == $key){
								$comments[$key]['children'][] = $comment;
							}
						}
					}
					krsort($comments);
					$data['comments'] = $comments;
			$this->loadView('comment_list',$data);
		}
			
			
	  // Удаление комментария
	  function e_Delete($id_comment) {
		  $row = $this->get_row($id_comment);
		  if(empty($row) or !$this->is_delete(null, $id_comment)) return false;
		  $this->delete_row("cmmnt_comment",$id_comment);
		  return true;
	  
	  }
		function AfterUpdate($table, $row_id, $cell_id, $value, $old_value){
		}
}
?>