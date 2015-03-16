<?php
/**
 * Содержит класс Contenter
 * @package IC-CMS
 * @subpackage Core
 */
 
/**
 * Класс Contenter отвечает за весь функционал связанный с редактированием на
 * лету и возможными другими функциями контентных компонентов
 *
 * Класс реализует возможности
 * - Базовый класс для всех генерируемых контентных компонентов
 * - Реализует функционал для редактирования на лету
 * - Кеширует таблици и поля из БД для текущего компонента
 *
 * @requirement {@link Viewer}
 * @requirement {@link DBProc}
 * @modified v1.0 от 26 января 2012 года
 * @property array $cellIdCache {@link _sess_cellIdCache declatation} Кэш айди ячеек
 * @version 1.0
 * @author Иван Найдёнов
 * @author Александр Громовой
 * @package IC-CMS
 * @subpackage Core
 */
class Contenter extends Viewer
{

	/**
	 * Кэш айди ячеек
	 *
	 * Структура масива: <code>array(array(string $type, string $tableName, string $fieldName, int $rowId, string $resource))</code>
	 * @abstract Описывает сессионное поле. Прямой доступ запрещен. Используеться классом {@link Session}
	 * @var array
	 */
	private static $_sess_cellIdCache;

	/**
	 * Инициализатор.
	 * - Переопределяет БД префикс на более читаемый
	 */
	public function _init()
	{
		if ($this->cellIdCache === null)
			$this->cellIdCache = array();
				// Вставка jqurry из cdn
		// $this->includeJS('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
			// <script>window.jQuery || document.write("<script src=\'lib/js/jquery_1.9.1.js\'><\/script>")</script>
			// <script async ="true" src="lib/js/main.js');
		//$this->includeJS("jquery-2.1.1.min.js");
		$this->includeJS('main.js',6);
		$this->includeJS('viewer.js',6);

		//$this->includeJS('infinite.js');

		$this->includeJS('jquery.hotkeys.js',4);
		$this->includeJS('jquery.autoresize.js',5);
		
		$this->includeCSS('contenter.css');
	}

	private function findCacheKey($param_array)
	{
		if ($this->cellIdCache === null)
			return null;
		foreach ($this->cellIdCache as $key => $var)
			if ($var[0] == $param_array[0] && $var[1] == $param_array[1] && $var[2] == $param_array[2]&& $var[3] == $param_array[3])
				return $key;
		return null;
	}

	private function getCacheId($param_array, $rowId)
	{

//		var_dump($param_array, $this->cellIdCache);
		// если параметры еще не кешированы - сохраняем
		$key = $this->findCacheKey($param_array);
		if ($key === null) {
			$this->cellIdCache[] = $param_array;
			$key = $this->findCacheKey($param_array);
		}
		else
			$this->cellIdCache[$key] = $param_array;
		// получаем айди ячейки используя айди в кеше
		return 'cell' . $key . '_' . $rowId;
	}

	private function getParamsById($cellId)
	{
		list($cacheId, $rowId) = explode('_', substr($cellId, 4));
		if (isset($this->cellIdCache[$cacheId]))
			return array($this->cellIdCache[$cacheId], $rowId);
		else
			return false;
	}

