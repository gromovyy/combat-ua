<?php
/**
 * Содержит класс User
 * @package IC-CMS
 * @subpackage User
 */

/**
 * Класс User отвечает за действия с пользователем
 *
 * Класс реализует возможности
 * - Блокировки доступа к странице
 * - Проверки доступа на выполнение определенных действий
 * - Хранение и получение дополнительной информации о пользвателе
 * - Синхронизация с разными механизмами аутентификации, описанные интерфейсом {@link Auth}
 *
 * @requirement {@link Base}
 * @requirement {@link DBProc}
 * @requirement {@link UnregIPAuth}
 * @requirement {@link Session}
 * @modified v1.0 от 10 января 2012 года
 * @property int $id {@link _sess_id declatation} ID Текущего пользователя
 * @property string $auth {@link _sess_auth declatation} Выбраный класс аутентификации
 * @property array $permissionsChache {@link _sess_permissionsChache declatation} Кэш разрешений
 * @version 1.0
 * @author Иван Найдёнов
 * @package IC-CMS
 * @subpackage User
 */
class User extends Viewer
{
	/**
	 * ID Текущего пользователя
	 * @abstract Описывает сессионное поле. Прямой доступ запрещен. Используеться классом {@link Session}
	 * @name $id
	 * @var int
	 */
	protected static $_sess_id;
	protected static $_sess_real_id;
	
	/** 
	* Переменная задает режим правки/просмотра для текущего пользователя
	*/
	protected static $_sess_mode;
	/** 
	* Переменная задает текущую роль пользователя правки/просмотра для текущего пользователя
	*/
	protected static $_sess_user_role;
	protected static $_sess_real_user_role;
	
	/**
	 * Выбраный класс аутентификации
	 * @abstract Описывает сессионное поле. Прямой доступ запрещен. Используеться классом {@link Session}
	 * @name $auth
	 * @var string
	 */
	protected static $_sess_auth;

	/**
	 * Кэш разрешений
	 *
	 * Структура масива: <code>array(string $resource$action=>boolean|array(int=>boolean))</code>
	 * @abstract Описывает сессионное поле. Прямой доступ запрещен. Используеться классом {@link Session}
	 * @var array(string=>boolean|array(int=>boolean))
	 */
	//private
	static $_sess_permissionsChache;

	/**
	 * Обработчик по умолчанию отсутствия доступа к странице
	 * @var function(string,string,int)
	 */
	protected $accessDenied;
	public $userData;

	/**
	 * Конструктор класса
	 *
	 * Устанавливает текущего пользователя
	 */
	public function __construct()
	{
		Base::__construct();
	}

	/**
	 * Инициализатор.
	 * - Устанавливает метод аутентификации по умолчанию, если не задан
	 * - Определяет текущий идентификатор пользователя
	 * - Устанавливает реакцию по умолчанию на попытку не авторизированого доступа к виду или станице
	 * - Запоменает айди текущего пользователя для последубюего использования в бд
	 */
	protected function _init()
	{
		//file_put_contents('user.txt','init role='.$this->user_role.'id='.$this->id ,FILE_APPEND);
		//$this->id = 0;
		//$this->user_role = "unregistered";
		$tmp_role = $this->user_role;
		if (empty($tmp_role)) $this->user_role = "unregistered";
		$this->real_id = 0;
		$this->real_user_role = "unregistered";
		$this->auth = "UnregIPAuth";
		if ($this->mode == '') $this->mode ='view';
		//$this->mode = "view";
		$this->tryRestoreFromCookies(); // пробуем авторизировать из куки
		$this->Rule->getRules($this->user_role);
//		print_r($this->id);
		//	$this->getAuth()->e_loginProcess(); // выполняем авторизацию по айпи
		$this->accessDenied = array($this, 'defaultAccessDenied');
//		$this->setId($this->id);
	}

	public function setId($id)
	{
		
		$this->id = $id;
//		print_r("ID=".$id."ID=".$this->id);
//		ob_get_clean();
//		die();
//		$role = $this->DBProc->get_role($this->id);
		
//		print_r($this->auth);
//		$this->DBProc->set_current_id_user($this->id);
//		$this->userData = $this->DBProc->O(array('extR' => true))->get_user_data();
//		print_r($this->id);
	}
	
