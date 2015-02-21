<?php
/**
 * Содержит класс Viewer
 * @package IC-CMS
 * @subpackage Core
 */

/**
 * Класс Viewer обеспечивает единый механизм работы с видами
 *
 * - загрузка видов,
 * - загрузка библиотек css,
 * - загрузка библиотек js.
 * Реализует функции для работы с механизмами
 * каналов связи клиент-сервер.
 *
 * @author Александр Громовой
 * @version 1.0
 * @modified v1.0 от 02 августа 2011 года
 * @package IC-CMS
 * @subpackage Core
 */
class Viewer extends Base
{

	protected $position = array();
	protected static $js_libs = array();
	protected static $css_libs = array();
	protected static $keywords = array();
	public static $pageTitle = '';
	public static $layout = 'defaultLayout';
	public $role_id = "";  // Переменная, хранящая роль пользователя 
	protected static $ajaxResult = null;
	protected static $jsData = array();
	protected static $theme = "default";
	protected static $_sess_theme = "default";

	
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Проверяет сущевствование вида
	 *
	 * @param string $viewName Имя вида
	 * @param string $component Имя компонента для поторого ведеться проверка
	 * @return boolean результат проверки
	 */
	function checkViewExist($viewName, $component = NULL, $templateName = "default")
	{
		if ($component)
			$cmp = $component;
		else
			$cmp = $this->component;
		return  file_exists("template/".self::$theme."/".$cmp."_$viewName.php") ||
				file_exists("vw/".self::$theme."/$viewName.php") ||
				file_exists("cmp/$cmp/vw/$viewName.php") ||
				file_exists("cmp_system/$cmp/vw/$viewName.php") ||
				file_exists("cmp/$cmp/test/$viewName.php") ||
				file_exists("vw/$viewName.php");
	}

	/**
	 * По имени вида подключает файл, содержащий этот вид по имени текущего
	 * компонента.
	 *
	 * Сначала происходит поиск и загрузка вида пользователя, находящегося в
	 * папке vw корневой папки сайта.
	 *
	 * Если пользовательский вид не найден, тогда происходит поиск и загрузка
	 * вида из папки vw вызывающего компонента.
	 *
	 * Если и там файл не найден - тогда формируется событие об ошибке.
	 *
	 * Перед загрузкой вида входной массив с данными преобразоуется в список
	 * переменных имя = значение
	 *
	 * @param string $viewName имя вида
	 * @param array $data ассоциативный массив вида переменная = значение
	 * @param type $component Компонент для которого загружаеться вид
	 */
	function loadView($viewName, $data = NULL, $component = NULL, $bufferised=false)
	{
		try {
			if ($component)
				$cmp = $component;
			else
				$cmp = $this->component;
			// Проверка прав доступа пользователя к виду
			//if (!$this->Rule->checkView($cmp, $viewName)) return;
			// Преобразовываем входной ассоциативный массив в список переменных имя = значение
			if (!is_null($data))
				extract($data);
			if (self::$ajaxViewToUpdate) {
				$js_class .= 'js-ajaxscroll';
				$data_attr .= $this->ajax_get_clean();
			}
			// Форсмируем иментификатор вида
			$viewId = "{$cmp}_$viewName" . (isset($data['id']) ? '_' . $data['id'] : '');

			// Загружaем вид
			
			ob_start();
			
			// Получаем имя файла вида и файла языка
			$view_file = $this->getViewFileName($viewName, $cmp);
			$lang_file =  $this->getLangFileName($this->Lang->getLang(), $cmp);
			if (!empty($lang_file))
				include($lang_file);
				
			if (!empty($view_file))
				include($view_file);
			else
				$this->addEvent(ERROR, "Файл вида $viewName компонента $cmp не найден","Текущая тема: ".self::$theme."");

			$text = ob_get_clean();
			//echo $text;
			// Добавляем идентефикатор вида
			$viewId = "{$cmp}_$viewName" . (isset($data['id']) ? '_' . $data['id'] : '');
			$text = preg_replace('@^(.*?)<(\w+)(([^>]*?)(\s+class=[' . "'" . '"](.*?)[' . "'" . '"])(.*?)|(.*?))>@is', 
				                   '\\1<\\2 class="vw-file '.$js_class.' \\6" id="' . $viewId . '" \\4\\7\\8 '.$data_attr.'>', 
				                   $text);

			// выводим или  возвращаем
			if ($bufferised)
				return $text;
			else
				echo $text;
		}
		catch (ViewExit $e) { // Безопасный выход из вида
		}
	}
	
