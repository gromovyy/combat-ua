<?php
	class Updater extends Base {

		/**
		 * Необходимо ли весли лог обращений к БД
		 * @var boolean
		 */
		public $is_backup = TRUE;
	

	public function __construct()
	{
		parent::__construct();
	}

	function e_UpdateToFile($component) {
		$list = $this->updateToFileProcedures($component);
		$list .= $this->updateToFileTables($component);
		if ($list) {
			if (is_dir("cmp/$component")) {
				if (!is_dir("cmp/$component/sql")) mkdir("cmp/$component/sql",0755);
				file_put_contents("cmp/$component/sql/$component.sql",$list);
			}
			else {
				if (!is_dir("cmp_system/$component/sql")) mkdir("cmp_system/$component/sql",0755);
				file_put_contents("cmp_system/$component/sql/$component.sql",$list);
			}
		}
	}
	
	function e_UpdateToFileAll() {
		$user_component = $this->Component->getDirList('cmp');
		$system_component = $this->Component->getDirList('cmp_system');
		$components = array_merge($user_component, $system_component);
		foreach ($components as $component) {
			$this->e_UpdateToFile($component);
		}
	}
	
	function e_UpdateFromFile($component) {
		$this->updateFromFileProcedures($component);
		$this->updateFromFileTables($component);
	}
	
	function e_UpdateFromFileAll() {
		$user_component = $this->Component->getDirList('cmp');
		$system_component = $this->Component->getDirList('cmp_system');
		$components = array_merge($user_component, $system_component);
		//print_r($components);
		if($is_backup){ $this->e_BackUP(); }
		foreach ($components as $component) {
			$this->e_UpdateFromFile($component);
		}
	}
	
	/** Записывает текущую базу данных в файл cmp/$component/SQL/$component.sql 
		или cmp_system/$component/SQL/$component.sql */
		
	function updateToFileProcedures($component) {
		global $GL_DB_NAME;
		$proc_list = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_procedures();
		foreach ($proc_list as $number => $key){
			$proc = explode ('_',$key["Procedure"]);
			$name_proc = $proc[0];
			$cmp = strtolower(preg_replace('/[aeiouy]/', '', $component));
			if ($name_proc == $cmp) {
				$list .= "\n\n------------Создание процедуры \"{$key['Procedure']}\"------------\n\n";
				$list .= "DELIMITER $$\nDROP PROCEDURE IF EXISTS `".$key['Procedure']."` $$\n"; 	  
				$str = $key['Create Procedure'];
				$vol = '|CREATE .* PROCEDURE|';
				$str = preg_replace($vol,'CREATE PROCEDURE',$str);
				$str .="$$\nDELIMITER ;\n ";
				$list .= $str;
			}
		}
		if (empty($proc_list)) return;
		return $list;
	}
	/** Создает базу данных с файла cmp/$component/SQL/$component.sql 
		или cmp_system/$component/SQL/$component.sql 
		При этом создавая бэкап в папку cmp/$component/SQL/backup/ 
		или cmp/$component/SQL/backup/
		*/
	function updateFromFileProcedures($component) {
		global $GL_DB_NAME;
		if (is_dir("cmp/$component")) {
			$sql = "cmp/$component/sql/$component.sql";
			$dir = "cmp";
		}
		if (is_dir("cmp_system/$component")) {
			$sql = "cmp_system/$component/sql/$component.sql";
			$dir = "cmp_system";
		}
		$result = $this->getDbStructureFromFile($sql,$component);
		if ($result["procedures"]) {
			foreach ($result["procedures"] as $proc_name => $sql) {
				$drop = 'DROP PROCEDURE IF EXISTS `'. $proc_name.'`';
				if ($sql) {
					$q = $this->DBProc->queryForUpdater($drop);
					$q = $this->DBProc->queryForUpdater($sql);
				}
			}
		}
	}
	
	
	function updateToFileTables($component) {
		//$table = $this->e_ShowTables();
		$table_list = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_tables();
		$cmp = strtolower(preg_replace('/[aeiouy]/', '', $component));
		foreach ($table_list as $number => $table) {
			$name = explode ('_',$table["Table"]);
			if ($name[0] == $cmp) {
				$nameTable = $table["Table"];
				$tables[$nameTable] = preg_replace('/AUTO_INCREMENT\=\d+ /U','',$table['Create Table']);
				$tables[$nameTable] = preg_replace('/CREATE TABLE/U','CREATE TABLE IF NOT EXISTS',$tables[$nameTable]);
				// Проверка на constraint значения. Если нашли то добавляем вконец файла.
				if (preg_match_all("|CONSTRAINT.*|", $tables[$nameTable],$matches)) {
					$constraintStr .= "ALTER TABLE `$nameTable`";

					foreach ($matches[0] as $key => $value) {
						$constraintStr .= '  ADD '.$value."\n";
					}
					$constraintStr .= ";\n\n";
					// Удаляем упоминания о constraint
					$tables[$nameTable] = preg_replace('|CONSTRAINT.*|','',$tables[$nameTable]);
					// Удаляем последнюю запятую
					$tables[$nameTable] = preg_replace('|,(?![^,]*,)|',"",$tables[$nameTable]);


				}

				$queryTableToFile .= "-- ---------Создание таблицы \"$nameTable\"------------\n";
				$queryTableToFile .= $tables[$nameTable].';';
				$queryTableToFile .= "\n\n";
			}
		}

		$queryTableToFile = preg_replace("!,!",", ",$queryTableToFile);
		if (is_dir("cmp/$component")) {
			$dir = "cmp";
		}
		if (is_dir("cmp_system/$component")) {
			$dir = "cmp_system";
		}
		if ($queryTableToFile) {
			if (!is_dir("$dir/$component/sql")) mkdir("$dir/$component/sql",0755);
			$text .= "\n-- ---------------------------------------------------\n-- ---------------Начинаются таблицы------------------\n-- ---------------------------------------------------\n\n";
			$text .= $queryTableToFile;
			$text .= $constraintStr;
			return $text;
		}
	}
	
	function updateFromFileTables($component) {
		global $GL_DB_NAME;
		if (file_exists("cmp_system/$component/sql/$component.sql")) {
			$file_sql = "cmp_system/$component/sql/$component.sql";
		}
		if (file_exists("cmp/$component/sql/$component.sql")) {
			$file_sql = "cmp/$component/sql/$component.sql";
		} 
		$result = $this->getDbStructureFromFile($file_sql,$component);
		$table_list = ($is_backup) ? $this->DBProc->O(array('extC' => true, 'extR' => true))->get_tables() : array() ;
		if ($result["tables"]) {
			$i = 0;
			foreach ($result["tables"] as $key => $value ){
				foreach ($table_list as $number => $table) {
					if ($table["Table"] == $key ) {
						$nameTable = $table["Table"];
						$k = 1;
					}
				} 
				if ($k == 0) {
					$q = $this->DBProc->queryForUpdater($value);
				}
				else {
					foreach ($table_list as $number => $table) {
						if ($table["Table"] == $nameTable) {
							$tableQuery = $table["Create Table"];
						}
					} 
					preg_match_all("!\n[\s]+(`(.+)`.*),!Uis",$tableQuery,$structure_array);
					foreach ($structure_array[1] as $key1 => $value ){
						$new_array[$value] = $structure_array[2][$key1];// названия всех столбцов, что находяться в таблице
					}
					foreach ($result["structure_$component"][$i] as $field => $query) {
						foreach ($new_array as $key => $val) {
							if ($val == $field) {
								$j = 1;
							}
						}
						if ($j == 0) {
							$column = explode('`',$query);
							$arguments[0] = $nameTable;
							$arguments[1] = $column[1];
							$arguments[2] = $column[2];
							$a = $this->DBProc->O(array('extC' => true, 'extR' => true))->alter_table($nameTable,$column[1],$column[2]);
							echo "**************Изменена таблица $nameTable**************<br />";
						}
						$j = 0;
					} 
				}
				$k = 0;
				$i += 1;    
			}
		}
	}
	
		
		function getDbStructureFromFile($file_sql,$component){

			$result = array();
			// Загружаем файл
			if (file_exists($file_sql)) $file_content = file_get_contents($file_sql);
			// (Я решил не удалять их)Удаляем все коментарии. Строки коментария начинаются с двух дефисов и заканчиваются переносом строки.
			//-- =============================================================================
			//$file_content = preg_replace('!--(.*)[\n\r]!i', "", $file_content);

			//Находим все имена таблиц и загружаем их в массив $table_array
			preg_match_all('!CREATE[\s]+TABLE[\s]+.*`(.+)`.*;!Uis', $file_content, $table_array);
			// Записываем найденные результаты в массив results 
			$i = 0;
			foreach ($table_array[0] as $key => $query_for_table) {
				// Вытягиваем названия всех столбиков таблицы, помещаем их в ключ масива, а в значение - запрос на создание этого столбика
				preg_match_all("!\n[\s]+(`(.+)`.*),!Uis",$query_for_table,$structure_array);
				foreach ($structure_array[1] as $key1 => $value ){
					$new_array[$i][$value] = $structure_array[2][$key1];// названия всех столбцов, что находяться в таблице
				}
				// Витягиваем все что нужно для создания KEY
				preg_match_all("!\n[\s]+KEY[\s]+.*`(.*)`.*\n!Uis",$query_for_table,$key_array);
				foreach ($key_array[1] as $key => $value) {
					preg_match("!\n[\s]+KEY[\s]+.*`$value`.*`(.*)`.*\n!Uis",$query_for_table,$a);
					$kron[$i][$value] = $a[1];
				}
				// Витягиваем все что нужно для создания PRIMARY KEY
				preg_match_all("!\n[\s]+PRIMARY[\s]+KEY[\s]+.*(`(.*)`).*\n!Uis",$query_for_table,$primary_key_array);
				$n = 0;
				foreach ($primary_key_array[1] as $key => $value) {
					$primary_key[$i][$n] = $value;
					$n += 1;
				}
				// Витягиваем все что нужно для создания UNIQUE KEY
				preg_match_all("!\n[\s]+UNIQUE[\s]+KEY[\s]+.*`(.*)`.*\n!Uis",$query_for_table,$unique_key_array);
				foreach ($unique_key_array[0] as $a) {
					$nomer = $unique_key_array[1][0];
					preg_match("![\s]+UNIQUE[\s]+KEY[\s]+.*`$nomer`[\s]+.*`(.*)`.*!Uis",$a,$kapusta);
					$unique_key[$i][$pi] = $kapusta[1];
					
				}
			$i += 1;    
			}
			foreach ($table_array[1] as $key => $value) {
			 //echo $key;
			 $num = $key;
			 //Помещаем все что мы нашли в масив $result
				$result["tables"][$value] = $table_array[0][$key]; // = запрос
				foreach ($new_array as $x => $y) {
					foreach ($new_array[$x] as $key => $val) {
						//структура таблицы $result["structure_$component"][номер_таблицы_в_файле][названия_столбика ] = запрос_на_его_создание
						$result["structure_$component"][$x][$val] = $key;
					}
				}
				if ($kron) {
					foreach ($kron as $x => $y) {
						foreach ($kron[$x] as $key => $val) {
							//ключи таблицы: $result["key"][номер_таблицы_в_файле][значения_ключа?] = значения_ключа?
							$result["key"][$x][$key] = $val;
						}
					}
				}
				if ($primary_key) {
					foreach ($primary_key as $x => $y) {
						foreach ($primary_key[$x] as $key => $val) {
							//ключи таблицы: $result["primary_key"][номер_таблицы_в_файле][какая_то_цыфра] = значения_ключа?
							$result["primary_key"][$x][$key] = $val;
						}
					}
				}
				if ($unique_key) {
					foreach ($unique_key as $x => $y) {
						foreach ($unique_key[$x] as $key => $val) {
							//ключи таблицы: $result["unique_key"][номер_таблицы_в_файле][какая_то_цыфра] = значения_ключа?
							$result["unique_key"][$x][$key] = $val;
						}
					}
				}
				$table_name = $value;
				preg_match_all("!(CREATE[\s]+TABLE[\s]+.*`$value`.*);!Uis",$file_content,$table_query);
				foreach ($table_query as $key => $value1) {
					$result["table_query"][$key] = $value1;
				}
				/*echo "<br />Таблица $value:<br /> Ее код на создание: <br />";
				echo "{$result["tables"][$value]}<br />и ее структура :<br />";
				$value = "structure_".$value;
				print_r ($result[$value][$num]);
				echo "<br />KEY:<br />";
				print_R ($result["key"][$num]);
				echo "<br />PRIMARY KEY:<br />";
				print_R ($result["primary_key"][$num]);
				echo "<br />UNIQUE KEY:<br />";
				print_R ($result["unique_key"][$num]);
				echo "<br />------------------------------------------------------------------------------------<br /><br />";
				*/
			}
			//Находим все имена представлений и загружаем их в массив $view_array
			#preg_match_all("!CREATE[\s]+VIEW[\s]+`(.+)`.*;!Uis", $file_content, $view_array);

			// Записываем найденные результаты в массив results
			#foreach ($view_array[1] as $key => $value) {
			#	$result["views"][$value] = $view_array[0][$key];
			#}

			//Находим все процедуры и загружаем их в массив $procedure_array
			preg_match_all('|(CREATE[\s]+PROCEDURE[\s]+`(.+)`.*)\$\$|Uis', $file_content, $procedure_array);
			foreach ($procedure_array[2] as $key => $value) {
				$result["procedures"][$value] = $procedure_array[1][$key];
			} 
			return $result;
			//Возвращаем объединенный массив
		}
		
		function e_BackUP($db_host = NULL, $db_user = NULL, $db_passw = NULL, $db_name = NULL) {
				global $GL_HOST;
				global $GL_DB_USER;
				global $GL_DB_PASSW;
				global $GL_DB_NAME;
	
				if ($db_host == NULL) {
					$db_host = $GL_HOST;
					$db_user = $GL_DB_USER;
					$db_pass = $GL_DB_PASSW;
					$db_name = $GL_DB_NAME;
				}
			
			//$filename = $dir.$database.date('Y-m-d_H-i-s').'.sql';
			$time = getdate();
			$filename = 'dump.sql';
			ignore_user_abort(true);
			set_time_limit(0);
			/*// Дані які потрібнні для з`єднання з сервером баз даних
			$db_host = 'localhost';
			$db_user = 'root';
			$db_passw = '';     // Пустий пароль за замовчуванням
			$db_name = 'it-university_test'; // Ім`я бази даних із якою працюємо*/
			$mysqli = mysqli_connect($db_host, $db_user, $db_pass, $db_name); 
					// Створимо об`єкт з`єднання з базою даних
			if (mysqli_connect_errno($mysqli)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			DEFINE (MYSQL_VERSION, mysqli_get_server_version($mysqli));  // Визначимо версію сервера MySQL 
	
	
			mysqli_query($mysqli, 'SET NAMES utf8');
			// Створимо запити для бекапу кожного елементу бази даних
			$queries = array(
			   array('TABLE STATUS', 'Name','tables')
			);
			if (MYSQL_VERSION > 50014) {
				$queries[] = array("PROCEDURE STATUS WHERE db=DATABASE();", 'Name','procedures');   
				$queries[] = array("FUNCTION STATUS WHERE db=DATABASE();", 'Name','functions');
				$queries[] = array('TRIGGERS', 'Trigger','trigers');
				if(MYSQL_VERSION > 50100) $queries[] = array('EVENTS', 'Name','events');
			}
			
			// Для будь якого запиту використовується функція mysqli_query();
			// Якак має два параматра: об`єкт з`єднання з базою даних та сам запит
			foreach($queries AS $query){
			   $type = $query[2];
			   $res = mysqli_query($mysqli, 'SHOW ' . $query[0]);	
			   // Якщо запит не виконався через помилку можемо дізнатись чому 
			   if (!$res) {
					echo "Запит не виконався через таку помилку: (" . $mysqli->errno . ") " . $mysqli->error;
			   }
		
			   $todo[$type] = array();
			   $header[$type] = array();
		
		
			   while($item = $res->fetch_assoc()) {
				// Ім`я об`єкту ми зберігаємо у другому ключі, тип у третьому
					$name = $item[$query[1]];
					$type = $query[2];
					switch($type){
					case 'tables':
						 // Перевіряємо чи ця таблиця є видом
						if(MYSQL_VERSION > 40101 && is_null($item['Engine']) && preg_match('/^VIEW/i', $item['Comment'])) {
									$todo['views'] = array();
									$header['views']= array();
								continue;
						}
						else{
							$todo['tables'][]   = array($type, $name, !empty($item['Collation']) ? $item['Collation'] : '', $item['Auto_increment'], $item['Rows'], $item['Data_length']);
							$header['tables'][] = "{$name}`{$item['Rows']}`{$item['Data_length']}";
							$tabs++;
							$rows += $item['Rows'];
						}
						break;
						default:
							
								$todo[$type][] = array($type, $name, !empty($item['collation_connection']) ? $item['collation_connection'] : '');
								$header[$type][] = $name;
				   }     
			   }
		   }
		   if (MYSQL_VERSION > 50014 ) {
				$res = $mysqli->query("SELECT table_name, view_definition /*!50121 , collation_connection */ FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = '$db_name'");
				$views = $dumped = $views_collation = array();
				$re = "/`$db_name`.`(.+?)`/";
				while ($item = mysqli_fetch_assoc($res)) {
					preg_match_all($re, preg_replace("/^select.+? from/i", '', $item['view_definition']), $m);
					$used = $m[1];
					$views_collation[$item['table_name']] = !empty($item['collation_connection']) ? $item['collation_connection'] : '';
					$views[$item['table_name']] = $used;
				}
		
				while (count($views) > 0) {
					foreach ($views as $n => $view) {
						$can_dumped = true;
						foreach ($view AS $k) {
							if (isset($views[$k]) && !isset($dumped[$k])) $can_dumped = false;
						}
						if ($can_dumped) {
							$todo['views'][] = array('views',$n,$views_collation[$n]);
							$header['views'][] = $n;
						}
						$dumped[$n] = 1;
						unset($views[$n]);
					}
			
				}
				unset($dumped);
				unset($views);
				unset($views_collation);
			}
	
			$continue = false;
			$mysqli->query("SET SQL QUOTE SHOW CREATE = 1");
			$fcache = 0;
	
			if (MYSQL_VERSION > 40101) $mysqli->query("SET SESSION character_set_results = 'utf8_general_ci'");
			$time_old = time();
			$time_web = ini_get('max_execution_time');
			$exit_time = $time_old +$time_web - 1;
			$no_cache = MYSQL_VERSION < 40101 ? 'SQL_NO_CACHE ' : '';
			$varTime = array(time(), time(), $rows, 0, '', '', '', 0, 0, 0, 0, TIMER, "\n");
			foreach ($todo as $key => $value) {
				if (empty($varTime[4])) $varTime[4] = $key;
				foreach ($value as $key1 => $value1) {
					if (empty($varTime[5])) {
						$varTime[5] = $value1[1];
						$varTime[7] = 0;
						$varTime[8] = !empty($value1[4]) ? $value1[4] : 0;
					}
					switch ($value1[0]) {
						case 'tables':
							$from = '';
							$fcache = '';
							$res = $mysqli->query("SHOW CREATE TABLE `$value1[1]`");
							$item = mysqli_fetch_assoc($res);
							$createTable=$item['Create Table'];
							//$createTable = preg_replace('/AUTO_INCREMENT=\d+ /U','',$item['Create Table']);
							$fcache .= "\n\n-- Создание таблицы `{$value1[1]}`{$value1[2]}\t\n{$createTable};\n";
							$varTime[7] = 0;
					
							$notNum = array();
							$res = $mysqli->query("SHOW COLUMNS FROM `$value1[1]`");
							$fields = 0;
							while ($columns = mysqli_fetch_assoc($res)) {
								$notNum[$fields] = preg_match("/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year)/", $columns['Type']) ? 0 : 1; 
								$fields++;
							}
							$time_old = time();
							$z = 0;
							$res = $mysqli->query("SELECT $no_cache* FROM `$value1[1]`");
							$numRows = mysqli_num_ROWS($res);
							$x = 1;
							if ($numRows) {
								$fcache .= "\n-- Данные таблицы $value1[1] \n INSERT INTO `$value1[1]` VALUES \n";
								while ($row = mysqli_fetch_row($res)) {
									$fcache .= "( ";
									$comma = '';
									foreach ($row as $num => $cell) {
										if ($cell === NULL) {
											$fcache .= "$comma NULL " ;
										}
										else {
											$fcache .= "$comma'".$cell."' " ;
										}
										$comma = ',';
									}
									if ($x < $numRows) {
										$fcache .= " ),\n";
										$x += 1;
									}
									else {
										$fcache .= ");\n";
										$x = 0;
									}
								}
							}
							file_put_contents ($filename,$fcache,FILE_APPEND);
							$fcache = '';
							break;
							case "procedures":
								$res = $mysqli->query("SHOW CREATE PROCEDURE `$value1[1]`");
								$item = mysqli_fetch_assoc($res);
								$item["Create Procedure"] = preg_replace("/DEFINER=`.+?`@`.+?` /", '',$item["Create Procedure"]);
								$fcache .= "\n-- Создание процедуры $value1[1]\nDELIMETER $$\n".$item["Create Procedure"]."$$\nDELIMETER ;\n";
								file_put_contents ($filename,$fcache,FILE_APPEND);
								$fcache = '';
							break;
							case "trigers":
								$res = $mysqli->query("SHOW CREATE TRIGGER `$value1[1]`");
								$item = mysqli_fetch_assoc($res);
								$item["SQL Original Statement"] = preg_replace("/DEFINER=`.+?`@`.+?` /", '',$item["SQL Original Statement"]);
								$fcache .= "\n-- Создание тригера $value1[1]\n".$item["SQL Original Statement"]."\n\n";
								file_put_contents ($filename,$fcache,FILE_APPEND);
								$fcache = '';
							break;
							case "views":
								$res = $mysqli->query("SHOW CREATE VIEW `$value1[1]`");
								$item = mysqli_fetch_assoc($res);
								$item["Create View"] = preg_replace("/CREATE .* VIEW/", 'CREATE ALGORITHM=UNDEFINED SQL SECURITY VIEW',$item["Create View"]);
								$fcache .= "\n-- Создание вида $value1[1]\n".$item["Create View"]."\n\n";
								file_put_contents ($filename,$fcache,FILE_APPEND);
								$fcache = '';
							break;
							case "events":
								$res = $mysqli->query("SHOW CREATE EVENT `$value1[1]`");
								$item = mysqli_fetch_assoc($res);
								$item["Create Event"] = preg_replace("/DEFINER=`.+?`@`.+?` /", '',$item["Create Event"]);
								$fcache .= "\n-- Создание event $value1[1]\n".$item["Create Event"]."\n\n";
								file_put_contents ($filename,$fcache,FILE_APPEND);
								$fcache = '';
							break;
						default: 
					}
				}
			}
			if (!is_dir("files")) mkdir("files",0777);
			if (!is_dir("files/dump")) mkdir ("files/dump",0777);
			$dir = "files/dump/";
			$zip = new ZipArchive();
			$time = getdate();
			$time["hours"] -= 1;
			$zipName = $dir.$GL_DB_NAME.'_'.$time["mday"].'-'.$time["mon"].'-'.$time["year"].'-'.$time["hours"].'-'.$time["minutes"].'-'.$time["seconds"].'.zip';
			if ($zip->open($zipName, ZIPARCHIVE::CREATE)!==TRUE) {
				exit("Невозможно открыть <$filename>\n");
			}
	
			$zip->addFile($filename);
			$zip->close();
			unlink($filename);
			echo "ВСЕ ОК :)";
		}
		
		
		}
	
?>