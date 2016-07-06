<?php
namespace engine\models;

use \PDO;

class AdviceAnalyzingModel
{

    /**
     * Вывод списка специализаций
     *
     * @return mixed
     */
    public static function selectSpecializations()
    {
        $bdconnect = ModelTools::bdconnect();
        $sql = 'SELECT spec_id, association FROM specializations_analyzing WHERE 1';
        $query = $bdconnect->prepare($sql);
        $query->execute();
        $row = $query->fetchAll();
        $sql = null;
        $query = null;
        $bdconnect = null;
        return ($row);
    }

    /**
     * Вывод списка специализаций по id
     *
     * @param $spec_id
     *
     * @return mixed
     */
    public static function selectSpecializationBySpecId($spec_id)
    {
        $bdconnect = ModelTools::bdconnect();
        $sql = 'SELECT spec_id, association FROM specializations_analyzing WHERE spec_id = :spec_id LIMIT 1';
        $query = $bdconnect->prepare($sql);
        $query->bindValue(':spec_id', $spec_id, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch();
        $sql = null;
        $query = null;
        $bdconnect = null;
        return ($row);
    }

    /**
     * Обновление ассоциаций в таблицы специализаций
     *
     * @param $spec_id
     * @param $association
     *
     *
     */
    public static function updateSpecializationBySpecId($spec_id,$association)
    {
        $bdconnect = ModelTools::bdconnect();
        $sql = 'UPDATE specializations_analyzing SET association=:association WHERE spec_id=:spec_id LIMIT 1';
        $query = $bdconnect->prepare($sql);
        $query->bindValue(':spec_id', $spec_id, PDO::PARAM_INT);
        $query->bindValue(':association', $association, PDO::PARAM_STR);
        $query->execute();
        $sql = null;
        $query = null;
        $bdconnect = null;
    }

}
