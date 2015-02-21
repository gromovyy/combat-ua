<?php
/**
 * Содержит класс EmailPasAuth
 * @package IC-CMS
 * @subpackage User
 */

/**
 * Класс EmailPasAuth отвечает аутентификацию пользователя с помощю эмейла и пароля
 *
 * @requirement {@link User}
 * @requirement {@link Auth}
 * @requirement {@link DBProc}
 * @modified v1.0 от 10 января 2012 года
 * @version 1.0
 * @author Иван Найдёнов
 * @package IC-CMS
 * @subpackage User
 */
class EmailPasAuth extends User implements IAuth
{

	/**
	 * Опция, показывает разрешена ли регистрация новых пользователей
	 * @var boolean
	 */
	public $allowRegister = true;

	/**
	 * {@inheritdoc}. Не используеться
	 */
	
	public function e_RegisterForm($message=''){
		//Если пользователь уже зарегистрирован, перенаправляем его на его личную страничку
		/*if ($this->User->getRole() !="unregistered") {
			ob_get_clean();
			// Перенаправление на страничку с формой регистрации.
			exit;
		}*/
		$this->loadView('register_form', array('message'=>$message));
		
	}
	
	public function e_RegisterFormTitle(){
		$this->loadView('register_form_title');
	}
	
	public function e_RegisterInstruction(){
		//Если пользователь уже зарегистрирован, перенаправляем его на его личную страничку
		if ($this->User->getRole() !="unregistered") {
			
			return;
		}
		$this->loadView('register_instruction');
	}
	
	public function e_RegisterUser(){


		//Если не существует, регистрируем пользователя
		$this->RegisterUser($_POST['email'],$_POST['password'], $_POST, true);

		ob_get_clean();
		// Возвращение на предыдущую страничку.
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
		//ob_get_clean();
		// Перенаправление на страничку с инструкциями о регистрации.
		//header("Location:"."http://".$_SERVER['HTTP_HOST']."/Инструкции-по-регистрации");
		//return true;
		//exit;
	}

	public function RegisterUser($email,$pass, $member =  array( ), $activate = false)
	{

		global $GL_EMAIL;
		//Проверка, существует ли пользователь с таким email
		//Если существует - сообщаем ему об этом
		$id_user = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_user_by_email($email);
		if (!empty($id_user)) {
			ob_get_clean();
			// Перенаправление на страничку с формой регистрации.
			header("Location: http://".$_SERVER['HTTP_HOST']."/Форма-регистрации/Такой-email-уже-существует");
			exit;	
		}
		// Если пользователя с таким email не существует регистрируем
		$this->DBProc->register_user($email, $pass);
		$id_user = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_user_by_email($email);
		

		if ($activate) {
			$this->Contenter->set_cell("usr_user","status",$id_user,"active");
		}else{
			//Отправляем пользователю email с ключем.
			//Получаем ключ
			$email_key = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_key($id_user);
			$url = "http://".$_SERVER['HTTP_HOST']."/Подтверждение-регистрации/1/$email_key/$id_user";
			$this->SendEmail($_POST['email'],$GL_EMAIL,$url,$_POST['password']);
		}

		//Создаем участника с именем и связываем его с текущим пользователем
		$id_user = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_user_by_email($email);
		$id_member = $this->Member->R(ADMIN)->Insert($id_user,"player", $member['name'],$member['surname'], $member['phone'], $member['email']);

		// Переводим пользователя на страницу с инструкциями о регитрации


		return $id_user;
	}

	function e_SubmitView() {
		$this->loadView("submit_view");
	}
	
	
	function e_SubmitRegistration($key, $id_user) {
		$id_user = $this->DBProc->O(array('extC' => true, 'extR' => true))->set_active($key, $id_user);
		if(!empty($id_user))
			$this->loadView("success_view");
		else 
			$this->loadView("fail_view");
	}
	
	function e_AuthorizationError(){
		$this->loadView("authorization_error");
	}
	
 
	public function serializeToCookies()
	{

	}

	/**
	 * {@inheritdoc}. Не используеться
	 */
	public function deserializeFromCookies()
	{

	}

	/**
	 * Показывает поля для ввода эмейла и пароля
	 */
	public function e_LoginForm()
	{
		$this->loadView('login_form');
	}

	/**
	 * Обработка формы логина
	 * @return void
	 */
	public function e_loginProcess($url = null)
	{
		$email = $_POST['email'];
		$password = $_POST['password'];
		$cookie = $_POST['cookie'] === 'checked';
		$ip = $_POST['ip'] === 'checked';
		// Получаем идентификатор пользователя если правельный логин - пароль. экранирование проходит автоматически
		$data = $this->DBProc->O(array('extC' => true, 'extR' => true))->authenticate($email, $password);
		//$this->saveUserIp('@id_user'); // Сохраняем ip пользователя
		
		if ($data['id_user'] == null) { // Если идентификатор не получен, значит нет такой пары логин-пароль
//			$this->addEvent(ERROR, 'Логин или пароль не верны', "");
			ob_get_clean();

	
			// Перенаправление на страничку с формой регистрации.
			header("Location:"."//".$_SERVER['HTTP_HOST'].self::$baseUrl."Ошибка-авторизации");
			return;
		}
		$this->auth = "EmailPasAuth";
		$this->User->setMode("view");
		$this->User->setId($data['id_user']);
		$this->User->setRole($data['role']);
		$this->Rule->getRules($this->user_role);
		$this->User->saveToCookies(true); // запоменаем в куки
		$redirectTo = ($url) ? $url : getenv("HTTP_REFERER") ;
		$this->redirect($redirectTo); // возращаемся на предыдущую страницу
	}

