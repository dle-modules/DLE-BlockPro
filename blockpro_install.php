<?php
// Первым делом подключаем DLE_API как это ни странно, но в данном случаи это упрощает жизнь разработчика.
include('engine/api/api.class.php');

/**
 * Массив с конфигурацией установщика, ведь удобно иметь одинаковый код для разных установщиков разных модулей.
 *
 * @var array
 */
$cfg = [
	// Идентификатор модуля (для внедения в админпанель и назначение имени иконки с расширением .png)
	'moduleName'    => 'blockpro',
	// Название модуля - показывается как в установщике, так и в админке.
	'moduleTitle'   => 'BlockPro',
	// Описание модуля, для установщика и админки.
	'moduleDescr'   => 'Модуль вывода новостей для DLE',
	// Версия модуля, для установщика
	'moduleVersion' => '5.1.5',
	// Дата выпуска модуля, для установщика
	'moduleDate'    => '01.12.2019',
	// Версии DLE, поддержваемые модулем, для установщика
	'dleVersion'    => '10.x',
	// ID групп, для которых доступно управление модулем в админке.
	'allowGroups'   => '1',
	// Массив с запросами, которые будут выполняться при установке
	'queries'       => [
		1 => 'CREATE TABLE IF NOT EXISTS `' . PREFIX . '_blockpro_blocks` (
  `id` tinyint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `block_id` varchar(100) NOT NULL,
  `params` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `block_id` (`block_id`)
) ENGINE=MyISAM;',
	],
	// Устанавливать админку (true/false). Включает показ кнопки установки и удаления админки.
	'installAdmin'  => true,
	// Отображать шаги утановки модуля
	'steps'         => true,
	// Показывать лицензионное соглашение?	
	'showLicense'   => true,

];

// Определяем кодировку.
$fileCharset = chasetConflict($cfg);

// Лицензионное соглашение

$licenseText = '<div class="content"><div class="col col-mb-12"><div style="max-height: 60vh; overflow: auto; border: solid 1px #ccc; padding: 10px;"><h2 class="mt0">MIT License</h2><p>Copyright (c) 2017 Модули для DataLife Engine</p><p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p><p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p><p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p></div></div> <!-- .col col-mb-12 --></div> <!-- .content -->';

// Лицензионное соглашение
// Шаги установки модуля
/** @var array $config */
$steps = <<<HTML
<h2 class="mt0">Редактирование файлов</h2>
<ol>
	<li>
		Открыть файл <b>/templates/{$config['skin']}/main.tpl</b> 
	</li>
	<li>
		Добавить после <b>{AJAX}</b> или после <b>{jsfiles}</b>:
		<textarea readonly class="code" rows="1"><link href="{THEME}/blockpro/css/blockpro.css" rel="stylesheet" /></textarea>
	</li>
	<li>
		Добавить после <b>{AJAX}</b> или после <b>{jsfiles}</b>:
		<textarea readonly class="code" rows="1"><script src="{THEME}/blockpro/js/blockpro.js"></script></textarea>
		или
		<textarea readonly class="code" rows="1"><script src="{THEME}/blockpro/js/blockpro_new.js"></script></textarea>
		если хотите использовать возможность навигации по стрелкам браузера при ajax-переключении страниц модуля.
	</li>
	<li>Выполнить установку админчасти и таблиц модуля (кнопка ниже).</li>
</ol>
HTML;


