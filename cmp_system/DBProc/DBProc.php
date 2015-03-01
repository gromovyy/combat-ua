<?php
/**
 * Содержит класс для работы с процедурами БД
 * @package IC-CMS
 * @subpackage Core
 */

/**
 * Класс DBProc урезанная и обновленная версия класса {@link DB} с возможностью
 * работы только с процедурами
 *
 * @example docs/examples/DBProc_usage.php
 *
 * @requirement {@link Base}
 * @modified v1.0 от 10 января 2012 года
 * @version 1.0
 * @author Иван Найдёнов
 * @package IC-CMS
 * @subpackage Core
 */
class DBProc extends Base
{

	/**
	 * Объект драйвера BD mysqli
	 * @var mysqli
	 */
	private $mysqli;

	/**
	 * Необходимо ли весли лог обращений к БД
	 * @var boolean
	 */
	//protected $is_log = TRUE;

	/**
	 * Масив вызовов процедур для буферезированных (пакетных) запросов
	 * @var array
	 */
	protected $callBuffer = NULL;

	/**
	 * Сохраненные опции для применения опций на вызов или период
	 * @var array
	 */
	protected $savedOptions;

	/**
	 * Опция: раскрывать одноелементный масив в случае если sql вернул только 1 табличный результат
	 * @var boolean
	 */
	public $extractOneTable = true;

	/**
	 * Опция: раскрывать одноелементный масив в случае если в таблице только 1 сторка
	 * @var boolean
	 */
	public $extractOneRow = false;

	/**
	 * Опция: раскрывать одноелементный масив в случае если в строке только одно поле
	 * @var boolean
	 */
	public $extractOneColumn = false;

	/**
	 * Опция: экранировать параметры, передаваемые в процедуру
	 * @var boolean
	 */
	public $escapeParams = true;	
	
	/**
	 * Опция: Пытаться добавить стандартные столбики в таблици если их нет
	 * @var boolean
	 */
	private $tableFix = true;

	/**
	 * Конструктор класса.
	 *
	 * Подключение к базе данных с использованием глобальных переменных конфигурационного файла {@link config.php}
	 *
	 * Устанавливает по умолчанию кодировку UTF8
	 *
	 * @global string $GL_HOST Адрес сервера mysql из настроек. Используеться по умолчанию
	 * @global string $GL_DB_USER Имя пользователя mysql из настроек. Используеться по умолчанию
	 * @global string $GL_DB_PASSW Пароль пользователя mysql из настроек. Используеться по умолчанию
	 * @global string $GL_DB_NAME Название базы данных из настроек. Используеться по умолчанию
	 * @param string $db_host Адрес сервера mysql
	 * @param string $db_user Имя пользователя mysql
	 * @param string $db_passw Пароль пользователя mysql
	 * @param string $db_name Название базы данных
	 */
	public function __construct($db_host = NULL, $db_user = NULL, $db_passw = NULL, $db_name = NULL)
	{
		Base::__construct();
		$this->dbprefix = 'db';
		if (in_array('DB', get_declared_classes()) && $db_host == NULL) {
			// если уже был инициализирован класс DB и используются глобальные настройки,
			// взять объект mysqli из него
			$this->mysqli = $this->DB->mysqli;
		}
		else { // иначе создать используя голобальные или задданные настройки
			global $GL_HOST;
			global $GL_DB_USER;
			global $GL_DB_PASSW;
			global $GL_DB_NAME;

			if ($db_host == NULL) {
				$db_host = $GL_HOST;
				$db_user = $GL_DB_USER;
				$db_passw = $GL_DB_PASSW;
				$db_name = $GL_DB_NAME;
			}


			$this->mysqli = new mysqli($db_host, $db_user, $db_passw, $db_name);
			if ($this->mysqli->connect_error) {
				die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
			}
			// Устанавливаем кодировку
			if($this->mysqli->character_set_name()!='utf8'){
				$this->query("SET NAMES utf8");
			};
		}
	}