	/**
	 * Страница формы регистрации
	 */
	public function e_Register()
	{
		if ($allowRegister) { // если регистрация разрешена
			$this->loadView('RegisterPage');
		}
		else
			$this->addEvent(ERROR, "Регистрация запрещена");
	}

	/**
	 * Страница формы регистрации
	 */
	public function e_FormReminder()
	{
		if ($allowRegister) { // если регистрация разрешена
			$this->loadView('reminder_form');
		}
		else
			$this->addEvent(ERROR, "Невозможно восстановить пароль");
	}

	// Отправка сообщения о потверждении регистрации
	function SendEmail($to,$from,$url,$passw)
		{
		global $GL_SITE_NAME;
        $title = "$GL_SITE_NAME подтверждение регистрации";	
		$mess = '<html><body>Чтобы подтвердить регистрацию на сайте '.$_SERVER['HTTP_HOST'].', перейдите по следующей ссылке<br><br>';
		$mess .= $url.'<br>';
		$mess .= 'Если Вы не регистрировались на сайте '.$_SERVER['HTTP_HOST'].' - просто проигнорируйте это письмо<br><br>';
		$mess .= 'С наилучшими пожеланиями, Администрация сайта '.$_SERVER['HTTP_HOST'].'<br><br>';
		$mess .= 'Логин для входа на сайт:'.$to.'<br>';
		$mess .= 'Пароль для входа на сайт:'.$passw.'<br>';
		$mess .= '</body></html>';
		
		
		$headers  .= "MIME-Version: 1.0\r\n";
		$headers  .= "Content-type: text/html; charset=UTF-8\r\n"; 
		$headers .= "From:".$from; 
		mail($to, '=?UTF-8?B?'.base64_encode($title).'?=', $mess, $headers);
	}
		///////////восстановление пароля.......................
	public function e_ShowRecoverForm ($status='') {
		$data['status'] = $status;
		
		$this->loadView('pass_recover',$data);
	}
	public function e_ShowRecoverStatus () {
		
		$this->loadView('status_recover');
	}
	function e_PasswordRecover() {
		global $GL_SITE_NAME;
			$is_email_valid = $this->DBProc->O(array('extC' => true, 'extR' => true))->is_email_valid($_POST['email']);
			$email = $_POST['email'];
			if (empty($is_email_valid)) {
				$this->redirect('Нагадати-пароль/1');
				exit;
			} 
			$id = $is_email_valid['id_user'];
			$user_data=     $this->DBProc->O(array('extC' => true, 'extR' => true))->get_name_by_id_user($email);
			$name     =     $user_data['name'];
			$to       =     "$name < $email >, ";
			$email_key=     $this->KeyGenerator($email);
			$this->Contenter->set_cell("usr_user","email_key",$id,$email_key);
	        $title    =     "$GL_SITE_NAME Вiдновлення паролю";
			$mess     =     '<html><body>Для того щоб вiдновити пароль перейдiть по цiй адресi:<br>';
			$url      =     "http://".$_SERVER['HTTP_HOST'].self::$baseUrl."Підтвердження-зміни/".$email_key."/".$user_data['id_user'];
			$mess    .=     "<a href='$url'>".$url."</a>";
			$mess    .=     '</body></html>';
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\r\n";
			$headers .= "From:".$_SERVER['HTTP_HOST'];
			mail($to, '=?UTF-8?B?'.base64_encode($title).'?=', $mess, $headers);
			ob_get_clean();
			$this->redirect('Нагадати-пароль/2');
	}


	public function e_SetNewPass() {
			$user = $this->DBProc->O(array('extC' => true, 'extR' => true))->is_key_valid($_POST['key'],$_POST['id']);
			$new_pass = md5($_POST['new_pass'].'pdzjnb');
			$this->Contenter->set_cell("usr_user","password",$user['id_user'],$new_pass);
			$this->Contenter->set_cell("usr_user","email_key",$user['id_user'],null);
			$this->redirect('Зміна-паролю/1');
		}
	public function e_RecoverEnd($status) {

			$data['status'] = $status;
			$this->loadView('end',$data);
		}

	public function KeyGenerator($email) {
		$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
		$numChars = strlen($chars);
		$string = '';
		for ($i = 0; $i < 6; $i++) {
		  $string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
			$email_key = md5($email.$string);
		return $email_key;
	}
	public function e_PasswordRecoverConfirm($key,$id) {
			$status = $this->DBProc->O(array('extC' => true, 'extR' => true))->is_key_valid($key,$id);
			if (!empty($status)) {
				$data['key'] = $key;
				$data['status'] = 1;
				$data['id'] = $id;
				
			} else {
				$data['status'] = 0;
			}
			$this->loadView('pass_recover_confirm',$data);
		}

////////////////////////////////////////////////////////////////////////////////////
	
}