	function getId()
	{
		//return $this->id;
		return $this->id;
	}
	
	function getRealId()
	{
		//return $this->id;
		return $this->real_id;
	}

	public function isLogedIn()
	{
		return $this->user_role != "unregistered";
	}

	public function showLoginBox()
	{
		/* if ($this->user_role == "unregistered")
			$this->loadView('login_form');
		else
			$this->loadView('logedin_box'); */
			$this->loadView('menu_box'); 
			
	}

	/**
	 * Возвращает текущий компонент для аутентификации
	 * @return IAuth
	 */
	function getAuth()
	{
		return $this->{$this->auth};
	}

	/**
	 * Функция вызов которой во время логина необходим в каждом компоненте аутентификации для
	 *  занесения в базу ip пользователя
	 */
	protected function saveUserIp($id=null)
	{
		if ($id === null)
			$id = $this->id;
		$this->DBProc->login_save_ip($id, getenv("REMOTE_ADDR"));
	}

	/**
	 * Пробует востановить пользователя сохраненного в куки. Вызываеться при отсутствии сессии
	 *
	 * Если есть куки - запускает процедуру с входными параметрами cookie_public
	 * и текущий ip. Процедура ищет в базе cookie_private сгренерированный с
	 * использованием ip, если не находит пробует найти cookie_private без
	 * привязки к ip. Процедура возвращает id_user и метод аутентификации.
	 *
	 * На стороне php вызываеться {@link IAuth::deserializeFromCookies} нужного метода аутентификации
	 *
	 * @see saveToCookies подробнее описания cookie_public и cookie_private
	 * @return boolean Если удалось востановить true, иначе false
	 */
	private function tryRestoreFromCookies()
	{
		//$this->mode = "edit";
		if (isset($_COOKIE['cmsid'])) {
			$data = $this->DBProc->O(array('extR' => true))->
					login_from_cookie($_COOKIE['cmsid'], getenv("REMOTE_ADDR"));
			if ($data === array()) {
				return false;
				}
			else {
				//file_put_contents('user.txt',print_r($data,true)." id=".$this->id."role=".$this->user_role ,FILE_APPEND);
				$this->real_id = $data['id'];
				$tmp_id = $this->id;
				if (empty($tmp_id))
					$this->id = $data['id'];
				$this->auth = $data['auth'];
				$this->real_user_role = $data['role'];
				$tmp_role =  $this->user_role;
				if (empty($tmp_role) or $tmp_role == 'unregistered')
					$this->user_role = $data['role'];
				$this->Rule->getRules($this->user_role);
				//$this->mode = "view";
				//$this->Rule->getCmpRules($this->user_role);
				return true;
			}
		}
		$this->mode = "view";
		return false;
	}

	/**
	 * Сохраняет пользователя в куки. Вызываеться при логине
	 *
	 * Запускает процедуру с входными параметрами id_user, ip (или пустая
	 * строка если не заданна привязка к ip) и метод аутентификации и получаем на выход значие cookie_public
	 *
	 * Внутри функция генерирует cookie_public (возвращает) и cookie_private (записывает в базу)
	 * и записывает в базу метод аутентификации
	 *
	 * cookie_public - значение хранящаеся на стороне пользователя.
	 * генерируеться как MD5(RAND())
	 *
	 * cookie_private - значение хранящаеся в базе данных. генерируется как
	 * MD5(CONCAT(cookie_public, ip, salt)), где salt - любое значение константное
	 * для сайта и не известное пользователю. может храниться в базе данных или
	 * задаваться литерально в самой процедуре
	 *
	 * @param boolean $bindToIp Необходимо ли привязывать сохраненные куки к ip пользователя
	 */
	protected function saveToCookies($bindToIp)
	{
//		print_r("AUTH=".$this->auth);
//		print_r("ID=".$this->id);
//		echo ob_get_clean();
//		die();
		setcookie('cmsid', $this->DBProc->O(array('extR' => true, 'extC' => true))->save_to_cookie($bindToIp ? getenv("REMOTE_ADDR") : null, $this->auth, $this->id), time() + 60 * 60 * 24 * 30, '/');
		//var_dump($_COOKIE);
	}

