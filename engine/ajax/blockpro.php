<?php
/*
=============================================================================
BlockPro - ajax часть модуля
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/


@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -12));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR . '/data/config.php';

/** @var array $config */
if ($config['version_id'] > 10.2) {
	date_default_timezone_set($config['date_adjust']);
	$_TIME = time();
} else {
	$_TIME = time() + ($config['date_adjust'] * 60);
}

if ($config['http_home_url'] == "") {
	$config['http_home_url'] = explode("engine/ajax/blockpro.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';

if (function_exists('dle_session')) {
	dle_session();
} else {
	@session_start();
}

$is_logged = false;
$member_id = [];

if ($config['allow_registration']) {
	require_once ENGINE_DIR . '/modules/sitelogin.php';
}
if (!$is_logged) {
	$member_id['user_group'] = 5;
}
/** @var array $user_group */
if (!$user_group) {
	$user_group = get_vars("usergroup");
}
if (!$cat_info) {
	$cat_info = get_vars("category");
}

$pageNum = (isset($_REQUEST['pageNum'])) ? (int)$_REQUEST['pageNum'] : 1;
$blockId = (isset($_REQUEST['blockId'])) ? $_REQUEST['blockId'] : false;

$cashe_tmp             = $config['allow_cache'];
$config['allow_cache'] = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.
$_cr                   = dle_cache($blockId);
$config['allow_cache'] = $cashe_tmp;

if ($_cr) {

	$isAjaxConfig             = true;
	$ajaxConfigArr            = unserialize($_cr);
	$ajaxConfigArr['pageNum'] = $pageNum;

	if ($ajaxConfigArr['cacheLive']) {
		// Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
		$ajaxConfigArr['cachePrefix'] = 'base';
	}

	// Формируем имя кеша
	$cacheName = implode('_', $ajaxConfigArr) . $config['skin'];

	// Определяем необходимость создания кеша для разных групп
	$cacheSuffix = ($ajaxConfigArr['cacheSuffixOff']) ? false : true;

	// Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
	$_end_file = (!$ajaxConfigArr['cacheSuffixOff']) ? ($is_logged) ? '_' . $member_id['user_group'] : '_0' : false;
	$filedate  = ENGINE_DIR . '/cache/' . $ajaxConfigArr['cachePrefix'] . '_' . md5($cacheName) . $_end_file . '.tmp';

	// Если установлено время жизни кеша
	if ($ajaxConfigArr['cacheLive']) {

		if (@file_exists($filedate)) {
			$cache_time = time() - @filemtime($filedate);
		} else {
			$cache_time = $ajaxConfigArr['cacheLive'] * 60;
		}
		if ($cache_time >= $ajaxConfigArr['cacheLive'] * 60) {
			$clear_time_cache = true;
		}
	}

	$seconds = 172800; // 2 дня для кеша в браузере

	header("Content-type: text/html; charset=" . $config['charset']);
	header('Cache-Control: public, max-age=' . $seconds);

	if (file_exists($filedate)) {
		$etag         = md5_file($filedate);
		$lastModified = filemtime($filedate);

		header("Expires: " . gmdate("D, d M Y H:i:s", $lastModified + $seconds) . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");

		header("Etag: $etag");
		if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified ||
			@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
		) {
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
	}

	include ENGINE_DIR . '/modules/base/blockpro.php';

} else {
	die('cache not found');
}