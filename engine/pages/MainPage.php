<?php
namespace engine\pages;

use engine\components\AdviceAnalyzing;
use engine\components\AdviceAnalyzingTools;
use engine\components\Html;
use engine\components\Json;
use engine\components\Session;
use engine\components\Tools;
use engine\components\UploadTools;
use engine\components\Url;
use engine\components\User;
use engine\components\Validator;
use engine\models\AdviceModel;
use engine\models\FeedbackModel;
use engine\models\ModelTools;
use engine\models\UserModel;
use engine\settings\Settings;
use engine\templates;

class MainPage
{

    public static function url()
    {
        return Url::main();
    }

    private static $title = 'Испытание';
    private static $description = 'Интеллектуальная система классификации обращений пациентов. Черняев Артём';


    public static function base()
    {
        self::$title .= Settings::TITLE_SEPARATOR . Settings::SITE_NAME;
        ?>
        <?= Html::beginHtml() ?>
        <head>
            <?= Html::charset() ?>
            <?= Html::metaViewport() ?>
            <?= Html::metaIEEdge() ?>
            <?= Html::cssFile('normalize.css') ?>
            <?= Html::cssFile('bootstrap.min.css') ?>
            <?= Html::cssFile('icons.css') ?>
            <?= Html::cssFile('general.style.css') ?>
            <?= Html::jsFile('jquery-2.2.0.min.js') ?>
            <?= Html::jsFile('bootstrap.min.js') ?>
            <?= Html::favicon() ?>

            <?= Html::title(self::$title) ?>
            <?= Html::description(self::$description) ?>
        </head>
        <body>
        <header>
            <div class="mt__header">
                <div class="container">
                    <div class="mt__header__title">
                        Интеллектуальная система классификации обращений пациентов
                        <small>Экспертная система</small>
                    </div>

                </div>
            </div>
        </header>
        <article>
            <?= self::content() ?>
        </article>
        <footer>
            <div class="mt__footer">
                <div class="container">
                    Черняев Артём 2016г.
                </div>
            </div>
        </footer>
        <?= Html::jsFile('main.js') ?>
        </body>
        <?= Html::endHtml() ?>
        <?
    }

    private static function content()
    {
        $selectSpecializations = function () {
            $specializationsArray = AdviceModel::selectSpecializations();
            if (!empty($specializationsArray)) {
                foreach ($specializationsArray as $value) {
                    ?>
                    <option value="<?= $value['spec_id'] ?>"><?= $value['name'] ?></option>
                    <?
                }
            }
        };


        ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <h2 class="mt__title">Испытание</h2>


                    <div class="form-group">
                        <label for="inputMessage">Вопрос</label>
                            <textarea class="form-control mtf__advice__inputMessage" id="inputMessage"
                                      aria-describedby="helpBlock1" placeholder="Напишите сюда свой вопрос"
                                      tabindex="1" name="advice"></textarea>
                        <span id="helpBlock1" class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <button onclick="return MainClass.experiment()" id="buttonExperiment" type="submit"
                                class="btn btn-primary">Получить специализацию
                        </button>
                        <button onclick="return MainClass.clear()" type="submit"
                                class="btn btn-default">Очистить
                        </button>
                    </div>

                    <div class="mtr">

                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="mt__rightContent clearfix">
                        <h2 class="mt__title">Обучение</h2>

                        <div class="form-group">
                            <label for="selectSpecialization">Выбор специализации</label>
                            <select id="selectSpecialization" class="form-control">
                                <option value="0">Выберите категорию</option>
                                <?= $selectSpecializations() ?>
                            </select>
                            <span id="helpBlock2" class="help-block"></span>
                        </div>
                        <div class="pull-left">
                        <button onclick="return MainClass.training()" id="buttonTraining" type="submit"
                                class="btn btn-success">Обучить систему
                        </button>
                        </div>
                        <div class="pull-left">
                        <div class="mtt__success"><i class=" icon icon24-check"></i></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?
        return '';
    }


}