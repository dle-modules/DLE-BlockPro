<?
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

class bpModifiers extends base {

	/**
	 * @param        $data    - контент
	 * @param        $limit
	 * @param string $etc     - Окончание обрезанного текста
	 * @param bool   $wordcut - жесткое ограничение символов
	 *
	 * @internal param $length - максимальный размер возвращаемого контента
	 * @return string $data - обрезанный результат
	 */
	public static function textLimit($data, $limit, $etc = '&hellip;', $wordcut = false, $charset = 'utf-8') {
		$data = strip_tags($data, '<br>');
		$data = trim(str_replace(array('<br>', '<br />'), ' ', $data));

		if ($limit && dle_strlen($data, $charset) > $limit) {
			$data = dle_substr($data, 0, $limit, $charset) . $etc;
			if (!$wordcut && ($word_pos = dle_strrpos($data, ' ', $charset))) {
				$data = dle_substr($data, 0, $word_pos, $charset) . $etc;
			}

		}

		return $data;
	}

	/**
	 * Функция ресайза картинок
	 *
	 * @param  string  $data       Строка, из которой будем выдёргивать картинку
	 * @param  string  $noimage    картинка-заглушка
	 * @param  string  $imageType  Тип картинки (small/original) - для получения соответствующей картинки
	 * @param  integer $number     Номер картинки в контенте
	 * @param  string  $size       Размер картики (например 100 или 100x150)
	 * @param  string  $quality    Качество картинки (0-100)
	 * @param  string  $resizeType Тип ресайза (exact, portrait, landscape, auto, crop)
	 * @param  boolean $grabRemote Грабить сторонние картинки к себе (true/false)
	 * @param  boolean $showSmall  Обрабатывать уменьшенную копию, если есть
	 * @param  boolean $subdir     Подпапка для картинок (иногда бывает нужно)
	 * @param  array   $config     Массив с конфигом DLE
	 *
	 * @return string              Путь к уменьшенной или оригинальной картнке
	 */

	public static function getImage($data, $noimage = '', $imageType = 'small', $number, $size, $quality, $resizeType = 'auto', $grabRemote = true, $showSmall = false, $subdir = false, $config = array()) {
		$resizeType = ($resizeType == '' || !$resizeType) ? 'auto' : $resizeType ;
		// Присваиваем картинке значение noimage;
		$image = $noimage;

		// Задаём подпапку при необходимости
		if ($subdir) {
			$subdir = $subdir . '/';
		}
		// Задаём папку для картинок
		$dir_suffix = $subdir . $size . '/';

		$dir = ROOT_DIR . '/uploads/base/' . $dir_suffix;
		$data = stripslashes($data);

		if (preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i', $data, $m)) {

			$url = $m[1][$number - 1];
			// Выдёргиваем оригинал, на случай если уменьшить надо до размеров больше, чем thumb или medium в новости и если это не запрещено в настройках.
			$imgOriginal = ($showSmall) ? $url : str_ireplace(array('uploads/thumbs', 'uploads/medium'), 'uploads', $url);


			// Удаляем текущий домен (в т.ч. с www) из строки.
			$urlShort = str_ireplace(array('http://' . $_SERVER['HTTP_HOST'], 'http://www.' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], 'https://www.' . $_SERVER['HTTP_HOST']), '', $imgOriginal);


			// Проверяем наша картинка или чужая.
			$isHttp = (preg_match('~^http(s)?://~', $urlShort)) ? true : false;

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
	public static function declinationWords($n = 0, $words) {
		$words = explode('|', $words);
		$n     = intval($n);

		return $n % 10 == 1 && $n % 100 != 11 ? $words[0] . $words[1] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $words[0] . $words[2] : $words[0] . $words[3]);
	}


} // bpModifiers

