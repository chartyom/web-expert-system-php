<?php
namespace engine\components;

/*
 * 1. Позволяет хранить, выводить, обновлять данные формата JSON в пользовательской сессии
 *
*/
class Session {


	/**
	 * Добавлет запись в сессию
	 * Example
	 * $_SESSION['json_data'] => { 1:'first element', 2:'second element', 'id3':'three element'}
	 * @param string $session_name
	 * @param        $array
	 *
	 */
	public static function setJsonData($session_name='json_data',$array = [1=>'first element',2=>'second element','id3'=>'three element'])
	{
		$sessionArray = [];
		if(isset($_SESSION[$session_name])){
			$sessionArray = Json::decode($_SESSION[$session_name]);
		}
		if(is_array($array)){
			foreach($array AS $key=>$val){
				$sessionArray[$key] = $val;
			}
		}
		$_SESSION[$session_name] = Json::encode($sessionArray);
	}


	/**
	 * Выводит массив данных из идентификатора $session_name
	 * Example
	 * getJsonData($session_name='json_data') => [ 1=>'first element', 2=>'second element', 'id3'=>'three element']
	 * @param string $session_name
	 *
	 * @return array
	 */
	public static function getJsonData($session_name='json_data')
	{
		if(isset($_SESSION[$session_name])) {
			return Json::decode($_SESSION[$session_name]);
		}
		return null;
	}

	/**
	 * Удаление данных из сессии
	 *
	 * @param string $session_name
	 *
	 */
	public static function removeJsonData($session_name='json_data')
	{
		if(isset($_SESSION[$session_name])) {
			unset($_SESSION[$session_name]);
		}
	}

	/**
	 * Вывод данных из сессии по идентификатору
	 * Example
	 * getJsonDataById($session_name='json_data',$id = 'id3') => 'three element'
	 *
	 * @param string $session_name
	 * @param        $id
	 *
	 * @return mixed
	 */
	public static function getJsonDataById($session_name='json_data',$id = 'id3')
	{
		if(isset($_SESSION[$session_name])) {
			$sessionArray = Json::decode($_SESSION[$session_name]);
			if(is_array($sessionArray) && !empty($sessionArray[$id])) {
				return $sessionArray[$id];
			}
		}
		return null;
	}

	/**
	 * Удаление данных из сессии по идентификатору
	 * Example
	 * 'three element'
	 *
	 * @param string $session_name
	 * @param        $id
	 *
	 */
	public static function removeJsonDataById($session_name='json_data',$id = 'id3')
	{
		if(isset($_SESSION[$session_name])) {
			$sessionArray = Json::decode($_SESSION[$session_name]);
			if(is_array($sessionArray) && !empty($sessionArray[$id])) {
				unset($sessionArray[$id]);
				$_SESSION[$session_name] = Json::encode($sessionArray);
			}
		}
	}


}