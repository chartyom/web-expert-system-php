<?php

return [
    'engine\settings\Settings' => SETTINGS_PATH.'/Settings.php',
    'engine\settings\Database' => SETTINGS_PATH.'/Database.php',

    'engine\components\Cookie' => COMPONENTS_PATH.'/Cookie.php',
    'engine\components\Random' => COMPONENTS_PATH.'/Random.php',
    'engine\components\Url' => COMPONENTS_PATH.'/Url.php',
    'engine\components\Files' => COMPONENTS_PATH.'/Files.php',
    'engine\components\Html' => COMPONENTS_PATH.'/Html.php',
    'engine\components\Json' => COMPONENTS_PATH.'/Json.php',
    'engine\components\Request' => COMPONENTS_PATH.'/Request.php',
    'engine\components\Response' => COMPONENTS_PATH.'/Response.php',
    'engine\components\Validator' => COMPONENTS_PATH.'/validator/Validator.php',
	'engine\components\AdviceAnalyzing' => COMPONENTS_PATH.'/advice/AdviceAnalyzing.php',
    'engine\components\AdviceAnalyzingTools' => COMPONENTS_PATH.'/advice/AdviceAnalyzingTools.php',
	'engine\components\Session' => COMPONENTS_PATH.'/Session.php',

    'engine\pages\MainPage' => PAGES_PATH.'/MainPage.php',
    'engine\pages\ApiPage' => PAGES_PATH.'/ApiPage.php',

    'engine\Models\ModelTools' => MODELS_PATH.'/ModelTools.php',
	'engine\Models\AdviceAnalyzingModel' => MODELS_PATH.'/advice/AdviceAnalyzingModel.php',
    'engine\Models\AdviceModel' => MODELS_PATH.'/advice/AdviceModel.php',
];

