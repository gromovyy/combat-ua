<?php
class Task extends Contenter {

	// Основная функция, отображающая список задач
	public function e_List($tasks, $id_page) {	
		if (empty($tasks['rows'])) $tasks['rows'] = array();
		$data['tasks'] = $tasks['rows'];
		$data['tasks_in_work'] = $this->Model->getTasksInWork();
		$data['is_user_in_work'] = $this->isUserInWork($data['tasks_in_work']);
		$data['is_show_closed'] = $this->Model->is_show_closed;
		$data['combo_projects'] = $this->Project->get_combo_projects();
		$data['combo_members'] = $this->Member->combo_user_list();
		$data['limit_count'] = $this->Model->getLimitCount();
		$data['order_field'] = $this->Model->order_field;
		$data['order_direction'] = ($this->Model->order_direction == "DESC")?'down':'up';
		$data['id_page'] = $id_page;
		$data['tasks_count'] = $tasks['num_rows'];
		//echo "limit_count".$data['limit_count'];
		$this->loadView("list", $data);
	}
	
	public function e_isUpdateTask($id_task_max, $id_project){
		
	}
	

	
	
	// Возвращает true если текущий пользователь занят работой и false, если свободен
	public function isUserInWork($tasks_in_work){
		$id_user = $this->User->getId();
		foreach($tasks_in_work as $work){
			if ($work['id_owner'] == $id_user and $work['state'] == 'open') return true;
		}
		return false;
	}
	
	
	
	// Отображает полный список задач
	public function e_FullList($id_project = NULL) {
		$id_page = $_GET['page'];
		//$tasks = $this->Model->getTasks($id_page, $id_user);
		$state = ($this->Model->is_show_closed)? array("closed"): array("control","open");
		$id_user = $this->User->getId();
		$tasks = $this->Model->getAllTasks('all', $state, $id_page, $id_project, $id_user);
		
		$data['id_user'] = $id_user;
		$data['id_page'] = $id_page;
		$data['tasks'] = $tasks;
		//echo $this->Model->is_show_closed.'CLOSED';
		$data['is_show_closed'] = $this->Model->is_show_closed;
		$this->loadView('full_list', $data);
	}
	
	// Отображает полный список временных затрат пользователя
	public function e_TimeList($id_project = NULL) {
		if (empty($id_user))
			$id_user = $this->User->getId();
			
		// Если не задана - устанавливаем дату сегодняшнюю в качестве окончания временного фильтра
		$data['date_to'] = (empty($_POST['date_to']))? date('Y-m-d') :$_POST['date_to'];
		
		// Если не задана - Устанавливаем дату за 30 дней до текущей даты в качестве начала временного филььтра
		if (empty($_GET['date_from'])) {
			$date_from_obj = new DateTime(date('Y-m-d'));
			$interval_30_days = new DateInterval('PT0S');
			$interval_30_days->d = 30;
			$date_from_obj->sub($interval_30_days);
			$date_from  = $date_from_obj->format('Y-m-d');
		}
		else 
			$date_from = $_POST['date_from'];
		$data['date_from'] = $date_from;
		
		$days = $this->Model->getTimeList($id_user, $id_project, $data['date_from'], $data['date_to'] );
		// print_r($days);
		$data['days'] = $days;
		$this->loadView('time_list', $data);
	}
	
	// Отображает полный список временных затрат пользователя
	public function e_ProjectTimeList($id_project = NULL) {
		if (empty($id_user))
			$id_user = $this->User->getId();
			
		// Если не задана - устанавливаем дату сегодняшнюю в качестве окончания временного фильтра
		$data['date_to'] = (empty($_POST['date_to']))? date('Y-m-d') :$_POST['date_to'];
		
		// Если не задана - Устанавливаем дату за 30 дней до текущей даты в качестве начала временного филььтра
		if (empty($_GET['date_from'])) {
			$date_from_obj = new DateTime(date('Y-m-d'));
			$interval_30_days = new DateInterval('PT0S');
			$interval_30_days->d = 30;
			$date_from_obj->sub($interval_30_days);
			$date_from  = $date_from_obj->format('Y-m-d');
		}
		else 
			$date_from = $_POST['date_from'];
		$data['date_from'] = $date_from;
		
		$data['projects'] = $this->Model->getProjectTimeList($id_user, $id_project, $data['date_from'], $data['date_to'] );
		$this->loadView('project_time_list', $data);
	}
	
