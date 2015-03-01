<?php
/**
 * Содержит класс Base и глобальные константы уровней событий
 * @package IC-CMS
 * @subpackage Core
 */

 
define('FORBIDDEN', 0); // Комбинация прав select = 0, insert = 0, update = 0, delete = 0
define('ADMIN', 1);  	// Комбинация прав select = 1, insert = 1, update = 1, delete = 1
define('READER', 2);    // Комбинация прав select = 1, insert = 0, update = 0, delete = 0
define('OWNER', 3);		// Комбинация прав select = 1, insert = 1, update = 2, delete = 2
define('SELECT', 4);    // select = 1
define('INSERT', 5);    // insert = 1
define('UPDATE', 6);       // update = 1
define('UPDATE_OWNER', 7); // update = 2
define('DELETE', 8);    	// delete = 1
define('DELETE_OWNER', 9);  // delete = 2
 
 
/**
 * Константа показующаяя DEBUG уровень события.
 * Используеться для вывода отладочной информации
 */

 
define('DEBUG', 0);

/**
 * Константа показующаяя EVENT уровень события.
 */
define('EVENT', 1);

/**
 * Константа показующаяя INFO уровень события.
 */
define('INFO', 2);

/**
 * Константа показующаяя WARNING уровень события.
 * Используеться для вывода информации об ошибках, не влияющих на возможность
 * закончить текущую операцию
 */
define('WARNING', 3);

/**
 * Константа показующаяя ERROR уровень события.
 * Используеться для вывода информации об ошибках, при которых текущую операцию
 * закончить невозможно
 */
define('ERROR', 4);

/**
 * Константа показующаяя SEQURITY уровень события.
 * Используеться для вывода информации об ошибках, которые несут себе опасность
 * того, что кто-то пытаеться взломать систему
 */
define('SEQURITY', 5);

/**
 * Базовый класс для всех компонентов CIT-CMS
 *
 * Класс Base обеспечивает автоматическую подгрузку необходимых компонентов.
 *
 * Все наследники данного класса умеют создавать динамически объект компонента
 * в момент его первого использования
 *
 * Возможности класса:
 * - Динамический доступ к компонентам по имени - функция  __get($component)
 * - Возможность добавления сообщений (при наличии компонента DB)
 *
 * @modified v1.0 от 02 августа 2011 года
 * @property-read DBProc $DBProc
 * @property-read Session $Session
 * @property-read User $User
 * @property-read UnregIPAuth $UnregIPAuth
 * @property-read EmailPasAuth $EmailPasAuth
 * @property-read Tester $Tester
 * @property mixed Console
 * @version 1.0
 * @author Александр Громовой
 * @package IC-CMS
 * @subpackage Core
 */
class Base
{

	/**
	 * Массив для  хранения динамически-создаваемых компонентов
	 * @static
	 * @var array
	 */
	protected static $components = array();

	/**
	 * Массив для  хранения динамически-создаваемых компонентов
	 * @static
	 * @var array
	 */
	protected static $cmp_rules = array();
	
	/**
	 * Базовая часть ссылки. Ссылка на папку содержащую index.php
	 * @var string
	 */

	 
	public static $baseUrl;
	public static $sql_log_buffer;

	/**
	 * Переменная хранит имя компонента
	 * @var string
	 */
	var $component;
	


	/**
	 * Переменная хранит версию компонента
	 * @var string
	 */
	var $version;
	
	/**
	 * Переменная хранит БД префикс компонента
	 *
	 * БД префикс используеться в названиях сущостей БД относящихся к этому
	 * компоненту. По умолчанию соответствует названию компонента без строчных
	 * гласных букв, например для Base - bs, а для ITMaster - itmstr
	 *
	 * @var string
	 */
	var $dbprefix;	
	
	/**
	 * Переменная хранит суффикс компонента
	 *
	 * БД префикс используеться в названиях сущостей БД относящихся к этому
	 * компоненту. По умолчанию соответствует названию компонента без строчных
	 * гласных букв, например для Base - bs, а для ITMaster - itmstr
	 *
	 * @var string
	 */
	var $sufix;

	/**
	 * Переменная для блокировки добавления новых сообщений
	 *
	 * FALSE - разрешено
	 *
	 * TRUE - запрещено
	 *
	 * @static
	 * @var bool
	 */
	protected static $is_block_event = FALSE;
	 
