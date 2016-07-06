<?php
namespace engine\components;

class Cookie {


    /**
     * Получение защищенного $_COOKIE
     * @param $name
     *
     * @return array|string
     */
    public static function get($name)
    {
	    if(!empty($_COOKIE[$name])){
            return mb_strtolower(trim(strip_tags($_COOKIE[$name])));
        } else {
		    return null;
	    }
    }

    /**
     * Проверка $_COOKIE на существование
     * @param $param
     *
     * @return array|string
     */
    public static function is_cookie($param)
    {
        if(isset($_COOKIE[$param])){
            return !empty($_COOKIE[$param]) ? true : false;
        } else{
            return false;
        }
    }

    /**
     * Задаёт новое значение cookie
     *
     * @param null $name
     * @param null $value
     * @param null $timelife
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httponly
     */
    public static function set($name = null,$value = null,$timelife = null,$path = null,$domain=null,$secure = null,$httponly=null)
    {
        setcookie($name,$value,$timelife,$path,$domain,$secure,$httponly);
    }

    /**
     * Очищает cookie
     *
     * @param      $name
     * @param null $domain
     */
    public static function clear($name = null,$domain = null)
    {
        self::set($name, '', 0, '/', $domain);
    }

}