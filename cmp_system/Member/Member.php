<?php 
	class Member extends Contenter {
		
		// Диалог удаления
		function e_DeleteDlg($id_user) {
			$data['id_user'] = $id_user;
			$this->loadView('modal_delete_dlg', $data);
		}
		
		function e_List() {
			$data['members'] = $this->getList('member', NULL, NULL, NULL, NULL,'name');
			// Определяем текущего пользователя
			$data['id_user_active'] = $this->User->getId();
			$this->loadView('list',$data);		
		}
		
        
        function e_Personal_information($id_person) {
            if (!$this->is_select()) return;
            $data["id_person"] = $id_person;
            $data["view"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_person);
            $this->loadView('view_personal_info',$data);
        }
		
		function e_Profile() {
			$id_user  = $this->User->getId();
			$data['member'] = $this->get_row($id_user);
			$data['user'] = $this->get_row($id_user, 'usr_user');
            $this->loadView('profile',$data);
        }
		
        function e_ViewShort($id_person, $page="") {
            if (!$this->is_select()) return;
            $data["id_person"] = $id_person;
            $data["page"] = $page;
            $data["view"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_person);
            $this->loadView('view_short',$data);
        } 
		
		function getName($id_user = NULL){
			if (empty($id_user))
				$id_user = $this->User->getId();
			if  (!empty($id_user))
				$member = $this->get_row($id_user, "mmbr_member");
			return $member['name'];
		}
		        
		function e_View($id_member, $page = "Взаєморозрахунки") {
			if (!$this->is_select()) return;
			$data["page"] = $page;
			$data["id_person"] = $id_member;
			$this->loadView('view',$data);
		}
		
		function getPersonId($id_user) {
			
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view_by_user_id($id_user);
			return $person['id_person'];
		}
		
		function getPersonPhotoId($id_user) {
			
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view_by_user_id($id_user);
			return $person['id_photo'];
		}
		
		function getUserId($id_person) {
			
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_person);
			return $person["id_owner"];
		}
		
		function getPerson($id_user) {
			
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view_by_user_id($id_user);
			return $person;
		}
		
		// Возвращает идентификатор команды
		function getIdTeam($id_user) {
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view_by_user_id($id_user);
			return $person["id_team"];
		}
		
		function e_MemberDescription($id_member) {
//			$data['view'] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_member);
			
			if (!$this->is_select()) return;
			
			$data['view'] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_member);
			$data["id_person"] = $id_member;
			$this->loadView('member_description',$data);
		}
		
		function e_MemberLink($id_user) {
			if (!$this->is_select()) return;
			
			//$data["view"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view_by_user_id($id_user);
			$this->loadView('member_link', $data);
		}
		
		function e_Delete($id_row) {
		
			if ($this->User->is_user_changed() and $this->User->getId()==$id_row)
				 $this->User->e_RestoreRole();
				 
			// Проверка прав доступа на операцию
			if (!$this->is_delete( $this->get_owner($id_row) )) return false;
			
			//$this->Photo->R(ADMIN)->DeleteObject("Member", $id_row);
			//$this->Video->R(ADMIN)->DeleteObject("Member", $id_row);
			
			//$this->User->Delete($id_row);
			$this->delete_row("usr_user",$id_row);
			$this->delete_row("mmbr_member",$id_row);
			
		}
		
		function Insert($id_user, $role="player", $name="Имя", $surname="Прізвище", $phone = "", $email = "", $birthday = "") {
			// Проверка прав доступа на операцию вставки
			if (!$this->is_insert()) return false;
			
			$id_row = $this->create_row("mmbr_member");
			//$id_main_photo = $this->Photo->R(ADMIN)->e_Insert("Member", $id_row, null, "avatar");
            
			//$this->set_cell("pht_photo", "id_owner", $id_main_photo, $id_user);
			$this->set_cell("mmbr_member","id_photo", $id_row, $id_main_photo);
			$this->set_cell("mmbr_member", "id_user", $id_row, $id_user);
			$this->set_cell("mmbr_member", "id_owner", $id_row, $id_user);
			$this->set_cell("mmbr_member", "name", $id_row, $name);
			$this->set_cell("mmbr_member", "email", $id_row, $email);
			$this->set_cell("mmbr_member", "surname", $id_row, $surname);
			$this->set_cell("mmbr_member", "phone", $id_row, $phone);
			$this->set_cell("mmbr_member", "birthday", $id_row, $birthday);
			
			return $id_row;
		}

		
		function e_Blog($letter) {
				if (is_null($letter)) $letter = "";
				$data['blog_view'] = $this->DBProc->list($letter);
				$this->loadView('blog_view',$data);
		}
		// Функция, формирующая ссылку на этот объект.
		function href($id_user = NULL, $name=null, $surname=null) {
			if (empty($id_user))
				$id_user = $this->User->getId();
			if  (empty($id_user)) return;
			
			if (empty($name) and empty($surname)) {
				$person = $this->get_row($id_user, 'mmbr_member');
				$name = $person["name"];				
				$surname = $person["surname"];
			}
			return "profile/$id_user";
		}
		
		function FIO($id_person) {
			$person = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_person);
			return $person["name"]." ".$person["surname"];
		}
		
		
		function combo_user_list() {
			$member_list = $this->getList('member');
			foreach ( $member_list as $key=>$member) {
				$result[$key]["id"] = $member["id_user"];
				$result[$key]["v"] = $member["surname"]." ".$member["name"];
			}
			return $result;
		}
		
		
		
		// учителя
		
		// Формирует список учителей
		function combo_teacher_list(){
			$member_list = $this->DBProc->teacher_list();
			foreach ( $member_list as $key=>$member) {
				$result[$key]["id"] = $member["id_owner"];
				$result[$key]["v"] = $member["surname"]." ".$member["name"];
			}
			return $result;
		}
}
?>