<?php
namespace engine\models;

use \engine\components\UserGroup;
use \PDO;

class AdviceModel
{
    /**
     * Выводит доступные специализации
     *
     * @return array
     */
    public static function selectSpecializations()
    {
        $bdconnect = ModelTools::bdconnect();
        $sql = 'SELECT spec_id, name FROM specializations_analyzing WHERE 1';
        $query = $bdconnect->prepare($sql);
        $query->execute();
        $row = $query->fetchAll();
        $sql = null;
        $query = null;
        $bdconnect = null;
        return ($row);
    }

    /**
     * Выводит название специализации по ID
     *
     * @param $spec_id
     *
     * @return array
     */
    public static function selectSpecializationById($spec_id)
    {
        $bdconnect = ModelTools::bdconnect();
        $sql = 'SELECT spec_id, name FROM specializations_analyzing WHERE spec_id = :spec_id LIMIT 1';
        $query = $bdconnect->prepare($sql);
        $query->bindValue(':spec_id', $spec_id, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch();
        $sql = null;
        $query = null;
        $bdconnect = null;
        return ($row);
    }

}