	public function e_ControlList($id_project = NULL) {
		$id_page = $_GET['page'];
		$id_user = $this->User->getId();
	
		$state = ($this->Model->is_show_closed)? array("closed"): array("control", "open");
		$tasks = $this->Model->getAllTasks('controller', $state, $id_page, $id_project, $id_user);
		//$tasks = $this->Model->getControlTasks($id_user, $id_page);
		
		$data['id_user'] = $id_user;
		$data['id_page'] = $id_page;
		$data['tasks'] = $tasks;
		$data['is_show_closed'] = $this->Model->is_show_closed;
		$this->loadView('control_list', $data);
	}
	
	public function e_UserList($id_project = NULL) {
		$id_page = $_GET['page'];
		$id_user = $this->User->getId();
		
		$state = ($this->Model->is_show_closed)? array("control", "closed"): array("open");
		$tasks = $this->Model->getAllTasks('worker', $state, $id_page, $id_project, $id_user);
		// $tasks = $this->Model->getMyTasks($id_user, $id_page);
		
		$data['id_user'] = $id_user;
		$data['id_page'] = $id_page;
		$data['tasks'] = $tasks;
		$data['is_show_closed'] = $this->Model->is_show_closed;
		$this->loadView('user_list', $data);
	}
/*	
	public function e_ProjectList($id_project, $id_user = NULL) {
		//echo "ID_project $id_project";
		$id_page = $_GET['page'];
		if (empty($id_user))
			$id_user = $this->User->getId();
			
		$state = ($this->Model->is_show_closed)? "closed": "open";
		$tasks = $this->Model->getAllTasks('control', $state, $id_page, $id_project, $id_user);
		$tasks = $this->Model->getProjectTasks($id_project, $id_page, $id_user);
		$data['id_user'] = $id_user;
		$data['id_page'] = $id_page;
		$data['tasks'] = $tasks;
		$data['id_project'] = $id_project;
		$data['is_show_closed'] = $this->Model->is_show_closed;
		$this->loadView('project_list', $data);
	}
*/
	// Выводит пагинацию
	public function Pagination($tasks_count, $id_page) {
		$i = 0;
		$limit_count = $this->Model->getLimitCount();
		while ($tasks_count > 0) {
			$data['pages'][] = ++$i;
			$tasks_count -= $limit_count;
		}
		$data['current_page'] = $id_page;
		//print_r($data);
		$this->loadView('pagination', $data);
	}
	
	// Устанавливает количество задач на странице
	public function e_SetTaskOnPage($task_on_page){
		if (is_numeric($task_on_page) and $task_on_page > 0) 
			$this->Model->setLimitCount($task_on_page);
	}
	
	// Устанавливает количество задач на странице
	public function e_SetShowClosed($is_show_closed){
		if (is_numeric($is_show_closed)) 
			$this->Model->setIsShowClosed($is_show_closed);
	}
	
	// Устанавливает сортировку
	public function e_SetTaskOrder($field){
		$this->Model->setTaskOrder($field);
	}
	
	// Форма для добавления и редактирования задания
	public function e_ModalForm($id_task = NULL, $id_project = NULL){
	
		// Если задача уже существует, получаем все её данные
		if (!empty($id_task)) {
			$data['task'] = $this->get_row($id_task);
			if (!empty($data['task']['id_attachment']))
				$data['attachment'] = $this->get_row($data['task']['id_attachment'], 'attchmnt_attachment');
		}
		else {
			// Устанавливаем начальные значения дат при новой задаче
			$data['task']['start_date'] = date('Y-m-d H:i');
			//print_r($data['task']['start_date']);
			$date_from_obj = new DateTime(date('Y-m-d H:i'));
			$interval_2_days = new DateInterval('PT0S');
			$interval_2_days->d = 2;
			$date_from_obj->add($interval_2_days);
			$data['task']['finish_date']  = $date_from_obj->format('Y-m-d H:i');
			$data['task']['id_owner'] = $this->User->getId();
			$data['task']['id_project'] = $id_project;
		}
		$data['combo_projects'] = $this->Project->get_combo_projects($id_user);
		$data['combo_members'] = $this->Member->combo_user_list();
		// Сохраняем данные списка для передачи на клиент в JSON формате
		$data['task']['task-modal-label'] = 'Новая задача';
		$data['task']['file-name'] = 'Файл не выбран';
		
		self::$jsData['form']['task-modal-form'] = $data['task'];
		
		$this->loadView('task_modal_form',$data);
	}
	
