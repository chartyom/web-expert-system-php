<?php
namespace engine\models;

use engine\settings\Database;
use \PDO;

class ModelTools
{
    /**
     * Настройка PDO
     *
     * @return array
     */
    public static function option()
    {
        return array(
            PDO:: MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	        PDO::MYSQL_ATTR_FOUND_ROWS   => TRUE,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
    }

    /**
     * Создание подключения к Базе Данных
     *
     * @return PDO
     */
    public static function bdconnect()
    {
        return new PDO(Database::DSN, Database::USERNAME, Database::PASSWORD, self::option());
    }

    /**
     * Сборщик полей из массива для запроса в базу данных
     *
     * Пример работы
     * ['user_id','first_name','group']
     *  `user_id`, `first_name`, `group`
     *
     * Namespace
     * [['u'=>'user_id'],['u'=>'first_name'],['u'=>'group'],['a'=>'first_user_id']]
     *  `u`.`user_id`, `u`.`first_name`, `u`.`group`, `a`.`first_user_id`
     *
     * Add "AS"
     * [['u'=>'user_id'],['u'=>'first_name'],['u'=>'group'],['a'=>'first_user_id','doctor_user_id']]
     *  `u`.`user_id`, `u`.`first_name`, `u`.`group`, `a`.`first_user_id` AS `doctor_user_id`
     *
     * @param array $fieldsArray
     *
     * @return string
     */
    public static function pickerFields($fieldsArray = []){
        $data = '';
        if(is_array($fieldsArray)){

            $count = count($fieldsArray) - 1;
            foreach($fieldsArray as $key=>$value){
                if(is_array($value)){
                    /*Присвоение message_user_id AS user_id */
                    foreach($value AS $prefix=>$childValue){
                        if(is_string($prefix)){
                            $data .= ' `'.$prefix.'`.`'.$childValue.'`';
                        }else{
                            if($prefix===1){
                                $data .= ' AS `'.$childValue.'`';
                            } else {
                                $data .= ' `'.$childValue.'`';
                            }
                        }
                    }

                } else {
                    $data .= ' `'.$value.'`';
                }

                if($key==$count){
                    $data .= ' ';
                } else{
                    $data .= ',';
                }
            }
        } else {
	        $data .= ' `'.$fieldsArray.'` ';
        }
        return $data;
    }

    /**
     * РАЗРАБОТКА ПРИОСТАНОВЛЕНА!!!!!!!!!!!!
     * Сборщик полей сортировки из массива для условий отбора запроса к базе данных
     *
     * Пример работы
     * ['group','1']
     *  `group`=1
     *
     * [[['u'=>'group',1],['u'=>'group',2]],['usp'=>'remove',0],['first_user_id',5]]
     *  (`u`.`group`=1 OR `u`.`group`=2) AND `usp`.`remove` = 0, `first_user_id` = 5
     *
     *
     * @param array $fieldsArray
     *
     * @return string
     */
    private static function where($fieldsArray = []){

        /**
         * определяет является ли значение числом или словом, в зависимости от
         * состояния значения ставятся или не ставятся кавычки
         * @param $value
         *
         * @return string
         */
        $detectInt = function($value){
            $data= '';
            if(is_int($value)){
                $data .= $value;
            } else {
                $data .= '"'.$value.'"';
            }
            return $data;
        };

        $data = '';
        /* [ ] */
        if(is_array($fieldsArray)){
            /* [[ ]] */
            if(is_array($fieldsArray[0])){
                $count = count($fieldsArray) - 1;
                foreach($fieldsArray as $key=>$value){
                    /* [[[ ]]] */
                    if(is_array($value)){
                        $data .= '(';
                        /*Присвоение message_user_id AS user_id */
                        foreach($value AS $prefix=>$childValue){
                            if(is_string($prefix)){
                                $data .= ' `'.$prefix[0].'`.`'.$childValue.'`=';
                            }else{
                                if($prefix===1){
                                    $data .= $detectInt($childValue).' ';
                                } else {
                                    $data .= ' `'.$childValue.'`=';
                                }
                            }
                        }
                        $data .= ')';
                    } else {
                        $data .= ' `'.$value.'`';
                    }

                    if($key==$count){
                        $data .= ' ';
                    } else{
                        $data .= ',';
                    }
                }
            } else {
                $data .= ' `'.$fieldsArray[0].'`='.$detectInt($fieldsArray[1]).' ';
            }
        }
        return $data;
    }

    /**
     * Вывод количества полей
     *
     * @param int $with
     * @param int $amount
     *
     * @return string
     */
    public static function limit($with=0,$amount=1){
        $data = ' LIMIT';
        if(!is_int($amount) || $amount<1){
            $amount = 1;
        }
	    if(!is_int($with) || $with<0){
		    $with = 0;
	    }
        if($with>0 && $amount>0){
            $data .= ' '.$with.', '.$amount.'';
        } else {
            $data .= ' '.$amount.'';
        }
        return $data;
    }

	/**
	 * Выводит сформированный для поиска набор слов
	 * Пример:
	 * , два три "№;%:?* десять
	 * Результат:
	 * (?:два.+три.+десять)|(?:десять.+два.+три)|(?:три.+десять.+два)
	 * @param $searchWords
	 *
	 * @return string
	 *
	 */
	public static function searchRegexp($searchWords){
		//очищает знаки препинания
		$searchWords = preg_replace('/[^\d\w ]+/iu', ' ', $searchWords);
		//очищает пробелы по краям текста
		$searchWords = trim($searchWords);
		//Замена любых пробелов на | (знак "или")
		$searchWords = preg_replace('/[ ]+/', ',', $searchWords);
		$searchWordsArray = explode(',',$searchWords);
		$searchPattern = '';
		if(!empty($searchWordsArray)){
			$shiftArray = [];
			$searchWordsArrayCount = count($searchWordsArray);
			for($i=0; $i < $searchWordsArrayCount; $i++){
				$searchPatternInner = '';
				for($h=0; $h < $searchWordsArrayCount; $h++){
					/*Генерация начального массива, который будет изменяться*/
					if($i===0){
						$shiftArray[] = $h;
					}
					$searchPatternInner .= ($h===0) ? $searchWordsArray[$shiftArray[$h]] : '.+'.$searchWordsArray[$shiftArray[$h]];
				}
				array_unshift($shiftArray,array_pop($shiftArray));
				$searchPattern .= ($i===0) ? '('.$searchPatternInner.')': '|('.$searchPatternInner.')';
			}
		}
		return '('.$searchPattern.')';
	}
}
