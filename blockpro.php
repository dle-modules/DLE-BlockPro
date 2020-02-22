<?php
/*
=============================================================================
BlockPro - Вывод виджета.
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
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
define('ENGINE_DIR', ROOT_DIR.'/engine');
include_once ENGINE_DIR.'/classes/plugins.class.php';

/** @var array $config */
date_default_timezone_set($config['date_adjust']);


if ($config['http_home_url'] == '') {
    $config['http_home_url'] = explode('blockpro.php', $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = 'http://'.$_SERVER['HTTP_HOST'].$config['http_home_url'];
}

require_once(DLEPlugins::Check(ENGINE_DIR.'/modules/functions.php'));

check_xss();

dle_session();


$is_logged = false;
$member_id = [];

if ($config['allow_registration']) {
    require_once(DLEPlugins::Check(ENGINE_DIR.'/modules/sitelogin.php'));
}
if (!$is_logged) {
    $member_id['user_group'] = 5;
}
if (!$cat_info) {
    $user_group = get_vars('usergroup');
}
if (!$cat_info) {
    $cat_info = get_vars("category");
}

$blockId = $isJs = $isRSS = $isIframe = false;
if (isset($_REQUEST['block'])) {
    $blockId = $_REQUEST['block'];
    $isJs    = true;
}
if (isset($_REQUEST['channel'])) {
    $blockId = $_REQUEST['channel'];
    $isRSS   = true;
}
if (isset($_REQUEST['frame'])) {
    $blockId  = $_REQUEST['frame'];
    $isIframe = true;
}
$blockId = $db->safesql($blockId);

$_cr = false;
if ($blockId) {
    $arCr = $db->super_query("SELECT params FROM ".PREFIX."_blockpro_blocks WHERE block_id='{$blockId}'");
    $_cr  = $arCr['params'];
}
$externalOutput = '';

if ($_cr) {
    // Если запись существует — работаем.
    $isAjaxConfig  = true;
    $ajaxConfigArr = unserialize($_cr);

    if ($ajaxConfigArr['cacheLive']) {
        // Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
        $ajaxConfigArr['cachePrefix'] = 'base';
    }

    // Формируем имя кеша
    $cacheName = implode('_', $ajaxConfigArr).$config['skin'];

    // Определяем необходимость создания кеша для разных групп
    $cacheSuffix = ($ajaxConfigArr['cacheSuffixOff']) ? false : true;

    // Если установлено время жизни кеша
    if ($ajaxConfigArr['cacheLive']) {
        // Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
        $_end_file = (!$ajaxConfigArr['cacheSuffixOff']) ? ($is_logged) ? '_'.$member_id['user_group'] : '_0' : false;
        $filedate  = ENGINE_DIR.'/cache/'.$ajaxConfigArr['cachePrefix'].'_'.md5($cacheName).$_end_file.'.tmp';

        if (@file_exists($filedate)) {
            $cache_time = time() - @filemtime($filedate);
        } else {
            $cache_time = $ajaxConfigArr['cacheLive'] * 60;
        }
        if ($cache_time >= $ajaxConfigArr['cacheLive'] * 60) {
            $clear_time_cache = true;
        }
    }

    // Говорим модулю, что он — внешний блок
    $external = true;
    include(DLEPlugins::Check(ENGINE_DIR.'/modules/base/blockpro.php'));

    if ($isJs) {
        header("Content-type: text/javascript; charset=".$config['charset']);
        // Подготавливаем контент к выводу
        /** @var string $output */
        $result         = prepareBlock($output);
        $externalOutput = '\''.$result.'\'';

        // Подсчитаем время выполнения скрипта и добавим данные об этом в вывод.
        $timeStop = round(microtime(true) - $timeStart, 5);

        $jsBlockId = str_replace('-', '', $blockId);

        $consoleLog = 'console.log(\'[blockpro]: id: '.$blockId.', time: '.$timeStop.' s.\');';

        $printOutput = 'var j'.$jsBlockId.' = document.getElementById(\''.$blockId.'\');if(j'.$jsBlockId.'){j'
            .$jsBlockId.'.innerHTML = '.$externalOutput.'};'.$consoleLog;
    }
    if ($isRSS) {
        header("Content-type: application/xml; charset=".$config['charset']);

        $printOutput = $output;
    }

    if ($isIframe) {
        header("Content-type: text/html; charset=".$config['charset']);

        $printOutput = $output;
    }


    // Выводим результаты работы модуля в виде js-строки
    /** @var string $printOutput */
    echo $printOutput;

} else {
    if ($isJs) {
        $noData = 'console.warn(\'[blockpro]: no content to show\');';
    } else {
        $noData = 'no content to show';
    }
    echo $noData;
}

function prepareBlock($text) {
    $search = ["\n", "\t"];
    $text   = str_replace($search, '', $text);
    $text   = addslashes($text);

    return $text;
}
