<?php

/**
 * Путь к директории программной платформы.
 */
defined('ENGINE_PATH') or define('ENGINE_PATH', __DIR__);

/**
 * Путь к додиректории с компонентами.
 */
defined('COMPONENTS_PATH') or define('COMPONENTS_PATH', __DIR__."/components");

/**
 * Путь к додиректории с модулями.
 */
defined('MODULES_PATH') or define('MODULES_PATH', __DIR__."/modules");


/**
 * Путь главной директории веб-приложения.
 */
defined('MAIN_PATH') or define('MAIN_PATH', __DIR__."/../");

/**
 * Путь до додиректории со страницами веб-приложения.
 */
defined('PAGES_PATH') or define('PAGES_PATH', __DIR__."/pages");

/**
 * Путь до додиректории с классами взаимодействия с бд.
 */
defined('MODELS_PATH') or define('MODELS_PATH', __DIR__."/models");

/**
 * Путь до додиректории с шаблонами
 */
defined('TEMPLATES_PATH') or define('TEMPLATES_PATH', __DIR__."/templates");

/**
 * Путь до додиректории с настройками
 */
defined('SETTINGS_PATH') or define('SETTINGS_PATH', __DIR__."/settings");

/**
 * Установка используемого формата.
 */
defined('ENGINE_FORMAT') or define('ENGINE_FORMAT', "UTF-8");

/**
 * Использование режима разработчика.
 * .. в разработке
 */
defined('ENGINE_DEBUG') or define('ENGINE_DEBUG', true);