	 function getR($type) {
		// Если компонента не существует в массиве правил, добавляем компонент.
		if (!isset(self::$cmp_rules[$this->component]))
			{
			$this->Rule->insertCmpRule($this->component);
			$this->Rule->getCmpRules($this->User->getRole());
		}
		// Если входной тип не равен одному из 4 значений - возвращаем предупреждение
		if (($type != "select") and ($type != "insert") and ($type != "delete") and ($type != "update") and ($type != "visibility")) {
			echo "Недопусимый тип правила доступа к компоненту $type ;";
			return null;
		}
		// Возвращаем правило
		return self::$cmp_rules[$this->component][$type];
	} 
	 
	 /**
	 * Устанавливает текущую роль компонента
	 * возвращает обект для дальнейшей работы
	 * @param type $role
	 * @return Component
	 */
	public function R($role)
	{
		// Если в таблице прав компонента нет прав для данного компонента - добавляем их в БД
		// По умолчанию устанавливаются все права Администратора
		if (!isset(self::$cmp_rules[$this->component]))
			{
			$this->Rule->insertCmpRule($this->component);
			$this->Rule->getCmpRules($this->User->getRole());
		}
		switch ($role) {
			case FORBIDDEN: self::$cmp_rules[$this->component]["select"] = 0;
							self::$cmp_rules[$this->component]["insert"] = 0;
							self::$cmp_rules[$this->component]["update"] = 0;
							self::$cmp_rules[$this->component]["delete"] = 0;
							break;
			case ADMIN: 	self::$cmp_rules[$this->component]["select"] = 1;
							self::$cmp_rules[$this->component]["insert"] = 1;
							self::$cmp_rules[$this->component]["update"] = 1;
							self::$cmp_rules[$this->component]["delete"] = 1;
							break;
			case READER: 	self::$cmp_rules[$this->component]["select"] = 1;
							self::$cmp_rules[$this->component]["insert"] = 0;
							self::$cmp_rules[$this->component]["update"] = 0;
							self::$cmp_rules[$this->component]["delete"] = 0;
							break;
			case OWNER: 	self::$cmp_rules[$this->component]["select"] = 1;
							self::$cmp_rules[$this->component]["insert"] = 1;
							self::$cmp_rules[$this->component]["update"] = 2;
							self::$cmp_rules[$this->component]["delete"] = 2;
							break;
			case SELECT: 	self::$cmp_rules[$this->component]["select"] = 1;
							break;
			case INSERT: 	self::$cmp_rules[$this->component]["insert"] = 1;
							break;
			case UPDATE: 	self::$cmp_rules[$this->component]["update"] = 1;
							break;
			case UPDATE_OWNER: 	self::$cmp_rules[$this->component]["update"] = 2;
							break;
			case DELETE: 	self::$cmp_rules[$this->component]["delete"] = 1;
							break;
			case DELETE_OWNER: 	self::$cmp_rules[$this->component]["delete"] = 2;
							break;
		
		}
		return $this;
	}
	
	// Получаем все права в виде массива
	public function getAllRules(){
		return self::$cmp_rules[$this->component];
	}
	
	// Устанавливаем все права в виде массива
	public function setAllRules($rules_array){
		self::$cmp_rules[$this->component] = $rules_array;
		return $this;
	}
	
	
	
	
	

	

	
	// Очищает апсолютно все. Точка.
	function getTextFromHTML($htmlText)
	{
		//$content = $this->Safehtml->parse($htmlText);
		//file_put_contents('content.txt',$htmlText);
		//file_put_contents('content.txt',$content, FILE_APPEND);
		
		/* $search = array ("'<script[^>]*?>.*?</script>'si",  // Remove javaScript 
		   "'<div[^>]*?>'si",  // Remove styles 
		   "'</div>'si",
		   "'<script[^>]*?>'si",
		   "'</script>'si");                    // write as php

		$replace = array ("", 
						  "", 
						  "",
						  "",
						  ""); 
						  
		return preg_replace($search, $replace, $htmlText);*/
		$obg = new Safehtml;
		return $obg->parse($htmlText);
	}


