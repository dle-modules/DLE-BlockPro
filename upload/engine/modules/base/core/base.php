<?php
/*
=============================================================================
BASE - базовый класс для модулей
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

if (!defined('DATALIFEENGINE')) {
	die("Go fuck yourself!");
}

define('BASE_DIR', ENGINE_DIR . '/modules/base');

include('db.class.php');
require_once('Fenom.php');
\Fenom::registerAutoload();

require_once(ENGINE_DIR . '/modules/functions.php');

/**
 * BaseClass
 */
class base {

	public $result;

	var $dle_config = array();
	var $cfg = array();
	var $db = false;
	var $tpl = false;
	var $tplOptions = array();

	function __construct($dle_config, $cfg, $tplOptions) {
		// Подрубаем конфиг DLE
		$this->dle_config = $dle_config;

		// Подрубаем конфиг модуля
		$this->cfg = $cfg;

		// Подрубаем переменные шаблонизатора
		$this->tplOptions = $tplOptions;

		// Подрубаем класс для работы с БД
		$this->db = new SafeMysql(array(
				'host'    => DBHOST,
				'user'    => DBUSER,
				'pass'    => DBPASS,
				'db'      => DBNAME,
				'charset' => COLLATE)
		);

		// Подрубаем шаблонизатор
		$this->tpl = Fenom::factory(
			ROOT_DIR . '/templates/' . $this->dle_config['skin'] . '/',
			ENGINE_DIR . '/cache/',
			$this->tplOptions
		);

		// Добавляем свой модификатор в шаблонизатор для ограничения кол-ва символов в тексте
		$this->tpl->addModifier(
			'limit', function ($data, $limit, $etc = '&hellip;', $wordcut = false) {
				return textLimit($data, $limit, $etc, $wordcut);
			}
		);

		// Добавляем свой модификатор в шаблонизатор для вывода картинок
		$this->tpl->addModifier(
			'image', function ($data, $noimage = '', $imageType = 'small', $number, $size, $quality, $resizeType = 'auto', $grabRemote = true, $showSmall = false, $subdir = false) {
				return getImage($data, $noimage, $imageType, $number, $size, $quality, $resizeType, $grabRemote, $showSmall, $subdir);
			}
		);

		// Добавляем свой модификатор в шаблонизатор для вывода картинок
		$this->tpl->addModifier(
			'declination', function ($n, $word) {
				return declinationWords($n, $word);
			}
		);

		// Добавляем свой модификатор в шаблонизатор для вывода форматированной даты
		$this->tpl->addModifier(
			'dateformat', function ($data, $_f = false) {
				return formateDate($data, $_f);
			}
		);

		// Добавляем свой модификатор в шаблонизатор для вывода форматированной даты
		$this->tpl->addModifier(
			'catinfo', function ($data, $info = false, $noicon = false) {
				return getCatInfo($data, $info, $noicon);
			}
		);
	}

	/**
	 * @param $data - массив с информацией о статье
	 *
	 * @return string URL для категории
	 */

	public function getPostUrl($data) {
		global $cat_info;

		if ($this->dle_config['allow_alt_url'] && $this->dle_config['allow_alt_url'] != 'no') {
			if (
				($this->dle_config['version_id'] < 9.6 && $this->dle_config['seo_type'])
				||
				($this->dle_config['version_id'] >= 9.6 && ($this->dle_config['seo_type'] == 1 || $this->dle_config['seo_type'] == 2))
			) {
				if (intval($data['category']) && $this->dle_config['seo_type'] == 2) {
					$url = $this->dle_config['http_home_url'] . get_url(intval($data['category'])) . '/' . $data['id'] . '-' . $data['alt_name'] . '.html';
				} else {
					$url = $this->dle_config['http_home_url'] . $data['id'] . '-' . $data['alt_name'] . '.html';
				}
			} else {
				$url = $this->dle_config['http_home_url'] . date('Y/m/d/', $data['date']) . $data['alt_name'] . '.html';
			}
		} else {
			$url = $this->dle_config['http_home_url'] . 'index.php?newsid=' . $data['id'];
		}

		return $url;
	}

	/**
	 * Получение диапазона между двумя цифрами, и не только
	 *
	 * @param bool $diapazone
	 * @param bool $subcats
	 *
	 * @internal param string $diapasone
	 * @return string
	 * @author   Elkhan I. Isaev <elhan.isaev@gmail.com>
	 */

