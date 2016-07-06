<?php
namespace engine\components;

class Request {

    /**
     * Получение защищенного $_GET
     * @param $param
     *
     * @return array|string
     */
    public static function get($param)
    {
	    if(isset($_GET[$param])){
	        if(is_array($_GET[$param])){
	            $array = [];
	            foreach($_GET[$param] as $key=>$val){
	                $array[$key] = Html::encode(trim($val));
	            }
	            return $array;
	        } else{
	            $result = Html::encode(trim($_GET[$param]));
	            return isset( $result ) ? $result : null;
	        }
	    }
	    return null;
    }


    /**
     * Получение защищенного $_REQUEST
     * @param $param
     *
     * @return array|string
     */
    public static function request($param)
    {
	    if(isset($_REQUEST[$param])){
	        if(is_array($_REQUEST[$param])){
	            $array = [];
	            foreach($_REQUEST[$param] as $key=>$val){
	                $array[$key] = Html::encode(trim($val));
	            }
	            return $array;
	        } else{
	            $result = Html::encode(trim($_REQUEST[$param]));
	            return isset( $result ) ? $result : null;
	        }
	    }
	    return null;
    }

    /**
     * Получение $_POST
     *
     * @param $param
     *
     * @return array|string
     */
    public static function post($param)
    {
	    if(isset($_POST[$param])){
	        if(is_array($_POST[$param])){
	            $array = [];
	            foreach($_GET[$param] as $key=>$val){
	                $array[$key] = trim($val);
	            }
	            return $array;
	        } else{
	            if(is_bool($_POST[$param])){
	                return $_POST[$param];
	            } else {
	                return trim($_POST[$param]);
	            }
	        }
	    }
	    return null;
    }

    /**
     * @return string true or false
     */
    public static function is_post()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return string true or false
     */
    public static function is_get()
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function is_ajax(){

        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
            !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
            strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest"){

            return true;
        }

        return false;
    }

}