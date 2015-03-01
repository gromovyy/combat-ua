<?php 

class Tracker extends Contenter {
	
	public function e_Main($id_project = NULL) {
		if (empty($id_user))
			$id_user = $this->User->getId();
		$data['id_user'] = $id_user;
		$data['id_project'] = $id_project;
		$project = $this->Project->get_row($id_project);
		$data['statistics']	= $this->Task->Model->getNewStatistics();
		$data['project_name'] = $project['name'];
		$this->loadView("main", $data);
	}
	
	public function e_Project($id_project, $id_user = NULL) {
		if (empty($id_user))
			$id_user = $this->User->getId();
		$data['id_user'] = $id_user;
		$data['id_project'] = $id_project;
		$data['project'] = $this->get_row($id_project,'prjct_project');
		$this->loadView("project", $data);
	}
	
		
	public function e_WorkTracker($id_user = NULL) {
		if (empty($id_user))
			$id_user = $this->User->getId();
		$data['id_user'] = $id_user;
		$data['periods'] = array();
		$data['works'] = array();
		$this->loadView("work_tracker", $data);
	}
	
	public function e_StartWork($id_task, $id_user = NULL) {
		if (empty($id_task)) return;
		if (empty($id_user)) $id_user = $this->User->getId();
		// Если есть работа в состоянии паузы - переводим в состояние завершена
		$works = $this->Model->getTaskWorks($id_task);
		//file_put_contents('test.txt', print_r($works,true), FILE_APPEND);
		foreach($works['rows'] as $work) {
			if ($work['state'] == 'paused')
				$this->set_cell('trckr_tracker','state', $work['id_tracker'], 'closed');
		}
		
		$id_row = $this->e_Insert();
		$this->set_cell('trckr_tracker','id_task', $id_row, $id_task);
		
		$date = date('Y-m-d H:i:s');
		$this->set_cell('trckr_tracker','work_start', $id_row, $date);
		$this->set_cell('trckr_tracker','id_owner', $id_row, $id_user);
		$this->set_cell('trckr_tracker','state', $id_row, 'open');
		return true;
	}
	
	public function e_StopWork() {
		$comment = $_POST['work'];
		$id_tracker = $_POST['id_tracker'];
		$track = $this->get_row($id_tracker);
		$date = date('Y-m-d H:i:s');
		if (!empty($track)) {
			$this->set_cell('trckr_tracker','work_stop', $id_tracker, $date);
			$this->set_cell('trckr_tracker','comment', $id_tracker, $comment);
			$this->set_cell('trckr_tracker','state', $id_tracker, 'closed');
			
			$this->Attachment->e_UploadMultipleAttachment($id_tracker, 'Tracker');
			//if (!empty($_FILES['attachment']['name']))  $this->e_UploadAttachment($id_tracker);
		}
		// Возвращение на предыдущую страничку.
		ob_get_clean();
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
		return false;
	}
	
	// Пауза
	public function e_PauseWork($id_tracker) {
		$track = $this->get_row($id_tracker);
		$date = date('Y-m-d H:i:s');
		if (!empty($track)) {
			$this->set_cell('trckr_tracker','work_stop', $id_tracker, $date);
			$this->set_cell('trckr_tracker','state', $id_tracker, 'paused');
		}
	}
	
	public function e_AddComment() {
		$this->loadView('add_comment', $data);
	}
	
	
}

?>