	public function e_getTask($id_task){
		$task = $this->get_row($id_task);
		if (!empty($task['id_attachment'])) {
			$attachments = $this->Attachment->Model->getAttachments('Task', $id_task);
			$task['attachments'] = $attachments['rows'];
			//$task['file-name'] = $attachment['title'];
		}
		$task['task-modal-label'] = 'Редактирование задачи';
		echo json_encode($task);
		exit;
	}
	
	// Получает статистику новых задач
	public function e_getWorks($id_task){
		$data['task'] = $this->get_row($id_task);
		$works = $this->Model->getWorks($id_task);//get_row($id_task);
		$data['task']['works'] = $works['rows'];
		$this->loadView('task_works', $data);
	}	
	
	// Получает статистику новых задач
	public function e_getNewStatistics(){
		$statistics = $this->Model->getNewStatistics();//get_row($id_task);
		echo json_encode($statistics);
		exit;
	}
	
	public function e_ProjectOwnerTask($id_project_owner = NULL) {
		$data['tasks'] = $this->getList('task', NULL, 'Project', $id_project);
		
		$data['project'] = $this->get_row($id_project, 'prjct_project');
		$data['id_project'] = $id_project;
		$data['combo_projects'] = $this->Project->get_combo_projects();
		$data['combo_members'] = $this->Member->combo_user_list();
		
		$this->loadView("list", $data);
	}
	
	public function e_Insert(){
		$id_row = parent::e_Insert();
		$row = $this->get_row($id_row, 'tsk_task');
		$start_date = $row['create_date'];
		$this->set_cell('tsk_task','start_date', $id_row, $start_date);
		return $id_row;
	}
	
	public function e_InsertProject($id_project) {
		$id_row = $this->e_Insert();
		//$this->set_cell('tsk_task','id_project', $id_row, $id_project);
		if (!empty($id_project)) {
			$project = $this->get_row($id_project, 'prjct_project');
			if (!empty($project))
				$this->set_cell('tsk_task','id_project', $id_row, $id_project);
		}
		return $id_row;
	}
	
	public function e_InsertControl($id_user = NULL) {
		$id_row = $this->e_Insert();
		if (empty($id_user)) $id_user = $this->User->getId();
		$this->set_cell('tsk_task','id_owner', $id_row, $id_user);
		return $id_row;
	}
	
	public function e_InsertUser($id_user = NULL) {
		$id_row = $this->e_Insert();
		if (empty($id_user)) $id_user = $this->User->getId();
		$this->set_cell('tsk_task','id_worker', $id_row, $id_user);
		return $id_row;
	}
	
	// Перевод задачи с состояния "новая" в состояние "не новая"
	// Может сделать только исполнитель
	public function e_NotNewTask($id_task) {
		$task = $this->get_row($id_task);
		if (!empty($task) and $task['id_worker'] == $this->User->getId() and ($task['is_new'] == 1) and $task['state'] == 'open') {
			$this->set_cell('tsk_task','is_new', $id_task, 0);
			echo 1;
			exit;
		}
		if (!empty($task) and $task['id_owner'] == $this->User->getId() and ($task['is_new'] == 1) and $task['state'] == 'control') {
			$this->set_cell('tsk_task','is_new', $id_task, 0);
			echo 1;
			exit;
		}
		exit;
	}
	