	/**
	 * Кеширует все разрешения
	 */
	private function chachePermissions()
	{
		$chache = $this->DBProc->chache_permissions();
		foreach ($chache as $perm) {
			switch ($perm['value'])
			{
				case 2:
					$val = false;
					break;
				case 1:
					$val = true;
					break;
				case 0:
					$val = array();
					break;
			}
			$this->permissionsChache[$perm['resource']][$perm['action']] = $val;
		}
	}

	/**
	 * Устанавливает полученый ранее кэш разрешений на определенный ресурс
	 *
	 * @param string $resource Имя ресурса
	 * @param array(array(string=>int|boolean)) $chache Двумерный масив
	 * вида <code>array(array('id'=>int, string $action=>boolean))</code>
	 */
	public function setPermissionResourceChache($resource, $chache)
	{

	}

	/**
	 * Проверяет наличие разрешение на выполнение действия $action над ресурсом
	 * $resource с идентификатором $id
	 *
	 * Для порверки беруться данные из кэша. Если кэш не был установлен заранее
	 * с помощью {@link setPermissionResourceChache}, то он будетзапрошен из базы данных
	 *
	 * @param string $resource Название ресурса
	 * @param string $action Название действия которое будет производиться с ресурсом
	 * @param int $id Идентификатор ресурса. Используеться только при ограничении
	 * доступа к некоторым ресурсам
	 * @return boolean Если доступ разрешен будет возвращено true. Если доступ
	 * запрещен, или разрешен для некоторых ресурсов данного типа, но идентификатор
	 * не указан, будет возвращено false
	 */
	public function checkAccess($resource, $action, $id=null)
	{
		if ($this->permissionsChache === null) // если нету кэша разрешений - получаем
			$this->chachePermissions();
		$permVal = $this->permissionsChache[$resource][$action];
		if (!is_array($permVal)) // если нет различия для разных айди ресурсов
			return (bool) $permVal; // возвращем значение
		else if ($id === null) // если есть различия, а айди не задан - запрещаем доступ
			return false;
		else if (is_bool($permVal[$id])) // Если в кеше есть значене для текущего айди
			return $permVal[$id]; // возвращем значение
		else {
			// иначе получаем значения из бд
			$this->DBProc->StartBuffer(array('extC' => true, 'extR' => true));
			$this->DBProc->check_permission($resource, $action, $this->id, $id, '@permVal');
			$this->DBProc->Get('@permVal');
			$permVal = $this->DBProc->CallBuffer();
			$this->permissionsChache[$resource][$action][$id] = $permVal; // и кешируем
			return $permVal;
		}
	}

	/**
	 * Ограничивает доступ к виду наличием разрешения на выполнение действия
	 * $action над ресурсом $resource с идентификатором $id
	 *
	 * Может быть вызван только внутри вида
	 *
	 * В случае отсутствия доступа вызывает $function, или если она не заданна,
	 * то вызымаеться {@link pageDenied обработчик по умолчанию отсутствия доступа к странице}
	 *
	 * @param string $resource Название ресурса
	 * @param string $action Название действия которое будет производиться с ресурсом
	 * @param int $id Идентификатор ресурса. Используеться только при ограничении
	 * доступа к некоторым ресурсам
	 * @param function(string,string,int) $function Пользовательская функция для
	 * обработки запрета доступа к странице. В качестве параметров будут переданы
	 * ресурс действие и айди. Если не задано будет выполненa функция по
	 * умолчанию {@link pageDenied}
	 */
	public function checkViewAccess($resource, $action, $id=null, $function=null)
	{
		if (!$this->checkAccess($resource, $action, $id)) {
			if ($function === null)
				call_user_func($this->accessDenied, $resource, $action, $id);
			else
				call_user_func($function, $resource, $action, $id);
			throw new ViewExit(); // Выход из текущего вида
		}
	}
	