	/**
	 * Базовый конструктор компонента.
	 *
	 * Устанавливает имя версию и префикс бд по умолчанию. Имя по умолчанию
	 * соответствует имени класса, версия по умолчанию - 1.0, а бд префикс по
	 * умолчанию - имя без строчных гласных букв, преведенное в нижний регистр
	 *
	 * Так же прописывает в консоль сообщене на уровне DEBUG о том что
	 * компонент создался
	 */
	protected function __construct()
	{
		$this->component = get_class($this);
		$this->dbprefix = strtolower(preg_replace('/[aeiouy]/', '', $this->component));
		$this->sufix = strtolower($this->component);
		$this->version = "1.0";

	}

	/**
	 * Проверяет сущевствоване компонента по его имени
	 *
	 * Магический метод __isset вызываеться при проверки сущесвования поля
	 * $component, например isset($this->User). Проверяет сущесвоване компонента
	 * посредством проверки сущесвования php файла, содержащего этот компонент
	 *
	 * @param string $component Имя проверяемого компонента
	 * @return bool true - если компонент сущевствет. Иначе false
	 */
	public function __isset($component)
	{
		return (
			file_exists("cmp/$component/$component.php") or 
			file_exists("cmp_system/$component/$component.php") or
			file_exists("cmp/{$this->component}/model/$component.php") or
			file_exists("cmp_system/{$this->component}/model/$component.php")// or
			//file_exists("lib/php/$component/$component.php") or
			//file_exists("lib/php/$component.php")
			);
	}

	/**
	 * Получает обект компонента по его имени или сессионное поле
	 *
	 * Магический метод __get запускается, когда идут вызовы к необъявленным атрибутам
	 * класса Base. Если имя атрибута имеет имя одного из компонентов -
	 * компонент создается динамически и сохраняется в переменную ассоциативного
	 * массива $components по имени компонента. Если происходит повторное
	 * обращение к компоненту - тогда возвращается уже существующий объект
	 * в массиве $components
	 *
	 * Если существует компонент сессия пробуем получить сессионое поле с
	 * помощью вызова функции {@link magicGetEmbedding}
	 *
	 * @param string $component Имя вызываемого компонента или сессионного поля
	 * @return Base|mixed Обект вызываемого компонента или значение сессионного поля
	 */
	public function &__get($component)
	{
		// Если зарезервированное слово - Model, тогда ищем класс модели текущего компонента
		if ($component == 'Model')
			$component = $this->component.'Model';

		if (isset($this->$component)) { // Проверяем сществует ли такой компонент
			if (class_exists($component)) { // Если компонент - класс - получаем его объект
				// Если компонент уже создан - возвращаем результат
				if (array_key_exists($component, self::$components)) {
							
				}
				// Если компонент еще не создан - создаем
				else {
					self::$components[$component] = new $component();
					// Устанавливаем права доступа к компоненту
					//$this->Rule->setCmpRule($component);
					// если в компоненте есть метод _init, для дополнительной инициализации
					if (method_exists($component, '_init')) {
						$refl = new ReflectionMethod($component, '_init');
						// только если метод _init объявлен в этом классе, а не унаследован
						if ($refl->class == $component)
							self::$components[$component]->_init();
						}
					}
					
				return self::$components[$component];
			}
			else { // иначе, если интерфейс
				$this->addEvent(ERROR, "Попытка обращения к интерфейсу $component как к компоненту");
				return null;
			}
		}
		else if (isset($this->Session)) { // Если Сущевствует компонент Session пробуем получить сессионное поле
			return $this->Session->magicGetEmbedding($component);
		}
		else {
			$this->addEvent(ERROR, "Попытка обращения к несуществующему свойству");
			return null;
		}
	}

	/**
	 * Устанавливает значение сессионного поля
	 *
	 * Если существует компонент сессия пробуем установить  значение сессионного
	 * поля с помощью вызова функции {@link magicSetEmbedding}
	 *
	 * @param string $name Имя устанавлеваемого сессионного поля
	 * @param mixed $value Устанавлеваеме значение сессионного поля
	 */
	public function __set($name, $value)
	{
		if (!isset($this->$name) && isset($this->Session)) {
			$this->Session->magicSetEmbedding($name, $value);
		}
	}

