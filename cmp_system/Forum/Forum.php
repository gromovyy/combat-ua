<?php

class Forum extends Contenter
{
	// Отображение заголовок списка секций
	function e_ListTitle() {
		$this->loadView("list_title");
	}
	// Отображение списка секций
	function e_List() {
		$data["forums"] = $this->DBProc->list();
		foreach($data["forums"] as $forum) {
			if (!empty($forum["is_automatic"]))
			$this->SynchronizeForum($forum["id_forum"], $forum["component"], $forum["object"], $forum["field_name1"], $forum["field_name2"]);
		}
		$data["forums"] = $this->DBProc->list();
		$this->loadView("list", $data);
	}
	
	// Отображение заголовка списка комментариев
	function e_TopicListTitle($id_forum) {
		$data["forum"] = $this->get_row($id_forum);
		$this->loadView("topic_list_title", $data);
	}

	// Отображение списка тем
	function e_TopicList($id_forum, $page=1) {
		$count = 100;
		$start_topic = $count*($page - 1);
		$data["forum"] = $this->get_row($id_forum);
		// Если списки форума формируются автоматически - добавляем новые темы форума 
		if (!empty($data["forum"]["is_automatic"]))
			$this->SynchronizeForum($id_forum, $data["forum"]["component"], $data["forum"]["object"], $data["forum"]["field_name1"], $data["forum"]["field_name2"]);
		
		// Получаем актуальный список тем
		$data["topics"] = $this->DBProc->topic_list($id_forum, $start_topic, $count);
		
		//print_r($data["topics"]);
		$this->loadView("topic_list", $data);
	}
	
	// Отображение заголовка списка комментариев
	function e_CommentListTitle($id_topic) {
		$data["topic"] = $this->get_row($id_topic, "frm_topic");
		$data["forum"] = $this->get_row($data["topic"]["id_forum"]);
		$this->loadView("comment_list_title", $data);
	}
	
	// Отображение списка комментариев
	function e_CommentList($id_topic, $page=1) {
		$data["topic"] = $this->get_row($id_topic, "frm_topic");
		$data["forum"] = $this->get_row($data["topic"]["id_forum"]);
		$this->loadView("comment_list", $data);
	}
	
	// Отображени списка последних комментариев
	function e_LastComments() {
        $data['comments'] = $this->DBProc->last_comments();
		$this->loadView("last_comment_list", $data);
	}
	// Функция проверяет, есть ли новые темы. Если есть - добавляет их в форум.
	function SynchronizeForum($id_forum, $component, $object, $field_name1, $field_name2) {
		
		$new_topic_list = $this->DBProc->new_topic_list($id_forum, $component, $object);
		//print_r($new_topic_list);
		
		foreach ($new_topic_list as $new_topic) {
			$this->InsertLinkedTopic($id_forum, $component,$new_topic["id_object"], $object, $field_name1, $field_name2);
		}
	}
	// Добавление пустой секции
	function e_Insert(){
		// Проверяем права на вставку приложения
		if (!$this->is_insert()) return false;
		
		// Добавляем новый форум
		$id_row = $this->create_row("frm_forum");
		return $id_row;
	}
	
	// Удаление форума
	function e_Delete($id_forum) {
		$forum = $this->get_row($id_forum);
		if (empty($forum)) return false;
		if (!$this->is_delete($forum["id_owner"],  $id_forum)) return false;
		$this->delete_row("frm_forum",$id_forum);
		return true;
	}
	
	// Добавление пустой темы
	function e_InsertTopic($id_forum){
		// Проверяем права на вставку приложения
		if (!$this->is_insert_topic($id_forum)) return false;
	
		$id_row = $this->create_row("frm_topic");
		$this->set_cell("frm_topic","id_forum", $id_row, $id_forum);
		
		// Устанавливаем дату последнего комментария в дату создания темы
		$topic = $this->get_row($id_row, "frm_topic");
		$this->set_cell("frm_topic","last_comment_date", $id_row, $topic["create_date"]);
		
		// Устанавливаем привязку для коментариев к этой теме
		$this->set_cell("frm_topic","component", $id_row, "Forum");
		$this->set_cell("frm_topic","id_object", $id_row, $id_row);
		
		return $id_row;
	}
	
