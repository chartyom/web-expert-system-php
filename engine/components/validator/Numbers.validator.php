<?php
namespace engine\components\validator;

class NumbersValidator{

    private static $pattern = '/^[-]?[1-9]{1}[0-9]*$/i';

    public static function numbers($data){

            if (preg_match(self::$pattern, $data)) {
                return true;
            }
            return false;
    }
}