	public function getDiapazone($diapazone = false, $subcats = false) {
		if ($diapazone !== false) {
			$diapazone = str_replace(" ", "", $diapazone);

			if (strpos($diapazone, ',') !== false) {
				$diapazoneArray = explode(',', $diapazone);
				$diapazoneArray = array_diff($diapazoneArray, array(NULL));

				foreach ($diapazoneArray as $v) {
					if (strpos($v, '-') !== false) {
						preg_match("#(\d+)-(\d+)#i", $v, $test);

						$diapazone = !empty($diapazone) && is_array($diapazone) ?
							array_merge($diapazone, (!empty ($test) ? range($test[1], $test[2]) : array()))
							: (!empty ($test) ? range($test[1], $test[2]) : array());

					} else {
						$diapazone = !empty($diapazone) && is_array($diapazone) ?
							array_merge($diapazone, (!empty ($v) ? array((int)$v) : array()))
							: (!empty ($v) ? array((int)$v) : array());
					}
				}

			} elseif (strpos($diapazone, '-') !== false) {

				preg_match("#(\d+)-(\d+)#i", $diapazone, $test);
				$diapazone = !empty ($test) ? range($test[1], $test[2]) : array();

			} else {
				$diapazone = array((int)$diapazone);
			}
			if (!empty($diapazone)) {
				if ($subcats && function_exists('get_sub_cats')) {
					foreach ($diapazone as $d) {
						$_sc = explode('|', get_sub_cats($d));
						foreach ($_sc as $v) {
							array_push($diapazone, $v);
						}
					}
				}
				$diapazone = array_unique($diapazone);
			} else {
				$diapazone = array();
			}

			$diapazone = implode(',', $diapazone);
		}

		return $diapazone;

	}

	/**
	 * Формируем ссылки с тегами
	 *
	 * @param  string $tags строка с тегами
	 *
	 * @return string       строка с сылками
	 */

	public function tagsLink($tags) {
		$showTags = '';
		if ($this->dle_config['allow_tags'] && $tags) {
			$showTagsArr = array();
			$tags        = explode(",", $tags);

			foreach ($tags as $value) {
				$value = trim($value);
				if ($this->dle_config['allow_alt_url'] && $this->dle_config['allow_alt_url'] != 'no') {
					$showTagsArr[] = "<a href=\"" . $this->dle_config['http_home_url'] . "tags/" . urlencode($value) . "/\">" . $value . "</a>";
				} else {
					$showTagsArr[] = "<a href=\"" . $this->dle_config['http_home_url'] . "?do=tags&amp;tag=" . urlencode($value) . "\">" . $value . "</a>";
				}

				$showTags = implode(', ', $showTagsArr);
			}
		}

		return $showTags;
	}


} //BaseClass

/**
 * @param        $data    - контент
 * @param        $limit
 * @param string $etc     - Окончание обрезанного текста
 * @param bool   $wordcut - жесткое ограничение символов
 *
 * @internal param $length - максимальный размер возвращаемого контента
 * @return string $data - обрезанный результат
 * @todo   Внести эту функцию внутрь класса(надо ли?).
 */
function textLimit($data, $limit, $etc = '&hellip;', $wordcut = false) {
	global $config;
	$data = strip_tags($data, '<br>');
	$data = trim(str_replace(array('<br>', '<br />'), ' ', $data));

	if ($limit && dle_strlen($data, $config['charset']) > $limit) {
		$data = dle_substr($data, 0, $limit, $config['charset']) . $etc;
		if (!$wordcut && ($word_pos = dle_strrpos($data, ' ', $config['charset']))) {
			$data = dle_substr($data, 0, $word_pos, $config['charset']) . $etc;
		}

	}

	return $data;
}


/**
 * Функция ресайза картинок
 *
 * @param  string   $data       Строка, из которой будем выдёргивать картинку
 * @param  string  $noimage    картинка-заглушка
 * @param  string  $imageType  Тип картинки (small/original) - для получения соответствующей картинки
 * @param  int     $number     Номер картинки в контенте
 * @param  string  $size       Размер картики (например 100 или 100x150)
 * @param  string  $quality    Качество картинки (0-100)
 * @param  string  $resizeType Тип ресайза (exact, portrait, landscape, auto, crop)
 * @param  boolean $grabRemote Грабить сторонние картинки к себе (true/false)
 * @param  boolean $showSmall  Обрабатывать уменьшенную копию, если есть
 * @param  boolean $subdir     Подпапка для картинок (иногда бывает нужно)
 *
 * @return string              Путь к уменьшенной или оригинальной картнке
 */