	/**
	* Функция возвращает имя файла вида заданного компонента. Если компонент не задан возвращается вид текущего компонента
	*/
	function getViewFileName($viewName, $cmp = NULL) {
		if (empty($cmp)) $cmp = $this->component;
		
		if (file_exists("template/".self::$theme."/$cmp/vw/$viewName.php"))
					$view_file  =  "template/".self::$theme."/$cmp/vw/$viewName.php";
		else if (file_exists("cmp_system/$cmp/vw/$viewName.php")) {
					// copy ("cmp_system/$cmp/vw/$viewName.php", "template/".self::$theme."/".$cmp."_$viewName.php");
		            // include("template/".self::$theme."/".$cmp."_$viewName.php");
					$view_file = "cmp_system/$cmp/vw/$viewName.php";
			}
		else if (file_exists("cmp/$cmp/vw/$viewName.php"))  {
					// copy ("cmp/$cmp/vw/$viewName.php", "template/".self::$theme."/".$cmp."_$viewName.php");
					// include("template/".self::$theme."/".$cmp."_$viewName.php");
					$view_file = "cmp/$cmp/vw/$viewName.php";
		}
		else if (file_exists("cmp/$cmp/test/$viewName.php"))
		            $view_file = "cmp/$cmp/test/$viewName.php";
		else 
			$view_file = NULL;
		return $view_file;
	}
	
	/**
	* Функция возвращает имя языкового файла вида заданного компонента. Если компонент не задан возвращается вид текущего компонента
	*/
	function getLangFileName($lang, $cmp = NULL) {
		if (empty($cmp)) $cmp = $this->component;
		
		if (file_exists("template/".self::$theme."/$cmp/lang/$lang.php"))
					$view_file  =  "template/".self::$theme."/$cmp/lang/$lang.php";
		else if (file_exists("cmp_system/$cmp/lang/$lang.php")) {
					$lang_file = "cmp_system/$cmp/lang/$lang.php";
			}
		else if (file_exists("cmp/$cmp/vw/$lang.php"))  {
					$lang_file = "cmp/$cmp/lang/$lang.php";
		}
		else 
			$lang_file = NULL;
		return $lang_file;
	}
	
	/**
	* Функция возвращает имя файла вида заданного компонента. Если компонент не задан возвращается вид текущего компонента
	*/
	function getComponentFileName($cmp = NULL){
		if (empty($cmp)) $cmp = $this->component;
		if (file_exists("cmp_system/$cmp")) {
					$component_file = "cmp_system/$cmp";
			}
		else if (file_exists("cmp/$cmp"))  {
					$component_file = "cmp/$cmp";
		}
		else 
			$component_file = NULL;
		return $component_file;
	}
	
	/**
	 * Загрузка позиции в определенный вид
	 */
	function loadPosition($positionName, $viewName) {
		if (isset($this->position[$viewName][$positionName]))
			echo $this->position[$viewName][$positionName];
		else
			$this->addEvent(NOTICE, "Попытка выгрузить незаданную позицию $positionName вида $viewName компонента {$this->component}","Текущая тема: ".self::$theme."");
	}
	
	/**
	 * Сохранение содержимого позиции в массив 
	 */
	function setPosition($positionName, $viewName, $content) {
//		echo "Установка позиции $positionName, вида $viewName и содержимого $content";
		$this->position[$viewName][$positionName] .= $content;
	}
	

	function updateView($viewName, $data=null, $component=null)
	{
		self::$ajaxResult['updatedViews'][] = $this->loadView($viewName, $data, $component, true);
	}




