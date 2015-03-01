<?php
/**
 * Содержит классы для работы с БД
 * @package IC-CMS
 * @subpackage Core
 */

/**
 * Класс DB обеспеспечивает интерфейс обращения к БД
 *
 * Реализует основные операции работы с БД –
 * - подключение - {@link __construct}
 * - выборка - {@link Select}
 * - вставка - {@link Insert}
 * - обновление - {@link Update}
 * - удаление - {@link Delete}
 * - вызовы процедур mysql - {@link Call}
 * Обеспечивает ограничение прав доступа к таблицам, столбцам и строкам.
 *
 * @deprecated рекомендуеться использовать {@link DBProc}
 * @author Бобырь Катерина
 * @author Александр Громовой
 * @author Иван Найдёнов
 * @version 1.2
 * @modified v1.1 от 02 августа 2011 года
 * @modified v1.2 от 06 января 2012 года
 * @package IC-CMS
 * @subpackage Core
 */
class DB extends Base
{

	/**
	 * Переменная, хранящая роль пользователя
	 * @deprecated Отказ от ролей
	 * @var int
	 */
	var $role_id = "";

	/**
	 * Объект драйвера бд mysqli
	 * @var mysqli
	 */
	var $mysqli;

	/**
	 * @var boolean
	 */
	var $is_log = FALSE;

	/**
	 * Буфер лога
	 * @var string
	 */
	var $log_buffer = "";

	/**
	 * Конструктор класса.
	 *
	 * Подключение к базе данных с использованием глобальных переменных конфигурационного файла {@link config.php}
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

//		echo $db_host . "DB_NAME" . $db_name;
//		echo "HOST = $GL_HOST, USER = $GL_DB_USER, Password = $GL_DB_PASSW, DB_name = $GL_DB_NAME";

		Base::__construct();
		$this->version = "1.2";

		// Если существует компонент User - получаем роль текущего пользователя.  Иначе - устанавливаем пустое значение.
//		if (isset($this->User)) $this->role_id = $this->User->getRole();
//		else $this->role_id = "";



		$this->mysqli = new mysqli($db_host, $db_user, $db_passw, $db_name);
		if ($this->mysqli->connect_error) {
			die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
		}

		$this->query("SET NAMES utf8");
	}

	/**
	 * Проверка есть ли <i>запрет</i> на доступ к таблице
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param int $role_id роль пользователя
	 * @param string $operation операция - возможные значения - 'select', 'insert',  'update', 'delete'
	 * @return boolean Возвращает ненулевой результат, если доступ к данной таблице запрещен. Если разрешен - возвращает 0;
	 */
	public function isForbiddenTableAccess($table, $role_id, $operation)
	{
		// Проверка ограничений входных параметров
		// Если проверка не пройдена - наложение запрета на доступ к таблице.
		if (!$this->checkParams(func_get_args(), "NOT EMPTY, INT OR EMPTY,(select:update:insert:delete)", __FILE__, __LINE__, __FUNCTION__))
				return TRUE;

		$sql = "SELECT table_name from `db_table_access`
					where table_name = '$table' and role_id = '$role_id' and access_$operation = 1";
		$res = $this->query($sql);


		// Если запреты есть - генерируем событие безопасности и возвращаем TRUE
		if ($res->num_rows != 0) {
			$this->addEvent(SEQURITY, "Запрос на операцию $operation к запрещенной таблицы $table . Роль $role_id");
			return TRUE;
		}
		// Если запретов нет - возвращаем FALSE
		return FALSE;
	}

