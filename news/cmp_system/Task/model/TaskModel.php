<?php 
	class TaskModel extends DBProc {
	//protected $limit_start = 0;
	public static $_sess_limit_count;// Кол-во выводимых задач
	public static $_sess_order_field;// Поле сортировки
	public static $_sess_order_direction;// Направление сортировки
	public static $_sess_is_show_closed;// Отображать открытые задачи или закрытые
	
	protected function _init()
	{
		$limit_count = $this->limit_count;
		if (empty($limit_count)) $this->limit_count = 50;
		$order_field = $this->order_field;
		if (empty($order_field)) $this->order_field = 'finish_date';
		$order_direction = $this->order_direction;
		if (empty($order_direction)) $this->order_direction = 'ASC';
		$show_closed = $this->show_closed;
		if (empty($show_closed)) $this->show_closed = false;
	}
	public function getTasks($id_page = 1, $id_user = NULL) {
		//echo 'Task count'.$this->limit_count;
		$state = ($this->is_show_closed)? 'closed': 'open';	
		if (empty($id_user)) $id_user = $this->User->getId();
		if ($this->User->getRole() == 'administrator' && $id_user == $this->User->getId()) {
			$params = array ( 'tables' => array('t'=>'tsk_task'),
						  'fields' => array('t.*'),
						  'order' => array( 'priority' => 'DESC',
											'start_date' => 'DESC'),
						  'limit_start' => $this->getLimitStart($id_page),
						  'limit_count' => $this->limit_count
						);
			//if (!$this->is_show_closed)
			$params['where'] = array( 
									'field'=>'t.state',
									'operator' => '=',
									'value' => $state);
		}
		else {
			
			$params = array ( 'tables' => array('t'=>'tsk_task'),
							  'where' => array(
											'operator'=>'OR',
											array( 
											'field'=>'t.id_worker',
											'operator' => '=',
											'value' => $id_user),
											array( 
											'field'=>'t.id_owner',
											'operator' => '=',
											'value' => $id_user)),
							  'fields' => array('t.*'),
							  'order' => array( 'priority'=> 'DESC', 'start_date' => 'DESC'),
							  'limit_start' => $this->getLimitStart($id_page),
							  'limit_count' => $this->limit_count
						);
			//if (!$this->is_show_closed) 
			$params['where'] = array(
									'operator'=>'AND',
									array(
											'operator'=>'OR',
											array( 
											'field'=>'t.id_worker',
											'operator' => '=',
											'value' => $id_user),
											array( 
											'field'=>'t.id_owner',
											'operator' => '=',
											'value' => $id_user)),
									array( 
									'field'=>'t.state',
									'operator' => '=',
									'value' => $state));
		}
				//$result = $this->Select($params);
				
		return $this->Select($params);
		}
		
	// возвращает первый порядковый номер записи на указанной странице
	// первая страница имеет номер 1
	public function getLimitStart($id_page){
		if (empty($id_page) or (!is_numeric($id_page)) or $id_page == 1) return 0;
		return $this->limit_count * (intval($id_page)-1);
	}
	
	public function setLimitCount($limit_count) {
		//if (empty($id_page) or (!is_numeric($id_page))) return;
		$this->limit_count = $limit_count;
	}
	
	public function setIsShowClosed($is_show_closed) {
		//if (empty($id_page) or (!is_numeric($id_page))) return;
		if(!empty($is_show_closed))
			$this->is_show_closed = true;
		else 
			$this->is_show_closed = false;
	}	
	
	public function setTaskOrder($field) {
		if (!in_array($field, array("id_task", "project_name","name", "id_attachment", "priority", "start_date", "finish_date", "close_date", "worker_name","controller_name"))) return;
		
		//Если уже есть поле сортировки - меняем направление сортировки
		if ($this->order_field == $field) {
			if ($this->order_direction == "DESC")
				$this->order_direction = "ASC";
			else
				$this->order_direction = "DESC";
		}
		//Если нет поля сортировки - устанавливаем новое поле и направление по спаданию
		else {
			$this->order_field = $field;
			$this->order_direction = "DESC";
		}
	}
	
	public function getLimitCount(){
		return $this->limit_count;
	}
	// Получаем список текущих работ заданного пользователя
	public function getTasksInWork($id_user = NULL) {
		$params = array ( 'tables' => 'trckr_tracker',
						  'fields' => array('*'),
						  'index_field' => 'id_task',
						  'index_unique'
						);
						//echo "HELLO";
		$date = date('Y-m-d H:i:s');
		$params['where'] = array(
									'operator'=>'OR',
									array( 
									'field'=>'state',
									'operator' => '=',
									'value' => 'open'),
									array(
									'field'=>'state',
									'operator' => '=',
									'value' => 'paused'
									)
									);
		// Если указан пользователь - возвращаем задачи только этого пользователя						
		if (!empty($id_user)) 
			$params['where'][] = array(
									'field'=>'id_owner',
									'operator' => '=',
									'value' => $id_user
									);
		$result = $this->Select($params);
		return $result['rows'];
	}
	
	public function getTasksTime($tasks) {
		//echo "HELLO";
		if (!is_array($tasks) or count($tasks)<1 ) return array();
		foreach ( $tasks as $task ) {
			$array_id_task[] = $task['id_task'];
		}
		
		$date = date('Y-m-d H:i:s');
		$params = array ( 'tables' => 'trckr_tracker',
						  'fields' => array('*'),
						  'index_field' => 'id_task',
						  'where' => array(
									'field'=>'id_task',
									'operator' => 'in',
									'value' => $array_id_task
									)
						);
		$work_log = $this->Select($params);
		// Рассчитываем интервалы
		foreach($work_log['rows'] as $id_task => $works) {
			$full_interval = new DateInterval('PT0S');
			foreach ($works as $key => $work) {
				$interval = $this->getInterval($work['work_start'], $work['work_stop']);
				$full_interval = $this->addInterval($full_interval, $interval);
				$work_log['rows'][$id_task][$key]['work_time'] =  $this->formatIntervalHMS($interval);
			}
			$work_log['rows'][$id_task]['works'] = $works;
			$work_log['rows'][$id_task]['full_time'] = $this->formatIntervalHMS($full_interval);
			$work_log['rows'][$id_task]['full_time_second'] = $this->getIntervalInSec($full_interval);
		}
		return $work_log['rows'];
	}
	
	// Возвращает все комментарии выбранной работы
	public function getWorks($id_task){
		$params = array ( 'tables' => array('tr' => 'trckr_tracker'),
						  'fields' => array('tr.*'),
						  'order' => array( 'tr.work_start' => 'ASC'),
						  
						  'where' => array( 
									'field'=>'tr.id_task',
									'operator' => '=',
									'value' => $id_task
									)
						);
		return $this->Select($params);
	}
	
	
	public function getTimeList($id_user, $id_project, $date_from, $date_to) {
		//echo "$id_user, $date_from, $date_to";
		$params = array ( 'tables' => array('tr' => 'trckr_tracker',
											array('t'=> 'tsk_task',
												  'join' => 'inner',
												  'on_left' =>'tr.id_task',
												  'on_right' =>'t.id_task'
											),
											array('p' => 'prjct_project',
												  'join' => 'left',
												  'on_left' => 't.id_project',
												  'on_right' => 'p.id_project'
												 )
											),
						  'fields' => array('tr.*', 't.name as `task_name`', 'p.name as `project_name`'),
						  'order' => array( 'tr.work_start' => 'DESC'),
						  
						  'where' => array(
									'operator' => 'AND',
									array( 
									'field'=>'tr.id_owner',
									'operator' => '=',
									'value' => $id_user
									),
									array( 
									'field'=>'date(tr.work_start)',
									'operator' => '>=',
									'value' => $date_from
									),
									array( 
									'field'=>'date(tr.work_stop)',
									'operator' => '<=',
									'value' => $date_to
									)
								)
						);
		if (!empty($id_project)) {
			$params['where'][] = array( 
									'field'=>'t.id_project',
									'operator' => '=',
									'value' => $id_project
									);
		}

		$work_log = $this->Select($params);
		//print_r($work_log);
		if (empty($work_log['rows']) or count($work_log['rows'])<1) return array(); 
		foreach($work_log['rows'] as $key=>$work) {
				$work_start = new DateTime($work['work_start']);
				$interval = $this->getInterval($work['work_start'], $work['work_stop']);
				$day_works[$work_start->format('d-m-Y')]['rows'][$key] = $work;
				$day_works[$work_start->format('d-m-Y')]['rows'][$key]['interval'] = $interval;
				$day_works[$work_start->format('d-m-Y')]['rows'][$key]['full_time'] = $this->formatIntervalHMS($interval);
				$day_works[$work_start->format('d-m-Y')]['rows'][$key]['comment'] = (empty($work['comment']))? 'Пауза' : $work['comment'];
			}
			
		$all_days_interval = new DateInterval('PT0S');
		foreach ($day_works as $key=>$day){
			$full_interval = new DateInterval('PT0S');
			$full_comment = "";
			foreach($day['rows'] as $work) { 
				$full_interval  = $this->addInterval($full_interval, $work['interval']);
				$all_days_interval = $this->addInterval($all_days_interval, $work['interval']);
				// if(!empty($work['comment']))
					// $full_comment .= '<div>'.$work['comment'].'</div>';
			}
			$day_works[$key]['full_time'] = $this->formatIntervalHMS($full_interval);
			$day_works[$key]['full_comment'] = $full_comment;
		}
		
		$result['rows'] = $day_works;
		$result['full_time'] = $this->formatIntervalHMS($all_days_interval);;
		return $result;
	}
	public function getProjectTimeList($id_user, $id_project, $date_from, $date_to) {
		//echo "$id_user, $date_from, $date_to";
		$params = array ( 
						   'tables' => array('p' => 'prjct_project',
											array('tsk' => 'tsk_task',
												  'join' => 'inner',
												  'on_left' => 'p.id_project',
												  'on_right' => 'tsk.id_project'
												 ),
											array ('t' => 'trckr_tracker',
												   'join' => 'inner',
												   'on_left' => 'tsk.id_task',
												   'on_right' => 't.id_task'
												)
											),
						  'fields' => array('p.id_project',
											'p.name as `project_name`', 
											'tsk.id_task',
											'tsk.name as `task_name`',
											'tsk.state',
											't.comment',
											't.work_start',
											't.work_stop'),
						  'where' => array(
									'operator' => 'AND',
									array( 
									'field'=>'t.id_owner',
									'operator' => '=',
									'value' => $id_user
									),
									array( 
									'field'=>'date(work_start)',
									'operator' => '>=',
									'value' => $date_from
									),
									array( 
									'field'=>'date(work_stop)',
									'operator' => '<=',
									'value' => $date_to
									)
								),
							'index_field' => array('id_project', 'id_task')
						);
						
		if (!empty($id_project)) {
			$params['where'][] = array( 
									'field'=>'tsk.id_project',
									'operator' => '=',
									'value' => $id_project
									);
		}
		$project_tasks = $this->Select($params);
		//print_r($project_tasks);
		if (empty($project_tasks['rows']) or count($project_tasks['rows'])<1) {
			$project_tasks['rows'] = array();
			return $project_tasks;
		}
		
		$full_time_interval = new DateInterval('PT0S');
		foreach($project_tasks['rows'] as $project_id=>$project) {
			$full_project_interval = new DateInterval('PT0S');
			foreach($project as $task_id=>$task) {
				$full_task_interval = new DateInterval('PT0S');
				foreach ($task as $work) {
					$work_interval = $this->getInterval($work['work_start'], $work['work_stop']);
					$full_task_interval = $this->addInterval($full_task_interval, $work_interval);
					$full_project_interval = $this->addInterval($full_project_interval, $work_interval);
					$full_time_interval = $this->addInterval($full_time_interval, $work_interval);
				}
				$project_tasks['rows'][$project_id][$task_id]['state'] = $work['state'];
				$project_tasks['rows'][$project_id][$task_id]['name'] = $work['task_name'];
				$project_tasks['rows'][$project_id][$task_id]['full_time'] = $this->formatIntervalHMS($full_task_interval);
			}
			$project_tasks['rows'][$project_id]['name'] = $work['project_name'];
			$project_tasks['rows'][$project_id]['full_time'] = $this->formatIntervalHMS($full_project_interval);
		}
		$project_tasks['full_time'] = $this->formatIntervalHMS($full_time_interval);
		//print_r($project_tasks);
		return $project_tasks;
	}
	
	// Возвращает интервал между двумя датами - объект класса DateInterval
	// Если вторая дата - пустая или 0000-00-00, тогда берется разница от текущей даты
	public function getInterval($date_from, $date_to){
		$datetime1 = new DateTime($date_from);
		if ($date_to == '0000-00-00 00:00:00' or $date_to == '0000-00-00' or empty($date_to)) {
			$current_date = date('Y-m-d H:i:s');
			$datetime2 = new DateTime($current_date);
		}
		else 
			$datetime2 = new DateTime($date_to);
		$interval = $datetime1->diff($datetime2);
		return $interval;
	}
	
	// Складывает два интервала и возвращает результирующий интервал.
	public function addInterval($interval1, $interval2){
		$base_date = new DateTime(date('Y-m-d H:i:s'));
		$diff_date = new DateTime(date('Y-m-d H:i:s'));
		$diff_date->add($interval1);
		$diff_date->add($interval2);
		$interval = $base_date->diff($diff_date);
		return $interval;
	}
	
	// Форматирует интервал и возвращает строку вида H:M:S.
	public function formatIntervalHMS($interval){
		$hours = $interval->h;
		$hours = $hours + ($interval->days*24);
		$minutes = $interval->format("%I");	
		$secunds = $interval->format("%S");
		if ($hours<=9) $hours ='0'.$hours;
		//if ($hours<=9) $hours ='0'.$hours;
		
		return "$hours:$minutes:$secunds";
	}	
	// Возвращает интервал в секундах.
	public function getIntervalInSec($interval){
		$hours = $interval->h;
		$minutes = $interval->format("%I");	
		$secunds = $interval->format("%S");
		$hours = $hours + ($interval->days*24);
		return $hours*3600 + $minutes*60 + $secunds;
	}
	
	// Возвращает все задачи заданного проекта
	public function getProjectTasks($id_project , $id_page = NULL, $id_user = NULL) {
		$state = ($this->is_show_closed)? 'closed': 'open';	
		if (empty($id_user)) $id_user = $this->User->getId();
		$params = array ( 'tables' => 'tsk_task',
						  'fields' => array('*'),
						  'order' => array( 'priority'=> 'DESC'),
						  'limit_start' => $this->getLimitStart($id_page),
						  'limit_count' => $this->limit_count
						);
		if ($this->User->getRole() == 'administrator' and $id_user == $this->User->getId()) {
			
			/*if ($this->is_show_closed) {
				$params['where'] =  array(
									'field'=>'id_project',
									'operator' => '=',
									'value' => $id_project
									);
			}
			else {*/
				$params['where'] =  array (
									'operator'=>'AND',
									array( 
									'field'=>'state',
									'operator' => '=',
									'value' => $state),
									array(
									'field'=>'id_project',
									'operator' => '=',
									'value' => $id_project
									));
				
		//}
		} else {
			//$id_user = $this->User->getId();
			//if (!$this->is_show_closed) {
				$params['where'] =  array (
									'operator'=>'AND',
									array( 
									'field'=>'state',
									'operator' => '=',
									'value' => $state),
									array(
									'field'=>'id_project',
									'operator' => '=',
									'value' => $id_project
									),array(
											'operator'=>'OR',
											array( 
											'field'=>'id_worker',
											'operator' => '=',
											'value' => $id_user),
											array( 
											'field'=>'id_owner',
											'operator' => '=',
											'value' => $id_user))
									);
			}
			// else {
				// $params['where'] =  array(
									// 'operator'=>'AND',
									// array(
											// 'field'=>'id_project',
											// 'operator' => '=',
											// 'value' => $id_project
											// ),
									// array(  'operator'=>'OR',
											// array( 
												// 'field'=>'id_worker',
												// 'operator' => '=',
												// 'value' => $id_user),
											// array( 
												// 'field'=>'id_owner',
												// 'operator' => '=',
												// 'value' => $id_user))
									// );
			// }
		// }
						
		return $this->Select($params);
	}
	
	// Возвращает все задачи пользователя проекта
	public function getControlTasks($id_user , $id_page = NULL ) {
		$state = ($this->is_show_closed)? 'closed': 'open';	
		if (empty($id_user)) $id_user = $this->User->getId();
		
		$params = array ( 'tables' => 'tsk_task',
						  'fields' => array('*'),
						  'order' => array( 'priority'=> 'DESC'),
						  'limit_start' => $this->getLimitStart($id_page),
						  'limit_count' => $this->limit_count
						);
		//if (!$this->is_show_closed)
			$params['where'] = array (
									'operator'=>'AND',
									array( 
									'field'=>'state',
									'operator' => '=',
									'value' => $state),
									array(
									'field'=>'id_owner',
									'operator' => '=',
									'value' => $id_user
									));
		// else
			// $params['where'] = 	array(
									// 'field'=>'id_owner',
									// 'operator' => '=',
									// 'value' => $id_user
									// );
						
		return $this->Select($params);
		}
		
		// Возвращает все задачи пользователя проекта
	public function getMyTasks($id_user , $id_page = NULL) {
		$state = ($this->is_show_closed)? 'closed': 'open';	
		if (empty($id_user)) $id_user = $this->User->getId();
		
		$params = array ( 'tables' => 'tsk_task',
						  'fields' => array('*'),
						  'order' => array( 'priority'=> 'DESC'),
						  'limit_start' => $this->getLimitStart($id_page),
						  'limit_count' => $this->limit_count
						);
		//if (!$this->is_show_closed)
			$params['where'] = array (
									'operator'=>'AND',
									array( 
									'field'=>'state',
									'operator' => '=',
									'value' => $state),
									array(
									'field'=>'id_worker',
									'operator' => '=',
									'value' => $id_user
									));
		// else
			// $params['where'] = 	array(
									// 'field'=>'id_worker',
									// 'operator' => '=',
									// 'value' => $id_user
									// );
						
		return $this->Select($params);
		}
	
	// Возвращает все задачи заданного проекта
	public function getAllTasks($task_type = 'all', $state = array('open','control'), $id_page = NULL, $id_project=NULL, $id_user = NULL, $date_from = NULL, $date_to = NULL, $id_task_last = NULL) {
		
		if (empty($id_user)) $id_user = $this->User->getId();
		$role = $this->User->getRole();
		
		$params = array ( 'tables' => array('t'=>'tsk_task',
											array('p' => 'prjct_project',
												  'join' => 'left',
												  'on_left' => 't.id_project',
												  'on_right' => 'p.id_project'
												 ),
											array ('mc' => 'mmbr_member',
												   'join' => 'inner',
												   'on_left' => 't.id_owner',
												   'on_right' => 'mc.id_owner'
												),
											array ('mw' => 'mmbr_member',
												   'join' => 'left',
												   'on_left' => 't.id_worker',
												   'on_right' => 'mw.id_owner'
												)),
						  'fields' => array('t.*','p.name as `project_name`', 'mc.name as `controller_name`', 'mw.name as `worker_name`'),
						  'order' => array( $this->order_field => $this->order_direction, 't.id_task'=>'DESC'),
						  'limit_start' => $this->getLimitStart($id_page),
						  'limit_count' => $this->limit_count,
						  'where' =>  array (
									'operator'=>'AND',
									array( 
									'field'=>'t.state',
									'operator' => 'in',
									'value' => $state)
						));
		
		// Три можливих типи - співробітник, контролер, нові
		switch ($task_type) {
			// сотрудник
			case 'worker': 
				$params['where'][] = array( 'field'=>'t.id_worker',
											'operator' => '=',
											'value' => $id_user);
				break;
			case 'controller': 
				$params['where'][] = array( 'field'=>'t.id_owner',
											'operator' => '=',
											'value' => $id_user);
				break;
			case 'new':
				$params['where'][] = array( 'field'=>'t.is_new',
											'operator' => '=',
											'value' => '1');
				break;
			case 'all': break; 
		}
		// Фильтр по дате
		if (!empty($date_from))
			$params['where'][] = array( 
									'field'=>'date(work_start)',
									'operator' => '>=',
									'value' => $date_from
									);
		if (!empty($date_to))
			$params['where'][] = array( 
									'field'=>'date(work_stop)',
									'operator' => '<=',
									'value' => $date_to
									);
		// Фильтр по роли
		
		if ($this->User->getRole() != 'administrator') {	
		$params['where'][] =  array(
										'operator'=>'OR',
										array( 
										'field'=>'t.id_worker',
										'operator' => '=',
										'value' => $id_user),
										array( 
										'field'=>'t.id_owner',
										'operator' => '=',
										'value' => $id_user)
									);
		} 
		
		// Фильтр по проекту
		if (!empty($id_project))
			$params['where'][] =	array(
									'field'=>'t.id_project',
									'operator' => '=',
									'value' => $id_project
									);
		// Фильтр по максимальному номеру задания
		if (!empty($id_task_last))
			$params['where'][] =	array(
									'field'=>'t.id_task',
									'operator' => '>',
									'value' => $id_task_last
									);
		$result_tasks = $this->Select($params);
		//print_r($result_tasks);
		// Получаем время по задачам и заносим его в массив задач
		$task_time = $this->getTasksTime($result_tasks['rows']);
		//print_r($task_time);
		foreach ($result_tasks['rows'] as $key=>$task) {
			$result_tasks['rows'][$key]['works'] = $task_time[$task['id_task']]['works'];
			$result_tasks['rows'][$key]['full_time'] = $task_time[$task['id_task']]['full_time'];
			$result_tasks['rows'][$key]['full_time_second'] = $task_time[$task['id_task']]['full_time_second'];
		}
		return $result_tasks;
	}
	
	// Получение статистики новых заданий
	public function getNewStatistics(){
		$tasks =  $this->getAllTasks('new', array('open', 'control'), NULL, NULL, NULL, NULL, NULL);
		//print_r($tasks);
		$user_id = $this->User->getId();
		foreach($tasks['rows'] as $task) {
			if (($task['state'] == 'open' and $user_id == $task['id_worker']) or
				($task['state'] == 'control' and $user_id == $task['id_owner'])) {
				$statistics['projects'][$task['id_project']]['all'] += 1;
				$statistics['all'] += 1;
				if ($this->User->getId() == $task['id_worker'] and $task['state'] == 'open' ){
					$statistics['projects'][$task['id_project']]['worker'] += 1;
					$statistics['worker'] += 1;
				}
				if ($this->User->getId() == $task['id_owner'] and $task['state'] == 'control') {
					$statistics['projects'][$task['id_project']]['controller'] += 1;
					$statistics['controller'] += 1;
				}
			}
		}
		return $statistics;
	}
	
}

?>