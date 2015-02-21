<?php
/**
 * Содержит класс Site
 * @package IC-CMS
 * @subpackage Core
 */

/**
 * Класс сайта - обрабатывает все входящие сообщения и в зависимости от механизма связи распределяет их обработку
 *
 * При формировании новой странички используется механизм буфферизации для
 * реализации принципа подключения библиотек "по необходимости".
 * Также используеться буферизация для замены ссылок в сгенерированной страничке
 *
 * @author Дмитрий Витюк
 * @author Александр Громовой
 * @author Иван Найдёнов
 * @version 1.3
 * @modified v1.3 от 27 октября 2013 года
 * @package IC-CMS
 * @subpackage Core
 */
class Site extends Contenter
{
	public $is_edit = true;
	public $title;
	private $baseView = "defaultLayout";
	static protected $settings;
	/**
	 * Конструктор класса Site.
	 * - Заносит объект сайт в компоненты, так как первый вызов идет не через
	 * магический метод {@link __get}
	 * - Переопределяет версию
	 * - Инициализирует компоненты Session и Lang
	 */
	public function __construct()
	{
		parent::__construct();
		self::$components['Site'] = $this;
		$this->version = "1.2";
		
        // Устанавливаем права компонентов для текущей роли
		$this->Rule->getCmpRules($this->User->getRole());
		
		// Временно, пока не работает страничка с настройками сайта
		self::$settings['is_authorize'] = true;
		
		// Работаем с мультиязычностью
		//if (isset($this->Lang))
			//$this->Lang;		
	}

	/**
	 * Функция проводит главный разбор событий
	 *
	 * Порядок проверки действий для обработки события
	 * <ol>
	 * <li>Публичный метод в с именем e_$evt в компоненте $cmp</li>
	 * <li>Вид $evtPage для компонента $cmp загружаеться как страница</li>
	 * <li>Вид $evt для компонента $cmp загружаеться как вид</li>
	 * <li>Обрабатываеться зарегестрированное глобальное событие из масива
	 * {@link $GL_GLOBAL_DISPATCH}</li>
	 * <li>Функция Dispatch компонента $cmp (или его родителя если в
	 * компоненте отсутствет такая функция)</li>
	 * <li>Сообщение о {@link SEQURITY} ошибке</li>
	 * </ol>
	 *
	 */
	public function Dispatch()
	{

		// Получаем части адреса.
		$urlParts = $this->getUrlParts();
		self::$jsData['baseUrl'] = self::$baseUrl;

		foreach ($urlParts as &$part) {
			$part = urldecode($part);
		}
		
		// Если в настройках установлен заход по логину и пользователь не залогинен - перенаправление на
		$id = $this->User->getId();
		
		if (self::$settings['is_authorize'] and empty($id) 
		    and $urlParts[0] != "login" 
			and $urlParts[0] != "password-recover"
			and $urlParts[0] != "EmailPasAuth") {
			$this->redirect("login");
			exit;
		}
		//Если механизм с перезагрузкой - в зависимости от входной ссылки заполняем позиции  шаблонов разными компонентами и выводим результат пользователю.
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
			$_POST['isAjax'] = 1;
		}
		
