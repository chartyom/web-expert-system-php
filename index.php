<?php
require(__DIR__.'/engine/Engine.php');

use engine\components\Request;
use engine\components\Response;
use engine\components\Validator;
use engine\pages;

switch ( Request::get('__base') ) {
    case 'api':
        pages\ApiPage::base();
        break;
    case '':
        pages\MainPage::base();
        break;
    /* Link error 404 */
    default:
        Response::redirect("/");
}