function installer() {
	global $config, $dle_api, $cfg, $steps, $fileCharset, $licenseText;

	$output = $queriesTxt = '';

	$queries        = (count($cfg['queries'])) ? true : false;
	$adminInstalled = false;
	if ($cfg['installAdmin']) {
		$aq = $dle_api->db->super_query("SELECT name FROM " . PREFIX . "_admin_sections WHERE name = '{$cfg['moduleName']}'");

		$adminInstalled = ($aq['name'] == $cfg['moduleName']) ? true : false;

	}
	if (isset($_POST['notaccept']) && $cfg['showLicense'] && !$adminInstalled) {
		$output = <<<HTML
		<div class="content">
			<div class="col col-mb-12">
				<div class="alert">
					Вы отказались от установки модуля. <br>Не забудьте удалить загруженные файлы.
				</div>
			</div>
		</div>
HTML;
	} elseif (empty($_POST['accept']) && $cfg['showLicense'] && !$adminInstalled) {
		$output = <<<HTML
		<form method="post">
			<div class="content">
				<div class="col col-mb-12">
					$licenseText
				</div>
				<div class="col col-mb-12 mt30">
					<button type="submit" name="notaccept" value="y" class="btn btn-red">Не согласен</button>
					<button type="submit" name="accept" value="y" class="btn">Согласен, продолжить установку</button>
				</div>
			</div>
		</form>
HTML;
	} else {
		if ($queries) {
			foreach ($cfg['queries'] as $qq) {
				$queriesTxt .= '<textarea readonly class="code" rows="10">' . $qq . '</textarea>';
			}
		}


		// Если через $_POST передаётся параметр install, производим инсталляцию, согласно параметрам
		if (!empty($_POST['install'])) {
			// Выводим результаты  установки модуля
			$output .= '<div class="descr"><ul>';

			if ($queries) {
				// Выполняем запросы из массива.
				foreach ($cfg['queries'] as $q) {
					$query[] = $dle_api->db->query($q);
				}

				$output .= '<li><b>Запросы выполнены!</b></li>';
			}

			// Установка админки (http://dle-news.ru/extras/online/include_admin.html)
			if ($cfg['installAdmin']) {

				$install_admin = $dle_api->install_admin_module($cfg['moduleName'], $cfg['moduleTitle'], $cfg['moduleDescr'], $cfg['moduleName'] . '.png', $cfg['allowGroups']);

				if ($install_admin) {
					$output .= '<li><b>Админчасть модуля установлена</b></li>';
				}
			}

			$output .= '<li><b>Установка завершена!</b></li></ul></div>';
			$output .= '<div class="alert">Не забудьте удалить файлы установщика (blockpro_install.php и bp_check.php)!</div>';
			/** @var bool $install_admin */
			if ($cfg['installAdmin'] && $install_admin) {
				$output .= '<p><a class="btn" href="/' . $config['admin_path'] . '?mod=' . $cfg['moduleName'] . '" target="_blank" title="Перейти к управлению модулем">Настройка модуля</a></p> <hr>';
			}

		} // Если через $_POST передаётся параметр remove, производим удаление админчасти модуля
		elseif (!empty($_POST['remove'])) {
			$dle_api->uninstall_admin_module($cfg['moduleName']);
			$output .= '<div class="descr"><p><b>Админчасть модуля удалена</b></p></div>';
			$output .= '<div class="alert">Не забудьте удалить файл установщика!</div>';
		} // Если через $_POST ничего не передаётся, выводим форму для установки модуля
		else {
			// Выводим кнопку удаления  модуля
			if ($cfg['installAdmin'] && $adminInstalled) {
				$uninstallForm = <<<HTML
			<hr>
			<div class="form-field clearfix">
				<div class="h2">Удаление админчасти модуля</div>
				<form method="POST">
					<input type="hidden" name="remove" value="1">
					<input type="hidden" name="accept" value="y">
					<button class="btn btn-red" type="submit">Удалить админчасть модуля</button>
				</form>
			</div>
HTML;
			}
			// Выводим кнопку установки модуля с допзпросами
			if ($queries) {
				$installForm = <<<HTML
			<div class="form-field clearfix">
				<form method="POST">
					<input type="hidden" name="install" value="1">
					<input type="hidden" name="accept" value="y">
					<button class="btn btn-blue" type="submit">Установить модуль</button>
					<span id="wtq" class="btn btn-normal btn-border btn-gray">Какие запросы будут выполнены?</span>
				</form>
			</div>
			<div class="queries clearfix hide">
				<div class="content">
					$queriesTxt
				</div>
			</div>
HTML;
			} // Выводим кнопку установки админчасти модуля
			else {
				if (!$adminInstalled) {
					$installForm = <<<HTML
				<div class="form-field clearfix">
					<div class="label">Установка админчасти</div>
					<div class="control">
						<form method="POST">
							<input type="hidden" name="install" value="1">
							<button class="btn" type="submit">Установить админчасть модуля</button>
						</form>
					</div>
				</div>
HTML;
				}
			}

			// Вывод
			if ($cfg['steps']) {
				$output .= $steps;
			}
			/** @var string $installForm */
			/** @var string $uninstallForm */
			$output .= <<<HTML
			<p class="alert">Перед установкой модуля обязательно <a href="/{$config['admin_path']}?mod=dboption" target="_blank" title="Открыть инструменты работы с БД DLE в новом окне">сделайте бэкап БД</a>!</p>
			<div class="descr">
				<h2>Установка таблиц модуля и админчасти</h2>

				$installForm
				$uninstallForm
			</div>
HTML;


		}

	}


	// Если руки пользователя кривые, или он просто забыл перекодировать файлы - скажем ему об этом.
	if ($fileCharset['conflict']) {
		$output = '<h2 class="red ta-center">Ошибка!</h2><p class="alert">Кодировка файла установщика (<b>' . $fileCharset['charset'] . '</b>) не совпадает с кодировкой сайта (<b>' . $config['charset'] . '</b>). <br />Установка не возможна. <br />Перекодируйте все php файлы модуля и запустите установщик ещё раз.</p> <hr />';
	}

	// Функция возвращает то, что должно быть выведено
	return $output;
}

