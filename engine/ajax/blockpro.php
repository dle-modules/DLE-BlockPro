<?php
/*
=============================================================================
BlockPro - ajax часть модуля
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
email:   pafnuty10@gmail.com
=============================================================================
*/

if(!defined('DATALIFEENGINE')) {
    header( "HTTP/1.1 403 Forbidden" );
    header ( 'Location: ../../' );
    die( "Hacking attempt!" );
}

$pageNum = (isset($_REQUEST['pageNum'])) ? (int)$_REQUEST['pageNum'] : 1;
$blockId = (isset($_REQUEST['blockId'])) ? $_REQUEST['blockId'] : false;
$thisUrl = (isset($_REQUEST['thisUrl'])) ? (string)$_REQUEST['thisUrl'] : false;

$cashe_tmp             = $config['allow_cache'];
$config['allow_cache'] = 1;
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
    $cacheName = implode('_', $ajaxConfigArr).$config['skin'];

    // Определяем необходимость создания кеша для разных групп
    $cacheSuffix = ($ajaxConfigArr['cacheSuffixOff']) ? false : true;

    // Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
    $_end_file = (!$ajaxConfigArr['cacheSuffixOff']) ? ($is_logged) ? '_'.$member_id['user_group'] : '_0' : false;
    $filedate  = ENGINE_DIR.'/cache/'.$ajaxConfigArr['cachePrefix'].'_'.md5($cacheName).$_end_file.'.tmp';

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

    header("Content-type: text/html; charset=".$config['charset']);
    header('Cache-Control: public, max-age='.$seconds);

    if (file_exists($filedate)) {
        $etag         = md5_file($filedate);
        $lastModified = filemtime($filedate);

        header("Expires: ".gmdate("D, d M Y H:i:s", $lastModified + $seconds)." GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");

        header("Etag: $etag");
        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified
            || @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
        ) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
    }

    include(DLEPlugins::Check(ENGINE_DIR.'/modules/base/blockpro.php'));

} else {
    die('cache not found');
}