	/**
	 * Возвращает строку с фильтрами для данной роли пользователя.  Если фильтров нет - возвращает пустую строку.
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param int $role_id роль пользователя
	 * @param string $operation операция - возможные значения - 'select', 'update', 'delete'
	 * @return string
	 */
	public function getRowCondition($table, $role_id, $operation)
	{

		// Проверка ограничений входных параметров
		// Если проверка не пройдена - возврат пустых фильтров.
		if (!$this->checkParams(func_get_args(), "NOT EMPTY, INT OR EMPTY,(select:update:delete)", __FILE__, __LINE__, __FUNCTION__))
				return "";

		$sql = "SELECT * from `db_row_access`
					where table_name = '$table' and role_id = '$role_id' and filter_$operation = 1";
		$res = $this->query($sql);

		//Если фильтры отсутствуют, возвращаем пустую строку.
		if ($res->num_rows == 0) return "";

		//Если фильтры есть - формируем строку с фильтрами
		$cond = " AND (";
		while ($row = $res->fetch_assoc()) {
			$cond .= " " . $row['filter_col_name'] . " " . $row['filter_operation'] . " " . $row['filter_value'] . " ";
		}
		$cond .= ") ";
		return $cond;
	}

	/**
	 * Возвращает массив запрещенных столбцов.
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param int $role_id роль пользователя
	 * @param string $operation операция - возможные значения - 'select',  'update'
	 * @return array
	 */
	public function getForbiddenColumn($table, $role_id, $operation)
	{
		// Проверка ограничений входных параметров
		// Если проверка не пройдена - возврат пустого фильтра.
		if (!$this->checkParams(func_get_args(), "NOT EMPTY, INT OR EMPTY,(select:update)", __FILE__, __LINE__, __FUNCTION__))
				return "";

		$sql = "SELECT * from `db_column_access`
					where table_name = '$table' and role_id = '$role_id' and access_$operation = 1";
		$res = $this->query($sql);

		// Если фильтры отсутствуют, возвращаем пустую строку.
		if ($res->num_rows == 0) return "";

		// Если есть запрещенные столбцы - возвращаем массив с именами запрещенных столбцов
		while ($row = $res->fetch_assoc()) {
			$result[] = $row["column_name"];
		}
		return $result;
	}

	/**
	 * Делает выборку из базы данных с применением фильтра по таблицах, столбцах и строках
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param array $cols массив столбцов. Например array("col1", "col2", "col3")
	 * @param string $condition условия по выборке.  Например,  $condition = "col1 = NULL and col2 > '20'"
	 * @param string $colsOrder столбец(столбцы) для сортировки вывода. Например $colsOrder = "col3"
	 * @param string $colsGroup столбец(столбцы)  по которым проводится групировка.  Например, $colsGroup = "col1, col2"
	 * @param string $limit ограничение количества выборанных записей.  Например $limit = "0, 30"
	 * @param array $joinType массив типов соединений. Например, array("INNER JOIN", "LEFT JOIN")
	 * @param array $joinTable массив таблиц для подсоединений. Размер должен быть такой же, как в joinType. Например, array("table2", "table3")
	 * @param array $joinCond имя столбцов, по которым делается соединение. Например  array("table1.id = table2.id", "table1.id = table3.id");
	 * @return array
	 */
	public function Select(
	$table, $cols, $condition = "NULL", $colsOrder = "NULL", $colsGroup = "NULL", $limit = "NULL", $joinType = "()", $joinTable = "()", $joinCond = "()")
	{

		// Проверка ограничений входных параметров
		// Если проверка не пройдена - возврат пустых значений.
		if (!$this->checkParams(func_get_args(), "NOT EMPTY, ARRAY,,NOT EMPTY, NOT EMPTY, NOT EMPTY", __FILE__, __LINE__, __FUNCTION__))
				return "";

		// Проверка ограничения по таблицам для данной роли пользователя. Если есть запрет - возврат пустого результата
		if ($this->isForbiddenTableAccess($table, $this->role_id, 'select'))
				return "";

		// Загрузка ограничений по строкам для данной роли пользователя
		$row_condition = $this->getRowCondition($table, $this->role_id, 'select');
		if ($row_condition != "")
				if ($condition == "NULL") $condition = '1 = 1' . $row_condition;
			else $condition .= $row_condition;

		// Формируем целевой запрос
		// Формируем список столбцов, по которым делается выборка
		$query_cols = "{$cols[0]}";
		for ($i = 1; $i < count($cols); $i++)
			$query_cols = $query_cols . ",{$cols[$i]}";

		//Формируем сам запрос
		$sql = "SELECT $query_cols FROM $table";
		//добавляем условие, если оно есть
		if (($joinType != "()") and ($joinCond != "()") and ($joinTable != "()"))
				for ($i = 0; $i < count($joinType); $i++)
				if (isset($joinType[$i]) and isset($joinTable[$i]) and isset($joinCond[$i]))
						$sql = $sql . " " . $joinType[$i] . " JOIN " . $joinTable[$i] . " ON " . $joinCond[$i];
		if ($condition != "NULL" and $condition != "")
				$sql = $sql . " WHERE " . $condition;
		if ($colsGroup != "NULL") $sql = $sql . " GROUP BY " . $colsGroup;
		if ($colsOrder != "NULL") $sql = $sql . " ORDER BY " . $colsOrder;
		if ($limit != "NULL") $sql = $sql . " LIMIT " . $limit;

		// Выполняем запрос в БД.
		$q = $this->query($sql);

		// Загрузка ограничений по столбцам для данной роли пользователя
		$forbidden_columns = $this->getForbiddenColumn($table, $this->role_id, 'select');

		//Собираем массив данных запроса на возврат, удаляя значения запрещенных столбцов
		if ($q) {
			while ($row = $q->fetch_assoc()) {
				if (is_array($forbidden_columns)) {
					foreach ($forbidden_columns as $value) {
						unset($row[$value]);
					}
					$result[] = $row;
				}
				else $result[] = $row;
			}
		}
		else $result = NULL;

		// Если установлен параметр  $limit = 1 (одна запись),  возвращаем не двухмерный, а одномерный массив.
		if ($result && ($limit == 1)) {
			$result = $result[0];
		}
		return $result;
	}

