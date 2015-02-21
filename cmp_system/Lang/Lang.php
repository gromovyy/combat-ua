<?php
class Lang extends Contenter
{
	protected static $_sess_current_lang;
	public $filter_strings = array('&times;', '&nbsp;','&laquo;','&raquo;','×','');
	// Supported language
	
	public $supported_langs = array('ua','ru');

	/**
	 * Инициализатор, задает текущий язык для использования в бд
	 */
	public function _init()
	{
		GLOBAL $GL_LANG;
		if ($this->current_lang === null)
			$this->current_lang = $GL_LANG;
	}
	
	public function e_Translage(){
		$this->loadView('translate');
	}
	
	public function e_SetLanguage($lang){
		if (empty($lang)) return;
		$this->current_lang = $lang;
	}
	
	public function getLang(){
		return $this->current_lang;
	}
	
	public function e_GoogleTranslate($lang_from, $lang_to){
		
	}
	
	// Создает языковые файлы из указанного вида
	public function e_GetLangFromView($component, $view, $lang = NULL){
		
		GLOBAL $GL_LANG;
		if(empty($lang))
			$lang = $GL_LANG;
		$view_file = $this->Viewer->getViewFileName($view, $component);
		if (empty($view_file)) return false;
		$view_text = file_get_contents($view_file);
		
		// Удалить все комментарии
		$lang_string = preg_replace('#<!--.*-->#suUi','',$view_text);
		// Удалить все скрипты
		$lang_string = preg_replace('#<script>.*</script>#suUi','',$lang_string);
		// Удалить весь php-код
		$lang_string = preg_replace('#<\?.*\?>#suUi','',$lang_string);
		
		// Выделить текст из мета-тегов
		
		// Выделить текст из тегов alt
		
		// Выделить текст из тегов alt
		
		// Заменить весь текст вне тегов.
		
		$lang_string = preg_replace('#<[^<]*>#suUi','|||',$lang_string);
		echo $lang_string;
		
		// Повторно заменяем, если была вложенность
		$lang_string = preg_replace('#<[^<]*>#suUi','|||',$lang_string);
		echo $lang_string;
		//echo $lang_string;
		
		// Соединить все разделители и убрать пробелы и все записи без букв(цифры, разделители) между ними
		$lang_string = preg_replace('#(\s)*(\|\|\|)(\s|\||[^a-zA-Za-яА-Я])*(\|\|\|)#u','|||',$lang_string);
		
		
		//echo $lang_string;
		// Убираем все строки, в которых нет букв, например телефон
		//$lang_string = preg_replace('#\|\|\|([^a-zA-Za-яА-Я]*)(?:\|\|\|)#suUi','|||',$lang_string);
		
		echo $lang_string;
		
		// Разбиваем всю языковую строку на отдельные подстроки
		$dirty_strings = explode('|||', $lang_string);
		
		
		//print_r($this->filter_strings);
		// Чистим от неязыковых элементов
		foreach( $dirty_strings as $str ){
			if (in_array(trim(mb_strtolower($str)), $this->filter_strings))	 
				continue;
			//if(!empty($str)) 
			$lang_strings[] = $str;
		}
		
		if (empty($lang_strings)) {
			echo "Языковых строк в виде $view компонента $component не найдено";
			return true;
		}
		// Убираем повторяющиеся элементы
		$lang_strings = array_unique($lang_strings);
		
		$lang_strings_sorted = $lang_strings;
		// Сортируем $lang_string по спаданию длины
		usort($lang_strings_sorted,'lensort_desc');
		print_r($lang_strings_sorted);
		
		// Если нет языковых строк - уходим
		if (empty($lang_strings)) return true;
		
		// Открываем языковый файл.
		$tmp = explode('/vw/',$view_file);
		//print_r($tmp);
		
		// Если нет папки lang - создаем
		if (!file_exists( $tmp[0].'/lang'))
			mkdir($tmp[0].'/lang', 0777, true);
			
		// Формируем имя файла
		$lang_file = $tmp[0].'/lang/'.$lang.'.php';
		
		// Если файл существует - получаем последний номер языковой переменной
		if (file_exists($lang_file)) {
			// $old_lang_file = file_get_contents($lang_file);
			// preg_match_all('#lang\[(\d+)\]#ui', $old_lang_file, $out, PREG_PATTERN_ORDER);
			// print_r($out);
			// $i = max($out[1])+1;
			// if ($i<=0 or empty($i))
				// $i =1;
				
			// Внедряем массив из языкового файла
			include($lang_file);

			// Выбираем все слова из языкового файла
			// Убираем дубликаты
		}
		if(empty($_)) 
			$_ = array();
		
		foreach($lang_strings as $lang_string) {
				$_[] = $lang_string;
		}
		
		$_ = array_unique($_);
		
		// Создаем копию массива со спаданием по длине строк, чтобы избежать некорректных замен.
		
		$view_new_text = $view_text;
		
		//Обновляем содержимое языкового файла
		foreach($_ as $key => $lang_string){
			$lang_file_text .= '$_[\''.$key."'] = '".str_replace("'","\'",$lang_string)."';\n";
			$i++;
		}
		//Обновляем содержимое вида
		foreach($lang_strings_sorted as $lang_string) {
			$view_new_text = str_replace($lang_string, '<?php echo $_[\''.array_search($lang_string, $_).'\'];?>', $view_new_text);	
		}
		
		// Записываем языковый файл в выбранном языке в режиме дописывания
		file_put_contents($lang_file, '<?php '."\n".$lang_file_text."?>");
		
		// Находим имя для бекапа старого вида
		$i = 0;
		while(file_exists($view_file.'_old'.$i)) {
			$i++;
		}
		// Сохранить копию старого вида
		file_put_contents($view_file.'_old'.$i, $view_text);
		
		// Перезаписать старый вид новым файлом
		file_put_contents($view_file, $view_new_text);
		/**/
		
		
	}
	
	// Создает языковые файлы из Видов
	public function e_GetLangFromComponentViews($component, $lang = NULL){
		$cmp_folder = $this->getComponentFileName($component);
		if (!file_exists($cmp_folder.'/vw')) return;
		$file_list = scandir($cmp_folder.'/vw');
		foreach ($file_list as $file_name) {
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			echo $file_name;
			if ($ext == 'php')
				$this->e_GetLangFromView($component, pathinfo($file_name, PATHINFO_FILENAME), $lang);
		}
	}

}
// Функция сравнения срок по длине для сортировки по спаданию длины
function lensort_desc($a,$b){
	$la = strlen( $a); $lb = strlen( $b);
	if( $la == $lb) {
		return strcmp( $a, $b);
	}
	return $lb - $la;
}
// Функция сравнения срок по длине для сортировки по возрастанию длины
function lensort_asc($a,$b){
	$la = strlen( $a); $lb = strlen( $b);
	if( $la == $lb) {
		return strcmp( $a, $b);
	}
	return  $la - $lb;
}


?>