		if (empty($_POST['isAjax'])) {
			
			// Второй параметр, если он необходим при загрузке страниц, всегда обозначает идентификатор объекта и должен быть числом.
			// Поэтому ставим защиту от различных хакерских ХSS атак
			$id_object = (int) $urlParts[1];
		

			// Загружаем позиции сайта
		/*	switch ($urlParts[0]) {
						case "Вход": 		$this->is_edit = false;
							//$this->baseView="workLayout";					
							ob_start(); 
								$this->loadView('loginform');
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;	
						case "Роли":			$this->is_edit = false;
							$this->baseView="systemLayout";					
							ob_start(); 
								$this->Role->e_List();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;							
						case "Типы-страниц":	$this->is_edit = false;
							$this->baseView="systemLayout";					
							ob_start(); 
								$this->Site->e_PageType();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;															
						case "Типы-фотографий":	$this->is_edit = false;
							$this->baseView="systemLayout";					
							ob_start(); 
								$this->Photo->e_PhotoType();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;															
						case "Права-видов":			$this->is_edit = false;
							$this->baseView="systemLayout";					
							ob_start(); 
								$this->Rule->e_List();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;	
						case "Права-компонентов":			$this->is_edit = false;
							$this->baseView="systemLayout";					
							ob_start(); 
								$this->Rule->e_ListCmp();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;
							case "Сообщения-системы":			$this->is_edit = false;
							$this->baseView = "systemLayout";		
							$this->title = "Cистемные сообщения";
							ob_start(); 
								$this->e_Events($urlParts[1]);
								$this->setPosition("position1", "workLayout", ob_get_clean());
							break;	
						case "Форма-регистрации":		$this->is_edit = false;
							$this->baseView="workLayout";					
							ob_start(); 
								$this->EmailPasAuth->e_RegisterFormTitle();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							ob_start(); 
								$this->EmailPasAuth->e_RegisterForm($id_object);
								$this->setPosition("position2", "workLayout", ob_get_clean());
							break;	
					
						case "Ошибка-авторизации": $this->is_edit = false;
							$this->baseView="workLayout";					
							ob_start(); 
								$this->EmailPasAuth->e_RegisterFormTitle();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							ob_start(); 
								$this->EmailPasAuth->e_AuthorizationError();
								$this->setPosition("position2", "workLayout", ob_get_clean());
							break;	
						case "Подтверждение-регистрации":		$this->is_edit = false;
							$this->baseView="workLayout";					
							ob_start(); 
								$this->EmailPasAuth->e_RegisterFormTitle();
								$this->setPosition("position1", "workLayout", ob_get_clean());
							ob_start(); 
								$this->EmailPasAuth->e_SubmitRegistration($urlParts[2],$urlParts[3]);
								$this->setPosition("position2", "workLayout", ob_get_clean());	
							ob_start(); 
							echo'';
								$this->setPosition("position3", "workLayout", ob_get_clean());
							break;
						default:*/
						if(empty($urlParts[0])) {$urlParts[0] = 'main';}
						// Выгрузка списка позиций из бд
						$positions = $this->DBProc->get_positions($urlParts[0]);
						if(!empty($positions)){
							$this->is_edit = $positions[0]['is_edit'];
							$this->baseView = $positions[0]['base_view'];
							$this->title = $positions[0]['title'];
							$this->Viewer->setTheme($positions[0]['theme']);
							$params = explode('/', $positions[0]['url']);
							// Params string
							for ($i = 0; $i < count($urlParts); $i++){
								$assoc_param[$params[$i]] = $urlParts[$i];
								// устанавливаем дополнительные параметры в title
								
								if(strpos( $params[$i], '$') !== false){ // Если ключ переменная
									$name = str_replace('-', ' ', $urlParts[$i]);									// Очищаем от дефисов
									$this->title = str_replace($params[$i], $name, $this->title);
									
								}
							}
							
							foreach($positions as $key => $row){
								ob_start();
								$func = $row['function'];
								$comp = $row['component'];
								// Применение жестких значений для функций вместо переменных.
								if((empty($assoc_param[$row['p1']])) and (strpos("$", $assoc_param[$row['p1']]))){ $assoc_param[$row['p1']] = $row['p1']; }
								if((empty($assoc_param[$row['p2']])) and (strpos("$", $assoc_param[$row['p2']]))){ $assoc_param[$row['p2']] = $row['p2']; }
								if((empty($assoc_param[$row['p3']])) and (strpos("$", $assoc_param[$row['p3']]))){ $assoc_param[$row['p3']] = $row['p3']; }
								if((empty($assoc_param[$row['p4']])) and (strpos("$", $assoc_param[$row['p4']]))){ $assoc_param[$row['p4']] = $row['p5']; }
								if((empty($assoc_param[$row['p5']])) and (strpos("$", $assoc_param[$row['p5']]))){ $assoc_param[$row['p5']] = $row['p5']; }

								$params = array($assoc_param[$row['p1']], $assoc_param[$row['p2']], $assoc_param[$row['p3']], $assoc_param[$row['p4']], $assoc_param[$row['p5']]);
								// Обнуляем списсок аргументов на всякий случай.
								$args = Array();
								// Собираем аргументы для функции, избаляемся от null
 								foreach ($params as $param){
									if(!empty($param)){ $args[] = $param; }
								}
								call_user_func_array(array(&$this->$comp, $func),$args);
								$this->setPosition($row['name'],$row['view'], ob_get_clean());
							}
							
						}else{

							// Если позиции не заданы в базе данных,пытаемся загрузить метод компонента .
							$cmp = $urlParts[0];
							$evt = $urlParts[1];
							$evt_method = 'e_' . $evt;
							if (method_exists($cmp, $evt_method)) {
								$this->baseView="workLayout";
								$reflection = new ReflectionMethod($cmp, $evt_method);
								if (!$reflection->isPublic())
									$this->addEvent(SEQURITY, "Попытка доступа к запрещенной функции $cmp.$evt");
								else
									call_user_func_array(array(&$this->$cmp, $evt_method), array_slice($urlParts, 2));

							}else{
							// Если мы ничего не нашли то тогда запускаем страницу 404 или перенапраляем на гравную
								if ($this->checkViewExist('404')) {
									header("HTTP/1.0 404 Not Found");
									$this->baseView = '404';
								}else{
									header("HTTP/1.0 404 Not Found");
									die('<h1>Resource not found on this server</h1>');
								}
							}
						}
						/*break;
		}*/
 			// Загружаем меню
			ob_start();
				$this->Menu->e_ShowMenu();
			$this->setPosition("position_menu", "header", ob_get_clean());

			//Вывод результатов
			// Генерация уникальной страници
			
			ob_start();
				$this->loadView($this->baseView);
			$result_page = ob_get_clean();
			echo $this->linkReplace($result_page, self::$baseUrl);
			

		}