	/*	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * 	Вставка записи в базу данных с применением фильтра по таблицах
	 * 	Параметры:
	 * 	$table - имя таблицы.
	 * 	$cols - массив столбцов. Например array("col1", "col2", "col3")
	 * 	$values - массив значений. Например array("val1", "val2", "val3")
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	/**
	 * Вставка записи в базу данных с применением фильтра по таблицах
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param array $cols массив столбцов. Например array("col1", "col2", "col3")
	 * @param array $values массив значений. Например array("val1", "val2", "val3")
	 * @return boolean
	 */
	public function Insert($table, $cols="()", $values="()")
	{

		// Проверка ограничения по таблицам для данной роли пользователя. Если есть запрет - возврат пустого результата
//		if ($this->isForbiddenTableAccess($table, $this->role_id, 'insert')) return "";
		//формируем из массива столбцов строку типа (name, link, date)
		if ($cols != "()") {
			$query_cols = "({$cols[0]}";

			for ($i = 1; $i < count($cols); $i++)
				$query_cols = $query_cols . ", {$cols[$i]}";

			$query_cols = $query_cols . ")";
		}
		else $query_cols = "()";

		// Формируется массив значений для вставки
		if ($values != "()") {
			$query_values = "('{$values[0]}'";
			for ($i = 1; $i < count($values); $i++)
				$query_values = $query_values . ",'{$values[$i]}'";
			$query_values = $query_values . ")";
		}
		else $query_values = "()";

		//формируем и выполняем сам запрос
		$sql = "INSERT INTO $table $query_cols VALUES $query_values";
		echo $sql;
		$result = $this->query($sql);

		return $result;
	}