	// Добавление связанной темы
	function InsertLinkedTopic($id_forum, $component, $id_object, $object, $field_name1, $field_name2){
		$id_row = $this->create_row("frm_topic");
		$this->set_cell("frm_topic","id_forum", $id_row, $id_forum);
		$this->set_cell("frm_topic","component", $id_row, $component);
		$this->set_cell("frm_topic","id_object", $id_row, $id_object);
		$this->set_cell("frm_topic","object", $id_row, $object);
		$this->set_cell("frm_topic","id_owner", $id_row, 1);
		
		// Копирование имени темы с имени объекта
		$table = $this->$component->getTableByObject($object);
		$row = $this->get_row($id_object, $table);
	/*	if (empty($row[$field_name1])) 
			$name = $row[$field_name2];
		else if (empty($row[$field_name2]))
			$name = $row[$field_name1];
		else
			$name = $row[$field_name1].*/
		$name = trim($row[$field_name1]." ".$row[$field_name2]);
		$this->set_cell("frm_topic","name", $id_row, $name);
		
		return $id_row;
	}
	
	// Удаление темы форума
	function e_DeleteTopic($id_topic) {
		$topic = $this->get_row($id_topic, "frm_topic");
		if (empty($topic)) return false;
		if (!$this->is_delete($topic["id_owner"],  $id_topic, null, "frm_topic")) return false;
		$this->delete_row("frm_topic",$id_topic);
		return true;
	}
	
	// Изменяет источник для тем - ручной или из других компонент
	function e_ChangeTopicSource($id_forum){
		$forum = $this->get_row($id_forum);
		if (empty($forum)) return false;
		if (!$this->is_update($forum["id_owner"],  $id_forum)) return false;
		
		if (!empty($forum["is_automatic"]))
			$this->set_cell("frm_forum","is_automatic",$id_forum,0);
		else
			$this->set_cell("frm_forum","is_automatic",$id_forum,1);
		return true;
	}
	
	// Устанавливает возможность добавления новых тем участниками
	function e_ChangeMemberInsert($id_forum){
		$forum = $this->get_row($id_forum);
		if (empty($forum)) return;
		if (!$this->is_update($forum["id_owner"],  $id_forum)) return false;
		
		if (!empty($forum["is_member_insert"]))
			$this->set_cell("frm_forum","is_member_insert",$id_forum,0);
		else
			$this->set_cell("frm_forum","is_member_insert",$id_forum,1);	
	}
	
	function is_update_topic($id_topic){
	}
	
	// Функция, проверяющая можно ли вставлять тему для данного форума
	function is_insert_topic($id_forum){
		// для незарегистрированных пользователей запрещаем вставлять темы.
		$id_user = $this->User->getId();
		if (empty($id_user)) return false;
		// Проверяем, существует ли такой форум.
		// Если такого форума не существует - выходим:
		$forum = $this->get_row($id_forum);
		if (empty($forum)) return false;
		
		// Если это не администратор, проверяем, можно ли добавлять топики в данном форуме
		if (empty($forum["is_member_insert"]) and $this->User->getRole() !="administrator") return false;
		// если две темы сегодня пользователь уже создал, добавлять тем больше нельзя!
		if ($this->User->getRole() != "administrator") {
			$new_topic_count = $this->DBProc->O(array('extC' => true, 'extR' => true))->new_topic_count($this->User->getId());
			if ($new_topic_count > 1) return false;
		}
		return true;
	}
	
	// Функция, формирующая ссылку на этот объект.
	function href($id_forum, $name=null) {
			if (empty($name)) {
				$forum = $this->get_row($id_forum);
				$name = $forum["name"];				
			}
			return "Тема/$id_forum/".$this->getUrlEncoded($name);
	}
	
	// Функция, формирующая ссылку на список комментариев.
	function href_topic($id_topic, $name=null) {
			if (empty($name)) {
				$topic = $this->get_row($id_topic, "frm_topic");
				$name = $topic["name"];				
			}
			return "Коментарі/$id_topic/".$this->getUrlEncoded($name);
	}
}