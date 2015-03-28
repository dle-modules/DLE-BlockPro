<?php
/**
 * ===============================================================
 * Файл: less-verstka.php
 * ---------------------------------------------------------------
 * Версия: 2.1.2 (30.12.2013)
 * ===============================================================
 * 
 * Использование: 
 * ---------------------------------------------------------------
 * Где нибудь в начале header.php прописать:
	<?require_once($_SERVER['DOCUMENT_ROOT'].'/res/less/less.php');?>
 * Все настройки чуть ниже.
 * ===============================================================
 */


/**
 * Настройки компиляции
 */
define('LANG_CHARSET', 'UTF-8');

// Ведение лог-файла
$lessLog      = true;			// Вести лог-файл с отображением времени выполнения компиляции. true включит ведение лога.
$lessFileSize = '15';			// максимальный размер файла лога, в килобайтах (если размер файла будет больше, он удалится).
$lessLogFile  = 'less-log';		// Имя лог-файла. Файл является html-страницей и записывается в корень сайта.

// Определяем входящий и выходящий файлы и определяем сжимать или нет выходящий файл.
$inputFile    = $_SERVER['DOCUMENT_ROOT'].'/local/codenails/less/template_styles.less'; // Файл template_styles.less, лежащий в текущем шаблоне сайта
$outputFile   = str_ireplace('less', 'css', $inputFile); // Файл template_styles.css - который подключается к шаблону
$normal       = false;			// true для отключения сжатия выходящего файла.
$alertError	  = false;			// false для показа ошибок компиляции вверху страницы (по умолчанию показываются js-алертом);

$copyText = '@author: Павел Белоусов (www.info-expert.ru)'; // Текст, который будет записан в начало файла CSS вместе со статистикой


/**
 * Конец настроек
 */
// Если включено логирование - "запускаем счётчик времени".
if($lessLog) {
	$timeStart = microtime(true);
	$logError = '';
}


// Выполняем функцию компиляции
try {
	autoCompileLess($inputFile, $outputFile, $normal, $copyText);
} catch (exception $e) {
	// Если что-то пошло не так - скажем об этом пользователю способом, указанным в настройках и запишем в лог.
	$logError = str_replace($_SERVER['DOCUMENT_ROOT'], '', $e->getMessage());
	$showError = ($alertError) ? '<script>alert("Less error: '.str_replace('"', ' ', $logError).'")</script>' : '<div style="text-align: center; background: #fff; color: red; padding: 5px;">Less error: '.$logError.'</div>';

	echo $showError;


}