		// Если механизм без перезагрузки, работа ведется напрямую по компонентах
		else {
				//Определяем имя компонента и событие
				if (!isset($urlParts[0]))
					 $urlParts[0] = 'Site';
				if (!isset($urlParts[1]))
					$urlParts[1] = 'Main';
				$cmp = $urlParts[0];
				$evt = $urlParts[1];




				ob_start();


				if (!isset($this->$cmp)) {
					$this->addEvent(SEQURITY, "Попытка доступа к несуществующему компоненту $cmp");
				}
				else {
					try {
						$evt_method = 'e_' . $evt;
						// Пробуем запустить функцию 'e_'.$evt из компонента
						if (method_exists($cmp, $evt_method)) {
							$reflection = new ReflectionMethod($cmp, $evt_method);
							if (!$reflection->isPublic()) {
								$this->addEvent(SEQURITY, "Попытка доступа к запрещенной функции $cmp.$evt");
							}
							else {
								call_user_func_array(array(&$this->$cmp, $evt_method), array_slice($urlParts, 2));
							}
						}
						// Пробуем загрузить вид $evt
						//else if ($this->checkViewExist($evt, $cmp)) {
						//	$this->$cmp->loadView($evt, NULL);
						//}
		//			// Проверяем не зарегестрированно ли такое глобальное событе
		//			else if (key_exists($evt, $GL_GLOBAL_DISPATCH))
		//			{
		//				$params = array_diff_key($urlParts, array('evt' => ''));
		//				call_user_func_array(array(&$this->$GL_GLOBAL_DISPATCH[$evt], $evt_method), $params);
		//			}
		//				else if (method_exists($cmp, 'Dispatch')) {
		//					if ($cmp == 'Site')
		//						$this->Base->Dispatch($cmp, $evt, array_slice($urlParts, 2));
		//					else
		//						$this->$cmp->Dispatch($cmp, $evt, array_slice($urlParts, 2));
		//				}
						else {
							$this->addEvent(SEQURITY, "Попытка доступа к несуществующему событию $evt компонента $cmp");
						}
					}
					catch (PageExit $e) { // выход из страници в случае отсутствия доступа
					}
					catch (AjaxErrorException $e) {
						self::$ajaxResult['error'] = $e->getMessage();
					}
				}
				
				//$result_page = 
				/*if (empty($_POST['update'])) {
					if ($result_page) {
						if (!isset(self::$ajaxResult['error']))
							self::$ajaxResult['error'] = '';
						self::$ajaxResult['error'] .= $result_page;
					}
				*/
				self::$ajaxResult['updatedViews'][] = $this->linkReplace(ob_get_clean(),self::$baseUrl);
				
				self::$ajaxResult['jsData'] = self::$jsData;
				$result = json_encode(self::$ajaxResult);
				echo $result;
			//	}
				//else
					//echo $this->linkReplace($result_page, $baseUrl);
		}
		/************ Вывод дебага **************/
			if (isset($_GET['debug'])) 
				echo '<pre>'.self::$sql_log_buffer.'</pre>';
	}
	
	function PageNotFound() {
		$this->loadView('page_not_found');
	}
	
	function e_PageType() {
		if (!($this->User->getRole()=="administrator")) return false;
		$page_type_list = $this->getList('page_type');
		$page_position_list = $this->getList('page_position');
		$data['page_type_list'] = Array();
		foreach ($page_type_list as $page_type) {
			$data['page_type_list'][$page_type['id_page_type']] = $page_type;
		}
		foreach ($page_position_list as $page_position) {
			$data['page_type_list'][$page_position['id_page_type']]['position'][] = $page_position;
		}
		
		$data['combo_component'] = $this->Component->combo_component();
		//$data['combo_base_view'] = $this->Component->combo_view('Site');
		$data['combo_base_view'] = $this->combo_base_view();
		$data['combo_theme_view'] = $this->combo_theme_list();
		
		$this->loadView('page_type_list', $data);
	}
	
	function e_Events($type = 'events'){
		$data['types'] = Array('debug', 'event', 'info', 'warning', 'error', 'security');
		if( !in_array($type,$data['types']) ){ $type = 'events'; }
		$result = $this->Base->getEvents();
		$data['events'] = $result[$type];
		$this->loadView('events_list',$data);
	}
	
	// Стартовая страничка после установки
	function e_Welcome(){
		$this->loadView('welcome');
	}
	
	// Стартовая страничка админки
	function e_WelcomeAdmin(){
		$this->loadView('welcome_admin');
	}
	
	function e_LoginForm() {
		$this->loadView('loginform');
	}

	
	function e_InsertPageType($url = null, $positions = array()) {
		// Проверка прав доступа на операцию вставки
		if (!($this->User->getRole()=="administrator")) return false;
		
		$id_page_type = $this->create_row("st_page_type");
		if ($url) {
			$this->set_cell("st_page_type","url",$id_page_type,$url);
		}
		// Возможность установки позиций страници одновременно с добавлением типа странички
		foreach ($positions as  $position) {
			$id_page_position = $this->e_InsertPagePosition($id_page_type);
			if( $position['name'     ] ){ $this->set_cell("st_page_position", 'name'     , $id_page_position, $position['name'     ]); }
			if( $position['component'] ){ $this->set_cell("st_page_position", 'component', $id_page_position, $position['component']); }
			if( $position['view'     ] ){ $this->set_cell("st_page_position", 'view'     , $id_page_position, $position['view'     ]); }
			if( $position['function' ] ){ $this->set_cell("st_page_position", 'function' , $id_page_position, $position['function' ]); }
			if( $position['p1'       ] ){ $this->set_cell("st_page_position", 'p1'       , $id_page_position, $position['p1'       ]); }
			if( $position['p2'       ] ){ $this->set_cell("st_page_position", 'p2'       , $id_page_position, $position['p2'       ]); }
			if( $position['p3'       ] ){ $this->set_cell("st_page_position", 'p3'       , $id_page_position, $position['p3'       ]); }
			if( $position['p4'       ] ){ $this->set_cell("st_page_position", 'p4'       , $id_page_position, $position['p4'       ]); }
			if( $position['p5'       ] ){ $this->set_cell("st_page_position", 'p5'       , $id_page_position, $position['p5'       ]); }
		}

		return $id_page_type;
	}
	
	function e_DeletePageType($id_page_type) {
		// Проверка прав доступа на операцию
			if (!($this->User->getRole()=="administrator")) return false;
			$this->delete_row("st_page_type",$id_page_type);
			return true;
	}
	
	function e_InsertPagePosition($id_page_type) {
		// Проверка прав доступа на операцию вставки
		if (!($this->User->getRole()=="administrator")) return false;
		
		$id_row = $this->create_row("st_page_position");
		$this->set_cell("st_page_position","id_page_type",$id_row,$id_page_type);
		return $id_row;
	}
	
	function e_DeletePagePosition($id_page_position) {
		// Проверка прав доступа на операцию
		if (!($this->User->getRole()=="administrator")) return false;
		$this->delete_row("st_page_position",$id_page_position);
		return true;
	}

	// Функция для пустого вывода в позицию
	public function e_Empty() {
		return '';
	}
	
	
}

class PageExit extends Exception
{
	// Заглушка
}

class AjaxErrorException extends Exception
{
	// Заглушка
}