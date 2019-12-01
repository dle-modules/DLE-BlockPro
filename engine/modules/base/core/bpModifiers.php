<?php

/*
=============================================================================
bpModifiers - класс с модификаторами для шаблонизайтора модуля blockPro
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
	 * @param string $charset
	 *
	 * @return string $data - обрезанный результат
	 */
	public static function textLimit($data, $limit, $etc = '&hellip;', $wordcut = false, $charset = 'utf-8') {
		$data = strip_tags($data, '<br>');
		$data = trim(str_replace(['<br>', '<br />'], ' ', $data));

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
	 * @param  string  $noimage    Картинка-заглушка
	 * @param  string  $imageType  Тип картинки (small/original/intext) - для получения соответствующей картинки или массива картинок
	 * @param          integer     /string    $number       Номер картинки в контенте или all для вывода всех картинок
	 * @param  string  $size       Размер картики (например 100 или 100x150)
	 * @param  string  $quality    Качество картинки (0-100)
	 * @param  string  $resizeType Тип ресайза (exact, portrait, landscape, auto, crop)
	 * @param  boolean $grabRemote Грабить сторонние картинки к себе (true/false)
	 * @param  boolean $showSmall  Обрабатывать уменьшенную копию, если есть
	 * @param  boolean $subdir     Подпапка для картинок (иногда бывает нужно)
	 * @param  array   $config     Массив с конфигом DLE
	 * @param  string  $service    Сервис, серез который будем делать ресайз (local/tinypng/kraken)
	 *
	 * @return string                         Путь к уменьшенной или оригинальной картнке
	 */

	public static function getImage($data, $noimage = '', $imageType = 'small', $number, $size, $quality, $resizeType = 'auto', $grabRemote = true, $showSmall = false, $subdir = false, $config = [], $service = 'local') {

		$resizeType = ($resizeType == '' || !$resizeType) ? 'auto' : $resizeType;
		$quality    = ($quality == '' || !$quality) ? '100' : $quality;
		$noimage    = str_replace($config['http_home_url'] . '/', $config['http_home_url'], $noimage);

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

		$data     = stripslashes($data);
		$arImages = [];

		if (preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i', $data, $m)) {

			$i = 1; // Счётчик
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
					/** @var string $imageItem */
					$imageItem = $imgResized = '';

					// Если в настройках вызова указано выдёргивание оригинала — отдадим оригинал.
					if ($imageType == 'original') {
						$imageItem = str_ireplace(['/thumbs', '/medium'], '', $url);
					}
					// Если intext — отдадим то, что получили в тексте.
					if ($imageType == 'intext') {
						$imageItem = $url;
					}
					// Если small — то будем работать с картинкой.
					if ($imageType == 'small') {
						// Выдёргиваем оригинал, на случай если уменьшить надо до размеров больше, чем thumb или medium в новости и если это не запрещено в настройках.
						$imageItem = ($showSmall) ? $url : str_ireplace(['/thumbs', '/medium'], '', $url);

						// Удаляем текущий домен (в т.ч. с www) из строки.
						$urlShort = str_ireplace(['http://' . $_SERVER['HTTP_HOST'], 'http://www.' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], 'https://www.' . $_SERVER['HTTP_HOST']], '', $imageItem);

						// Проверяем наша картинка или чужая.
						$isRemote = (preg_match('~^http(s)?://~', $urlShort)) ? true : false;

						// Проверяем разрешено ли тянуть сторонние картинки.
						$grabRemoteOn = ($grabRemote) ? true : false;

						// Отдаём заглушку, если ничего нет.
						if (!$urlShort) {
							/** @var string $imageItem */
							$imageItem = $noimage;
							continue;
						}

						// Если внешняя картинка и запрещего грабить картинки к себе — возвращаем её.
						if ($isRemote && !$grabRemoteOn) {
							/** @var string $imgResized */
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

							if ($service == 'tinypng' && $config['tinypng_key'] == '') {
								$service = 'local';
							}
							if ($service == 'kraken' && $config['kraken_key'] == '') {
								$service = 'local';
							}

							// Определяем новое имя файла
							$fileName = md5($size . '_' . $resizeType . '_' . $imgResized) . '_' . $service . '.' . pathinfo($imgResized, PATHINFO_EXTENSION);

							$newFile = $dir . $fileName;
							// Если картинки нет в папке обработанных картинок, то попробуем её получить.
							if (!file_exists($newFile)) {
								// Если картинка локальная, или картинка внешняя, но разрешено её стянуть — работаем.
								if (!$isRemote || ($grabRemoteOn && $isRemote)) {
									self::getImageWith($service, $imgResized, $newFile, $size, $resizeType, $quality, $config);
								}
							}

							$imgResized = (file_exists($newFile)) ? $config['http_home_url'] . $imageDir . $fileName : $noimage;

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

		} else {
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
	 * @param string $service
	 * @param        $originalFile
	 * @param        $newFile
	 * @param        $size
	 * @param        $method
	 * @param string $quality
	 * @param array  $config
	 */
	public static function getImageWith($service = 'local', $originalFile, $newFile, $size, $method, $quality = '100', $config = []) {
		// Разделяем высоту и ширину
		$imgSize = explode('x', $size);

		// Если указана только одна величина - присваиваем второй первую, будет квадрат для exact, auto и crop, иначе класс ресайза жестоко тупит, ожидая вторую переменную.
		if (count($imgSize) == '1') {
			$imgSize[1] = $imgSize[0];
		}

		switch ($service) {
			// Тянем картинку через встроенный класс ресайза
			case 'local':
				// Определяемся с возможными методами уменьшения картинок.
				$arMethods  = ['exact', 'portrait', 'landscape', 'auto', 'crop'];
				$resizeType = (in_array($method, $arMethods)) ? $method : 'auto';

				// Подрубаем локальный класс для картинок
				$resizeImg = new resize($originalFile);
				$resizeImg->resizeImage( // Создание уменьшенной копии
					$imgSize[0], // Размер картинки по ширине
					$imgSize[1], // Размер картинки по высоте
					$resizeType // Метод уменьшения (exact, portrait, landscape, auto, crop)
				);
				$resizeImg->saveImage($newFile, $quality); // Сохраняем картинку в заданную папку
				break;

			// Тянем картинку через tinyPNG
			case 'tinypng':

				// Подключаем необходимые классы (да, без composer в DLE тяжело живётся).
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify.php';
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify/Client.php';
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify/Exception.php';
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify/ResultMeta.php';
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify/Source.php';
				require_once ENGINE_DIR . '/modules/base/resizers/tinypng/Tinify/Result.php';

				// Определяемся с возможными методами уменьшения картинок.
				$arMethods  = ['portrait', 'landscape', 'auto', 'crop'];
				$resizeType = (in_array($method, $arMethods)) ? $method : 'auto';

				// Определяемся с опциями обработки картинки
				$imgSize[0] = (int)$imgSize[0];
				$imgSize[1] = (int)$imgSize[1];

				$arTinyOptions = [];

				switch ($resizeType) {
					case 'portrait':
						$arTinyOptions = [
							'method' => 'scale',
							'height' => $imgSize[1],
						];
						break;

					case 'landscape':
						$arTinyOptions = [
							'method' => 'scale',
							'width'  => $imgSize[0],
						];
						break;

					case 'auto':
						$arTinyOptions = [
							'method' => 'fit',
							'width'  => $imgSize[0],
							'height' => $imgSize[1],
						];
						break;

					case 'crop':
						$arTinyOptions = [
							'method' => 'cover',
							'width'  => $imgSize[0],
							'height' => $imgSize[1],
						];
						break;
				}

				// Вызываем класс и обрабатываем картинку
				\Tinify\setKey($config['tinypng_key']);

				$source  = \Tinify\fromFile($originalFile);
				$resized = $source->resize($arTinyOptions);
				$resized->toFile($newFile);

				unset($source, $resized);

				break;

			// Тянем картинку через Kraken
			case 'kraken':
				// Определяемся с возможными методами уменьшения картинок.
				$arMethods  = ['exact', 'portrait', 'landscape', 'auto', 'crop'];
				$resizeType = (in_array($method, $arMethods)) ? $method : 'auto';
				// У Kraken есть свой метод crop, но нам нужен fit
				if ($method == 'crop') {
					$resizeType = 'fit';
				}

				// Подключаем класс Kraken.
				require_once ENGINE_DIR . '/modules/base/resizers/kraken/Kraken.php';

				$kraken = new Kraken($config['kraken_key'], $config['kraken_secret']);

				// Проверяем наша картинка или чужая. Нужно для корректной передачи данных в kraken
				$isRemote = (preg_match('~^http(s)?://~', $originalFile)) ? true : false;

				// Параметры ресайза
				$krakenResize = [
					'width'    => $imgSize[0],
					'height'   => $imgSize[1],
					'strategy' => $resizeType,
				];

				if ($isRemote) {
					// Если картинка сторонняя
					$krakenParams = [
						'url'    => $originalFile,
						'wait'   => true,
						'resize' => $krakenResize,
					];

					$krakenData = $kraken->url($krakenParams);
				} else {
					// Если картинка наша
					$krakenParams = [
						'file'   => $originalFile,
						'wait'   => true,
						'resize' => $krakenResize,
					];

					$krakenData = $kraken->upload($krakenParams);
				}

				if ($krakenData['success']) {
					$newImg = self::curlGet($krakenData['kraked_url']);
					file_put_contents($newFile, $newImg);
				}

				break;

			// Тянем картинку через tinyPNG
		}
	}

	public static function curlGet($url) {
		$ch              = curl_init();
		$default_curlopt = [
			CURLOPT_TIMEOUT        => 15,
			CURLOPT_RETURNTRANSFER => 1,
			// CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0",
		];
		$curlopt         = [CURLOPT_URL => $url] + $default_curlopt;
		curl_setopt_array($ch, $curlopt);
		$response = curl_exec($ch);
		if ($response === false) {
			trigger_error(curl_error($ch));
		}

		curl_close($ch);

		return $response;
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
		$n     = abs((int) $n); // abs на случай отрицательного значения

		return $n % 10 == 1 && $n % 100 != 11 ? $words[0] . $words[1] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $words[0] . $words[2] : $words[0] . $words[3]);
	}

	/**
	 * Функция для вывода print_r в шаблон
	 *
	 * @param  mixed $var входящие данные
	 *
	 * @return string    print_r
	 */
	public static function dump($var) {
		return print_r($var, true);
	}

	/**
	 * @param            $array
	 * @param bool|false $fields
	 * @param            $db
	 *
	 * @return mixed
	 */
	public static function getAuthors($array, $fields = false, $db) {

		$array = array_unique($array);
		if ($fields) {
			$fields = trim($fields);
		} else {
			$fields = '*';
		}
		if (count($array) == 1) {
			$select = 'SELECT ?p FROM ?n WHERE name = ?s';
			$array  = $array[0];
		} else {
			$select = 'SELECT ?p FROM ?n WHERE name IN(?a)';
		}

		$_result = $db->getAll($select, $fields, USERPREFIX . '_users', $array);

		foreach ($_result as $key => $user) {
			unset($user['password']);
			$result[$user['name']] = $user;
		}

		/** @var array $result */
		return $result;
	}

	/**
	 * Функция для вывода даты в формате "time ago"
	 *
	 *
	 * @param      string   $date       Дата новости
	 * @param      integer  $precision  Кол-во частей
	 *
	 * @return     string   отформатированная строка
	 */
	public static function timeAgo($date, $precision = 2) {
		$precision = ($precision === 0 || $precision === 1) ? 1 : $precision;
		$times     = [
			31536000 => '|год|года|лет',
			2592000  => 'месяц||а|ев',
			604800   => 'недел|ю|и|ь',
			86400    => '|день|дня|дней',
			3600     => 'час||а|ов',
			60       => 'минут|у|ы|',
		];

		$timeDiff = time() - strtotime($date);

		if ($timeDiff < 60) {
			$output = 'меньше минуты';
		} else {
			$output = [];
			$precisionCount   = 0;

			foreach ($times as $period => $name) {

				if ($precisionCount >= $precision || ($precisionCount > 0 && $period < 1)) {
					break;
				}
				$result = floor($timeDiff / $period);

				if ($result > 0) {
					$output[] = $result . ' ' . self::declinationWords($result, $name);

					$timeDiff -= $result * $period;
					$precisionCount++;
				} else {
					if ($precisionCount > 0) {
						$precisionCount++;
					}
				}
			}

			$last            = array_slice($output, -1);
			$first           = join(', ', array_slice($output, 0, -1));
			$both            = array_filter(array_merge([$first], $last), 'strlen');
			$outputFormatted = join(' и ', $both);


			$output = $outputFormatted;
		}

		return $output . ' назад';
	}

	/**
	 * Функция для конфертации строки в json на случай, если не работает модификатор json_decode
	 * @param string $var "{"one":"val"}"
	 *
	 * @return array json-массив
	 */
	public static function jsonDecode($var) {
		$decoded = html_entity_decode($var, ENT_COMPAT);
		return json_decode($decoded, true);
	}


} // bpModifiers

