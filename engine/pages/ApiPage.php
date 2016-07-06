<?php
namespace engine\pages;

use engine\components\AdviceAnalyzing;
use engine\components\Html;
use engine\components\Request;
use engine\components\Response;
use engine\components\Url;
use engine\models\AdviceModel;

class ApiPage
{

    public static function url()
    {
        return Url::main() . '/api';
    }

    public static function base()
    {
        switch (Request::get('action')) {
            case 'training':
                self::training();
                break;
            case 'experiment':
                self::experiment();
                break;
            case '':
                $output['response']['status'] = 'active';
                $output['response']['author'] = 'Chernyaev Artyom';
                Response::json($output);
                break;
        }

    }

    /**
     * Проверка работы ЭС
     *
     * POST ?action=experiment
     *
     * Output
     * {"response":{"success":1,"specId":(int),"specName":(string)}}
     *
     * Errors
     * {"response":{"error":1,"errorCode":412}} - некорректный ввод входных данных
     * {"response":{"error":1,"errorCode":403}} - доступ ограничен
     *
     */
    private static function experiment()
    {
        if (Request::is_post()) {
            $message = Html::encode(Request::post('message'));
            if (!empty($message)) {
                /*Экспертная система*/
                $spec_id = AdviceAnalyzing::getSpecializationId($message);
                $output['response']['success'] = 1;
                $output['response']['message'] = $message;
                if(!empty($spec_id)){
                    $output['response']['specId'] = $spec_id;
                    $output['response']['specName'] = AdviceModel::selectSpecializationById($spec_id)['name'];
                } else {
                    $output['response']['specId'] = "Не определён";
                    $output['response']['specName'] = " - ";
                }

                Response::json($output);
            }

            $output['response']['error'] = 1;
            $output['response']['errorCode'] = 412;
            Response::json($output);
        }
        $output['response']['error'] = 1;
        $output['response']['errorCode'] = 403;
        Response::json($output);
    }

    /**
     * Обучение ЭС
     *
     * POST ?action=training
     *
     * Output
     * {"response":{"success":1}}
     *
     * Errors
     * {"response":{"error":1,"errorCode":412}} - некорректный ввод входных данных
     * {"response":{"error":1,"errorCode":403}} - доступ ограничен
     *
     */
    private static function training()
    {
        if (Request::is_post()) {

            $message = Html::encode(Request::post('message'));
            $spec_id = (int) Request::post('specId');
            if (
                !empty($message) &&
                !empty($spec_id)
            ) {
                AdviceAnalyzing::setAssociationBySpecId($spec_id, $message);
                $output['response']['success'] = 1;
                Response::json($output);
            }
            $output['response']['error'] = 1;
            $output['response']['message'] = $message;
            $output['response']['specId'] = $spec_id;
            $output['response']['errorCode'] = 412;
            Response::json($output);

        }
        $output['response']['error'] = 1;
        $output['response']['errorCode'] = 403;
        Response::json($output);

    }
}