	/**
	 * Функция query запускает на выполнение в БД код sql и получает множественные ответы в виде масива.
	 *
	 * Так же может заменять масивы с одним елементом на просто еденичный елемент, в случе необходимости
	 *
	 * @param string $sql код sql
	 * @return array|boolean
	 */
	protected function query($sql)
	{
		if (isset($_GET['debug']))  {
			self::$sql_log_buffer .= microtime().":".$sql."\r";
		}
			//$this->log_buffer .= $sql;

		if ($this->mysqli->multi_query($sql)) { // Выполняем запрос
			if ($this->mysqli->more_results()) { // Проверяем есть ли табличный ответ
				$result = array();
				do {
					if ($q = $this->mysqli->store_result()) { // Получаем одну таблицу (результат одного Seleсt`a)
						$tempres = array();
						if ($this->extractOneColumn && $q->field_count == 1) {
							// Извлекаем еденичные колонки если задданно
							while ($row = $q->fetch_assoc())
								$tempres[] = array_shift($row);
						}
						else {
							
							while ($row = $q->fetch_assoc()) {
								$tempres[] = $row;
								//print_r($row);
							}
						}
						// Извлекаем еденичные строки если задданно
						if ($this->extractOneRow && $q->num_rows == 1)
							$tempres = $tempres[0];
						$result[] = $tempres;
						$q->free();
					}
				} while ($this->mysqli->next_result());
				// Извлекаем еденичные таблицы если задано
				if ($this->extractOneTable && count($result) == 1)
					$result = $result[0];
				// проверяем не было из ошибки из за  отсутствующего столбика
				if ($this->mysqli->errno)
					$this->addEvent(DEBUG, $sql, $this->mysqli->error,$this->mysqli->error);
				// проверяем небыло ли ошибки после первого результата
				if ($this->mysqli->error)
					$this->addEvent(ERROR, $sql, $this->mysqli->error,$this->mysqli->error);
			}
			else // запрос не выводит не одного табличного результата
				$result = true;
		}
		else { 
			// возникла ошибка до вывода первого результата
			$result = $this->addEvent(ERROR, $sql, $this->mysqli->error,$this->mysqli->errno);
		}

		return $result;
	}

	/**
	 * Запускает режим буферизации вызовов процедур.
	 *
	 * В режиме буферизации каждая процедура не выполняеться в момент вызова,
	 * а записываеться в буфер {@link $callBuffer}. Выполнение же процедур
	 * проходит пакетно в момент вызова метода Call();
	 *
	 * Опционально может установить параметры на период буферизации
	 *
	 * @param type $options
	 */
	public function StartBuffer($options=null)
	{
		if ($options !== null) {
			$this->SaveOptions(); // сохраняем предидущие опции
			$this->SetOptions($options); // устанавливаем новые опции
		}
		$this->callBuffer = array(); // инициализируем буффер
	}

	/**
	 * Инициирует пакетный вызов процедур и завершает режим буферизации вызовов
	 *
	 * Опционально может установить параметры на момент вызова
	 *
	 * @return array|boolean
	 */
	public function CallBuffer($options=null)
	{
		if ($this->callBuffer === null) // если буфер не проинициализирован - выходим
			return false;
		if (empty($this->callBuffer)) // если нечего выполнять - выходим
			return true;
		$this->SetOptions($options); // устанавливаем новые опции если они есть
		$sql = implode("\n", $this->callBuffer);
		$this->callBuffer = null; // завершаем режим буферризации
		$return = $this->query($sql); // выполняем запрос
		$this->RestoreOptions(); // Вспоменаем настройки если они были запомнены
		return $return;
	}

