<?php
/**
 * Содержит класс UnregIPAuth
 * @package IC-CMS
 * @subpackage User
 */

/**
 * Класс UnregIPAuth отвечает аутентификацию незарегестрированного пользователя с помощью IP
 *
 * @requirement {@link User}
 * @requirement {@link Auth}
 * @requirement {@link DBProc}
 * @modified v1.0 от 12 января 2012 года
 * @version 1.0
 * @author Иван Найдёнов
 * @package IC-CMS
 * @subpackage User
 */
class UnregIPAuth extends User implements IAuth
{

	/**
	 * Инициализатор.
	 * - Переопределяет БД префикс
	 */
	protected function _init()
	{
		$this->dbprefix = 'ipath';
	}

	function serializeToCookies()
	{
		$this->addEvent(WARNING, "Данный тип аутентификации не поддерживает сериализацию в куки");
	}

	function deserializeFromCookies()
	{
		$this->addEvent(WARNING, "Данный тип аутентификации не поддерживает сериализацию в куки");
	}

	function showLoginForm()
	{
		$this->addEvent(WARNING, "Данный тип аутентификации не поддерживает явный логин с помощью формы");
	}

	function e_loginProcess()
	{
		// ? как объединить незарегистрированных пользователей и зарегистрированных.
		//если не объединять то есть опасность неправельного определения запрета повторных действий и т.п.
		$this->DBProc->StartBuffer(array('extC' => true, 'extR' => true));
		$this->DBProc->get_user_by_ip(getenv("REMOTE_ADDR"), '@id_user');
		$this->DBProc->Get('@id_user');
		$this->id = (int) $this->DBProc->CallBuffer();
	}

}
