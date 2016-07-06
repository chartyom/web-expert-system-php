<?php
namespace engine\settings;

class Settings
{
	const SITE_NAME = 'Интеллектуальная система классификации обращений пациентов';
	const TITLE_SEPARATOR = ' — ';
	const VERSION = '1.1.5';

	const EMAIL_SUPPORT = 'help@medvice.info';
	const EMAIL_NOREPLY = 'noreply@medvice.info';

	/*Путь к папке загрузки файлов*/
	const PATH_UPLOAD = '/uploads/';

	const DEFAULT_FORMAT_DATE = 'd.m.Y \в H:i';
	/*
	 * Массив с доменами, которые могут использоваться в Response::redirect()
	 * Пример заполнение:
	 * ['example.ru', 'mysite.com']
	 */
	const ACCESS_DOMAIN = [];
}
