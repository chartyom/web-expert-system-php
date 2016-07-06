<?php

/**
* Глобальные константы
*/
require(__DIR__ . '/Defined.php');

class Engine
{
	private static $classes;
	
    function __construct()
    {
		self::$classes = Engine::createObjectArray(require(ENGINE_PATH . '/Autoload.php'));
    }

    /**
     * Запуск приложения.
     */
    public function run()
    {
        self::autoload(self::$classes);
    }

    /**
     * Создаёт объекты из массива данных.
     * @param array $array
     * @return Object array
     */
    public static function createObjectArray($array){
        if(is_array($array))
        {
            foreach ($array AS $key=>$value)
            {
                if(is_array($value))
                {
                    $array[$key] = self::createObjectArray($value);
                }
                else
                {
                    $array[$key] = $value;
                }
            }
        }
        return (object) $array;
    }

    /**
     * Загрузка классов.
     * @param $data
     * @return true || false
     */
    public static function autoload($data)
    {
        //for each directory
        if(is_array($data) || is_object($data)){
            foreach ($data as $directory)
            {
                require($directory);
            }
            return true;
        } else {
            if (!empty($data)) {
                require($data);
                return true;
            } else {
                return false;
            }
        }
    }

}


if(ENGINE_DEBUG==false) {
	ini_set('display_errors','off');
} else {
	// определеяем уровень протоколирования ошибок
	error_reporting(E_ALL | E_STRICT);
	// определяем режим вывода ошибок
	ini_set('display_errors','on');
}

(new Engine())->run();


?>