	/**
	 * Функция для отрисовки ячеек для отображения/редактирования на лету
	 *
	 * @param string $type тип требуемой ячейки
	 * @param string|array $tableName имя таблици содержащей ячейку
	 * @param string|array $fieldName имя поля (столбца) содержащего ячейку.
	 * @param int $rowId идентификатор сторки содержащей ячейку
	 * @param string|array $value отображаемое значение. Если не заданно будет получено из БД
	 * @param string $resource имя ресурса доступа, разрешение на редактирование который
	 * требуеться для изменения значения поля
	 */
	protected function Input($type, $tableName, $fieldName=null, $rowId=NULL, $value=NULL, $extraIn=array(), $id_owner=null)
	{
		$extra = $extraIn;
		$extra['type'] = $type;
		$extra['tableName'] = $tableName;
		$extra['fieldName'] = $fieldName;
		$extra['rowId'] = $rowId;
		$this->includeCSS('jquery-ui.min.css');
		$this->includeJS('jquery-ui.min.js');
		$this->includeJS('jquery-ui-timepicker-addon.js');
		// Если мы в режиме правки только своих объектов, получаем поле id_owner
		if (empty($id_owner) and ($this->getR("update")==2))
			$id_owner = $this->get_owner($rowId, $tableName);
		
		// получаем айди ячейки используя айди в кеше
		$id = $this->getCacheId(array($this->component, $type, $tableName, $fieldName, $extra), $rowId);
		
		$is_update = ($this->is_update($id_owner, $rowId, $tableName) and ($this->User->getMode()=="edit" or $extra['mode']=='edit'));
		if ($is_update) $this->includeJS("contenter.js");
		switch ($type)
		{
			// Поле для редактирования
			case 'text':
					if((empty($extra['valueList']))){
						echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"text(this);return false;\"":""). ">".(($value=="")?"&nbsp":$value)."</span>";
					}else{
						// Подключаем typeahead
						$this->Contenter->includeJS('typeahead.min.js');
						// Формируем массив строк.
						//$data = '{'.implode(',',$extra['valueList']).'}';
						$data = json_encode($extra['valueList']);
						$data = str_replace('"','\'',$data);
						// Выводим Инпут
						echo "<span".(($is_update)?" id='$id' class='contenter-input' onclick=\"text(this,".$data.");return false;\"":""). ">".(($value=="")?"&nbsp":$value)."</span>";
					}
					break;
					
			// Выпадающий список
			case 'combobox':
				$selected = $value; 
				foreach ($extra['valueList'] as $var)
					if ($var['id'] == $selected) {
						$value = $var['v'];
						break;
					}
				foreach ($extra['valueList'] as $key=>$var)
						$extra['valueList'][$key]['v'] = str_replace('"','&quot;', $extra['valueList'][$key]['v']);

				// Сохраняем данные списка для передачи на клиент в JSON формате
				self::$jsData['input'][$id]['selected'] = $selected;
				self::$jsData['input'][$id]['valueList'] = $extra['valueList'];
				self::$jsData['input'][$id]['type'] = $type;
				// Выводим список
				echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"combobox(this);return false;\"":""). ">".(empty($value)?"&nbsp":$value)."</span>";
				break;
			case 'date':
				$this->includeCSS('jquery-ui.min.css');
				$this->includeJS('jquery-ui.min.js');
				echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"date(this);return false;\"":""). ">$value</span>";
				break;
			case 'datetime':
				$this->includeCSS('jquery-ui.min.css');
				$this->includeJS('jquery-ui.min.js');
				$this->includeJS('jquery-ui-timepicker-addon.js');
				if (!empty($extra['dateFormat']))
					echo "<span data-value='$value' ".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"datetime(this);return false;\"":""). ">".$this->formatDate($value, $extra['dateFormat'])."</span>";
				else
					echo "<span data-value='$value' ".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"datetime(this);return false;\"":""). ">$value</span>";
				break;
			case 'time':
				$this->includeCSS('jquery-ui.min.css');
				$this->includeJS('jquery-ui.min.js');
				$this->includeJS('jquery-ui-timepicker-addon.js');
				echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"time(this);return false;\"":""). ">$value</span>";
				break;
			case 'date-simple':
					$this->includeCSS('datestyle.css');
					$this->includeJS('simple-date-time.js');
					echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"date-simple(this);return false;\"":""). ">$value</span>";
				break;
			case 'datetime-simple':
					$this->includeCSS('datestyle.css');
					$this->includeJS('simple-date-time.js');
					echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"datetime-simple(this);return false;\"":""). ">$value</span>";
				break;
			case 'color':
				$this->includeJS('mColorPicker.js');
				echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"color(this);return false;\"":""). ">$value</span>";
				break;
			case 'password':
				echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"password(this);return false;\"":""). ">*****</span>";
				break;	
			case 'checkmark':
				if (empty($extra['valueList']) or !is_array($extra['valueList'])) {
					$value_yes = 1;
					$value_no = 0;
				} else {
					$value_yes = $extra['valueList'][0];
					$value_no = $extra['valueList'][1];
//	file_put_contents('test.txt', print_r($extra['valueList'], true), FILE_APPEND);
				}
				switch ($value) {
					case $value_yes:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: '$value_no'})\"";
								$class = "checkmark yes ";break;
					default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: '$value_yes'})\"";
								$class = "checkmark no ";break;
				}
				echo '<div id="'.$id.'" data-id="'.$id.'" class="contenter-input '.$class.$extra['class'].'" '.(($is_update)?$js:"").'></div>';
				break;
			default:
				if ($this->checkViewExist($type,'Contenter')) {
					$data = array('id'=> $id, 'is_update' => $is_update, 'value'=>$value, 'extra'=>$extra);
					$this->loadView($type,$data,'Contenter');
				} else {
					// Если нету требуемого вида загружаем текстовое редактирование по умолчанию.
					if((empty($extra['valueList']))){
						echo "<span".(($is_update)?" id='$id' class='contenter-input' onclick=\"text(this);return false;\"":""). ">".(($value=="")?"&nbsp":$value)."</span>";
					}else{
						// Подключаем typeahead
						$this->Contenter->includeJS('typeahead.min.js');
						// Формируем массив строк.
						//$data = '{'.implode(',',$extra['valueList']).'}';
						$data = json_encode($extra['valueList']);
						$data = str_replace('"','\'',$data);
						// Выводим Инпут
						echo "<span".(($is_update)?" id='$id' data-id='$id' class='contenter-input' onclick=\"text(this,".$data.");return false;\"":""). ">".(($value=="")?"&nbsp":$value)."</span>";
					}
				}
				break;
		}

		return true;
	}
	

	/**
	 * Отображает переключатель режима редактирования/просмотра с редактированием
	 */
	protected function showEditModeSwitcher()
	{
		$this->loadView('edit_mode_switcher', NULL, 'Contenter');
	}


	/**
	 * Событийная функция обработки изменения значения ячейки на лету
	 *
	 * @param string $cell_id айди ячейки
	 * @return void
	 */
	public function e_editCell($cell_id)
	{
		$params = $this->getParamsById($cell_id);
//			throw new AjaxErrorException('Test');
		if ($params === false) {
			echo $cell_id;
			throw new AjaxErrorException('Не найдена ячейка для редактирования. Попробуйте обновить страницу. Не забудьте скопировать измененный текст, если он ценен');
		}
		list(list($component, $type, $tableName, $fieldName, $extra), $rowId) = $params;
		if (isset($_POST['value']))
			$extra['value'] = $value =$_POST['value'];// htmlspecialchars_decode($_POST['value']);
		$value = $this->getTextFromHTML($value);
		//throw new AjaxErrorException($value);
	
		//self::$ajaxResult['script'] = $extra['jsOnChange'];
		//if ($extra['phpInstedChange'] !== null)
		//	call_user_func($extra['phpInstedChange'], $extra);
		//else {
		switch ($type)
		{
			case 'add':
//				if (!$this->User->checkAccess($extra['resource'], $extra['action'], $extra['resourceRowId']))
//					throw new AjaxErrorException('У вас нету права на выполнение этого действия');
				self::$ajaxResult['newRowId'] = $this->$component->DBProc->O(array('extR' => true, 'extC' => true))->create_row($tableName, $fieldName, $rowId);
				break;
			case 'delete':
//				if (!$this->User->checkAccess($extra['resource'], $extra['action'], $extra['resourceRowId']))
//					throw new AjaxErrorException('У вас нету права на выполнение этого действия');
				if( $this->$component->is_delete($this->get_owner($rowId, $tableName), $rowId))
						$this->$component->DBProc->delete_row($tableName, $rowId);
				break;
			default:
				if (isset($this->$type) && method_exists($type, computeChange))
					$this->$type->computeChange($extra);
				else {
					//var_dump($tableName, $fieldName, $rowId, $value) ;
//					if (!$this->User->checkAccess($extra['resource'], $extra['action'], $extra['resourceRowId']))
//						throw new AjaxErrorException('У вас нету права на выполнение этого действия');
					//if( $this->$component->is_update($this->get_owner($rowId, $tableName), $rowId, $tableName)) {
						//throw new AjaxErrorException($tableName);
						ob_start();
						if ($type == 'password') {
							$this->$component->set_cell($tableName, $fieldName, $rowId, md5($value.'pdzjnb'));
							$this->$component->Input($type, $tableName, $fieldName, $rowId, "", $extra);
						}
						else {
							$this->$component->set_cell($tableName, $fieldName, $rowId, $value);
							$this->$component->Input($type, $tableName, $fieldName, $rowId, $value, $extra);
						}
						
							
						
						self::$ajaxResult['updatedViews'][] = ob_get_clean();
					//}
		
					break;
				}
				break;
		}
		//	if ($extra['phpOnChange'] !== null)
		//		call_user_func($extra['phpOnChange'], $extra);
		//}
	}

	public function delete_row($table, $row_id)
	{
		// Запускаємо тригер перед видаленням.
		$this->BeforeDelete($table, $row_id);
		$result = $this->DBProc->delete_row($table, $row_id);
		// После обновления, запускаем событие AfterInsert
		
		return $result;
	}

	// Создает новый ряд
	public function create_row($table)
	{
		$id_row = $this->DBProc->O(array('extR' => true, 'extC' => true))->create_row($table, "id_owner", $this->User->getId());
		$this->set_cell($table, "order", $id_row, $id_row);
		// После обновления, запускаем событие AfterInsert
		$this->AfterInsert($table, $id_row);
		
		return $id_row;
	}
	
	// Изменяет содержимое ячейки БД
	public function set_cell($table, $column, $row_id,  $value)
	{
		// Получаем старое значение ячейки
		$old_value = $this->DBProc->O(array('extR' => true, 'extC' => true))->get_cell($table, $column, $row_id);
		// Если старое значение такое же, как новое - выходим без обновления
		if ($old_value == $value) return false;

		// Обновляем ячейку
		$id_updater = $this->User->getId();
		$result = $this->DBProc->set_cell($table, $column, $row_id, $value, $id_updater);
		
		// После обновления, запускаем событие AfterUpdate
		$this->AfterUpdate($table, $column, $row_id, $value, $old_value);
		return $result;
	}
	
	public function combo_country_list()
	{
		return $this->DBProc->country_list();
	}
	
	public function get_combo($array, $id_field, $value_field) {
		foreach ($array as $row) {
			$data[] = Array('id'=>$row[$id_field], 'v'=>$row[$value_field]);
		}
		return $data;
	}
	
	// Функция получает два массива и массив связи. 
	// Если в массиве связи нет соединительного ряда - он создается
	// Особенность работы функции - нужно перезагрузить страницу чтобы увидеть корректный результат.
	function getFullRelationArray($array1, $field1, $array2, $field2, $relation_array, $relation_table, $field3 = null, $id_owner = null) {
		if (!is_array($array1) or !is_array($array2)) return null;
		foreach ($relation_array as $row) {
			$tmp_result[$row[$field1]][$row[$field2]] = $row;
		}
		foreach ($array1 as $row1)
			foreach ($array2 as $row2) {
				
				if (!isset($tmp_result[$row1[$field1]][$row2[$field2]])) {
					$id_row = $this->create_row($relation_table);
					$this->set_cell($relation_table, $field1, $id_row, $row1[$field1]);
					$this->set_cell($relation_table, $field2, $id_row, $row2[$field2]);
					if (!empty($id_owner)) $this->set_cell($relation_table, "id_owner", $id_row, $id_owner);
					$result[$row1[$field1]][$row2[$field2]] = Array();
					}
				if (!empty($field3)) {
					if (!empty($tmp_result[$row1[$field1]][$row2[$field2]])) {
						$key = $tmp_result[$row1[$field1]][$row2[$field2]][$field3];
						$result[$key][$row1[$field1]][$row2[$field2]] = $tmp_result[$row1[$field1]][$row2[$field2]];
					}
				}
				else  {
					$result[$row1[$field1]][$row2[$field2]] = $tmp_result[$row1[$field1]][$row2[$field2]];
				}		
		}
		
		return $result;
	}
	
	//Функция для проверки собственника ресурса
	//Требует обязательного наличия поля id_owner в таблице $table.
	public function get_owner($id_row, $table = null)
	{
		if(empty($table))
			$table = $this->dbprefix."_".strtolower($this->component);
		return $this->DBProc->O(array('extR' => true, 'extC' => true))->get_row_owner($table, $id_row);
	}
        
	// Возвращает ряд таблицы вызывающего его компонента по его идентификатору и имени объекта
	public function get_row($id_row, $table = null)
	{
		if(empty($table))
			$table = $this->dbprefix."_".strtolower($this->component);
		return $this->DBProc->O(array('extR' => true, 'extC' => true))->get_row($table, $id_row);
	}
	
	/* Создает диалог удаления объекта */
	function e_DeleteDlg($id_row, $object = null,$update_link) {
		$table = $this->getTableByObject($object);
		if (!$this->is_delete(null, $id_row, $table)) return false;
		$data["id_object"]   = $id_row;
		$data["object"]      = $object;
		$data["update_link"] = $update_link;
		$data["component"]   = $this->component;
		if ($this->checkViewExist('delete_dlg')) {
			$this->loadView("delete_dlg", $data);
		} else {
			// Load default view
			$this->loadView("delete_dlg", $data,'Contenter');
		}
	}
	
	// Возвращает имя объекта по имени таблицы. Имя объекта формируется как имя таблицы без префикса компонента.
	function getObjectByTable($table){
		if (empty($table) or  ($table == $this->dbprefix."_".strtolower($this->component))) 
			{ $object = strtolower($this->component); }
		else 
			{ $object = substr($table,strpos($table, "_")+1); }
		return $object;
	}
	
	// Возвращает имя таблицы по имени объекта
	// Имя таблицы формируется как имя объекта в нижнем регистре с префиксом компонента
	function getTableByObject($object=NULL){
		if(empty($object))
			$table = $this->dbprefix."_".strtolower($this->component);
		else 
			$table = $this->dbprefix."_".strtolower($object);
		return $table;
	}
	
	// Возвращает имя таблицы по имени объекта и компонента
	// Имя таблицы формируется как имя объекта в нижнем регистре с префиксом компонента
	function getTableByComponentObject($component, $object = NULL){
		if(empty($component) and empty($object)) return '';
		if(empty($object))
			$table = $this->$component->dbprefix."_".strtolower($this->$component->component);
		else 
			$table = $this->$component->dbprefix."_".strtolower($object);
		return $table;
	}
	
	function getTableByTable($table){
		if(empty($table))
			$table = $this->dbprefix."_".strtolower($this->component);
		return $table;
	}
	

	
	// Функция для сортировки массива, возвращаемого после запроса к БД по указанному столбцу.
	function SortArrayByColumn($array, $column='order') {
			if (is_array($array)) {
			
				foreach ($array as $key => $row) {
					$volume[$key]  = $row[$column];
				}			
				array_multisort($volume, SORT_ASC, $array);
			}
			return $array;
	} 
	

	// Событие, которое возникает при любом изменении ячейки
	protected function AfterUpdate($table, $column, $row_id, $value, $old_value){
	
	}

	// Событие, которое возникает при любом добавлении ряда
	protected function AfterInsert($table, $row_id){
	
	}
		// Событие, которое возникает при любом удалении ряда
	protected function BeforeDelete($table, $row_id){
	
	}
	
	// Вывод диалога выборки объектов
	public function e_SelectDlg($bind_object){
		$this->loadView("select_dlg", $bind_object);
	}
	
	// Функция, которая устанавливает статус для текущего объекта
	// Реализация по умолчанию проверяет статусы всех объектов-детей в таблице contntr_bind 
	// И устанавливает итоговый статус нашего объекта
	protected function setState($object, $id_object){
	
	}
	
	
	// Если $id_visible = 1 возвращает состояние 'visible'
	// Если $id_visible = 0 возвращает состояние 'hidden'
	public function getVisibility($is_visible){
		return (empty($is_visible))?'hidden':'visible';
	}
	
	
	// Функция формирует Toolbox для работы с компонентами
	// $component_parent, $object_parent, $id_parent - название компонента, объекта и идентификатора экземпляра
	// $tool_list - список кнопок на панели инструментов.
	// Возможные значение массива $tool_list :(delete, edit, visible, hidden, down, up, left, right, add, select)
	
	protected function loadToolBox($object, $id_object=NULL, $component_bind, $object_bind, $id_object_bind, $tool_list, $update_link){
		$data['component'] = $this->component;
		$data['component_bind'] = $component_bind;
		$data['object_bind'] = $object_bind;
		$data['id_object_bind'] = $id_object_bind;
		$data['id_object'] = $id_object;
		$data['object'] = $object;
		$data['tool_list'] = $tool_list;
		$data['update_link'] = $update_link;
		

		$table = $this->getTableByComponentObject($this->component,$object);
		//Делаем необходимые проверки на вывод данных
		if (!$this->is_insert(null,$id_object,$table)) {
			if(($key = array_search('add',$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
		}
		if (!$this->is_update(null,$id_object,$table)) {
			if(($key = array_search('right'     ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('left'      ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('up'        ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('down'      ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('visibility',$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('visible'   ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('hidden'    ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('edit'      ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
		}
		if (!$this->is_delete(null,$id_object,$table)) {
			if(($key = array_search('delete'    ,$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
			if(($key = array_search('delete-dlg',$data['tool_list'])) !== false){unset($data['tool_list'][$key]);}
		}
		


		// Проверка на инсрумент видимости объекта.
		if (in_array("visibility",$tool_list)) {
			// Получаем строку с текущим объектом
			$result = $this->get_row($id_object , $table);
			$data['tool_list'][] = $this->getVisibility($result['is_visible']);
		}
		if (!empty($data['tool_list'])) {
			$this->loadView("toolbox", $data, "Contenter");
		}
		
	}
	
	// Функція додавання нового об'экту $object в поточний компонент та зв'язування його з іншим довільним об'єктом
	// Для успішного зв'язування, в таблиці з новим об'єктом повинен бути стовпчик, ім'я якого співпадає з 
	// первинним ключем у таблиці зв'язку.
	public function e_Insert($object=null, $component_bind=NULL, $id_object_bind=NULL, $object_bind=NULL){
		// Если имя объекта не задано используем стандартное имя таблици
		if(empty($object)){
			$object = strtolower($this->component);
		}
		$table = $this->getTableByObject($object);
		if (!$this->is_insert(null,null,$table)) return false;

		$id_object = $this->create_row($table);
		
		// Проверяем - если нужна пустая связь без ссылки на объект, создаем её
		if (!empty($component_bind) and !empty($id_object_bind)) {
			// Создаем новую запись в таблице связи
			$bind_table = $this->getTableByComponentObject($component_bind, $object_bind);
			$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);
			$this->set_cell($table, $bind_column_name, $id_object, $id_object_bind);
		}
		return $id_object;
	}
	
	// Функція додавання нового об'экту $object в поточний компонент та зв'язування його з іншим довільним об'єктом
	// Для успішного зв'язування, в таблиці з новим об'єктом повинен бути стовпчик, ім'я якого співпадає з 
	// первинним ключем у таблиці зв'язку.
	public function e_DeleteList($object, $component_bind, $id_object_bind, $object_bind=NULL){
	
		if (empty($component_bind) or empty($id_object_bind)) return false;
		if (empty($object_bind)) { $object_bind = strtolower($component_bind);}
		
		$table = $this->getTableByObject($object);
		$list = $this->getList($object, $component_bind, $id_object_bind, $object_bind);
		$id_column_name = "id_".strtolower($object);
		foreach ($list as $del_object)
			$result = $this->e_Delete($object, $del_object[$id_column_name]);
		
		return true;
	}
	
	// Функція видалення об'єкту
	public function e_Delete($object, $id_object){
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		// Проверка имем ли мы право на удаление этого рядка.
		if (!$this->is_delete(null , $id_object, $table)) return false;
		$instance = $this->get_row($id_object, $table);
		if (!empty($instance)) 
				$this->delete_row($table,$id_object);
		return true;
	}

	// Функція зміни видимості об'єкту
	public function e_Show($object, $id_object){
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		// Имеем ли мы право на изменение этого объекта.
		if (!$this->is_update(null, $id_object, $table)) return false;

		$instance = $this->get_row($id_object, $table);
		if (!empty($instance)) {
				if (empty($instance['is_visible'])) 
					$this->set_cell($table,"is_visible", $id_object, 1);
				else 
					$this->set_cell($table,"is_visible", $id_object, 0);
				return true;
			}
		return "В таблице $table экземпляра с идентификатором $id_object не существует";
	}
	
	//Сдвиг объекта на одну позицию влево
	function e_MoveLeft($object, $id_object, $component_bind=NULL, $id_object_bind=NULL, $object_bind=NULL){
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);

		// Имеем ли мы право на изменение этого объекта.
		if (!$this->is_update(null , $id_object, $table)) return false;
		if (!empty($component_bind) or !empty($object_bind))
		//$bind_table = $this->getTableByComponentObject($component_bind, $object_bind);
			$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);
		// Сдвигаем запись влево
		$this->DBProc->move_left($table, $id_object, $bind_column_name, $id_object_bind);
		return true;
	}
	
	//Сдвиг объекта на одну позицию влево
	function e_MoveRight($object, $id_object, $component_bind=NULL, $id_object_bind=NULL, $object_bind=NULL){
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);

		// Имеем ли мы право на изменение этого объекта.
		if (!$this->is_update($this->get_owner($id_object, $table), $id_object, $table)) return false;

		if (!empty($component_bind) or !empty($object_bind))
					$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);
		
		// Сдвигаем запись влево
		$this->DBProc->move_right($table, $id_object, $bind_column_name, $id_object_bind);
		return true;
	}
	
	// Функция возвращает список всех детей родителя
	// Параметры: 
	// $component_bind, $object_bind, $id_object_bind - название компонента, объекта и идентификатора родителя
	protected function getList($object=null, $id_owner=NULL, $component_bind=NULL, $id_object_bind=NULL, $object_bind=NULL, $sort_field=NULL,$limit_offset = null, $limit_count=null ) {
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		if (!empty($component_bind) or !empty($object_bind))
			$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);

		if (self::$ajaxInfiniteLoad) {
			$limit_count = self::$ajaxCount;
			$limit_offset = self::$ajaxOffset;
			self::$ajaxInfiniteLoad = false;
		}


		$result_array = $this->DBProc->list($table,$id_owner, $bind_column_name, $id_object_bind,$limit_offset,$limit_count);
		// Якщо вказано поле для сортування - сортуємо по ньому
		if (!empty($sort_field)) {
			foreach($result_array as $result)
				$result_sorted_array[$result[$sort_field]][] = $result;
			$result_array = $result_sorted_array;
		}
		if (empty($result_array))
			$result_array = Array();
		return $result_array;
	}
	
	// Функция возвращает список всех детей родителя
	// Параметры: 
	// $component_bind, $object_bind, $id_object_bind - название компонента, объекта и идентификатора родителя
	// Формат $parentArray = Array  (  
	//									Array($component_bind, $object_bind, $id_object_bind),
	//									...,
	//									Array($component_bind, $object_bind, $id_object_bind))
	// 		
	
	protected function getListByParent($object, $id_owner=NULL, $parent_array=Array(), $sort_field=NULL) {
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);
		$i= 1;
		foreach($parent_array as $parent) {
			$parent_link[$i]['table'] = $this->getTableByComponentObject($parent[0], $parent[1]);
			$parent_link[$i]['value'] = $parent[2];
			$i++;
		}
		$result_array = $this->DBProc->list_by_parent($table,$id_owner, $parent_link[1]['table'], $parent_link[1]['value'], $parent_link[2]['table'], $parent_link[2]['value'], $parent_link[3]['table'], $parent_link[3]['value']);
		if (!empty($sort_field)) {
			foreach($result_array as $result)
				$result_sorted_array[$result[$sort_field]][] = $result;
			$result_array = $result_sorted_array;
		}
		if (empty($result_array))
			$result_array = Array();
		return 	$result_array;
	}
	
	// Функция возвращает запись по связи
	// Параметры: 
	// $component_bind, $object_bind, $id_object_bind - название компонента, объекта и идентификатора родителя
	protected function getView($object, $id_object) {
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		return $this->get_row($id_object, $table);
	}
	
	// Функция возвращает порядковый номер ребенка родителя
	// Параметры: 
	// $component_parent, $object_parent, $id_parent - название компонента, объекта и идентификатора родителя
	// $component_child, $object_child, $id_child - название компонента, объекта и идентификатора ребенка.
	protected function getOrder($object, $id_object, $component_bind=NULL, $id_object_bind=NULL, $object_bind=NULL,$sort_field=NULL) {
		// Получаем таблицу связи
		$table = $this->getTableByObject($object);
		$bind_column_name = (empty($object_bind))?"id_".strtolower($component_bind):"id_".strtolower($object_bind);
		$order = $this->DBProc->O(array('extC' => true, 'extR' => true))->get_order($table, $id_object, $bind_column_name, $id_object_bind);
		return $order;
	}

	public function e_List($object = null)
	{
		if (empty($object)) {
			$object = $this->sufix;
		}
		// Получаем данные для вывода.
		$data['list'] = $this->getList($object);
		// Если в компоненте существует вид list то просто загружаем.
		if ($this->checkViewExist('list')) {
			return $this->loadView('list', $data);
		}
		if($this->User->getRole() != 'administrator'){ return; }

		$keys = array_keys($data['list'][0]); // Получаем ключи таблицы
		$data['keys']  = array_diff($keys,array("id_$object",'id_updater','order','id_owner','update_date','create_date','is_visible'));
		$data['table']= $this->getTableByObject($object);
		$data['object']= $object;
		$data['component']= $component;

		return $this->loadView('list', $data, 'Contenter');
	}
	
	
	// Проверка права на редактирование
	public function is_update($id_owner = null, $id_row = null, $object = null){
		return $this->is('update',$id_owner,$id_row,$object);
	}

	// Проверка права на выборку
	public function is_select($id_owner = null, $id_row = null, $object = null)
	{
		return $this->is('select',$id_owner,$id_row,$object);
	}
	
	// Проверка права на вставку
	public function is_insert($id_owner = null, $id_row = null, $object = null) {
		return $this->is('insert',$id_owner,$id_row,$object);
	}	
	
	// Проверка права на видимость невидимых елементов
	public function is_visible($id_owner = null, $id_row = null, $object = null) {
		return $this->is('visibility',$id_owner,$id_row,$object);
	}
		// Проверка права на удаление
	public function is_delete($id_owner=null, $id_row=null, $object = null) {
		return $this->is('delete',$id_owner,$id_row,$object);
	}

function is ($rule = 'select', $id_owner = null, $id_row = null, $table = null) {
		$id_user = $this->User->getId();
		// Если текущий пользователь имеет Администраторкие права на обновление, положительный результат
		if ($this->getR($rule)==1) { 
			return true; 
		}
		// Если пользователь имет право на свои обьекты
		if( ($this->getR($rule)==2) 
			&& ($this->User->getId() == $id_owner)
			&& (!empty($id_owner))
			) { return true; }
		
		// Для проверки других варинтов нужен доступ к бд
		if (!empty($id_row) && ($this->getR($rule)==2)) {
			$row = $this->get_row($id_row,$table);
			// Дополнительная проверка по текущему компоненту
			if( ($this->getR($rule)==2) 
				&& ($this->User->getId() == $row['id_owner'])
			) { return true; }
			
			$function = "is_$rule";
			// Проверяем доступ у родительского компонента.
			if(!empty($row['component'])){
				return $this->$row['component']->$function($id_user, $row['id_object'],$this->$row['component']->getTableByObject($row['object']));
			}
		}
		return false;
	}
	
	// Форматирование даты в нужном виде
	public function formatDate($date, $mode = "datetime"){
		$month = array( 
			"01" => "января", 
			"02" => "февраля", 
			"03" => "марта", 
			"04" => "апреля", 
			"05" => "мая", 
			"06" => "июня", 
			"07" => "июля", 
			"08" => "августа", 
			"09" => "сентября", 
			"10" => "октября", 
			"11" => "ноября", 
			"12" => "декабря"); 
			
		$dt = new DateTime($date);
		switch ($mode) {
			case "date": 
				return $dt->format('d')." ".$month[$dt->format('m')];
				break;
			case "time": 
				return $dt->format('H:i');	
				break;
			default: 
				return $dt->format('d')." ".$month[$dt->format('m')]." ".$dt->format('H:i');	
		}
	}

}