	/**
	 * Обновление записи  с применением фильтров доступа по таблицах, столбцах и записях
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param array $cols массив столбцов. Например array("col1", "col2", "col3")
	 * @param array $values массив значений. Например array("val1", "val2", "val3")
	 * @param string $condition условия по выборке.  Например,  $condition = "col1 = NULL and col2 > '20'"
	 * @return boolean
	 */
	public function Update($table, $cols, $values, $condition="NULL")
	{

		// Проверка ограничения по таблицам для данной роли пользователя. Если есть запрет - возврат пустого результата
		if ($this->isForbiddenTableAccess($table, $this->role_id, 'update'))
				return "";

		// Загрузка ограничений по строкам для данной роли пользователя
		$row_condition = $this->getRowCondition($table, $this->role_id, 'update');
		if ($row_condition != "")
				if ($condition == "NULL") $condition = '1 = 1' . $row_condition;
			else $condition .= $row_condition;

		// Загрузка ограничений по столбцам для данной роли пользователя
		$forbidden_columns = $this->getForbiddenColumn($table, $this->role_id, 'update');

		// Формируем список присваиваний вида имя столбца = 'значение'
		$assigment = "";

		// Из списка  убираем все запрещенные столбцы из списка для обновления
		foreach ($cols as $key => $column_name) {
			if (!in_array($column_name, $forbidden_columns)) {
				if ($assigment == "") $assigment = "$column_name = '{$values[$key]}'";
				else $assigment .= ", $column_name = '{$values[$key]}'";
			}
			else
					$this->addEvent(SEQURITY, "Запрос на запрещеный update колонки $column_name в таблицу $table", "Роль " . $this->role_id);
		}

		//формируем сам запрос
		$sql = "UPDATE $table SET $assigment";

		//добавляем условие, если оно есть
		if ($condition != "NULL") $sql = $sql . " WHERE " . $condition;

		// Выполняем запрос
		$result = $this->query($sql);

		return $result;
	}

	/**
	 * Удаление записи  с применением фильтров доступа по таблицах и записях
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @param string $table имя таблицы
	 * @param string $condition условия по выборке.  Например,  $condition = "col1 = NULL and col2 > '20'"
	 * @return boolean
	 */
	public function Delete($table, $condition)
	{

		// Проверка ограничения по таблицам для данной роли пользователя. Если есть запрет - возврат пустого результата
		if ($this->isForbiddenTableAccess($table, $this->role_id, 'delete'))
				return "";

		// Загрузка ограничений по строкам для данной роли пользователя
		$row_condition = $this->getRowCondition($table, $this->role_id, 'delete');
		if ($row_condition != "")
				if ($condition == "NULL") $condition = '1 = 1' . $row_condition;
			else $condition .= $row_condition;

		//Формируем запрос на удаление
		$sql = "DELETE FROM `$table` WHERE $condition";

		$result = $this->query($sql);
		return $result;
	}

	/**
	 * Проверяет соединение с БД
	 *
	 * Если соединение с базой данных есть - возвращает ТRUE, иначе - FALSE
	 *
	 * @param string $host имя хоста
	 * @param string $db имя базы данных
	 * @param string $user имя пользователя
	 * @param string $password пароль
	 * @return boolean
	 */
	static function checkDbConnection($host, $db, $user, $password)
	{

		//Проверка соединения с хостом

		$db = new mysqli($host, $user, $password, $db);
		if ($db->connect_error) return FALSE;

		//Если все соединения прошли успешно - возврат TRUE
		return TRUE;
	}

	/**
	 * Функция ShowTables возвращает массив со списком всех таблиц  текущей БД
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @global string $GL_DB_NAME Имя тебущей базы данных
	 * @return array
	 */
	public function ShowTables()
	{
		global $GL_DB_NAME;
		// Формируем запрос на выполнение
		$sql = "SHOW FULL TABLES IN `$GL_DB_NAME` WHERE TABLE_TYPE LIKE 'BASE TABLE'";

		// Выполняем запрос
		$q = $this->query($sql);

		while ($row = $q->fetch_assoc()) {
			$result[$row["Tables_in_$GL_DB_NAME"]] = "";
		}
		return $result;
	}

	/**
	 * Функция ShowViews возвращает массив со списком всех таблиц и представлений текущей БД
	 *
	 * @deprecated Отказ от доступа к таблицам напрямую
	 * @global string $GL_DB_NAME Имя тебущей базы данных
	 * @return array
	 */
	public function ShowViews()
	{
		global $GL_DB_NAME;
		// Формируем запрос на выполнение
		$sql = "SHOW FULL TABLES IN `$GL_DB_NAME` WHERE TABLE_TYPE LIKE 'VIEW'";

		// Выполняем запрос
		$q = $this->query($sql);

		while ($row = $q->fetch_assoc()) {
			$result[$row["Tables_in_$GL_DB_NAME"]] = "";
		}
		return $result;
	}

