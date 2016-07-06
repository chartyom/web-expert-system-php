<?php
namespace engine\components;

use engine\components\validator;
use engine\settings\Settings;

class Response {

    /**
     * Редирект
     * Предназначен для автоматического перенаправления на внутреннюю страницу сайта
     * Переход по внешним ссылкам по умолчанию закрыт
     * В файле с настройками (Settings) можно добавить домены, перенаправление на которые не будет заблокировано
     * Settings::ACCESS_DOMAIN позволяет добавить домены в исключение
     *
     * @param       $url
     */
    public static function redirect($url)
    {
        if(Validator::url($url)){
            $url_root_domain = Tools::getRootDomain($url);
            //Сравнение нового домена с домашним доменом
            if($url_root_domain == Url::root_domain()){
                $redir_page = $url;
            } else {
                $accessDomain = Settings::ACCESS_DOMAIN;
                $redir_page = Url::main().'?notice=redirect_impossible';
                //Сравнение нового домена с разрешенными доменами в файле настроек
                if(is_array($accessDomain) && !empty($accessDomain)) {
                    foreach($accessDomain as $domain){
                        //В результате успешного нахождения выходим из цикла
                        if($url_root_domain == $domain){
                            $redir_page = $url;
                            break;
                        }
                    }
                }
            }
        } else {
            $redir_page = Url::home().$url;
        }
        header("Location: ".$redir_page);
        exit();
    }

    /**
     * @param $value
     *
     * @return string
     */
    public static function json($value = [])
    {
        if(is_array($value)){
            header('Content-type: application/json');
            echo json_encode($value);
            exit;
        }
        echo $value;
        exit;
    }


}