<?php
namespace engine\components;

class Random {

    private static $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    private static $numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /*рандомный вывод включает в себя a-zA-Z0-1 Random::varied($count)*/
    public static function varied($count = 10)
    {
        $output="";
        for ($i = 0; $i < $count; $i++) {
            switch ( rand(0,2) ) {
                case 0:
                    $output = $output.strtoupper(self::$letters[rand(0,25)]);
                    break;
                case 1:
                    $output = $output.self::$letters[rand(0,25)];
                    break;
                case 2:
                    $output = $output.self::$numbers[rand(0,9)];
                    break;
            }
        }
        return $output;
    }

    public static function numbers($count = 10,$with = 0,$to = 9)
    {
        $output="";
        for ($i = 0; $i < $count; $i++) {
            $output = $output.self::$numbers[rand($with,$to)];
        }
        return $output;
    }

}