/**
 * Отлавливаем данные о кодировке файла (utf-8 или windows-1251);
 *
 * @param  string $string - строка (или массив), в которой требуется определить кодировку.
 *
 * @return array          - возвращает массив с определением конфликта кодировки строки и сайта, а так же сму кодировку
 *                        строки.
 */
function chasetConflict($string) {
	global $config;
	if (is_array($string)) {
		$string = implode(' ', $string);
	}
	$detect             = preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)+%xs', $string);
	$stringCharset      = ($detect == '1') ? 'utf-8' : 'windows-1251';
	$config['charset']  = strtolower($config['charset']);
	$return             = [];
	$return['conflict'] = ($stringCharset == $config['charset']) ? false : true;
	$return['charset']  = $stringCharset;

	return $return;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php echo $fileCharset['charset'] ?>">
	<title><?php echo $cfg['moduleTitle'] ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>

		.content {
			margin: 0 auto
		}

		.content:after, .content:before {
			content: " ";
			display: table
		}

		.content:after {
			clear: both
		}

		.content .content {
			margin-left: -10px;
			margin-right: -10px
		}

		.col {
			padding-left: 10px;
			padding-right: 10px;
			min-height: 1px;
			float: left;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box
		}

		.col-mb-12 {
			width: 100%
		}

		.col-mb-11 {
			width: 91.666666666667%
		}

		.col-mb-10 {
			width: 83.333333333333%
		}

		.col-mb-9 {
			width: 75%
		}

		.col-mb-8 {
			width: 66.666666666667%
		}

		.col-mb-7 {
			width: 58.333333333333%
		}

		.col-mb-6 {
			width: 50%
		}

		.col-mb-5 {
			width: 41.666666666667%
		}

		.col-mb-4 {
			width: 33.333333333333%
		}

		.col-mb-3 {
			width: 25%
		}

		.col-mb-2 {
			width: 16.666666666667%
		}

		.col-mb-1 {
			width: 8.3333333333333%
		}

		@media (min-width: 768px) {
			.content {
				max-width: 728px
			}

			.col {
				float: left
			}

			.col-12 {
				width: 100%
			}

			.col-11 {
				width: 91.666666666667%
			}

			.col-10 {
				width: 83.333333333333%
			}

			.col-9 {
				width: 75%
			}

			.col-8 {
				width: 66.666666666667%
			}

			.col-7 {
				width: 58.333333333333%
			}

			.col-6 {
				width: 50%
			}

			.col-5 {
				width: 41.666666666667%
			}

			.col-4 {
				width: 33.333333333333%
			}

			.col-3 {
				width: 25%
			}

			.col-2 {
				width: 16.666666666667%
			}

			.col-1 {
				width: 8.3333333333333%
			}
		}

		@media (min-width: 992px) {
			.content {
				max-width: 952px
			}

			.col {
				float: left
			}

			.col-dt-12 {
				width: 100%
			}

			.col-dt-11 {
				width: 91.666666666667%
			}

			.col-dt-10 {
				width: 83.333333333333%
			}

			.col-dt-9 {
				width: 75%
			}

			.col-dt-8 {
				width: 66.666666666667%
			}

			.col-dt-7 {
				width: 58.333333333333%
			}

			.col-dt-6 {
				width: 50%
			}

			.col-dt-5 {
				width: 41.666666666667%
			}

			.col-dt-4 {
				width: 33.333333333333%
			}

			.col-dt-3 {
				width: 25%
			}

			.col-dt-2 {
				width: 16.666666666667%
			}

			.col-dt-1 {
				width: 8.3333333333333%
			}
		}

		@media (min-width: 1200px) {
			.content {
				max-width: 1160px
			}

			.col {
				float: left
			}

			.col-ld-12 {
				width: 100%
			}

			.col-ld-11 {
				width: 91.666666666667%
			}

			.col-ld-10 {
				width: 83.333333333333%
			}

			.col-ld-9 {
				width: 75%
			}

			.col-ld-8 {
				width: 66.666666666667%
			}

			.col-ld-7 {
				width: 58.333333333333%
			}

			.col-ld-6 {
				width: 50%
			}

			.col-ld-5 {
				width: 41.666666666667%
			}

			.col-ld-4 {
				width: 33.333333333333%
			}

			.col-ld-3 {
				width: 25%
			}

			.col-ld-2 {
				width: 16.666666666667%
			}

			.col-ld-1 {
				width: 8.3333333333333%
			}
		}

		.center-block {
			margin: 0 auto
		}

		html {
			font-family: sans-serif;
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%
		}

		body {
			margin: 0
		}

		article, aside, details, figcaption, figure, footer, header, main, nav, section, summary {
			display: block
		}

		audio, canvas, progress, video {
			display: inline-block;
			vertical-align: baseline
		}

		audio:not([controls]) {
			display: none;
			height: 0
		}

		[hidden], template {
			display: none
		}

		a {
			background: 0 0
		}

		a:active, a:hover {
			outline: 0
		}

		abbr[title] {
			border-bottom: 1px dotted
		}

		b, strong {
			font-weight: 700
		}

		dfn {
			font-style: italic
		}

		h1 {
			margin: .67em 0
		}

		mark {
			background: #ff0;
			color: #000
		}

		small {
			font-size: 80%
		}

		sub, sup {
			font-size: 75%;
			line-height: 0;
			position: relative;
			vertical-align: baseline
		}

		sup {
			top: -.5em
		}

		sub {
			bottom: -.25em
		}

		img {
			border: 0
		}

		svg:not(:root) {
			overflow: hidden
		}

		figure {
			margin: 1em 40px
		}

		hr {
			-moz-box-sizing: content-box;
			box-sizing: content-box
		}

		pre {
			overflow: auto
		}

		code, kbd, pre, samp {
			font-family: monospace, monospace;
			font-size: 1em
		}

		button, input, optgroup, select, textarea {
			color: inherit;
			font: inherit;
			margin: 0
		}

		button {
			overflow: visible
		}

		button, select {
			text-transform: none
		}

		button, html input[type=button], input[type=reset], input[type=submit] {
			-webkit-appearance: button;
			cursor: pointer
		}

		button[disabled], html input[disabled] {
			cursor: default
		}

		button::-moz-focus-inner, input::-moz-focus-inner {
			border: 0;
			padding: 0
		}

		input {
			line-height: normal
		}

		input[type=checkbox], input[type=radio] {
			box-sizing: border-box;
			padding: 0
		}

		input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button {
			height: auto
		}

		input[type=search] {
			-webkit-appearance: textfield;
			-moz-box-sizing: content-box;
			-webkit-box-sizing: content-box;
			box-sizing: content-box
		}

		input[type=search]::-webkit-search-cancel-button, input[type=search]::-webkit-search-decoration {
			-webkit-appearance: none
		}

		legend {
			border: 0;
			padding: 0
		}

		textarea {
			overflow: auto
		}

		optgroup {
			font-weight: 700
		}

		td, th {
			padding: 0
		}

		.btn {
			display: inline-block;
			color: #fff;
			margin-bottom: 0;
			font-weight: 400;
			text-align: center;
			vertical-align: middle;
			cursor: pointer;
			background: #4a9fc5;
			border: 0;
			text-decoration: none;
			white-space: nowrap;
			padding: 10px 15px 8px;
			font-size: 18px;
			line-height: 20px;
			border-radius: 3px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			-webkit-transition: all ease .3s;
			-moz-transition: all ease .3s;
			-o-transition: all ease .3s;
			transition: all ease .3s;
			-webkit-box-shadow: 0 2px 0 #3584a7;
			-moz-box-shadow: 0 2px 0 #3584a7;
			box-shadow: 0 2px 0 #3584a7
		}

		.btn:focus {
			outline: #333 dotted thin;
			outline: -webkit-focus-ring-color auto 5px;
			outline-offset: -2px
		}

		.btn.active, .btn:focus, .btn:hover {
			color: #fff;
			background: #50bd98;
			text-decoration: none;
			-webkit-box-shadow: 0 2px 0 #3c9e7d;
			-moz-box-shadow: 0 2px 0 #3c9e7d;
			box-shadow: 0 2px 0 #3c9e7d
		}

		.btn.active, .btn:active {
			outline: 0;
			-webkit-box-shadow: 0 2px 0 #3c9e7d;
			-moz-box-shadow: 0 2px 0 #3c9e7d;
			box-shadow: 0 2px 0 #3c9e7d
		}

		.btn.disabled, .btn[disabled], fieldset[disabled] .btn {
			cursor: not-allowed;
			pointer-events: none;
			opacity: .65;
			-webkit-box-shadow: 0 2px 0 #4a9fc5;
			-moz-box-shadow: 0 2px 0 #4a9fc5;
			box-shadow: 0 2px 0 #4a9fc5
		}

		.btn-red {
			background: #c70000;
			-webkit-box-shadow: 0 2px 0 #940000;
			-moz-box-shadow: 0 2px 0 #940000;
			box-shadow: 0 2px 0 #940000
		}

		.btn-red:focus, .btn-red:hover {
			background: #940000;
			-webkit-box-shadow: 0 2px 0 #610000;
			-moz-box-shadow: 0 2px 0 #610000;
			box-shadow: 0 2px 0 #610000
		}

		.btn-red.active, .btn-red:active {
			background: #940000;
			-webkit-box-shadow: 0 2px 0 #940000;
			-moz-box-shadow: 0 2px 0 #940000;
			box-shadow: 0 2px 0 #940000
		}

		.btn-link {
			color: #4a9fc5;
			font-weight: 400;
			cursor: pointer;
			padding: 5px 8px 3px;
			font-size: 16px;
			line-height: 20px;
			border-radius: 3px
		}

		.btn-link, .btn-link:active, .btn-link[disabled], fieldset[disabled] .btn-link {
			background-color: transparent;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-link:focus, .btn-link:hover {
			background-color: transparent;
			color: #50bd98;
			text-decoration: underline;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-link[disabled]:focus, .btn-link[disabled]:hover, fieldset[disabled] .btn-link:focus, fieldset[disabled] .btn-link:hover {
			color: #f3f6f7;
			text-decoration: none;
			background-color: transparent;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-big {
			padding: 15px 25px;
			font-size: 20px;
			line-height: 20px;
			border-radius: 3px;
			margin-bottom: 2px
		}

		.btn-normal {
			padding: 8px 12px;
			font-size: 14px;
			line-height: 14px;
			border-radius: 3px
		}

		.btn-small {
			padding: 5px 8px 3px;
			font-size: 12px;
			line-height: 20px;
			border-radius: 3px;
			margin-bottom: 2px
		}

		.btn-mini {
			padding: 2px 9px 1px;
			font-size: 12px;
			line-height: 20px;
			border-radius: 3px;
			margin-bottom: 2px
		}

		.btn-border {
			background: 0 0;
			color: #4a9fc5;
			border: 1px solid #4a9fc5;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-border:focus, .btn-border:hover {
			color: #fff;
			background: rgba(74, 159, 197, .8);
			text-decoration: none;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-border.active, .btn-border:active {
			color: #fff;
			background: #4a9fc5;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-border.btn-red {
			color: #c70000;
			border: 1px solid #c70000
		}

		.btn-border.btn-red:focus, .btn-border.btn-red:hover {
			color: #fff;
			background: rgba(199, 0, 0, .8);
			text-decoration: none;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn-border.btn-red.active, .btn-border.btn-red:active {
			color: #fff;
			background: #c70000;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}

		.btn + .btn {
			margin-left: 15px
		}

		.btn-super {
			padding: 15px;
			text-transform: uppercase;
			font-size: 22px
		}

		.btn-square {
			-webkit-border-radius: 0;
			-moz-border-radius: 0;
			border-radius: 0
		}

		.btn-block {
			display: block;
			width: 100%;
			padding-left: 0;
			padding-right: 0;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box
		}

		.btn-block + .btn-block {
			margin-top: 5px
		}

		input[type=submit].btn-block, input[type=reset].btn-block, input[type=button].btn-block {
			width: 100%
		}

		.input {
			display: inline-block;
			height: 30px;
			padding: 10px;
			position: relative;
			margin-bottom: 10px;
			font-size: 16px;
			color: #545d70;
			vertical-align: middle;
			border: 1px solid #868fa4;
			outline: 0;
			background-color: #fff;
			-webkit-appearance: none;
			-webkit-box-shadow: 0 0 0 2px transparent;
			-moz-box-shadow: 0 0 0 2px transparent;
			box-shadow: 0 0 0 2px transparent;
			-webkit-transition: all ease .3s;
			-moz-transition: all ease .3s;
			-o-transition: all ease .3s;
			transition: all ease .3s;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box
		}

		.input:focus {
			outline: 0;
			border-color: #4a9fc5;
			-webkit-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			-moz-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			box-shadow: 0 0 0 2px rgba(74, 159, 197, .5)
		}

		textarea.input {
			height: auto;
			border: 1px solid #868fa4;
			background: #fff
		}

		textarea.input:focus {
			background: #fff;
			border-color: #4a9fc5
		}

		input[type=number].input {
			padding: 0 10px
		}

		.input-big {
			height: 40px
		}

		.input-rounded {
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px
		}

		.input-block {
			width: 100%
		}

		.checkbox {
			display: none
		}

		.checkbox + label {
			cursor: pointer;
			margin-top: 4px;
			display: inline-block
		}

		.checkbox + label span {
			display: inline-block;
			width: 18px;
			height: 18px;
			margin: -2px 4px 0 0;
			vertical-align: middle;
			background: #fff;
			cursor: pointer;
			border: 1px solid #868fa4;
			position: relative
		}

		.checkbox + label:focus span, .checkbox + label:hover span {
			border-color: #4a9fc5;
			-webkit-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			-moz-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			box-shadow: 0 0 0 2px rgba(74, 159, 197, .5)
		}

		.checkbox:checked + label span {
			border-color: #4a9fc5
		}

		.checkbox:checked + label span:before {
			content: ' ';
			position: absolute;
			border: solid #000;
			border-width: 0 0 2px 2px;
			height: 5px;
			width: 11px;
			left: 3px;
			top: 4px;
			-webkit-transform: rotate(-45deg);
			-ms-transform: rotate(-45deg);
			transform: rotate(-45deg)
		}

		.radio {
			display: none
		}

		.radio + label {
			cursor: pointer;
			margin-top: 4px;
			display: inline-block
		}

		.radio + label span {
			display: inline-block;
			width: 18px;
			height: 18px;
			margin: -2px 4px 0 0;
			vertical-align: middle;
			background: #fff;
			cursor: pointer;
			border: 1px solid #868fa4;
			position: relative;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px
		}

		.radio + label:focus span, .radio + label:hover span {
			border-color: #4a9fc5;
			-webkit-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			-moz-box-shadow: 0 0 0 2px rgba(74, 159, 197, .5);
			box-shadow: 0 0 0 2px rgba(74, 159, 197, .5)
		}

		.radio:checked + label span {
			border-color: #4a9fc5
		}

		.radio:checked + label span:before {
			content: ' ';
			position: absolute;
			height: 8px;
			width: 8px;
			background: #000;
			left: 5px;
			top: 5px;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px
		}

		textarea.code {
			width: 100%;
			margin: 10px 0;
			vertical-align: top;
			-webkit-transition: height .2s;
			-moz-transition: height .2s;
			transition: height .2s;
			outline: 0;
			display: block;
			color: #c70000;
			padding: 5px 10px;
			font: 400 14px/20px Consolas, 'Courier New', monospace;
			background-color: #f3f6f7;
			white-space: pre;
			white-space: pre-wrap;
			word-break: break-all;
			word-wrap: break-word;
			text-shadow: none;
			border: none;
			border-left: solid 3px #c70000;
			box-sizing: border-box
		}

		textarea.code:focus {
			background: #d4dfe3;
			border-color: #4a9fc5
		}

		table {
			max-width: 100%;
			background-color: transparent;
			border-collapse: collapse;
			border-spacing: 0
		}

		* html body, html {
			height: 100%
		}

		body {
			height: auto !important;
			height: 100%;
			min-height: 100%;
			position: relative
		}

		button, html, input, select, textarea {
			color: #545d70
		}

		body {
			font-size: 16px;
			line-height: 1.4;
			font-family: Arial, Helvetica, sans-serif;
			color: #545d70;
			background: #fff
		}

		::-moz-selection {
			background: #4e9cb5;
			color: #fff;
			text-shadow: none;
			text-shadow: 0 1px 1px rgba(0, 0, 0, .4)
		}

		::selection {
			background: #4e9cb5;
			color: #fff;
			text-shadow: none;
			text-shadow: 0 1px 1px rgba(0, 0, 0, .4)
		}

		hr {
			display: block;
			height: 1px;
			border: 0;
			border-top: 1px solid #d4dfe3;
			margin: 1em 0;
			padding: 0
		}

		hr.blue {
			border-color: #3c93ba
		}

		img {
			vertical-align: middle;
			max-width: 100%
		}

		fieldset {
			border: 0;
			margin: 0;
			padding: 0
		}

		textarea {
			resize: vertical
		}

		.pseudolink, a {
			color: #4a9fc5;
			text-decoration: underline
		}

		.pseudolink.active, .pseudolink:hover, a.active, a:hover {
			color: #50bd98;
			text-decoration: none
		}

		.pseudolink, [data-target-blank], [data-target-self] {
			cursor: pointer
		}

		.h1, h1 {
			font: 400 38px Helvetica, Arial, sans-serif;
			font-weight: 300;
			margin-top: 0;
			color: #4a9fc5
		}

		.h2, h2 {
			font: 400 29px Helvetica, Arial, sans-serif;
			font-weight: 300;
			color: #4a9fc5;
			margin: 1em 0
		}

		.h2 a, h2 a {
			text-decoration: none;
			color: #000;
			-webkit-transition: all ease .3s;
			-moz-transition: all ease .3s;
			-o-transition: all ease .3s;
			transition: all ease .3s
		}

		.h2 a:hover, h2 a:hover {
			color: #c70000
		}

		.top_nav-container {
			background-color: #4a9fc5;
			background-position: 50% 60% !important;
			color: #fff;
			padding: 0;
			margin-bottom: 20px
		}

		.main .top_nav-container {
			margin-bottom: 0
		}

		.container {
			padding: 20px 0
		}

		.container.container-dark {
			background: #868fa4;
			color: #fff
		}

		.container.top_nav-container {
			padding: 0
		}

		.container.container-blue {
			background: #4a9fc5;
			color: #fff
		}

		.container.container-blue.footer-container {
			background-position: 50% 100%
		}

		@media (max-width: 768px) {
			.container {
				padding: 20px 10px
			}
		}

		header .btn {
			margin: 0 5px 8px 0
		}

		.logo {
			display: inline-block;
			margin-top: 15px;
			margin-bottom: 15px
		}

		.margin-list > li {
			margin-bottom: 10px
		}

		code {
			padding: 2px 4px;
			color: #c70000;
			background-color: #f3f6f7;
			border: 1px solid #d4dfe3;
			font-size: 12px;
			border-radius: 5px
		}

		.footer-container a {
			color: #fff
		}

		ol.unstyled, ul.unstyled {
			margin: 0;
			padding: 0;
			list-style: none
		}

		.ta-center, .table td.ta-center, .table th.ta-center {
			text-align: center
		}

		img.ta-center {
			display: block;
			margin: 0 auto
		}

		.ta-left, .table td.ta-left, .table th.ta-left {
			text-align: left
		}

		.ta-right, .table td.ta-right, .table th.ta-right {
			text-align: right
		}

		.td-n {
			text-decoration: none
		}

		.hide {
			display: none
		}

		.d-b, .show {
			display: block
		}

		.d-ib, .inline-block {
			display: inline-block
		}

		.p-r {
			position: relative
		}

		.ir {
			background-color: transparent;
			border: 0;
			overflow: hidden
		}

		.ir:before {
			content: "";
			display: block;
			width: 0;
			height: 100%
		}

		.hidden {
			display: none !important;
			visibility: hidden
		}

		.visuallyhidden {
			border: 0;
			clip: rect(0 0 0 0);
			height: 1px;
			margin: -1px;
			overflow: hidden;
			padding: 0;
			position: absolute;
			width: 1px
		}

		.visuallyhidden.focusable:active, .visuallyhidden.focusable:focus {
			clip: auto;
			height: auto;
			margin: 0;
			overflow: visible;
			position: static;
			width: auto
		}

		.invisible {
			visibility: hidden
		}

		.pl20 {
			padding-left: 20px
		}

		.m0 {
			margin: 0
		}

		.mt0 {
			margin-top: 0
		}

		.mt30 {
			margin-top: 30px
		}

		.mb0 {
			margin-bottom: 0
		}

		.p0 {
			padding: 0
		}

		.pt0 {
			padding-top: 0
		}

		.pb0 {
			padding-bottom: 0
		}

		.text-muted {
			color: #a3aaba
		}

		.text-text {
			color: #545d70
		}

		.text-red {
			color: #c70000
		}

		.text-green {
			color: #50bd98
		}

		.text-orange {
			color: #e67e22
		}

		.alert {
			border: 1px solid #f1c40f;
			background: rgba(241, 196, 15, .1);
			color: #796307;
			padding: 20px
		}

		.alert-info {
			color: #3d7e93;
			background: rgba(74, 159, 197, .1);
			border-color: #4a9fc5
		}

		.clearfix:after, .clearfix:before {
			content: " ";
			display: table
		}

		.clearfix:after {
			clear: both
		}

		.clr {
			clear: both;
			height: 0;
			overflow: hidden
		}

		.fleft {
			float: left
		}

		.fright {
			float: right
		}

		.ov-h {
			overflow: hidden;
		}
	</style>
</head>

<body>
	<div class="body-wrapper clearfix">
		<header class="container top_nav-container container-blue">
			<div class="content">
				<div class="col col-mb-12 ta-center">
					<a href="/" class="logo" title="<?php echo $cfg['moduleTitle'] ?>">
						<img src="http://bp.pafnuty.name/images/logo.png" alt="<?php echo $cfg['moduleTitle'] ?>"/>
					</a>
				</div>
			</div>
		</header>
		<div class="container pb0">
			<div class="content">
				<div class="col col-mb-12 ta-center">
					<h1><?php echo $cfg['moduleTitle'] ?> v.<?php echo $cfg['moduleVersion'] ?>
						от <?php echo $cfg['moduleDate'] ?></h1>
					<div class="text-muted">Установка модуля</div>
					<hr>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="content">
				<div class="col col-mb-12">
					<?php
					$output = installer();
					echo $output;
					?>
				</div>
			</div>
		</div>

		<div class="container pt0">
			<div class="content">
				<div class="col col-mb-12">
					<hr class="mt0">
					Контакты для связи и техподдержки:<br>
					<a href="https://github.com/dle-modules/DLE-BlockPro" target="_blank"
					   title="Сайт поддержки">BlockPro</a> — техподдержка <br>
					<a href="http://bp.pafnuty.name/" target="_blank"
					   title="Официальный сайт модуля">bp.pafnuty.name</a> — документация <br>
				</div>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script>
			$(document)
				.on('click', '.code', function () {
					$(this).select();
				})
				.on('click', '#wtq', function () {
					$('.queries').slideToggle(400);
					$(this).toggleClass('active');
				});
		</script>
	</div>
	<!-- .body-wrapper clearfix -->
</body>
</html>