	/**
	 * Выполнение процедуры MySQL
	 *
	 * @param string $procedure имя процедуры
	 * @param string $param_string список входных/выходных параметров
	 * @return array|boolean Результат роботы процедуры
	 */
	protected function Call($procedure, $param_string = "")
	{
		$sql = "CALL $procedure ($param_string);"; // генерируем запрос
//echo $sql;
		if ($this->callBuffer === null) { // если буфер не проинициализирован
			$return = $this->query($sql); // выполняем процедуру
			$this->RestoreOptions();
			return $return;
		}
		else { // режим буферизации
			array_push($this->callBuffer, $sql); // добавляем в буффер
			return true;
		}
	}

	/**
	 * Магический метод для вызова процедуры по ее имени в стиле php
	 *
	 * @param string $name Имя вызываемой процедуры
	 * @param array $arguments Масив аргументов передаваемых в процедуру
	 * @return array|boolean Результат роботы процедуры
	 */
	public function __call($name, $arguments)
	{
		$trace = debug_backtrace(false); // получаем стек вызовов
		$dbprefix = $this->{$trace[2]['class']}->dbprefix; // получаем префикс вызвавшего класса
		//echo $dbprefix;
		// вормируем строку параметров
		$param_string = '';
		foreach ($arguments as $i => $arg) {
			$arg = str_replace("'","`",$arg);
			//$arg = str_replace('"',"&quot;",$arg);

			if ($i != 0) // если не первый елемент - ставим запятую
				$param_string .= ',';
			if ($arg === null || $arg === 'NULL') // если параметр null
				$param_string .= "NULL"; // пишем NULL без кавычек
//else if(substr_compare($arg, '@', 0, 1)===0)
			else if (isset($arg[0]) && $arg[0] == '@') { // если параметр - переменная sql
				$arg = substr($arg, 1); // обрезаем собаку
				if ($this->escapeParams) // при необходимости экранируем
					$arg = $this->mysqli->real_escape_string($arg);
				$param_string .= "@'$arg'"; // возвращаем собаку обрамив кавычками
			}
			else { // иначе - обычный входной аргумент
				if ($this->escapeParams) // если зданно - экранируем
					$arg = $this->mysqli->real_escape_string($arg);
				$param_string .= "'$arg'";
			}
		}
		// передаем сформированное имя процедуры и строку аргументов на выполнение
		return $this->Call($dbprefix . '_proc_' . $name, $param_string);
	}

	/**
	 * Получает sql переменную как табличный результат
	 *
	 * При небуферизированном выводе применяет {@link extractOneRow} и {@link extractOneColumn}
	 *
	 * @param string $name Имя sql переменной
	 */
	public function Get($name, $options=array())
	{
		if ($name[0] == '@') // обрезаем @
			$name = substr($name, 1);
		return $this->show_var($name); // вызываем процедуру для отображения значения переменной
	}

	/**
	 * Устанавливает новые опции
	 * @param array $options Oпции
	 */
	function SetOptions($options)
	{
		if (isset($options['extT']))
			$this->extractOneTable = $options['extT'];
		if (isset($options['extR']))
			$this->extractOneRow = $options['extR'];
		if (isset($options['extC']))
			$this->extractOneColumn = $options['extC'];
		if (isset($options['escape']))
			$this->escapeParams = $options['escape'];
	}

	/**
	 * Сохраняет опции перед пременением временных
	 */
	private function SaveOptions()
	{
		$this->savedOptions['extractOneTable'] = $this->extractOneTable;
		$this->savedOptions['extractOneRow'] = $this->extractOneRow;
		$this->savedOptions['extractOneColumn'] = $this->extractOneColumn;
		$this->savedOptions['escapeParams'] = $this->escapeParams;
	}

	/**
	 * Востанавливает сохраненные опции
	 */
	private function RestoreOptions()
	{
		if ($this->savedOptions === null)
			return;
		$this->extractOneTable = $this->savedOptions['extractOneTable'];
		$this->extractOneRow = $this->savedOptions['extractOneRow'];
		$this->extractOneColumn = $this->savedOptions['extractOneColumn'];
		$this->escapeParams = $this->savedOptions['escapeParams'];
		$this->savedOptions = null;
	}