	/**
	 * Ограничивает доступ к странице наличием разрешения на выполнение действия
	 * $action над ресурсом $resource с идентификатором $id
	 *
	 * В случае отсутствия доступа вызывает $function, или если она не заданна,
	 * то вызымаеться {@link pageDenied обработчик по умолчанию отсутствия доступа к странице}
	 *
	 * @param string $resource Название ресурса
	 * @param string $action Название действия которое будет производиться с ресурсом
	 * @param int $id Идентификатор ресурса. Используеться только при ограничении
	 * доступа к некоторым ресурсам
	 * @param function(string,string,int) $function Пользовательская функция для
	 * обработки запрета доступа к странице. В качестве параметров будут переданы
	 * ресурс действие и айди. Если не задано будет выполненa функция по
	 * умолчанию {@link pageDenied}
	 */
	public function checkPageAccess($resource, $action, $id=null, $function=null)
	{
		if (!$this->checkAccess($resource, $action, $id)) {
			if ($function === null)
				call_user_func($this->accessDenied, $resource, $action, $id);
			else
				call_user_func($function, $resource, $action, $id);
			throw new PageExit(); // Выход из обработки текущей страницы
		}
	}

	private function defaultAccessDenied($resource, $action, $id)
	{
		$message = "Попытка не авторизированного доступа к странице c ограниченым доступом";
		$idnote = $id === null ? '' : "c  id $id";
		$notes = "Отсутсует разрешение на действие $action над рессурсом $resource $idnote для пользователя $this->id";
		$this->addEvent(SEQURITY, $message, $notes);
	}

	public function e_Main()
	{
		$this->EmailPasAuth->showLoginForm();
	}

	/**
	 * Событийная функция показывающая страницу профиля пользователя
	 *
	 * @param int $idUser ID пользователя
	 */
	public function e_View($idUser)
	{

	}

	/**
	 * Событийная функция показывающая список пользователей
	 */
	public function e_List()
	{

	}

	/**
	 * Событийная функция показывающая страницу редактирования профиля (своего)
	 */
	public function e_Profile()
	{

	}
	
	// Изменение режима редактирования
	public function e_ChangeEditMode() {
		if ($this->mode == "view")
			$this->mode = "edit";
		else
			$this->mode = "view";
		$this->redirect(getenv("HTTP_REFERER")); // возращаемся на предыдущую страницу
	}
	
	// Восстановление роли пользователя
	public function e_RestoreRole(){
		$this->id = $this->real_id;
		$this->user_role = $this->real_user_role;
		$this->Rule->getRules($this->user_role);
		return true;
	}
	
	// Если сменен пользователь, возвращает true, если оригинальная роль - false 
	public function is_user_changed(){
		return ($this->id != $this->real_id);
	}
	
	// Если роль сменена, возвращает true, если оригинальная роль - false 
	public function is_role_changed(){
		$tmp_role = $this->user_role;
		$tmp_real_role = $this->real_user_role;
	    return ($this->user_role != $this->real_user_role);
	}
	
	// Изменение пользователя
	public function e_ChangeUser($id_user){
		if (empty($id_user)) return;
		if ($this->real_user_role != 'administrator') {
			echo "У вас нет прав на выполнение операции по смене пользователя";
			return false;
		}
		$user = end($this->getUser($id_user));
		// Если такой пользователь существует - загружаем его.
		//file_put_contents('user.txt', print_r($user, true),FILE_APPEND);
		if (!empty($user)) {
			$this->id = $id_user;
			$this->user_role = $user['role'];
			$this->Rule->getRules($this->user_role);
		}
		return true;
	}

	
	// Выход пользователя 
	public function e_SecurityCancel() {
		
		$this->Contenter->set_cell('usr_user', 'cookie_university', $this->id, $value);
		$this->redirect(getenv("HTTP_REFERER")); // возращаемся на предыдущую страницу
	}
	
	function getUser($id_user){
		return $this->DBProc->view($id_user);
	}
	
	/**
	 * Возвращает текущий режим
	 */
	function getMode()
	{
		return $this->mode;
	}
	
	function setMode($mode)
	{
		$this->mode = $mode;
	}
	
	function getRole()
	{
		return $this->user_role;
	}
	
	function getRealRole()
	{
		return $this->real_user_role;
	}
	
	function getName()
	{
		return "Іван Семенович";
	}
	
	function setRole($role)
	{
		$this->user_role = $role;
	}
	
	function Delete($id_user)
	{
		$this->DBProc->delete($id_user);
	}
	
	//function register
	
}