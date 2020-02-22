<?php
/*
=============================================================================
BlockPro
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
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
include_once ENGINE_DIR.'/classes/plugins.class.php';

/** @var array $config */
if ($config['http_home_url'] == "") {
	$config['http_home_url'] = explode("engine/ajax/base/save_block_pro.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/functions.php'));
if ($config['version_id'] > 9.6) {
	dle_session();
} else {
	@session_start();
}

$user_group = get_vars("usergroup");
if (!$user_group) {
	$user_group = [];
	$db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
	while ($row = $db->get_row()) {
		$user_group[$row['id']] = [];
		foreach ($row as $key => $value) $user_group[$row['id']][$key] = stripslashes($value);
	}
	set_vars("usergroup", $user_group);
	$db->free();
}
require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));

/**
 * Основной код файла
 */
/** @var array $member_id */
if ($member_id['user_group'] == '1') {
	if ($_REQUEST['blockId']) {
		if ($_REQUEST['saveBlock'] == 'Y') {
			$blockId = (isset($_REQUEST['blockId'])) ? $_REQUEST['blockId'] : false;

			$cashe_tmp             = $config['allow_cache'];
			$config['allow_cache'] = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.
			$_cr                   = dle_cache($blockId);
			$config['allow_cache'] = $cashe_tmp;

			if ($_cr) {
				$params   = $_cr;
				$name     = $db->safesql($_REQUEST['name']);
				$block_id = 'bp' . crc32($params . time());

				$block = $db->query("INSERT INTO " . PREFIX . "_blockpro_blocks (name, block_id, params) VALUES('$name', '$block_id', '$params')");
				if ($block) {
					$insertCode       = '<script type="text/javascript" async defer src="' . $config['http_home_url'] . 'blockpro.php?block=' . $block_id . '"></script><div id="' . $block_id . '"></div>';
					$insertRssCode    = $config['http_home_url'] . 'blockpro.php?channel=' . $block_id;
					$insertIframeCode = '<iframe src="' . $config['http_home_url'] . 'blockpro.php?frame=' . $block_id . '" frameborder="0"></iframe>';
					$modal            = '<div class="content">
						<div class="modal-white">
							<span class="modal-close popup-modal-dismiss">&times;</span>
							<div class="modal-header">
								<p>Блок сохранён!</p>
							</div>
							<div class="modal-content p10">
								<div class="clearfix">
									<div class="fz18 text-blue">Код для вывода виджета через javascript:</div>
									<textarea readonly class="input input-block-level code">' . $insertCode . '</textarea>
								</div>
								<div class="clearfix">
									<div class="fz18 text-blue">Код для вывода через RSS:</div>
									<textarea rows="1" readonly class="input input-block-level code">' . $insertRssCode . '</textarea>
								</div>
								<div class="clearfix">
									<div class="fz18 text-blue">Код для вывода через iframe:</div>
									<textarea readonly class="input input-block-level code">' . $insertIframeCode . '</textarea>
								</div>
								<div class="alert alert-info">Созданный виджет так же будет доступен на вкладке "Виджеты".</div>
								<div class="ta-center mb10 mt20">
									<span class="btn modal-close">Закрыть окно</span>
								</div>
							</div>
						</div>
					</div>';
				}
				$db->free;

			} else {
				$modal = '<div class="content">
					<div class="modal-white">
						<span class="modal-close popup-modal-dismiss">&times;</span>
						<div class="modal-header">
							<p>Произошла ошибка!</p>
						</div>
						<div class="modal-content p10 mt0">
							<div class="content">
								<div class="alert mb30">
									Невозможно получить конфиг текущего вызова модуля. <br>Попробуйте ещё раз.
								</div>
							</div>
							<div class="ta-center mb10">
								<span class="btn modal-close">Закрыть окно</span>
							</div>
						</div>
					</div>
				</div>';
			}

		} else {
			$modal = '<div class="col-mb-12 col-8 col-dt-6 col-ld-5 center-block">
				<form method="POST" action="' . $config['http_home_url'] . '/engine/ajax/base/save_block_pro.php" data-ajax-submit>
					<div class="content">
						<div class="modal-white">
							<span class="modal-close popup-modal-dismiss">&times;</span>
							<div class="modal-header">
								<p>Добавить новый виджет</p>
							</div>
							<div class="modal-content p10">
								<input type="hidden" name="saveBlock" value="Y">
								<div class="content">
									<div class="col col-mb-12">
										Код блока (не проверяется на уникальность)
										<input class="input input-block" type="text" name="blockId" value="' . $_REQUEST['blockId'] . '">
									</div>
								</div>
								<div class="content">
									<div class="col col-mb-12">
										Название блока (для показа в списке)
										<input class="input input-block" type="text" name="name" id="name">
									</div>
								</div>
								<div class="ta-center mb10">
									<button class="btn ladda-button" type="submit" data-style="expand-left"><span class="ladda-label">Сoхранить</span></button>
									<span class="btn btn-small btn-red modal-close">Закрыть</span>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>';
		}

		echo $modal;

	}

} else {
	die ('Access denied');
}