	/**
	 * Применяет опции для одного небуферезированного вызова Call или Get и
	 * возвращает обект {@link DBProc} для вызова Call
	 *
	 * В случае буферизации вызовов не имеет эфекта
	 *
	 * @param type $options
	 * @return DBProc
	 */
	public function O($options)
	{
		if ($this->callBuffer === null) { // если не режим буфферизации
			$this->SaveOptions();
			$this->SetOptions($options);
		}
		return $this;
	}

	/**
	 * Функция StartLog возвращает запускает режим логирования sql-запросов
	 */
	public function StartLog()
	{
		$this->is_log = TRUE;
	}

	/**
	 * Функция GetAndCleanDBLog возвращает историю запросов в текущем объекте БД, очищает её и останавливает режим логирования.
	 * @return string
	 */
	public function GetAndCleanDBLog()
	{
		//$result = $this->log_buffer;
		//$this->log_buffer = "";
		//$this->is_log = FALSE;
		return $result;
	}
	
	/**
	 * Функция queryForUpdater Выполняет прямой запрос к бд доступна только класу Updater.
	 * @return string
	 */
		public function queryForUpdater ($sql) {
       $trace = debug_backtrace(false);
       if ($trace[1]["class"] == "Updater") {
           $result = $this->query($sql);
           return $result;
       }
   }
	 // Перегрузка родительского метода
	 public function addEvent($type,$sql,$notes,$error){
		if($this->tableFix and ($error == 1054)){
			$this->tableFix = false;
			// Если процедура из Contenter
			if( strpos($sql, 'cntntr_proc')){
			
				$querryParts = explode('\'',$sql);
				$table = $querryParts[1];
				$this->table_fix($table);
				return $this->query($sql);
			}
		} else{
			parent::addEvent($type,"ERROR_SQL: Запрос <code>$sql</code> не был выполнен",$notes);
			return false;
		}
		
		
	 }
	
