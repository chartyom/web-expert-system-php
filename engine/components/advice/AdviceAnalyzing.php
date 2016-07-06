<?php
namespace engine\components;

use engine\models\AdviceAnalyzingModel;

class AdviceAnalyzing
{
    /**
     * Получение spec_id по сообщению
     */
    public static function getSpecializationId($message)
    {

        /*массив словообразовательных форм*/
        $messageArray = AdviceAnalyzingTools::getMessageBase($message);
        /*проверка на существование, сформированного из обращения, массива слов*/
        if (!empty($messageArray)) {
            $specializationsArray = AdviceAnalyzingModel::selectSpecializations(); // вывод списка всех специализаций
            /*проверка на существование массива специализаций*/
            if (!empty($specializationsArray)) {
                /*глобальный массив для отобра подходящих специализации*/
                $bestOffer = [];
                //цикл foreach [1]
                foreach ($specializationsArray AS $keyArray => $specialization) {
                    /*счетчик совпадений*/
                    $matchWords = 0;
                    /*
                     * Счетчик совпадений слов из массива ассоциаций, которые совпали с искомыми словами: ["word1","5"]
                     * word1 -> искомое слово, 5 -> количество совпадений (вес слова)
                     */
                    $weightWords = 0;
                    /*проверка на существование элемента ассоциаций*/
                    if (!empty($specialization['association'])) {
                        /*преобразование из формата JSON в массив*/
                        /*$specialization['association'] -> [["word1","5"],["word2","4"],["word3","7"]]*/
                        /*word1 -> искомое слово, 5 -> количество совпадений (вес слова)*/
                        $associationArray = Json::decode($specialization['association']);

                        /*Циклы нахождения совпадений*/
                        foreach ($messageArray AS $key1 => $value1) { //цикл foreach [2]
                            foreach ($associationArray AS $key2 => $value2) {//цикл foreach [3]
                                /*процент совпадения слов*/
                                $chance = AdviceAnalyzingTools::getSimilarity($value1, $value2[0]);
                                if ($chance > 90) {// выполняется, если процент совпадения выше 90
                                    $matchWords++;
                                    $weightWords = $weightWords + $value2[1];
                                }
                            }
                        }
                        /*добавление в глобальный массив количество совпадений слов*/
                        $bestOffer[] = [
                            'spec_id' => $specialization['spec_id'],
                            'match'   => $matchWords,
                            'weight'  => $weightWords,
                        ];
                    }
                }

                /*
                 * Проверка на существование отобранных специализаций
                 */
                if (!empty($bestOffer)) {
                    /*
                     * Получение идентификатора специализации с максимальной схожестью по словам и суммарным весом слов
                     */
                    $maxMatchWords = 0;
                    $maxWeightWords = 0;
                    $spec_id = 0;

                    foreach ($bestOffer AS $value) {
                        /*В случае одинаквых совпадений производится проверка по весу слов*/
                        if ($value['match'] > $maxMatchWords && $value['weight'] > $maxWeightWords) {
                            $maxWeightWords = $value['weight'];
                            $maxMatchWords = $value['match'];
                            $spec_id = $value['spec_id'];
                        }
                    }

                    return $spec_id;
                }
            }
        }

        return null;
    }


    /**
     * Добавляет новые слова в таблицу ассоциаций для выранной специализацииц
     *
     * @param $spec_id
     * @param $message
     *
     */
    public static function setAssociationBySpecId($spec_id, $message)
    {

        /*массив словообразовательных форм*/
        $messageArray = AdviceAnalyzingTools::getMessageBase($message);
        /*проверка на существование, сформированного из обращения, массива слов*/
        if (!empty($spec_id) && !empty($messageArray)) {
            $specializationsArray = AdviceAnalyzingModel::selectSpecializationBySpecId($spec_id); // вывод списка всех специализаций
            /*проверка на существование массива специализаций*/
            if (!empty($specializationsArray)) {

                /*
                 * Массив слов пользовательского обращения,
                 * которые не содержаться в массиве ассоциаций
                 */
                $newMessageArray = [];

                if (!empty($specializationsArray['association'])) {

                    /*преобразование из формата JSON в массив*/
                    /*$specializationsArray[$specializationsArrayKey]['association'] -> [["word1","5"],["word2","4"],["word3","7"]]*/
                    /*word1 -> искомое слово, 5 -> количество совпадений(вес слова) */
                    $associationArray = Json::decode($specializationsArray['association']);
                    /*
                     * Все найденные слова, которые не были в базе знаний, добавляются в конец массива C,
                     * Преобразуются в формат JSON и записываются в базу знаний
                     */
                    foreach ($messageArray AS $key1 => $value1) {
                        /*Удаление совпадений массива ассоциаций из массива обращения*/
                        foreach ($associationArray AS $key2 => $value2) {
                            /*процент совпадения слов*/
                            $chance = AdviceAnalyzingTools::getSimilarity($value1, $value2[0]);
                            /*
                             * Когда слова схожи на 100% они удаляются из массива слов
                             * И обновляется вес слова в массиве ассоциаций
                             */
                            if ($chance > 99) {
                                $value1 = '';
                                $associationArray[$key2][1]++;
                                unset($messageArray[$key1]);
                            }
                        }
                        /*
                         * Проверка на существование текущего слова.
                         * Когда слово совпало с элементом массива ассоциаций,
                         * то оно обнуляется, а текущий элемент массива сообщения удаляется.
                         */
                        if (!empty($value1)) {
                            $newMessageArray[] = [
                                $value1,
                                1,
                            ];
                        }
                    }
                    //Объединение массивов
                    $associationNew = array_merge($associationArray, $newMessageArray);
                    /*Преобразование в текстовый формат JSON*/
                    $associationJson = Json::encode($associationNew, JSON_UNESCAPED_UNICODE);
                } else {
                    $associationArray = [];
                    foreach ($messageArray AS $value) {
                        $associationArray[] = [
                            $value,
                            1,
                        ];
                    }
                    /*Преобразование в текстовый формат JSON*/
                    $associationJson = Json::encode($associationArray, JSON_UNESCAPED_UNICODE);
                }
                /*Обновление ассоциаций специализации*/
                AdviceAnalyzingModel::updateSpecializationBySpecId($spec_id, $associationJson);
            }
        }
    }
}