<?php
namespace engine\components;

class Url {

    private static $thisURL;
    
    private static $homeURL;

    private static $authURL;

    private static $devURL;

    private static $mainURL;

    private static $backURL;

    private static $root_domainURL;

    /**
     * Возвращает ссылку на текущую страницу сайта.
     * @author Artem
     * @return string http(s)://example.site/page or http(s)://subdomain.example.site/page
     */
    public static function this()
    {
        if (!self::$thisURL){
            self::$thisURL = self::home().$_SERVER['REQUEST_URI'];
        }
        return self::$thisURL;
    }

    /**
     * Возвращает ссылку на сайт.
     * @author Artem
     * @return string http(s)://example.site or http(s)://subdomain.example.site
     */
    public static function home()
    {
        if (!self::$homeURL){
            self::$homeURL = self::getProtocol().'://'.$_SERVER['SERVER_NAME'];
        }
        return self::$homeURL;
    }

    /**
     * Возвращает ссылку на сайт авторизации.
     * @author Artem
     * @return string -http://auth.example.site or -http(s)://auth.example.site http(s)://example.site/auth
     */
    public static function auth()
    {
        if (!self::$authURL){
            self::$authURL = self::getProtocol().'://'.self::root_domain().'/auth';
        }
        return self::$authURL;
    }

    /**
     * Возвращает ссылку на сайт разработки.
     * @author Artem
     * @return string -http://dev.example.site or -http(s)://dev.example.site  http(s)://example.site
     */
    public static function dev()
    {
        if (!self::$devURL){
            self::$devURL = self::getProtocol().'://'.self::root_domain();
        }
        return self::$devURL;
    }

    /**
     * Возвращает ссылку на главную страницу главного домена.
     * @author Artem
     * @return string http(s)://example.site
     */
    public static function main()
    {
        if (!self::$mainURL){
            self::$mainURL = self::getProtocol().'://'.self::root_domain();
        }
        return self::$mainURL;
    }

    /**
     * Возвращает ссылку на предыдущую страницу.
     * Проверяет соответствие доменов и если текущая страница и предыдущая страница равны, то отправляет на главную,
     * либо на указанную в параметре $alternative
     *
     * Важно!
     * Предыдущая страница получается из небезопасного параметра $_SERVER['HTTP_REFERER'],
     * который может отсутствовать в браузере или вести на другой ресурс.
     * Для этого и нужна альтернативная ссылка,
     * что бы в случае отсутствия у пользователя $_SERVER['HTTP_REFERER'],
     * его можно было вручную перевести на нужную страницу.
     *
     * Примеры $alternative:
     * $alternative = '/agreement'
     * $alternative = 'http:/example.ru/agreement'
     * $alternative = Url::auth().'?param=authorized'
     *
     * @author Artem
     *
     * @param $alternative
     *
     * @return string http(s)://example.site/page=article&id=777
     */
    public static function back($alternative = 0)
    {
        if (!self::$backURL){
            $referer = $_SERVER['HTTP_REFERER'];
            $referer_root_domain = Tools::getRootDomain($referer);
            if(Url::root_domain()==$referer_root_domain){
                if(self::home().$_SERVER['REQUEST_URI']!=$referer){
                    self::$backURL = $referer;
                } else {
                    /*Если не указана альтеративная ссылка, то выводит ссылку на главную страницу*/
                    if(!empty($alternative)){
                        /*Проверка на корректность альтернативной ссылки*/
                        if(Validator::url($alternative)){
                            self::$backURL = $alternative;
                        } else {
                            self::$backURL = self::home().$alternative;
                        }
                    } else {
                        self::$backURL = self::home();
                    }
                }
            } else {
                self::$backURL = self::home();
            }
        }
        return self::$backURL;
    }

    /**
     * Возвращает имя главного домена.
     * @author Artem
     * @return string example.site
     */
    public static function root_domain(){
        if (!self::$root_domainURL){
            $arr = explode('.',$_SERVER['SERVER_NAME']);
            $countDomain = count($arr);
            self::$root_domainURL = $arr[$countDomain-2].'.'.$arr[$countDomain-1];
        }
        return self::$root_domainURL;
    }
    /**
     * Возвращает протокол сайта.
     * @author Artem
     * @return string ( http | https )
     */
    private static function getProtocol()
    {
        $protocol='http';
        if (isset($_SERVER['HTTPS']))
            if (strtoupper($_SERVER['HTTPS'])=='ON')
                $protocol='https';
        return $protocol;
    }

	/**
     * В планах включение блокировщика нежелательных ссылок
     *
     * @param string $url
     *
     * @return string
     */
    public static function to($url = '')
    {
        return $url;
    }


}
