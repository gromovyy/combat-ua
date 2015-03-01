<?php
/**
 * Содержит класс Session
 * @package IC-CMS
 * @subpackage User
 */

/**
 * Класс Session отвечает управление механизмом сессий, блокируя использование
 * суперглобального массива _SESSION
 *
 * Возможности:
 * - Обращение к классу сессии как к масиву
 * - Возможность создания сессионных полей классов
 * - Возможность указания модификатора доступа к сесионному полю (public,
 * protected, private)
 * - Блокировка использования суперглобального масива _SESSION для
 * ограничения доступа к сессионным полям
 * - Функция для встраивания в магический метод {@link Base::__get} для удобства
 * доступа к сессионным полям
 * - В случае отсутствия в областе видимости сесионного поля производится
 * доступ к глобальной сессионной переменной
 *
 * @requirement {@link Base}
 * @modified v1.0 от 12 января 2012 года
 * @version 1.0
 * @author Иван Найдёнов
 * @package IC-CMS
 * @subpackage Core
 */
class Session extends Base implements ArrayAccess
{

	/**
	 * Масив для защищенный масив для хранения данных сесии в процесе работы скрипта
	 * @var array(string=>mixed) данные сессии
	 */
	private $data = array();

	/**
	 * Инициализатор открывает сессию, получает данные, сохраняет их в закрытую
	 * переменную и уничтажает сессию.
	 */
	protected function _init()
	{
		// проверка на session injection
		if (isset($_REQUEST['_SESSION'])) {
			$this->addEvent(SEQURITY, "ВНИМАНИЕ!! ПОПЫТКА ВЗЛОМА! Попытка подмены суперглобального масива _SESSION" .
					"пользовательской переменной с помощю register_globals");
			if (ini_get('register_globals') == 'on')
				die();
		}
		session_start(); // стартуем сессию
		//print_r($_SESSION);
		foreach ($_SESSION as $key => $value) {
			$this->data[$key] = $value; //сохраняем
			unset($_SESSION[$key]); //и очещаем все данные
		}
		
	}

	/**
	 * Деструктор создает сессию и записывает данные из закрытой переменной в сессию
	 */
	public function __destruct()
	{
		//возвращаем данные назад в сессию
		foreach ($this->data as $key => $value)
			$_SESSION[$key] = $value;
	}

	/**
	 * Метод для встраивания в {@link Base::__get}.
	 *
	 * Служит для организации доступа к сессионым полям через оператор -> , но
	 * не к глобальным сессионым переменным
	 *
	 * @param string $key Имя сессионного поля
	 * @return mixed Возвращает значение сессионного поля или null если такого <i>поля</i> нет.
	 */
	public function &magicGetEmbedding($key)
	{
		$key = $this->getRealKey($key, true); // формируем ключ
		if ($key !== false)
			return $this->data[$key];
	}

	/**
	 * Метод для встраивания в {@link Base::__set}.
	 *
	 * Служит для организации доступа к сессионым полям через оператор -> , но
	 * не к глобальным сессионым переменным
	 *
	 * @param string $key Имя сессионного поля
	 * @param mixed $value Устанавливаемое значение сессионного <i>поля</i>
	 * @return void
	 */
	public function magicSetEmbedding($key_in, $value)
	{
		//echo "HHHHHHHHHHHHHEELLLO!".$key_in.$value;
		$key = $this->getRealKey($key_in, true); // формируем ключ
		if ($key !== false)
			$this->data[$key] = $value;
		else
			$this->addEvent(WARNING, "Значение сессионного поля $key_in не было установлено, так как такого поля не сущевствует");
	}