	/**
	 * Функция для подключения файлов Javascript на страничке, где используется текущий вид.
	 * - Сначала происходит поиск и загрузка файла, находящегося в папке lib/js корневой папки сайта.
	 * - Если файл не найден, тогда происходит поиск и загрузка  файла из папки vw/js корневой папки сайта.
	 * - Если файл не найден, тогда происходит поиск и загрузка  файла из папки js вызывающего компонента.
	 * - Если и там файл не найден - тогда формируется событие об ошибке.
	 * @param type $file Имя файла. Неоходимо передавать без пути к файлу.
	 * @return boolean
	 */
	public function includeJS($file)
	{
		if ($component)
				$cmp = $component;
			else
				$cmp = $this->component;
				
		//Ищем файлы в соответствующих папках
		$lib_name = "";
		if (strstr($file, '//') !== false)
			$lib_name = $file;
		else if (file_exists("template/".self::$theme."/js/$cmp_$file"))
		         $lib_name = "template/".self::$theme."/js/$cmp_$file";
		else if (file_exists("cmp/{$this->component}/js/$file")) {
				 // copy ("cmp/{$this->component}/js/$file", "template/".self::$theme."/js/$cmp_$file");
				 // $lib_name = "template/".self::$theme."/js/$cmp_$file";
				 $lib_name = "cmp/{$this->component}/js/$file";
		}
		else if (file_exists("cmp_system/{$this->component}/js/$file")) {
				 // copy ("cmp_system/{$this->component}/js/$file", "template/".self::$theme."/js/$cmp_$file");
				 // $lib_name = "template/".self::$theme."/js/$cmp_$file";
				 $lib_name = "cmp_system/{$this->component}/js/$file";
		}
		else if (file_exists("lib/js/$file"))  {
				 // copy ("lib/js/$file", "template/".self::$theme."/js/$cmp_$file");
		         // $lib_name = "template/".self::$theme."/js/$cmp_$file";
				 $lib_name = "lib/js/$file";
		}
		else {
			$this->addEvent(ERROR, "Javascript файл $file компонента {$this->component} не найден");
			return FALSE;
		}

		// Если имя библиотеки не пустое и не повторяется, добавляем его в массив c именами библиотек
		if (($lib_name != "") and (!in_array($lib_name, self::$js_libs)))
			self::$js_libs[] = $lib_name;

		//Если подключение успешно - возвращаем TRUE
		return TRUE;

	}
	/**
	 * Функция для подключения файлов CSS на страничке, где используется текущий вид.
	 * - Сначала происходит поиск и загрузка файла, находящегося в папке lib/css корневой папки сайта.
	 * - Если файл не найден, тогда происходит поиск и загрузка  файла из папки vw/css корневой папки сайта.
	 * - Если файл не найден, тогда происходит поиск и загрузка  файла из папки css вызывающего компонента.
	 * - Если и там файл не найден - тогда формируется событие об ошибке.
	 * @param type $file Имя файла. Неоходимо передавать без пути к файлу.
	 * @return boolean
	 */
	public function includeCSS($file)
	{
	
		if ($component)
				$cmp = $component;
			else
				$cmp = $this->component;
				
	
		
		//Ищем файлы в соответствующих папках
		$lib_name = "";
//		echo "---------------------------------";
		if (strstr($file, '//') !== false)
			$lib_name = $file;
		else if (file_exists("template/".self::$theme."/css/$cmp_$file"))
		         $lib_name = "template/".self::$theme."/css/$cmp_$file";
		else if (file_exists("cmp/{$this->component}/css/$file")) {
				 // copy("cmp/{$this->component}/css/$file", "template/".self::$theme."/css/$cmp_$file");
		         // $lib_name = "template/".self::$theme."/css/$cmp_$file";	
				 $lib_name = "cmp/{$this->component}/css/$file";	
		}
		else if (file_exists("cmp_system/{$this->component}/css/$file")) {
				 // copy("cmp_system/{$this->component}/css/$file", "template/".self::$theme."/css/$cmp_$file");
		         // $lib_name = "template/".self::$theme."/css/$cmp_$file";
				 $lib_name = "cmp_system/{$this->component}/css/$file";	
		}
		else if (file_exists("lib/css/$file")) {
		         // copy("lib/css/$file", "template/".self::$theme."/css/$cmp_$file");
				 // $lib_name = "template/".self::$theme."/css/$cmp_$file";
				 $lib_name = "lib/css/$file";
		}
		else {
			$this->addEvent(ERROR, "CSS файл $file  компонента {$this->component} не найден");
			return FALSE;
		}

		// Если имя библиотеки не пустое и не повторяется, добавляем его в массив c именами библиотек
		if (($lib_name != "") and (!in_array($lib_name, self::$css_libs)))
			self::$css_libs[] = $lib_name;

		//Если подключение успешно - возвращаем TRUE
		return TRUE;
	}

	/**
	 * Функция для добавления ключевых слов к каждому виду
	 *
	 * Функция разбивает входные слова на отдельные составляющие и сохраняет их в массиве $keywords
	 * Например  $keyword_string = "дело, система, успех, счастье"
	 *
	 * @param string $keyword_string ключевые слова, например "дело, система, успех, счастье"
	 */
	public function addKeywords($keyword_string)
	{

		//Удаляем все пробельные символы из строки с ключевыми словами
		$keyword_string = preg_replace("!\s+!s", " ", $keyword_string);

		// Разделяем строку на слова с помощью функции explode и присоединяем новый масив к уже существующим значениям
		self::$keywords = array_merge(self::$keywords, explode(',', $keyword_string));
		// Фильтруем массив, чтобы каждое слово было уникальным
		self::$keywords = array_unique(self::$keywords);
	}

	/**
	 * Функция для подключения файлов Javascript в раздел <head> на страничке
	 * в виде 	<script type="text/javascript" src="FILE.JS"></script>
	 */
	public function loadJS()
	{
		foreach (self::$js_libs as $file)
			echo '<script type="text/javascript" src="' . $file . '"></script>' . "\n";  //.chr(10)
	}

	public function loadJsData(){
		$data=json_encode(self::$jsData);
		echo "<script>jsData=JSON.parse('$data')</script>";
	}

