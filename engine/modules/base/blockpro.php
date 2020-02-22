<?php
/*
=============================================================================
BLockPro - основной модуль
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
email:   pafnuty10@gmail.com
=============================================================================
 */

if (!defined('DATALIFEENGINE')) {
    header("HTTP/1.1 403 Forbidden");
    header('Location: ../../');
    die("Hacking attempt!");
}


include_once ENGINE_DIR.'/classes/plugins.class.php';

include(DLEPlugins::Check(ENGINE_DIR.'/modules/base/blockpro.inc.php'));