function getImage($data, $noimage = '', $imageType = 'small', $number, $size, $quality, $resizeType = 'auto', $grabRemote = true, $showSmall = false, $subdir = false) {
	global $config;

	// Присваиваем картинке значение noimage;
	$image = $noimage;

	// Задаём подпапку при необходимости
	if ($subdir) {
		$subdir = $subdir . '/';
	}
	// Задаём папку для картинок
	$dir_suffix = $size . '/' . $subdir;

	$dir = ROOT_DIR . '/uploads/base/' . $dir_suffix;
	$data = stripslashes($data);

	if (preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i', $data, $m)) {

		$url = $m[1][$number - 1];

		//Выдёргиваем оригинал, на случай если уменьшить надо до размеров больше, чем thumb в новости и если это не запрещено в настройках.
		$imgOriginal = ($showSmall) ? $url : str_ireplace('/thumbs', '', $url);


		// Удаляем текущий домен (в т.ч. с www) из строки.
		$urlShort = str_ireplace(array('http://' . $_SERVER['HTTP_HOST'], 'http://www.' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], 'https://www.' . $_SERVER['HTTP_HOST']), '', $imgOriginal);


		// Проверяем наша картинка или чужая.
		$isHttp = (stripos($urlShort, 'http:') === false) ? false : true;

		// Проверяем разрешено ли тянуть сторонние картинки.
		$grabRemoteOn = ($grabRemote) ? true : false;

		// Отдаём заглушку если это смайлик или спойлер, или если ничего нет.
		if (
			(stripos($urlShort, 'dleimages') !== false && stripos($urlShort, 'engine/data/emoticons') !== false)
			|| (!$urlShort)
		) {
			$imgResized  = false;
			$imgOriginal = false;
		} elseif ($isHttp && !$grabRemoteOn) {
			// Если внешняя картинка - возвращаем её если запрещено грабить в строке подключения
			$imgResized = $urlShort;
		} elseif ($data != '') {
			// Работаем с картинкой, если есть косяк - стопарим, такая картинка нам не пойдёт, вставим заглушку
			// Если есть параметр size и есть картинка - включаем обрезку картинок
			if ($size && $urlShort) {
				// Создаём и назначаем права, если нет таковых
				if (!is_dir($dir)) {
					@mkdir($dir, 0755, true);
					@chmod($dir, 0755);
				}
				if (!chmod($dir, 0755)) {
					@chmod($dir, 0755);
				}

				// Присваиваем переменной значение картинки (в т.ч. если это внешняя картинка)
				$imgResized = $urlShort;

				// Если не внешняя картинка - подставляем корневю дирректорию, чтоб ресайзер понял что ему дают.
				if (!$isHttp) {
					$imgResized = ROOT_DIR . $urlShort;
				}

				// Определяем новое имя файла
				$fileName = $size . '_' . $resizeType . '_' . strtolower(basename($imgResized));

				// Если картинки нет и она локальная, или картинка внешняя и разрешено тянуть внешние - создаём её
				if ((!file_exists($dir . $fileName) && !$isHttp) || (!file_exists($dir . $fileName) && $grabRemoteOn && $isHttp)) {
					// Разделяем высоту и ширину
					$imgSize = explode('x', $size);

					// Если указана только одна величина - присваиваем второй первую, будет квадрат для exact, auto и crop, иначе класс ресайза жестоко тупит, ожидая вторую переменную.
					if (count($imgSize) == '1') {
						$imgSize[1] = $imgSize[0];
					}

					// Подрубаем НОРМАЛЬНЫЙ класс для картинок
					require_once BASE_DIR . '/core/resize_class.php';
					$resizeImg = new resize($imgResized);
					$resizeImg->resizeImage( //создание уменьшенной копии
						$imgSize[0],
						$imgSize[1],
						$resizeType //Метод уменьшения (exact, portrait, landscape, auto, crop)
					);
					$resizeImg->saveImage($dir . $fileName, $quality); //Сохраняем картинку в папку /uploads/base/[размер_уменьшенной_копии]
				}
				// Если файл есть - отдаём картинку с сервера.
				if (file_exists($dir . $fileName)) {
					$imgResized = $config['http_home_url'] . 'uploads/base/' . $dir_suffix . $fileName;
				} else {
					$imgResized = $noimage;
				}

			} // Если параметра imgSize нет - отдаём оригинальную картинку
			else {
				$imgResized = $urlShort;
			}
		}

		if ($imageType == 'original') {
			$image = $imgOriginal;
		} elseif ($imageType == 'small') {
			$image = $imgResized;
		}

	}

	return $image;

}

/**
 * Функция для правильного склонения слов
 *
 * @param int    $n     - число, для которого будет расчитано окончание
 * @param string $words - варианты окончаний для (1 комментарий, 2 комментария, 100 комментариев)
 *
 * @return string - слово с правильным окончанием
 */
function declinationWords($n = 0, $words) {
	$words = explode('|', $words);
	$n     = intval($n);

	return $n % 10 == 1 && $n % 100 != 11 ? $words[0] . $words[1] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $words[0] . $words[2] : $words[0] . $words[3]);
}

