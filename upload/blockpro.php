<?php
/*
=============================================================================
BlockPro - Вывод виджета.
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
 */

$timeStart = microtime(true);

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR . '/data/config.php';

if ($config['version_id'] > 10.2) {
	date_default_timezone_set($config['date_adjust']);
	$_TIME = time();
} else {
	$_TIME = time() + ($config['date_adjust'] * 60);
}

if ($config['http_home_url'] == "") {
	$config['http_home_url'] = explode("blockpro.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';

check_xss();

if (function_exists('dle_session')) {
	dle_session();
} else {
	@session_start();
}

$is_logged = false;
$member_id = array();

if ($config['allow_registration']) {
	require_once ENGINE_DIR . '/modules/sitelogin.php';
}
if (!$is_logged) {
	$member_id['user_group'] = 5;
}
if (!$cat_info) {
	$user_group = get_vars("usergroup");
}
if (!$cat_info) {
	$cat_info = get_vars("category");
}

$blockId = $isJs = $isRSS = false;
if (isset($_REQUEST['block'])) {
	$blockId = $_REQUEST['block'];
	$isJs = true;
}
if (isset($_REQUEST['channel'])) {
	$blockId = $_REQUEST['channel'];
	$isRSS = true;
}
$blockId = $db->safesql($blockId);

$_cr = false;
if ($blockId) {
	$arCr = $db->super_query("SELECT params FROM " . PREFIX . "_blockpro_blocks WHERE block_id='{$blockId}'");
	$_cr = $arCr['params'];
}
$externalOutput = '';

if ($_cr) {
	// Если запись существует — работаем.
	$isAjaxConfig = true;
	$ajaxConfigArr = unserialize($_cr);

	if ($ajaxConfigArr['cacheLive']) {
		// Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
		$ajaxConfigArr['cachePrefix'] = 'base';
	}

	// Формируем имя кеша
	$cacheName = implode('_', $ajaxConfigArr) . $config['skin'];

	// Определяем необходимость создания кеша для разных групп
	$cacheSuffix = ($ajaxConfigArr['cacheSuffixOff']) ? false : true;

	// Если установлено время жизни кеша
	if ($ajaxConfigArr['cacheLive']) {
		// Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
		$_end_file = (!$ajaxConfigArr['cacheSuffixOff']) ? ($is_logged) ? '_' . $member_id['user_group'] : '_0':false;
		$filedate = ENGINE_DIR . '/cache/' . $ajaxConfigArr['cachePrefix'] . '_' . md5($cacheName) . $_end_file . '.tmp';

		if (@file_exists($filedate)) {
			$cache_time = time()-@filemtime($filedate);
		} else {
			$cache_time = $ajaxConfigArr['cacheLive'] * 60;
		}
		if ($cache_time >= $ajaxConfigArr['cacheLive'] * 60) {
			$clear_time_cache = true;
		}
	}

	// Говорим модулю, что он — внешний блок
	$external = true;
	include ENGINE_DIR . '/modules/base/blockpro.php';

	if ($isJs) {
		header("Content-type: text/javascript; charset=" . $config['charset']);
		// Подготавливаем контент к выводу
		$result = prepereBlock($output);
		$externalOutput = '\'' . $result . '\'';

		// Подсчитаем время выполнения скрипта и добавим данные об этом в вывод.
		$timeStop = round(microtime(true) - $timeStart, 5);

		$jsBlockId = str_replace('-', '', $blockId);

		$consoleLog = 'console.log(\'[blockpro]: id: ' . $blockId . ', time: ' . $timeStop . ' s.\');';

		$printOutput = 'var j' . $jsBlockId . ' = document.getElementById(\'' . $blockId . '\');if(j' . $jsBlockId . '){j' . $jsBlockId . '.innerHTML = ' . $externalOutput . '};' . $consoleLog;
	}
	if ($isRSS) {
		header("Content-type: application/xml; charset=" . $config['charset']);
		// Подготавливаем контент к выводу
		// $result = prepereBlock($output);
		
		$printOutput = $output;
	}


	// Выводим результаты работы модуля в виде js-строки
	echo $printOutput;

} else {
	if ($isJs) {
		// Если запись не найдена - выведем предупреждением в консоли, чтобы не захламлять сайт.
		$consoleLog = 'console.warn(\'[blockpro]: no content to show\');';
	}
	
	if ($isRSS) {
		// Если запись не найдена - выведем предупреждением в консоли, чтобы не захламлять сайт.
		$consoleLog = 'console.warn(\'[blockpro]: no content to show\');';
	}
	echo $consoleLog;
}

function prepereBlock($text) {
	$search = array("\n", "\t");
	$text = str_replace($search, '', $text);
	$text = addslashes($text);
	return $text;
}