	/**
	 * Родительский диспатч. Вызывается в последнем случае. Альтеративый вариант
	 * реализации голбальных событий
	 * @param string $cmp Вызываемый компонент
	 * @param string $evt Вызываемое событие
	 * @param array $args Остальные аргументы
	 */
	function Dispatch($cmp, $evt, $args)
	{
		switch ($evt)
		{
			// Встроенная возможность теcтирования компонентов
			case 'test':
				$this->Tester->loadView("test_$cmp", NULL, $cmp);
				break;
			default:
				$this->addEvent(SEQURITY, "Попытка доступа к несуществующему событию $evt компонента $cmp");
				break;
		}
	}

	/**
	 * Процедура добавляет событие в таблицу событий
	 *
	 * Процедура сама определяет сайт, компонент, IP пользователя,
	 * URL с которого идет запрос, URL куда идет пользователь, время события
	 * и добавляет их в таблицу event.
	 *
	 * 
	 * @param int $type Уровень сообщения. Может быть одним из значений
	 * {@link DEBUG}, {@link EVENT}, {@link INFO}, {@link WARNING}, {@link ERROR}
	 * или {@link SEQURITY}
	 * @param string $message Сообщение, описывающее событие
	 * @param string $notes Опциональная информация описывающая событие
	 * @return void
	 */
	public function addEvent($type, $message, $notes = NULL)
	{
		//Если установлена блокировка добавления сообщений - выход
		if (self::$is_block_event)
			return;

		// Выводим сообщение пользователю - для тестирования.
		if ($type > 3) {
			echo "<font color='red'>$message</font><br>";
			echo $notes;
			//print_r(debug_backtrace(false));
		}
		// Добавляем дополнительные данные 
		$notes .= '<br>';
		$site = $_SERVER['HTTP_HOST'];

		$user_ip = getenv("REMOTE_ADDR"); // получаем IP посетителя
		$url_from = urldecode(getenv("HTTP_REFERER")); // получает URL, с которого пришёл посетитель
		$url_to = urldecode(getenv("REQUEST_URI")); // получаем относительный адрес странички,
		$trace = print_r(debug_backtrace(), true);
		// Вызываем процедуру вставки событий, блокируя повторный вызов функции AddEvent,
		// чтобы не получилось бесконечного цикла.
		self::$is_block_event = TRUE;
		$this->DBProc->insert_event($site, $this->component, $this->version, $user_ip, $message, $type, $url_from, $url_to, $notes);
		self::$is_block_event = FALSE;
	}

	/**
	 * Получение новых событий с применением фильтров по сайту, компоненту,
	 * дате и типу,  а также у которых id_message > id_last_message..
	 *
	 * @param string $site Фильтр по имени сайта
	 * @param string $component Фильтр по имени компонента
	 * @param mixed $date_from Начальная дата фильтра по дате
	 * @param mixed $date_to Конечная дата фильтра по дате
	 * @param int $type Фильтр по уровню собыия
	 * @param int $id_last_event Фильтр по последнему id события
	 * @param string $limit Ограничение количевства записей, по умолжанию первые 50
	 * @return array Список найденых событий
	 */
	function getEvents($site="", $component="", $date_from="", $date_to="", $type="", $id_last_event="", $limit="0,50")
	{

		//Увеличиваем $date_to на 1 день для выполнения условия сравнения.
		if ($date_to != "") {
			$date_object = new DateTime($date_to);
			$interval = new DateInterval('P1D');
			$date_object->add($interval);
			$date_to = $date_object->format("Y-m-d");
		}

		//Формируем строку условий для запроса выборки новых событий.
		$condition = "1";
		$condition = ($site == "") ? $condition : "$condition AND site = '$site'";
		$condition = ($component == "") ? $condition : "$condition AND component = '$component'";
		$condition = ($date_from == "") ? $condition : "$condition AND event_time > '$date_from'";
		$condition = ($date_to == "") ? $condition : "$condition AND event_time < '$date_to'";
		$condition = ($type == "") ? $condition : "$condition AND type = $type";
		$condition = ($id_last_event == "") ? $condition : "$condition AND id_event > $id_last_event";

//		echo $condition;
//		echo"LIMIT=$limit";
		//Запрос в БД на получение новых событий.
		/* $result = $this->DB->Select("bs_view_event", array("*"), $condition, "id_event DESC", "NULL", $limit);
		$count = $this->DB->Select("bs_view_event", array("COUNT(*) as `count`"), $condition, "id_event DESC", "NULL", 1); */
		
		$querry = $this->DBProc->get_events();
		$result['events'] = $querry;
		$result['count']['events'] = count($result['events']);
			foreach($querry as $event){
				if($event['type']==0){ $result['debug']   [] = $event; }
				if($event['type']==1){ $result['event']   [] = $event; }
				if($event['type']==2){ $result['info']    [] = $event; }
				if($event['type']==3){ $result['warning'] [] = $event; }
				if($event['type']==4){ $result['error']   [] = $event; }
				if($event['type']==5){ $result['security'][] = $event; }
			}
		$result['count']['debug']   = count( $result['debug']   );
		$result['count']['event']   =	count( $result['event']   );
		$result['count']['info']    =	count( $result['info']    );
		$result['count']['warning'] =	count( $result['warning'] );
		$result['count']['error']   =	count( $result['error']   );
		$result['count']['security']=	count( $result['security']);
		
		return $result;		
	}

