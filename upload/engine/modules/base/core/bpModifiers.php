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
	 * @param  string  $imageType  Тип картинки (small/original/intext) - для получения соответствующей картинки или массива картинок
	 * @param  integer/string $number     Номер картинки в контенте или all для вывода всех картинок
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
		$quality = ($quality == '' || !$quality) ? '100' : $quality ;
		$noimage = str_replace($config['http_home_url'].'/', $config['http_home_url'], $noimage);

		// Удалим из адреса сайта последний слеш.
		$config['http_home_url'] = (substr($config['http_home_url'], -1, 1) == '/') ? substr($config['http_home_url'], 0, -1) : $config['http_home_url'];

		// Задаём папку  для загрзки картинок по умолчанию.
		$uploadDir = '/uploads/base/';

		// Задаём подпапку при необходимости
		if ($subdir) {
			// Если subdir начнается со слеша - значит это папка от корня сайта, а не подпапака в base.
			if (substr($subdir, 0, 1) == '/') {
				$uploadDir = $subdir;
			} else {
				$uploadDir = $uploadDir . $subdir . '/';
			}
		}

		// Задаём папку для картинок
		$imageDir = $uploadDir . $size . '/';

		$dir = ROOT_DIR . $imageDir;
		
		$data = stripslashes($data);
		$arImages = array();

		if (preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i', $data, $m)) {

			$i=1; // Счётчик
			// Если регулярка нашла картинку — работаем.
			if (isset($m[1])) {
				foreach ($m[1] as $key => $url) {
					// Если это смайлик или спойлер — пропускаем.
					if (stripos($url, 'dleimages') !== false || stripos($url, 'engine/data/emoticons') !== false) {
						continue;
					} 
					// Если номер картинки меньше, чем требуется — проходим мимо.
					if ($number != 'all' && $i < (int)$number) {
						// Не забываем прибавить счётчик.
						$i++;
						continue;
					}

					// Если в настройках вызова указано выдёргивание оригинала — отдадим оригинал.
					if ($imageType == 'original') {
						$imageItem = str_ireplace(array('/thumbs', '/medium'), '', $url);
					}
					// Если intext — отдадим то, что получили в тексте.
					if ($imageType == 'intext') {
						$imageItem = $url;
					}
					// Если small — то будем работать с картинкой.
					if ($imageType == 'small') {						
						// Выдёргиваем оригинал, на случай если уменьшить надо до размеров больше, чем thumb или medium в новости и если это не запрещено в настройках.
						$imageItem = ($showSmall) ? $url : str_ireplace(array('/thumbs', '/medium'), '', $url);

						// Удаляем текущий домен (в т.ч. с www) из строки.
						$urlShort = str_ireplace(array('http://' . $_SERVER['HTTP_HOST'], 'http://www.' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], 'https://www.' . $_SERVER['HTTP_HOST']), '', $imageItem);

						// Проверяем наша картинка или чужая.
						$isRemote = (preg_match('~^http(s)?://~', $urlShort)) ? true : false;

						// Проверяем разрешено ли тянуть сторонние картинки.
						$grabRemoteOn = ($grabRemote) ? true : false;

						// Отдаём заглушку, если ничего нет.
						if (!$urlShort) {
							$imageItem = $noimage;
							continue;
						} 

						// Если внешняя картинка и запрещего грабить картинки к себе — возвращаем её.
						if ($isRemote && !$grabRemoteOn) {
							$imgResized = $urlShort;
							continue;
						} 

						// Работаем с картинкой
						// Если есть параметр size — включаем ресайз картинок
						if ($size) {
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

							// Если не внешняя картинка — подставляем корневю дирректорию, чтоб ресайзер понял что ему дают.
							if (!$isRemote) {
								$imgResized = ROOT_DIR . $urlShort;
							}
							// Определяем новое имя файла
							$fileName = $size . '_' . $resizeType . '_' . strtolower(basename($imgResized));

							// Если картинки нет в папке обработанных картинок
							if(!file_exists($dir . $fileName)) {
								// Если картинка локальная, или картинка внешняя и разрешено тянуть внешние — обработаем её.
								if (!$isRemote || ($grabRemoteOn && $isRemote)) {
									// Разделяем высоту и ширину
									$imgSize = explode('x', $size);

									// Если указана только одна величина - присваиваем второй первую, будет квадрат для exact, auto и crop, иначе класс ресайза жестоко тупит, ожидая вторую переменную.
									if (count($imgSize) == '1') {
										$imgSize[1] = $imgSize[0];
									}

									// @TODO: по хорошему надо бы вынести ресайз в отдельный метод на случай, если понадобится другой класс для ресайза.
									// Подрубаем класс для картинок
									$resizeImg = new resize($imgResized);
									$resizeImg->resizeImage( // Создание уменьшенной копии
										$imgSize[0], // Размер картинки по ширине
										$imgSize[1], // Размер картинки по высоте
										$resizeType // Метод уменьшения (exact, portrait, landscape, auto, crop)
									);
									$resizeImg->saveImage($dir . $fileName, $quality); // Сохраняем картинку в заданную папку
								}
							} 
							
							$imgResized = $config['http_home_url'] . $imageDir . $fileName;							
 
						} else {
							// Если параметра imgSize нет - отдаём исходную картинку
							$imgResized = $urlShort;
						}

						// Отдаём дальше результат обработки.					
						$imageItem = $imgResized;

					} // if($imageType == 'small')
					$arImages[$i] = $imageItem;
					if ($number == $i) {
						break;
					}
					$i++;
				}
				
			} 

		} 
		else {
			// Если регулярка не нашла картинку - отдадим заглушку
			$arImages[$number] = $noimage;
		}

		// Если хотим все картинки — не вопрос, получим массив.
		if ($number == 'all') {
		
			return $arImages;
		}
		// По умолчанию возвращаем отдну картинку (понимаю, что метод должен возвращать всегда один тип данных, но это сделано из-за совместимости версий)
		return $arImages[$number];

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

	/**
	 * Функция для вывода print_r в шаблон
	 * @param  mixed     $var входящие данные]
	 * @return string    print_r
	 */
	public static function dump($var) {
		return print_r($var, true);
	}


} // bpModifiers

