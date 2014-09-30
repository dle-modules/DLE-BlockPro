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
$member_id = array();

if ($config['allow_registration']) {
	require_once ENGINE_DIR . '/modules/sitelogin.php';
}
if(!$is_logged) {
	$member_id['user_group'] = 5;
}
if (!$cat_info) {
	$user_group = get_vars("usergroup");
}
if (!$cat_info) {
	$cat_info = get_vars("category");
}

@header("Content-type: text/html; charset=" . $config['charset']);
$pageNum = (isset($_REQUEST['pageNum'])) ? (int)$_REQUEST['pageNum'] : 1;
$blockId = (isset($_REQUEST['blockId'])) ? $_REQUEST['blockId'] : false;

$cashe_tmp = $config['allow_cache'];
$config['allow_cache'] = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.
$_cr = dle_cache($blockId);
$config['allow_cache'] = $cashe_tmp;

if ($_cr) {
	$isAjaxConfig             = true;
	$ajaxConfigArr            = unserialize($_cr);
	$ajaxConfigArr['pageNum'] = $pageNum;

	include ENGINE_DIR . '/modules/base/blockpro.php';

} else {
	die('cache not found');
}

?>