	/**
	 * Функция для подключения файлов CSS в раздел <head> на страничке
	 * в виде 	<link rel="stylesheet" type="text/css" href="FILE.CSS" />
	 */
	public function loadCSS()
	{
		foreach (self::$css_libs as $file)
			echo '<link rel="stylesheet" type="text/css" href="' . $file . '"/>' . "\n"; //.chr(10)
	}

	/**
	 * Функция для вывода ключевых слов в раздел <meta name и <meta http-equiv
	 */
	public function loadKeywords()
	{
		$keyword_string = implode(", ", self::$keywords);
		echo '<meta name="keywords" content="' . $keyword_string . '">."\n"'; //.chr(10)
		echo '<meta http-equiv="Keywords" content="' . $keyword_string . '">."\n"'; //.chr(10)
	}

	/**
	 * Функция проверяет в режиме просмотра, не пуста ли переменная.
	 * V - сокращение от View
	 */
	public function V($var) {
		if (($this->User->getMode() !="edit") and empty($var))
			return false;
		else 
			return true;
		
	}
		function cutText($text, $is_img=false) {
			if(strpos($text,"</p>"))
				$text = substr($text, 0, strpos($text,"</p>"));
			
			$text = preg_replace("|<.*>|iU","",$text);
			return $text;
//			$text = preg_replace("|<[\]?p.*>|i","",$text);
//			echo preg_replace ( "|<p>(.*)<|iu","\\1" , $text)."HELLO";
	//		print_r($matches);
/*			if ($is_img) 
				return (strlen($text)>200)?substr($text, 0, 200)."...":$text;
			else
				return (strlen($text)>300)?substr($text, 0, 300)."...":$text;
		*/	
		}
		
		function cutTitle($text, $is_img=false) {
			$text = preg_replace("|<.*>|i","",$text);
			if ($is_img) 
				return (strlen($text)>50)?substr($text, 0, 91)."...":$text;
			else
				return (strlen($text)>70)?substr($text, 0, 171)."...":$text;
			
		}
		
		// Функция для получения базовых видов тем.
		function combo_base_view() {
			if (file_exists("template"))
				{
				// Получение видов без тем.
				// $component_view = $this->Component->getFileList("vw");
				// Получение списка тем. 
				$themes_list = $this->Component->getDirList("template");
				} 
			else return array();
			
			$i = 0;
			/* if ($component_view){
				foreach($component_view as $key=>$value) {
					if ($value == 'README') {continue;}
					$result[$i]['id'] = $value;
					$result[$i]['v'] = $value;
					$i++;
				}
			} */
			
			if($themes_list){
				foreach($themes_list as $theme){
					$theme_view = $this->Component->getFileList('template/'.$theme);
					if($theme_view){
						foreach($theme_view as $key=>$value) {
							$result[$i]['id'] = $value;
							$result[$i]['v'] = '['.$theme.'] '.$value;
							$i++;
						}
					}
				}
			}
			return $result;
		}	
		
		function combo_theme_list(){
		$result = array();
		 $themes_list = $this->Component->getDirList("template");
		if ($themes_list){
			foreach($themes_list as $key=>$value) {
				$result[$key]['id'] = $value;
				$result[$key]['v'] = $value;
			}
		}
		return $result;
		}
		
		// Функция устанавливает текущую тему.
		public function setTheme($theme){
			if (empty($theme)) $theme = 'default';
			self::$theme = $theme;
			$this->Viewer->theme = $theme;
		}

		protected static $ajaxInfiniteLoad;
		protected static $ajaxCount;
		protected static $ajaxOffset;
		protected static $ajaxViewToUpdate;
		public function ajax($count = 2,$viewToUpdate='')
		{

			//Определяем обновляемый вид.
			if (empty($viewToUpdate)) {
				$trace = debug_backtrace();
				$viewToUpdate = $trace[1]['class'] . '/' . substr($trace[1]['function'], 2);
			}
			self::$ajaxViewToUpdate = $viewToUpdate;
			self::$ajaxInfiniteLoad = true;
			self::$ajaxCount        = $count + (int) $_POST['ajaxOffset'];;
			self::$ajaxOffset       = 0;

			return $this;
		}

		public function ajax_get_clean()
		{
			$str = '';
			if (self::$ajaxViewToUpdate) {
				$offset = self::$ajaxOffset + self::$ajaxCount;

				$str .= 'data-view="'.self::$ajaxViewToUpdate.'" ';
				$str .= 'data-offset="'.$offset.'" ';

			}
			self::$ajaxViewToUpdate = '';
			self::$ajaxCount        = null;
			self::$ajaxOffset       = null;
			return $str;
		}

}

class ViewExit extends Exception
{

}