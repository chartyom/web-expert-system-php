<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 25.11.2015
 * Time: 2:15
 */

namespace engine\components;

use engine\components\validator\EmailValidator;
use engine\components\validator\NumbersValidator;
use engine\components\validator\UrlValidator;

class Validator {
    public static function url($url){
        require_once(COMPONENTS_PATH . "/validator/Url.validator.php");
        return UrlValidator::url($url);
    }

    public static function email($email){
        require_once(COMPONENTS_PATH . "/validator/Email.validator.php");
        return EmailValidator::email($email);
    }

    /**
     * Проверяет является ли значение целочисленным числом
     * Example
     * -150 True
     * 105 True
     * se323 False
     * 515.2 False
     * @param $numeric
     *
     * @return bool
     */
    public static function numbers($numeric){
        require_once(COMPONENTS_PATH . "/validator/Numbers.validator.php");
        return NumbersValidator::numbers($numeric);
    }

}