	/**
	 * Функция для получения ключа по которому хранятся запрашиваемые данные
	 *
	 * Ключ формируется в виде $cmpName::$publicKey, где $cmpName - имя
	 * компонента из которого происходит доступ (определяеться динамически), а
	 * $publicKey - входной параметр
	 *
	 * @param string $publicKey
	 * @param boolean $isFromMagic
	 * @return string|boolean
	 */
	private function getRealKey($publicKey, $isFromMagic=false)
	{
		$trace = debug_backtrace(); // Получаем стек вызовов
		$cmpCaller = $trace[2 + $isFromMagic]['class']; // получаем имя компонента запросившего сессионное поле
		if ($cmpCaller) {
			if ($isFromMagic) // Если идет запрос к сессионому полю
				$cmpNeedle = $trace[2]['object']->component; // Устанавливам имя запрашиваемого компонента
			else { // если идет вызов через интерфейс $this->Session[]
				if (strstr($publicKey, "::") === false) // если нет расширения области видимости
					$cmpNeedle = $cmpCaller; // требуеться вызывающий класс
				else {// иначе разделяем ключ получая из него ктребуемый класс и имя поля
					$pars = explode("::", $publicKey);
					$cmpNeedle = $pars[0];
					if ($cmpNeedle === "") // если облясть видимости расширяеться до глобальной - возвращаем ключ
						return $publicKey;
					$publicKey = $pars[1];
				}
			}
			$refl = new ReflectionClass($cmpNeedle); // Создаем новую рефлексию требуемого класса
			$prop_name = "_sess_$publicKey"; // формируем имя декларирующего поля
			if ($refl->hasProperty($prop_name)) { // если есть такое поле
				$prop = $refl->getProperty($prop_name); // получаем рефлексию этого поля
				$mods = $prop->getModifiers(); // получаем модификаторы этого поля
				if (($mods & 1) === 1 && ( // если декларирующее поля static и:
						$cmpCaller === $cmpNeedle || // вызывающий класс равен требуемому
						$mods >> 8 & 1 || // или модификатор доступа public
						// или модификатор доступа protected и вызывающий класс унаследован от тербуемого
						$mods >> 9 & 1 && is_subclass_of($cmpCaller, $cmpNeedle)))
					return $prop->class . '::' . $publicKey; // возращаем ключ требуемого класса
			}
			else {
				$refl = new ReflectionClass($cmpCaller);
				if ($refl->hasProperty($prop_name))
					return $cmpCaller . '::' . $publicKey;
			}
		}
		if ($isFromMagic) // если вызов через интерфейс сессионного поля не возвращаем ключа переменной
			return false;
		else
			return '::' . $publicKey; // возвращаем ключ глобальной сессионой переменной
	}

	/**
	 * Имплементация метода
	 * {@link http://php.net/search.php?show=quickref&pattern=ArrayAccess.offsetExists
	 * ArrayAccess::offsetExists} проверки сущствования ключа
	 *
	 * Вызываеться при использовании функций
	 * {@link http://php.net/search.php?show=quickref&pattern=isset isset} или
	 * {@link http://php.net/search.php?show=quickref&pattern=empty empty} но не
	 * {@link http://php.net/search.php?show=quickref&pattern=array_key_exists array_key_exists}
	 *
	 * @param string $key Имя сессионного поля или глобальной сессионной переменной
	 * @return boolean Возвращает true если существует сессионное поле или глобальная сессионная переменная $key
	 */
	public function offsetExists($key)
	{
		return isset($this->data[$this->getRealKey($key)]);
	}

	/**
	 * Имплементация метода
	 * {@link http://php.net/search.php?show=quickref&pattern=ArrayAccess.offsetGet
	 * ArrayAccess::offsetGet} для получения елемента с помощью оператора индексирования []
	 *
	 * @param string $key Имя сессионного поля или глобальной сессионной переменной
	 * @return mixed Возвращает значение сессионного поля или null если такого ключа нет.
	 */
	public function offsetGet($key)
	{
		return $this->data[$this->getRealKey($key)];
	}

	/**
	 * Имплементация метода
	 * {@link http://php.net/search.php?show=quickref&pattern=ArrayAccess.offsetSet
	 * ArrayAccess::offsetSet} для установки значения елемента с помощью оператора индексирования []
	 *
	 * @param string $key Имя сессионного поля или глобальной сессионной переменной
	 * @param mixed $value Устанавливаемое значение сессионного поля или глобальной сессионной переменной
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if ($key === null)
			$this->data[] = $value;
		else
			$this->data[$this->getRealKey($key)] = $value;
	}

	/**
	 * Имплементация метода
	 * {@link http://php.net/search.php?show=quickref&pattern=ArrayAccess.offsetUnset
	 * ArrayAccess::offsetSet} для удаления елемента с помощью функции {@link http://php.net/search.php?show=quickref&pattern=unset unset}
	 *
	 * @param string $key Имя сессионного поля или глобальной сессионной переменной
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->data[$this->getRealKey($key)]);
	}

}