	/******* Example of array ******************
	$params = array (    'tables' => array('a' => 'artcl_article',
											array('b' => 'btl_beticle',
												  'join' => 'inner',
												  'on_left' => 'a.id_article',
												  'on_right' => 'b.id_article'
												 ),
											array ('g' => 'gtcl_geticle',
												   'join' => 'left',
												   'on_left' => 'btl_beticle.id_beticle',
												   'on_right' => 'gtcl_geticle.id_beticle'
												)
											),
						  'distinct',
						  'fields' => array('id_article', 'name', 'COUNT(*) as `count`'),
						  'where' => array( 
											array(  'field'=>'name',
													'operator' => '>',
													'value' => 'Ваня'),
											'operator' => 'AND',
											array (
												array(	'field'=>'id_article',
														'operator' => '>',
														'value' => 'Ваня'),
												'operator' => 'OR',
												array(	'field'=>'count',
														'operator' => '=',
														'value' => '10')
												)
											),
						  'order' => array( 'direction'=> 'ASC',
										    'fields' => 'name'),
						  'group' =>array('id_article'),
						  'having' => array( 
											array(  'field'=>'name',
													'operator' => '>',
													'value' => 'Ваня'),
											'operator' => 'AND',
											array (
												array(	'field'=>'id_article',
														'operator' => '>',
														'value' => 'Ваня'),
												'operator' => 'OR',
												array(	'field'=>'count',
														'operator' => '=',
														'value' => '10')
												)
											),
						  'limit_start' => 0,
						  'limit_count' => 100,
						  'index_field' => 'id_article',
						  'index_unique',
						  'one_row',
						  'one_cell'
						);
	/***************** Example of JSON
	
	*************************/
	public function Select( $params )
	{
	
		if (empty($params)) {
			echo "<font color='red'>В запросе Select получены пустые параметры</font>";
			return false;
		}
		
		if (!is_array($params))
			$params = json_decode($params, true);
		
		// TODO
		// Здесь нужно выполнить проверку на кеширование и если запрос повторяется - выдавать кеш
		// TODO
		
		// Формируем целевой запрос
		
		// Формируем список столбцов, по которым делается выборка
		if ( empty($params['fields']) ) 
			$query_cols = "*";
		else if (is_array($params['fields']))
			$query_cols = implode(',', $params['fields']);
		else 
			$query_cols = $params['fields'];
			
		// Формируем список таблиц для выборки
		if ( empty($params['tables'])) {
			echo "<font color='red'>В запросе Select не заданы таблицы</font>";
			return false;
		}
		// Если указана только одна таблица без псевдонима
		else if (!is_array($params['tables'])) {
			$tables = $params['tables'];
		} 
		// Если в запросе больше чем одна таблица или одна таблица с псевдонимом
		else {
			$table_name = reset($params['tables']); 
			$table_alias = key($params['tables']); 
			$tables = "`$table_name`";
			if	(!is_numeric($table_alias))	
				$tables .= " as $table_alias";
			
			// Получение присоединяемых таблиц
			$join_tables = $params['tables'];
			// Убираем из массива первую таблицу, которую уже присоединили
			array_shift($join_tables);
		
			while (count($join_tables) > 0) {
				// получаем следующую таблицу
				$table = reset($join_tables);
				if (is_array($table)) {
					$table_name = reset($table); 
					$table_alias = key($table);
					if (!empty($table_alias) and !is_numeric($table_alias))
						$table_alias = " as $table_alias";
					else
						$table_alias = "";
					$join = strtoupper( (empty($table['join']))? 'INNNER': $table['join']);
					$on_left = $table['on_left'];
					$on_right = $table['on_right'];
					
					if (!empty($table_name) and !empty($on_left) and !empty($on_right))
						$tables .= " $join JOIN $table_name $table_alias ON $on_left = $on_right ";
				}
				array_shift($join_tables);
			}
		}
		// Если есть значение DISTINCT
		if (in_array("distinct", $params, true)) { 
			$distinct = "DISTINCT";
		}
			
		$sql = "SELECT $distinct SQL_CALC_FOUND_ROWS $query_cols FROM $tables";
		
		// Если указаны фильтры
		if (!empty($params['where']) and is_array($params['where'])) {
			$filters = $this->get_filters( $params['where']);
			if (!empty($filters))
			$sql .= " WHERE $filters";
		}
		
		// Если указана групировка
		if (!empty($params['group'])) {
			if  (is_array($params['group'])) 
				$group_fields = implode (',', $params['group']);
			else 
				$group_fields = $params['group'];

			$sql .= " GROUP BY $group_fields";
		}
		
		// Если указаны фильтры после групировки
		if (!empty($params['having']) and is_array($params['having'])) {
			$filters = $this->get_filters( $params['having']);
			if (!empty($filters))
			$sql .= " HAVING $filters";
		}
		
		// Если указана сортировка
		if (!empty($params['order']) and is_array($params['order'])) {
			foreach ($params['order'] as $field => $direction) {
				$order .= '`'.$field.'` '.$direction.',';
			}
			$order = rtrim($order, ",");
			
			// if ( !empty($params['order']['field'])) {
			// $order_direction = (empty($params['order']['direction']))? 'ASC' : $params['order']['direction'];
			// $order_field = $params['order']['field'];
			// $order = $order_field." ".$order_direction;
			// } else { 
				// if (is_array($params['order'])) 
					// foreach ($params['order'] as $order) {
						// $order_direction = (empty($order['direction']))? 'ASC' : $order['direction'];
						// $order_field = $order['field'];
						// $order .= $order_field." ".$order_direction",";
					// }
					
			// }
			if (!empty($order)) 
				$sql .= " ORDER BY $order";
		}
		
		// Если указаны лимиты
		if (isset($params['limit_start']) and !empty($params['limit_count'])) {
			$sql .= " LIMIT {$params['limit_start']}, {$params['limit_count']}";
		}
		
		if (isset($_GET['debug']))  {	
			self::$sql_log_buffer .= microtime().":".$sql."\r";
			//echo '<pre>'.microtime().":".$sql.'</pre>';
		}
		// Выполняем запрос в БД.
		$resource = $this->mysqli->query($sql);
		$result['num_rows'] = implode(mysqli_fetch_assoc($this->mysqli->query("SELECT FOUND_ROWS()")));
		//$result['num_rows'] = $this->mysqli->affected_rows;//$resource->num_rows;
		if (empty($resource)){ 
			// возникла ошибка до вывода первого результата
			$result = $this->addEvent(ERROR, $sql, $this->mysqli->error,$this->mysqli->errno);
			return $result;
		}
		
		//Собираем массив данных запроса на возврат, удаляя значения запрещенных столбцов
		$index_field = $params['index_field'];
		if (!empty($index_field) and !is_array($index_field)) $index_field = array($index_field);
		if ($resource) {
			while ($row = $resource->fetch_assoc()) {
				if (!empty($index_field)) { 
					if (!in_array('index_unique', $params, true))  {
						// Если не установлен параметр index_unique - индексированный массив массива рядов
						switch (count($index_field)) {
							case 1: $result['rows'][$row[$index_field[0]]][] = $row; break;
							case 2: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][] = $row; break;
							case 3: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]][] = $row; break;
							case 4: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]][$row[$index_field[3]]][] = $row; break;
							case 5: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]][$row[$index_field[3]]][$row[$index_field[4]]][] = $row; break;
							default: $result['rows'][] = $row; break;
						}
					} else {
						// Если установлен параметр index_unique - индексированный массив рядов
						switch (count($index_field)) {
							case 1: $result['rows'][$row[$index_field[0]]] = $row; break;
							case 2: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]] = $row; break;
							case 3: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]] = $row; break;
							case 4: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]][$row[$index_field[3]]] = $row; break;
							case 5: $result['rows'][$row[$index_field[0]]][$row[$index_field[1]]][$row[$index_field[2]]][$row[$index_field[3]]][$row[$index_field[4]]] = $row; break;
							default: $result['rows'][] = $row; break;
						}
					}
				}
				// Если поле для индексирования не указано - просто формируем массив.
				else
					$result['rows'][] = $row;
			}
		}
		
		if (empty($result['rows'])) $result['rows'] = array();
		
		
		
		// Если установлен параметр one_row - возвращаем первый ряд
		if (in_array('one_row', $params, true)) 
			$result = reset($result['rows']);
			
		// Если установлен параметр one_cell - возвращаем первую ячейку
		else if (in_array('one_cell', $params , true)) {
			$result = reset($result['rows']);
			$result = reset($result);
		}

		//print_r($result);
		return $result;
	}
	/* Example of $filters array
	 array( 
											array(  'field'=>'name',
													'operator' => '>',
													'value' => 'Ваня'),
											operator => 'AND',
											array (
												array(	'field'=>'id_article',
														'operator' => '>',
														'value' => 'Ваня'),
												operator => 'OR',
												array(	'field'=>'count',
														'operator' => '=',
														'value' => '10')
												)
											)
	
	*/
	protected function get_filters($filters) {
		if (!empty($filters['field']) and !empty($filters['operator']) and ($filters['value']!='')) {
			switch ( $filters['operator']) {
				case 'in':
					if (is_array($filters['value']))
						return "( {$filters['field']}  {$filters['operator']} ('".implode("','", $filters['value'])."'))";
					break;
				default :
					return "( {$filters['field']}  {$filters['operator']} '{$filters['value']}' )";
				
			}
		}
		
		if (!empty($filters['operator']))  
			$operator = $filters['operator'];

		foreach($filters as $filter) {
			if (!is_array($filter)) continue;
			$filter_array[] = $this->get_filters($filter);
		}
		if (count($filter_array) == 1) return "( ". end($filter_array) . " )";
		return "( ".implode (" ".$operator." ", $filter_array)." )";
	}
}