// Если разрешено, то пишем лог-файл с временем выполнения компиляции less-файлов :)
if($lessLog) {
	$timeStop = microtime(true);
	$lessLog = round(($timeStop - $timeStart), 6);
	$textColor = ($lessLog > '0.001') ? 'red' : 'green';
	$mem_usg = '';
	$lessLogFile = $_SERVER['DOCUMENT_ROOT'].'/'.$lessLogFile.'.html';
	if(function_exists("memory_get_peak_usage")) $mem_usg = round(memory_get_peak_usage()/(1024*1024),2)."Мб";
	if ((file_exists($lessLogFile) && filesize($lessLogFile) > $lessFileSize*1024)) {
		unlink($lessLogFile);
	}
	if (!file_exists($lessLogFile)) {
			$cLessFile = fopen($lessLogFile, "wb");
			$firstText = "
				<!DOCTYPE html>
				<html lang='ru'>
				<head>
					<title>Лог времени выполнения компиляции LESS</title>
					<meta charset='".LANG_CHARSET."'>
					<style>
						a {display: inline-block;margin-bottom: 5px;}
						.red {color: red;}
						.green {color: green;}
						table {margin: 50px auto; border-collapse: collapse;border: solid 1px #ccc; font: normal 14px Arial, Helvetica, sans-serif;}
						th b {cursor: help; color: #c00;}
						td {text-align: right;}
						th, td {font-size: 12px; border: solid 1px #ccc; padding: 5px 8px;}
						td:first-child {text-align: left;}
						tr:hover {background: #f0f0f0; color: #1d1d1d;}
					</style>
					<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
					<script>
						// Скрипт посчета среднего значения
						$.fn.getZnach = function (prop) {
							var options = $.extend({
								source: 'с',
								ins: '',
								quant: '5'
							}, prop);

							var summ = 0;
							this.each(function (i) {
								summ += +($(this).text().replace(/,/, '.').replace(options.source, ''));
							});
							$(options.ins).append('<br /><b title=\"Cреднее значение\">' + (summ / this.length).toFixed(options.quant) + options.source + '</b>');
						}
						// Инициализация скрипта
						jQuery(function ($) {
							$('td.timer').getZnach({
								ins: 'th.timer'
							});
							$('td.mem_usg').getZnach({
								source: 'Мб',
								ins: 'th.mem_usg',
								quant: '2'
							});
						});
					</script>
				</head>
				<body>
					<table class='stattable'>
						<tr>
							<th scope='col' class='queries'>Дата записи</th>
							<th scope='col' class='timer'>Вемя выполнения компилятора</th>
							<th scope='col' class='mem_usg'>Затраты памяти</th>
						</tr>
					\r\n</table></body></html>";
			fwrite($cLessFile, $firstText);
			fclose($cLessFile);

		} else {
			$cLessFileArr = file($lessLogFile);
			$lastLine = array_pop($cLessFileArr);
			$newText = implode("", $cLessFileArr);

			$newTextAdd = "добавляем строку, не спрашивайте, так надо!\r\n";
			if($logError) {
				$newTextAdd = "
					<tr>
						<td class='queries'>".date('Y-m-d H:i:s')."</td>
						<td colspan='2'><b class='red'>Ошибка: </b>".$logError."</td>
					</tr>\r\n";
			} else {
				$newTextAdd = "	
					<tr>
						<td class='queries'>".date('Y-m-d H:i:s')."</td>
						<td class='timer ".$textColor."'><b>".$lessLog."с</b></td>
						<td class='mem_usg'>".$mem_usg."</td>
					</tr>\r\n";
				
			}


			$cLessFile = fopen($lessLogFile, "w");	

			fwrite($cLessFile, $newText.$newTextAdd.$lastLine);
			fclose($cLessFile);
		}
	}

	/**
	 * Функция автокомпиляции less, запускается даже если изменён импортированный файл - очень удобно.
	 * функция взята из документации к классу и на просторах интернета.
	 * @param string $inpFile - входной файл (в котором могут быть и импортированные файлы)
	 * @param string $outFile - выходной файл
	 * @param string $nocompress - отключает сжатие выходного файла
	 * @return file
	 */
	function autoCompileLess($inpFile, $outFile, $nocompress = false, $copy) {
		
		$cacheFile = $inpFile.".cache";

		if (file_exists($cacheFile)) {
			$cache = unserialize(file_get_contents($cacheFile));
		} else {
			$cache = $inpFile;
		}

		// Подключаем класс для компиляции less 
		require "lessphp.class.php";
		$less = new lessc;
		if ($nocompress) {
			// Если запрещено сжатие - форматируем по нормальному с табами вместо пробелов.
			$formatter = new lessc_formatter_classic;
	        $formatter->indentChar = "\t";
	        $less->setFormatter($formatter);
		} else {
			// Иначе сжимаем всё в одну строку.
			$less->setFormatter('compressed');
		}
		// Массив с данными разультата компиляции
		$newCache = $less->cachedCompile($cache);

		// Выдёргиваем имена импортируемых файлов
		$sourceFiles = array();
		foreach ($cache["files"] as $key => $source) {
			$sourceFiles[] = basename($key);
		}

		// Добавляем копирайты и информацию по файлам в начало.
		$copy = '
/*! =========================================================================
   @outputFile: '.basename($outFile).'
   @inputFiles: '.implode(', ',$sourceFiles).'
   @date: '.date('Y-m-d H:i:s').'
   '.$copy.' */
/* ========================================================================== */

';

		if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($outFile, $copy.$newCache['compiled']);
		}
	}

?>