	// Добавление задачи через форму
	public function e_EditForm() {
		//print_r($_FILES);	
		// if (empty($_POST['name'])) return;
		
		// Если нет идентификатора задачи - создаем новую
		if (empty($_POST['id_task']))
			$id_row = $this->e_Insert();
		else 
			$id_row = $_POST['id_task'];
		
		//file_put_contents('test.txt', print_r($_POST, true));
		//file_put_contents('test.txt', print_r($_FILES, true), FILE_APPEND);
		
		if (!empty($_POST['name']))  $this->set_cell('tsk_task','name', $id_row, $_POST['name']);
		if (!empty($_POST['description']))  $this->set_cell('tsk_task','description', $id_row, $_POST['description']);
		// Если задача указывается, как открытая - ей устанавливается состояние новая.
		if (!empty($_POST['state'])) 		$this->set_cell('tsk_task','state', $id_row, $_POST['state']);
		if (!empty($_POST['priority']))  $this->set_cell('tsk_task','priority', $id_row, $_POST['priority']);
		if (!empty($_POST['id_project']))  $this->set_cell('tsk_task','id_project', $id_row, $_POST['id_project']);
		if (!empty($_POST['start_date']))  $this->set_cell('tsk_task','start_date', $id_row, $_POST['start_date']);
		if (!empty($_POST['finish_date']))  $this->set_cell('tsk_task','finish_date', $id_row, $_POST['finish_date']);
		if (!empty($_POST['id_worker']))  $this->set_cell('tsk_task','id_worker', $id_row, $_POST['id_worker']);
		if (!empty($_POST['id_owner']))  $this->set_cell('tsk_task','id_owner', $id_row, $_POST['id_owner']);
		
		// Обновляем приложения
		$this->Attachment->e_UploadMultipleAttachment($id_row, 'Task');
		
		// if (!empty($_FILES['attachment']['name']))  $this->e_UploadAttachment($id_row);
		
		// Возвращение на предыдущую страничку.
		// ob_get_clean();
		// header("Location:".$_SERVER['HTTP_REFERER']);
		// exit;
		// return false;
	}
	
	function is_update($id_owner, $rowId, $tableName){
		//file_put_contents('test_update.txt', print_r($task, true), FILE_APPEND);
		//$task = $this->get_row($rowId);
		//if ($this->User->getId() == $task['id_worker']) return true;
		return parent::is_update($id_owner, $rowId, $tableName);
	}
	
	// В зависимости от переходов из режима в режим - отображаем соответствующий вид
	public function e_StateSwitcher($id_task, $cur_state, $new_state = NULL) {
		if (empty($new_state)) {
			switch ($cur_state) {
				case "open": 	$new_state = "control"; break;
				case "control": $new_state = "closed"; 	break;
				case "closed": 	$new_state = "control"; break;
			}
		}
		
		$data['id_task'] = $id_task;
		$data['new_state'] = $new_state;
		$data['cur_state'] = $cur_state;
		if ($cur_state == 'open' and $new_state == 'control')
			$data['current_class'] = 'checkmark-no';
		if ($cur_state == 'control' and $new_state == 'open')
			$data['current_class'] = 'checkmark-yes';
		if ($cur_state == 'control' and $new_state == 'closed')
			$data['current_class'] = 'checkmark-blue-no';
		if ($cur_state == 'closed' and $new_state == 'control')
			$data['current_class'] = 'checkmark-blue-yes';
		
		$this->loadView('state_switcher',$data);
	}
	
	// Установить режим
	public function e_SetState($id_task, $new_state, $cur_state = NULL) {
		if (empty($id_task)) return false;
		if (empty($cur_state)) {
			$task = $this->get_row($id_task);
			$cur_state = $task['state'];
		}
		if ($cur_state == $new_state) return;
		// if ($cur_state == 'open_new' and $new_state == 'open') {
			// $this->set_cell('tsk_task','finish_date', $id_row, $_POST['finish_date']);
		// }
		
		// Записываем новое состояние
		$this->set_cell('tsk_task','state', $id_task, $new_state);
		
		// Обновляем состояние новизны
		if ($new_state == 'control' or $new_state == 'open')
			$this->set_cell('tsk_task','is_new', $id_task, 1);
		else 
			$this->set_cell('tsk_task','is_new', $id_task, 0);
		$this->e_StateSwitcher($id_task, $new_state, $cur_state);
	}
	
}
?>