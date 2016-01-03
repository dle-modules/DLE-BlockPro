<?php
/*
=============================================================================
DLE Yandex Maps
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

// Всякие обязательные штуки для ajax DLE
@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -17));

define('ENGINE_DIR', ROOT_DIR . '/engine');


include ENGINE_DIR . '/data/config.php';

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
if ($config['version_id'] > 9.6) {
	dle_session();
}
else {
	@session_start();
}


$user_group = get_vars("usergroup");
if (!$user_group) {
	$user_group = array();
	$db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
	while ($row = $db->get_row()) {
		$user_group[$row['id']] = array();
		foreach ($row as $key => $value) $user_group[$row['id']][$key] = stripslashes($value);
	}
	set_vars("usergroup", $user_group);
	$db->free();
}
require_once ENGINE_DIR . '/modules/sitelogin.php';
require_once ENGINE_DIR . '/modules/base/admin/blockpro/checkLicenseStatus.php';


/**
 * Основной код файла
 */
if ($member_id['user_group'] == '1') {

	die ($licenseStatus);
	
} else {
	die ('Access denied');
}
?>