/**
 * Форматируем дату
 *
 * @param  string $date дата
 * @param bool $_f
 *
 * @internal param bool $_f если false - форматирует в соответствии с настройками движка
 *
 * @return string        отформатированная дата
 */
function formateDate($date, $_f = false) {

	global $lang, $config, $langdate;

	if (!$lang['charset']) {
		@include_once ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';
	}

	$date = strtotime($date);

	if (!$_f) {

		if (date('Ymd', $date) == date('Ymd')) {
			$showDate = $lang['time_heute'] . langdate(', H:i', $date);
		} elseif (date('Ymd', $date) == date('Ymd') - 1) {
			$showDate = $lang['time_gestern'] . langdate(', H:i', $date);
		} else {
			$showDate = langdate($config['timestamp_active'], $date);
		}
	} else {
		$showDate = langdate($_f, $date);
	}

	return $showDate;
}

/**
 * @param      $category
 * @param bool $info
 * @param bool $noicon
 *
 * @return array|string
 */
function getCatInfo($category, $info = false, $noicon = false) {
	global $cat_info, $config;

	$my_cat      = array();
	$my_cat_icon = array();
	$my_cat_link = array();
	$cat_return  = array();


	$cat_list = explode(',', $category);

	foreach ($cat_list as $cat) {
		if (isset($cat_info[$cat])) {

			$my_cat[] = $cat_info[$cat]['name'];
			if ($cat_info[$cat]['icon']) {
				$my_cat_icon[] = '<img src="' . $cat_info[$cat]['icon'] . '" alt="' . $cat_info[$cat]['name'] . '" />';
			} else {
				$my_cat_icon[] = (!$noicon) ? false : '<img src="' . $noicon . '" alt="' . $cat_info[$cat]['name'] . '" />';
			}
			if ($config['allow_alt_url'] && $config['allow_alt_url'] != 'no') {
				$my_cat_link[] = '<a href="' . $config['http_home_url'] . get_url($cat_info[$cat]['id']) . '/">' . $cat_info[$cat]['name'] . '</a>';
			} else {
				$my_cat_link[] = '<a href="' . $config['http_home_url'] . '?do=cat&category=' . $cat_info[$cat]['alt_name'] . '">' . $cat_info[$cat]['name'] . '</a>';
			}
		}
	}

	$cat_return['name'] = implode(', ', $my_cat);
	$cat_return['icon'] = implode(', ', $my_cat_icon);
	$cat_return['link'] = implode(', ', $my_cat_link);
	$cat_return['url']  = ($category) ? $config['http_home_url'] . get_url(intval($category)) . '/' : '/';


	switch ($info) {
		case 'name':
			return $cat_return['name'];
			break;

		case 'icon':
			return $cat_return['icon'];
			break;

		case 'link':
			return $cat_return['link'];
			break;

		case 'url':
			return $cat_return['url'];
			break;

		default:
			return $cat_return;
			break;
	}

}

/**
 * @return mixed
 */
function getNormalHomeHttp() {
	$config['http_home_url'] = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $config['http_home_url']);

	return $config['http_home_url'];
}

function baseShowRating($id, $rating, $vote_num, $allow = true) {
	global $lang;

	if( $rating AND $vote_num ) $rating = round( ($rating / $vote_num), 0 );
	else $rating = 0;
	$rating = $rating * 20;

	if( !$allow ) {

		$rated = <<<HTML
<div class="rating">
	<ul class="unit-rating">
		<li class="current-rating" style="width:{$rating}%;">{$rating}</li>
	</ul>
</div>
HTML;

		return $rated;
	}

	$rated = <<<HTML
<div data-rating-layer="{$id}">
	<div class="rating">
		<ul class="unit-rating">
			<li class="current-rating" style="width:{$rating}%;">{$rating}</li>
			<li><a href="#" title="{$lang['useless']}" class="r1-unit" onclick="base_rate('1', '{$id}'); return false;">1</a></li>
			<li><a href="#" title="{$lang['poor']}" class="r2-unit" onclick="base_rate('2', '{$id}'); return false;">2</a></li>
			<li><a href="#" title="{$lang['fair']}" class="r3-unit" onclick="base_rate('3', '{$id}'); return false;">3</a></li>
			<li><a href="#" title="{$lang['good']}" class="r4-unit" onclick="base_rate('4', '{$id}'); return false;">4</a></li>
			<li><a href="#" title="{$lang['excellent']}" class="r5-unit" onclick="base_rate('5', '{$id}'); return false;">5</a></li>
		</ul>
	</div>
</div>
HTML;

	return $rated;
}


?>