	/**
	 * Функция возвращает список сайтов, отправлявших события
	 * @return array Cписок сайтов, отправлявших события
	 */
	function getSites()
	{
		return $this->getList("site");
	}

	/**
	 * Функция возвращает список компонентов, отправлявших события
	 * @return array Cписок компонентов, отправлявших события
	 */
	function getComponents()
	{
		return $this->getList("component");
	}



	/**
	 * Функция предназначена для проверки ограничений переменных.
	 * В случае нарушения ограничения функция формирует ошибку с описанием и возвращает FALSE
	 * Если ни одно из ограничений не нарушено - функция возвращает TRUE
	 *
	 * @param array|mixed $param_list Массив параметров или одиночный параметр
	 * @param string|array $constraints Строка или массив с ограничениями для каждого из параметров
	 * @param string $file Имя файла
	 * @param int $line Строка в которой происходит проверка
	 * @param string $function Функция в которой происходит проверка
	 * @return boolean Результат проверки
	 */
	public function checkParams($param_list, $constraints, $file = NULL, $line = NULL, $function = NULL)
	{
		$result = TRUE;

		// Если на вход передается всего один параметр, тогда формируем массив с параметрам
		if (!is_array($param_list)) {
			$params[] = $param_list;
		}
		else
			$params = $param_list;
		if (is_string($constraints)) { // Если ограничения заданны строкой - разбиваем на массив
			// Преобразовываем все буквы строки с ограничениями в нижний регистр
			$constraints = strtolower($constraints);
			// Разбиваем строку с ограничениями по параметрам
			$constr_array = explode(",", $constraints);
		}
		else // Преобразовываем все буквы строк с ограничениями в нижний регистр
			$constr_array = array_map(strtolower, $constraints);

		foreach ($constr_array as $key => $value) {
			// Если не существует соответствующего параметра - продолжаем цикл
			if (!array_key_exists($key, $params)) {
//				$this->addEvent(WARNING, "WARNING PARAMS: Отсутствует входной параметр для ограничения №$key.<br> Функция $function, файл $file, строка $line.");
				continue;
			}
			// Для каждого параметра
			$constraint_list = explode("&", $value);
			foreach ($constraint_list as $constraint) {
				$constr = trim($constraint);
				$param = $params[$key];
				// Если найдена открывающая скобка - значит указан допустимый список значений и  анализ проводится в ветке else.
				if (stripos($constr, "(") === false) {
					switch ($constr)
					{
						case "": break;
						case "not null":
							if (is_null($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр не может быть NULL.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "not empty":
							if (empty($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр не может быть EMPTY.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "int":
							if (!is_int($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть INT.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "int or empty":
							if (!(empty($param) or is_int($param))) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр может быть INT или NULL.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "numeric":
							if (!is_numeric($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть INT.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "string":
							if (!is_string($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть строкой.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "date":
							if (!preg_match('/^\d{2,4}[\W_]?\d{2}[\W_]?\d{2}$/', $param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть датой.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "array":
							if (!is_array($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть массивом.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "null or array":
							if (!(is_array($param) or is_null($param))) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key. Параметр должен быть массивом или NULL.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "file":
							if (!file_exists($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Файл $param не найден.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						case "dir":
							if (!is_dir($param)) {
								$this->addEvent(ERROR, "ERROR_PARAMS:Папка $param не найдена.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
								$result = FALSE;
							}
							break;
						default:
							$this->addEvent(WARNING, "WARNING PARAMS: Недопустимое значение ограничения $constr.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
							break;
					}
				}
				else {
					// Проверка на нахождение в списке допустимыз хначений.
					// Раскрываем скобки
					$constr = preg_replace("![\s]*[\(\)][\s]*!i", "", $constr);
					// Разбиваем строку на список допустимых значений
					$enum = preg_split("![\s]*[:]+[\s]*!i", $constr);
					// Если входной параметр не находится в списке допустимых значений - ошибка.
					if (!in_array(strtolower($params[$key]), $enum)) {
						$this->addEvent(ERROR, "ERROR_PARAMS:Недопустимое значение параметра №$key {$params[$key]}. Параметр должен принимать одно из значений $constr.", $this->retvardump($param) . "Функция $function, файл $file, строка $line.");
						$result = FALSE;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Возвращает результат вызова функции var_dump с переданными аргументами.
	 * @param string ... Произвольное количевтво аргументов.
	 * @return string var_dump переданных аргументов
	 */
	public function retvardump()
	{
		ob_start();
		$var = func_get_args();
		call_user_func_array('var_dump', $var);
		return ob_get_clean();
	}

	/**
	 * Генерирует URL адрес по переданным частям адреса
	 * @param string ... Произвольное количевтво частей адреса.
	 * @return string URL Адрес
	 */
	public function url()
	{
		$arr = array_filter(func_get_args());
		foreach ($arr as &$part) {
			$part = urlencode($part);
		}
		return implode('/', $arr);
	}

	/**
	 * Генерирует URL адрес для просмотра объекта компонента
	 * @param string $cmp Имя компонента на который генерируеться ссылка
	 * @param int $id Идентификатор просматриваемого объекта
	 * @param string $slug Текстовый Идентификатор для поисковой оптимизации
	 * @return string URL Адрес
	 */
	public function urlView($cmp, $id, $slug)
	{
		return $this->url($cmp, 'View', $id, $this->translit($slug));
	}

	/**
	 * Приводит строку в вид пригодный для использования в ссылках.
	 * Производит транслитерацию и очистку от не буквенно-цыферных символов
	 * @param string $str Строка для преобразования
	 * @return string Преобразованная строка
	 */
	public function translit($str)
	{
//		echo "translit".$str;
		// Убираем тэги
		$str = strip_tags($str);
		//Транслитирируем
		$str = strtr($str, array(
			'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Ґ' => 'g',
			'Д' => 'd', 'Е' => 'e', 'Є' => 'ye', 'Ё' => 'ye', 'Ж' => 'zh',
			'З' => 'z', 'І' => 'i', 'Ї' => 'yi', 'И' => 'i', 'Й' => 'y',
			'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o',
			'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
			'Ф' => 'f', 'Х' => 'kh', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh',
			'Щ' => 'shch', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e',
			'Ю' => 'yu', 'Я' => 'ya', 'а' => 'a', 'б' => 'b', 'в' => 'v',
			'г' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'є' => 'ye',
			'ё' => 'ye', 'ж' => 'zh', 'з' => 'z', 'і' => 'i', 'ї' => 'yi',
			'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
			'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
			'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y',
			'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
				)
		);
		//Приводим в нижний регистр
		$str = mb_strtolower($str,"UTF-8");
		//задаем масив правил для замены
		$trans = array(
			'&\#\d+?;'    => '',  // Убрать цифровые html сущности
			'&\S+?;'      => '',  // Убрать символьные html сущности
			'[\s_]+'    => '-', // Пробельные символы и точки заменить на дефис
			'[^a-z0-9\-\.]' => '',  // Убрать все что осталось кроме букв, цифр и дефиса
			'-+'          => '-', // Сократить повторы дефиса
			'-$'          => '',  // Убрать дефис в конце
			'^-'          => ''   // -//- в начале
		);
		foreach ($trans as $key => $val) {
			$str = preg_replace("#" . $key . "#i", $val, $str);
		}
//		echo "translit".$str;
		return trim(stripslashes($str));
	}
	
	

	/**
	 * Переадресует на целевую страницу или на главную, если страница не заданна
	 *
	 * @param string|array $uri Ссылка на целевую страницу в виде стоки или в виде масива частей ссылки
	 * @param string $method Метод переадресации, допустимые значения script, location, refresh
	 * @param int $http_response_code Http код возвращаемый серверу при использовании метода location
	 */
	public function redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		if (is_array($uri))
			$uri = call_user_func_array(array($this, 'url'), $uri);
		if (!preg_match('#^https?://#i', $uri)) {
			$uri = self::$baseUrl . $uri;
		}
		switch ($method)
		{
			case 'script':
				echo "<script>window.location.href='$uri';</script>";
				break;
			case 'refresh':
				header("Refresh:0;url=" . $uri);
				break;
			case 'location':
			default:
				header("Location: " . $uri, TRUE, $http_response_code);
				break;
		}
		exit;
	}
	
	/**
	 * Функция для возвращает код кириличной ссылки в Unicode
	 *
	 * @param string $utf_str кириличная ссылка на целевую страницу 
	 */
	// 
	function getUrlEncoded ($utf_str) {
		$str = strip_tags($utf_str);
		$str = stripslashes($str);
		$trans = array(
			'&\#\d+?;' => '',
			'&\S+?;' => '',
			'[\s_\,]+' => '-',
			'[^a-zA-Zа-яА-Яіїє0-9%:.\-\/]' => '',
			'-+' => '-',
			'-$' => '',
			'^-' => ''
		);
		foreach ($trans as $key => $val) {
			$str = preg_replace("#" . $key . "#usi", $val, $str);
		}
		$str = urlencode($str);
		$str = str_replace("%2F", "/", $str); 
		$str = str_replace("%3A", ":", $str); 
		
		return trim(stripslashes($str));
	}
	
		public function clearText($str){
	// Убираем тэги
		$str = strip_tags($str);
		
		//задаем масив правил для замены
		$trans = array(
			'[^a-zA-Zа-яА-Я0-9іїє;.`\-\'\s\!()\[\]]' => '' // Убрать все что осталось кроме букв, цифр дефиса, пробельных символов и точек
		);
		foreach ($trans as $key => $val) {
			$str = preg_replace("#" . $key . "#iu", $val, $str);
		}
		return trim($str);
	}
	
	public function is_future($date) {
		return (strtotime($date) > strtotime(date("Y-m-d H:i:s")))? true:false;
	}

	public function is_past($date) {
		return !($this->is_future($date));
	}
	

	public function e_DeleteEvent($row_id){
		$result = $this->Contenter->delete_row('bs_event', $row_id);
	}

	/**
	 * Замена ссылок в сгрнерированном коде страници
	 *
	 * Замена сылок: Добавление $baseUrl в начало href, src и action параметров
	 * ЛЮБЫХ html тегов, например a, link (href), img, script (src),
	 * form (action), только если ссылка не начинаеться с названия ЛЮБОЙ схемы
	 * протокола (внешняя ссылка), например http: https: ftp: mailto: javascript:,
	 *  c учетом whitesace`ов и невозможсти срабатываний вне парaметров тега
	 * (например переменная в скрипте)
	 * - возможно стоит перечислить все разрешенные протоколы, тогда вместо (?!(//|#)) будет (?!(https?|ftp|...)://)
	 * - возможно стоит перечислить все разрешенные теги, тогда вместо <[^>]+ будет <(a|img|...)[^>]+
	 *
	 * @param string $html Сгеренрированный код страници
	 * @param string $baseUrl Базовая ссылка
	 * @return string Код страници с преобразованными ссылками
	 */
	public function linkReplace($html, $baseUrl)
	{
		$html = preg_replace('#\s*<!--[^\[<>].*?(?<!!)-->#s', '', $html); // Удаление html коментариев из кода
		return preg_replace("@(<[^>]+(src|href|action)\s*=\s*['\"])(?!(//|#|(http|https|ftp)://))@i", '\\1' . $baseUrl, $html);
	}
	
	public function getUrlParts()
	{
		// Проводим разбор входной ссылки $_SERVER['SCRIPT_NAME'] а части $urlParts
		$baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$path_query = str_replace('//','/',$_SERVER['REQUEST_URI']);
		// Во входной строке не должно быть кавычек и скобок, точек с запятыми, очищаем их.
		$path_query = str_replace(array("'","(",")",";"),'',$path_query);
		$path_query = preg_replace('@' . $baseUrl . '(.*?)(\?.*)?$@i', '\\1', $path_query);
		$urlParts = explode('/', $path_query);
		// Фильстрация от пустых строк
		$urlParts = array_filter($urlParts, 'strlen');
		self::$baseUrl = $baseUrl;
		return $urlParts;
	}
}