	/**
	 * Функция ShowProcedures возвращает список процедур для текущей БД
	 * @global string $GL_DB_NAME Имя тебущей базы данных
	 * @return array
	 */
	public function ShowProcedures()
	{
		global $GL_DB_NAME;
		// Формируем запрос на выполнение
		$sql = 'SHOW PROCEDURE STATUS';

		// Выполняем запрос
		$q = $this->query($sql);

		while ($row = $q->fetch_assoc()) {
			if ($row["Db"] == $GL_DB_NAME) $result[$row["Name"]] = "";
		}
		return $result;
	}

	/**
	 * Функция запускает на выполнение в БД код sql
	 *
	 * @deprecated Рекомендуется использовать {@link multi_query}
	 * @param string $sql код sql
	 * @return MySQLi_Result|boolean
	 */
	public function query($sql)
	{

		if ($this->is_log) $this->log_buffer .= $sql;

		// Выполняем запрос
		$q = $this->mysqli->query($sql) or
				$this->addEvent(ERROR, "ERROR_SQL: Запрос $sql не был выполнен", $this->mysqli->error);
		return $q;
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
		$result = $this->log_buffer;
		$this->log_buffer = "";
		$this->is_log = FALSE;
		return $result;
	}

	/**
	 * Выполнение процедуры MySQL
	 *
	 * @param string $procedure имя процедуры
	 * @param string $param_string список входных/выходных параметров
	 * @param boolean $transformT Извлекать единичные таблицы
	 * @param boolean $transformR Извлекать единичные строки
	 * @param boolean $transformC Извлекать единичные поля
	 * @return array|boolean
	 */
	public function Call($procedure, $param_string = "", $transformT = true, $transformR = false, $transformC = false)
	{
		// Формируем запрос на выполнение
		$sql = "CALL $procedure ($param_string);";

//		echo $sql;
		// Выполняем запрос
		return $this->multi_query($sql, $transformT, $transformR, $transformC);
	}

	/**
	 * Функция multi_query запускает на выполнение в БД код sql и получает множественные ответы в виде масива.
	 *
	 * Так же может заменять масивы с одним елементом на просто еденичный елемент, в случе необходимости
	 *
	 * @param string $sql код sql
	 * @param boolean $transformT убирать одноелементный масив в случае если sql вернул только 1 табличный результат
	 * @param boolean $transformR убирать одноелементный масив в случае если в таблице только 1 сторка
	 * @param boolean $transformC убирать одноелементный масив в случае если в строке только одно поле
	 * @return array|boolean
	 */
	public function multi_query($sql, $transformT = true, $transformR = false, $transformC = false)
	{

		if ($this->is_log) $this->log_buffer .= $sql;

		if ($this->mysqli->multi_query($sql)) { // Выполняем запрос
			if ($this->mysqli->more_results()) { // Проверяем есть ли табличный ответ
				$result = array();
				do {
					if ($q = $this->mysqli->store_result()) { // Получаем одну таблицу (результат одного Seleсt`a)
						$tempres = array();
						if ($transformC && $q->field_count == 1) {
							while ($row = $q->fetch_assoc())
								$tempres[] = array_shift($row);
						}
						else {
							while ($row = $q->fetch_assoc())
								$tempres[] = $row;
						}
						if ($transformR && $q->num_rows == 1) $tempres = $tempres[0];
						$result[] = $tempres;
						$q->free();
					}
				} while ($this->mysqli->next_result());
				if ($transformT && count($result) == 1) $result = $result[0];
			}
			else $result = true;
		}
		else {
			$this->addEvent(ERROR, "ERROR_SQL: Запрос $sql не был выполнен", $this->mysqli->error);
			$result = false;
		}

		return $result;
	}

}

?>