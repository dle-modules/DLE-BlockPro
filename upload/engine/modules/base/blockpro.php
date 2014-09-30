<?php
/*
=============================================================================
BLockPro - основной модуль
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

if ($showstat) {
	$start  = microtime(true);
	$dbStat = '';
}
// Конфиг модуля
if ($isAjaxConfig) {
	$cfg = $ajaxConfigArr;
} else {
	$cfg = array(
		'moderate'       => !empty($moderate) ? $moderate : false, // Показывать только новости на модерации

		'template'       => !empty($template) ? $template : 'blockpro/blockpro', // Название шаблона (без расширения)

		'cachePrefix'    => !empty($cachePrefix) ? $cachePrefix : 'news', // Префикс кеша
		'cacheSuffixOff' => !empty($cacheSuffixOff) ? true : false, // Отключить суффикс кеша (будет создаваться один кеш-файл для всех пользователей). По умолчанию включен, т.е. для каждой группы пользователей будет создаваться свой кеш (на случай разного отображения контента разным юзерам).

		'cacheNameAddon' => '', // Назначаем дополнение к имени кеша, если имеются переменные со значениями this, они будут дописаны в имя кеша, иначе для разных мест будет создаваться один и тот же файл кеша

		'nocache'        => !empty($nocache) ? $nocache : false, // Не использовать кеш
		'cacheLive'      => (!empty($cacheLive) && !$mcache) ? $cacheLive : false, // Время жизни кеша в минутах

		'startFrom'      => !empty($startFrom) ? $startFrom : '0', // C какой новости начать вывод
		'limit'          => !empty($limit) ? $limit : '10', // Количество новостей в блоке
		'fixed'          => !empty($fixed) ? $fixed : 'yes', // Обработка фиксированных новостей (yes/only/without показ всех/только фиксированных/только обычных новостей)

		'postId'         => !empty($postId) ? $postId : '', // ID новостей для вывода в блоке (через запятую, или черточку)
		'notPostId'      => !empty($notPostId) ? $notPostId : '', // ID игнорируемых новостей (через запятую, или черточку)

		'author'         => !empty($author) ? $author : '', // Логины авторов, для показа их новостей в блоке (через запятую)
		'notAuthor'      => !empty($notAuthor) ? $notAuthor : '', // Логины игнорируемых авторов (через запятую)

		'xfilter'        => !empty($xfilter) ? $xfilter : '', // Имена дополнительных полей для фильтрации новостей по ним (через запятую)
		'notXfilter'     => !empty($notXfilter) ? $notXfilter : '', // Имена дополнительных полей для игнорирования показа новостей (через запятую)

		'xfSearch'       => !empty($xfSearch) ? $xfSearch : false, // синтаксис передачи данных: &xfSearch=имя_поля|значение||имя_поля|значение
		'notXfSearch'    => !empty($notXfSearch) ? $notXfSearch : false, // синтаксис передачи данных: &notXfSearch=имя_поля|значение||имя_поля|значение
		'xfSearchLogic'  => !empty($xfSearchLogic) ? $xfSearchLogic : 'OR', // Принимает OR или AND (по умолчанию OR)

		'catId'          => !empty($catId) ? $catId : '', // Категории для показа	(через запятую, или черточку)
		'subcats'        => !empty($subcats) ? $subcats : false, // Выводить подкатегории указанных категорий (&subcats=y), работает и с диапазонами.
		'notCatId'       => !empty($notCatId) ? $notCatId : '', // Игнорируемые категории (через запятую, или черточку)
		'notSubcats'     => !empty($notSubcats) ? $notSubcats : false, // Игнорировать подкатегории игнорируемых категорий (&notSubcats=y), работает и с диапазонами.

		'tags'           => !empty($tags) ? $tags : '', // Теги из облака тегов для показа новостей, содержащих их (через запятую)
		'notTags'        => !empty($notTags) ? $notTags : '', // Игнорируемые теги (через запятую)

		'day'            => !empty($day) ? $day : false, // Временной период для отбора новостей
		'dayCount'       => !empty($dayCount) ? $dayCount : false, // Интервал для отбора (т.е. к примеру выбираем новости за прошлую недею так: &day=14&dayCount=7 )
		'sort'           => !empty($sort) ? $sort : 'top', // Сортировка (top, date, comms, rating, views, title)
		'order'          => !empty($order) ? $order : 'new', // Направление сортировки (new, old)

		'avatar'         => !empty($avatar) ? $avatar : false, // Вывод аватарки пользователя (немного усложнит запрос).

		'showstat'       => !empty($showstat) ? $showstat : false, // Показывать время и статистику по блоку

		'related'        => !empty($related) ? $related : false, // Включить режим вывода похожих новостей (по умолчанию нет)
		'showNav'        => !empty($showNav) ? $showNav : false, // Включить постраничную навигацию
		'pageNum'        => !empty($pageNum) ? $pageNum : '1', // Текущая страница при постраничной конфигурации
		'navStyle'       => !empty($navStyle) ? $navStyle : 'classic', // Стиль навигации. Возможны следующие стили:
		/*
			classic:	<< Первая  < 1 [2] 3 >  Последняя >>
			digg:		<< Назад  1 2 ... 5 6 7 8 9 [10] 11 12 13 14 ... 25 26  Вперёд >>
			extended:	<< Назад | Страница 2 из 11 | Показаны новости 6-10 из 52 | Вперёд >>
			punbb:		1 ... 4 5 [6] 7 8 ... 15
		*/

	);
}

// Если имеются переменные со значениями this, изменяем значение переменной cacheNameAddon
if ($cfg['catId'] == 'this') {
	$cfg['cacheNameAddon'] .= $category_id . 'cId_';
}
if ($cfg['notCatId'] == 'this') {
	$cfg['cacheNameAddon'] .= $category_id . 'nCId_';
}
if ($cfg['postId'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["newsid"] . 'pId_';
}
if ($cfg['notPostId'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["newsid"] . 'nPId_';
}
if ($cfg['author'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["user"] . 'a_';
}
if ($cfg['notAuthor'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["user"] . 'nA_';
}
if ($cfg['tags'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["tag"] . 't_';
}
if ($cfg['notTags'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["tag"] . 'nT_';
}
if ($cfg['related'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST["newsid"] . 'r_';
}


if ($cfg['cacheLive']) {
	// Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
	$cfg['cachePrefix'] = 'base';
}

// Формируем имя кеша
$cacheName = implode('_', $cfg) . $config['skin'];

// Определяем необходимость создания кеша для разных групп
$cacheSuffix = ($cfg['cacheSuffixOff']) ? false : true;

// Если установлено время жизни кеша
if ($cfg['cacheLive']) {
	// Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
	$_end_file = (!$cfg['cacheSuffixOff']) ? ($is_logged) ? '_' . $member_id['user_group'] : '_0' : false;
	$filedate  = ENGINE_DIR . '/cache/' . $cfg['cachePrefix'] . '_' . md5($cacheName) . $_end_file . '.tmp';

	if (@file_exists($filedate)) {
		$cache_time = time() - @filemtime($filedate);
	} else {
		$cache_time = $cfg['cacheLive'] * 60;
	}
	if ($cache_time >= $cfg['cacheLive'] * 60) {
		$clear_time_cache = true;
	}
}

$output = false;

// Если nocache не установлен - пытаемся вывести данные из кеша.
if (!$cfg['nocache']) {
	$output = dle_cache($cfg['cachePrefix'], $cacheName, $cacheSuffix);
}
// Сбрасываем данные, если истекло время жизни кеша
if ($clear_time_cache) {
	$output = false;
}

if (!$output) {

	// Подключаем всё необходимое
	include_once('core/base.php');

	// Вызываем ядро
	$base = new base();

	// Назначаем конфиг модуля
	$base->cfg = $base->setConfig($cfg);

	// Пустой массив для конфга шаблонизатора.
	$tplOptions = array();

	// Если кеширование блока отключено - будем автоматически проверять скомпилированный шаблон на изменения.
	if ($base->cfg['nocache']) {
		$tplOptions['auto_reload'] = true;
	}


	// Подключаем опции шаблонизатора
	$base->tplOptions = $base->setConfig($tplOptions);
	// Подключаем шаблонизатор
	$base->getTemplater($base->tplOptions);

	// Определяем сегодняшнюю дату
	if ($base->dle_config['version_id'] > 10.2) {
		date_default_timezone_set($base->dle_config['date_adjust']);
		$tooday = date("Y-m-d H:i:s", time());
	} else {
		$tooday = date("Y-m-d H:i:s", (time() + $base->dle_config['date_adjust'] * 60));
	}

	// Массив с условиями запроса
	$wheres = array();

	// Условие для отображения только постов, прошедших модерацию или находящихся на модерации
	$wheres[] = ($base->cfg['moderate']) ? 'approve = 0' : 'approve';

	// Определяем в какую сторону направлена сортировка
	$ordering = ($base->cfg['order'] == 'new') ? 'DESC' : 'ASC';

	// Если без сортировки - сбрасываем направление
	if ($base->cfg['sort'] == 'none') {
		$ordering = false;
	}
	// Массив, куда будем записывать сортировки
	$orderArr = array();

	// Учёт фиксированных новостей
	switch ($base->cfg['fixed']) {
		case 'only':
			$wheres[] = 'fixed = 1';
			break;

		case 'without':
			$wheres[] = 'fixed = 0';
			break;

		default:
			if ($base->cfg['sort'] != 'random' && $base->cfg['sort'] != 'none') {
				$orderArr[] = 'fixed ' . $ordering;
			}
			break;
	}

	// Определяем тип сортировки
	switch ($base->cfg['sort']) {
		case 'none': // Не сортировать (можно использовать для вывода похожих новостей, аналогично стандарту DLE)
			// $orderArr[] = false;
			break;

		case 'date': // Дата
			$orderArr[] = 'p.date ' . $ordering;
			break;

		case 'rating': // Рейтинг
			$orderArr[] = 'e.rating ' . $ordering;
			break;

		case 'comms': // Комментарии
			$orderArr[] = 'p.comm_num ' . $ordering;
			break;

		case 'views': // Просмотры
			$orderArr[] = 'e.news_read ' . $ordering;
			break;

		case 'random': // Случайные
			$orderArr[] = 'RAND()';
			break;

		case 'title': // По алфавиту
			$orderArr[] = 'p.title ' . $ordering;
			break;

		case 'hit': // Правильный топ
			$orderArr[] = '(e.rating*100+p.comm_num*10+e.news_read) ' . $ordering;
			break;

		default: // Топ как в DLE (сортировка по умолчанию)
			$orderArr[] = 'e.rating ' . $ordering . ', p.comm_num ' . $ordering . ', e.news_read ' . $ordering;
			break;
	}



	// Фильтрация КАТЕГОРИЙ по их ID
	if ($base->cfg['catId'] == 'this') {
		$base->cfg['catId'] = ($base->cfg['subcats']) ? get_sub_cats($category_id) : $category_id;
	}
	if ($base->cfg['notCatId'] == 'this') {
		$base->cfg['notCatId'] = ($base->cfg['notSubcats']) ? get_sub_cats($category_id) : $category_id;
	}

	if ($base->cfg['catId'] || $base->cfg['notCatId']) {
		$ignore   = ($base->cfg['notCatId']) ? 'NOT ' : '';
		$catArr   = ($base->cfg['notCatId']) ? $base->getDiapazone($base->cfg['notCatId'], $base->cfg['notSubcats']) : $base->getDiapazone($base->cfg['catId'], $base->cfg['subcats']);
		$wheres[] = $ignore . 'category regexp "[[:<:]](' . str_replace(',', '|', $catArr) . ')[[:>:]]"';
	}

	// Фильтрация НОВОСТЕЙ по их ID
	if ($base->cfg['postId'] == 'this') {
		$base->cfg['postId'] = $_REQUEST["newsid"];
	}
	if ($base->cfg['notPostId'] == 'this') {
		$base->cfg['notPostId'] = $_REQUEST["newsid"];
	}

	if (($base->cfg['postId'] || $base->cfg['notPostId']) && $base->cfg['related'] == '') {
		$ignorePosts = ($base->cfg['notPostId']) ? 'NOT ' : '';
		$postsArr    = ($base->cfg['notPostId']) ? $base->getDiapazone($base->cfg['notPostId']) : $base->getDiapazone($base->cfg['postId']);
		$wheres[]    = $ignorePosts . 'id regexp "[[:<:]](' . str_replace(',', '|', $postsArr) . ')[[:>:]]"';
	}

	$_currentAuthor = false;
	// Фильтрация новостей по АВТОРАМ
	if ($base->cfg['author'] == 'this') {
		$base->cfg['author'] = $base->db->parse('?s', $_REQUEST["user"]);
		$_currentAuthor = true;
	}
	if ($base->cfg['notAuthor'] == 'this') {
		$base->cfg['notAuthor'] = $base->db->parse('?s', $_REQUEST["user"]);
		$_currentAuthor = true;
	}
	if ($base->cfg['author'] || $base->cfg['notAuthor']) {
		$ignoreAuthors = ($base->cfg['notAuthor']) ? 'NOT ' : '';
		$authorsArr    = ($base->cfg['notAuthor']) ? $base->cfg['notAuthor'] : $base->cfg['author'];
		$wheres[]      = ($_currentAuthor) 
							? $ignoreAuthors . 'autor = '. $authorsArr 
							: $ignoreAuthors . 'autor regexp "[[:<:]](' . str_replace(',', '|', $authorsArr) . ')[[:>:]]"';
	}

	// Фильтрация новостей по ДОПОЛНИТЕЛЬНЫМ ПОЛЯМ (проверяется только на заполненность)

	if ($base->cfg['xfilter'] || $base->cfg['notXfilter']) {
		$ignoreXfilters = ($base->cfg['notXfilter']) ? 'NOT ' : '';
		$xfiltersArr    = ($base->cfg['notXfilter']) ? $base->cfg['notXfilter'] : $base->cfg['xfilter'];
		$wheres[]       = $ignoreXfilters . 'xfields regexp "[[:<:]](' . str_replace(',', '|', $xfiltersArr) . ')[[:>:]]"';
	}

	// Фильтрация по ЗНАЧЕНИЮ ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ (beta)
	if ($base->cfg['xfSearch'] || $base->cfg['notXfSearch']) {

		// Массив для составления подзапроса
		$xfWheres = array();

		// Защита логики построения запроса от кривых рук (если прописать неправильно - будет логика OR)
		$_xfSearchLogic = (strtolower($base->cfg['xfSearchLogic']) == 'and') ? ' AND ' : ' OR ';

		// Определяем масивы с данными по фильтрации
		$xfSearchArray    = ($base->cfg['xfSearch']) ? explode('||', $base->cfg['xfSearch']) : array();
		$notXfSearchArray = ($base->cfg['notXfSearch']) ? explode('||', $base->cfg['notXfSearch']) : array();

		// Пробегаем по сформированным массивам
		foreach ($xfSearchArray as $xf) {
			$xfWheres[] = $base->db->parse('xfields LIKE ?s', '%' . $xf . '%');
		}
		foreach ($notXfSearchArray as $xf) {
			$xfWheres[] = $base->db->parse('xfields NOT LIKE LIKE ?s', '%' . $xf . '%');
		}

		// Добавляем полученные данные (и логику) в основной массив, формирующий запрос
		$wheres[] = implode($_xfSearchLogic, $xfWheres);
	}

	$_currentTag = false;
	// Фильтрация новостей по ТЕГАМ
	if ($base->cfg['tags'] == 'this') {
		$base->cfg['tags'] = $base->db->parse('?s', $_REQUEST["tag"]);
		$_currentTag = true;
	}
	if ($base->cfg['notTags'] == 'this') {
		$base->cfg['notTags'] = $base->db->parse('?s', $_REQUEST["tag"]);
		$_currentTag = true;
	}

	if ($base->cfg['tags'] || $base->cfg['notTags']) {
		$ignoreTags = ($base->cfg['notTags']) ? 'NOT ' : '';
		$tagsArr    = ($base->cfg['notTags']) ? $base->cfg['notTags'] : $base->cfg['tags'];
		$wheres[]      = ($_currentTag) 
							? $ignoreTags . 'tags = '. $tagsArr 
							: $ignoreTags . 'tags regexp "[[:<:]](' . str_replace(',', '|', $tagsArr) . ')[[:>:]]"';

	}

	// Если включен режим вывода похожих новостей:
	if ($base->cfg['related']) {
		if ($base->cfg['related'] == 'this' && $_REQUEST["newsid"] == '') {
			echo '<span style="color: red;">Переменная related=this работает только в полной новости и не работает с ЧПУ 3 типа.</span>';

			return;
		}
		$relatedId   = ($base->cfg['related'] == 'this') ? $_REQUEST["newsid"] : $base->cfg['related'];
		$relatedRows = 'title, short_story, full_story, xfields';
		$relatedId   = $base->db->parse('id = ?i', $relatedId);


		$relatedBody = $base->db->getRow('SELECT id, ?p FROM ?n WHERE approve AND ?p', $relatedRows, PREFIX . '_post', $relatedId);

		$bodyToRelated = (strlen($relatedBody['full_story']) < strlen($relatedBody['short_story'])) ? $relatedBody['short_story'] : $relatedBody['full_story'];
		$bodyToRelated = $base->db->parse('?s', strip_tags($relatedBody['title'] . " " . $bodyToRelated));

		$wheres[] = 'MATCH (' . $relatedRows . ') AGAINST (' . $bodyToRelated . ') AND id !=' . $relatedBody['id'];

	}


	// Определяем переменные, чтоб сто раз не писать одно и тоже
	$bDay      = (int)$base->cfg['day'];
	$bDayCount = (int)$base->cfg['dayCount'];

	// Разбираемся с временными рамками отбора новостей, если кол-во дней указано - ограничиваем выборку, если нет - выводим без ограничения даты
	if ($bDay) {
		$wheres[] = 'date >= "' . $tooday . '" - INTERVAL ' . $bDay . ' DAY';
	}

	// Если задана переменная dayCount и day, а так же day больше dayCount - отбираем новости за указанный интервал от указанного периода
	if ($bDay && $bDayCount && ($bDayCount < $bDay)) {
		$wheres[] = 'date < "' . $tooday . '" - INTERVAL ' . ($bDay - $bDayCount) . ' DAY';
	} else {
		// Условие для отображения только тех постов, дата публикации которых уже наступила
		$wheres[] = 'date < "' . $tooday . '"';
	}


	// Складываем условия
	$where = (count($wheres)) ? ' WHERE ' . implode(' AND ', $wheres) : '';

	// Если нужен вывод аватарок - добавляем дополнительные условия в запрос
	$ext_query_fields = ($base->cfg['avatar']) ? ', u.name, u.user_group, u.foto ' : '';
	$ext_query = ($base->cfg['avatar']) ? $base->db->parse(' LEFT JOIN ?n u ON (p.autor=u.name) ', USERPREFIX . '_users'): '' ;

	// Поля, выбираемые из БД
	$selectRows = 'p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, p.allow_comm, p.comm_num, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason' . $ext_query_fields;

	// Определяем необходимость и данные для сортировки
	$orderBy = (count($orderArr)) ? 'ORDER BY ' . implode(', ', $orderArr) : '';

	// Запрос в БД (данные фильтруются в классе для работы с БД, так что можно не переживать), главное правильно сконструировать запрос.
	$query = 'SELECT ?p FROM ?n p LEFT JOIN ?n e ON (p.id=e.news_id) ?p ' . $orderBy . ' LIMIT ?i, ?i';

	// Определяем с какой страницы начинать вывод (при постраничке, или если указано в строке).
	$_startFrom = ($base->cfg['pageNum'] >= 1) ? ($base->cfg['limit'] * $base->cfg['pageNum'] - $base->cfg['limit'] + $base->cfg['startFrom']) : 0;

	// Получаем новости
	$list = $base->db->getAll($query, $selectRows, PREFIX . '_post', PREFIX . '_post_extras', $ext_query . $where, $_startFrom, $base->cfg['limit']);

	// Обрабатываем данные функцией stripslashes рекурсивно.
	$list = stripSlashesInArray($list);

	// Путь к папке с текущим шаблоном
	$tplArr['theme'] = '/templates/' . $base->dle_config['skin'];

	// Обрабатываем данные в массиве.
	foreach ($list as $key => $value) {
		// Плучаем обработанные допполя.
		$list[$key]['xfields'] = stripSlashesInArray(xfieldsdataload($value['xfields']));

		// Массив данных для формирования ЧПУ
		$urlArr = array(
			'category' => $value['category'],
			'id'       => $value['id'],
			'alt_name' => $value['alt_name'],
			'date'     => $value['date']
		);
		// Записываем сформированный URL статьи в массив
		$list[$key]['url'] = $base->getPostUrl($urlArr);

		// Добавляем тег edit
		if( $is_logged and (($member_id['name'] == $value['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
			$list[$key]['allow_edit'] = true;
			$list[$key]['editOnclick'] = 'onclick="return dropdownmenu(this, event, MenuNewsBuild(\'' . $value['id'] . '\', \'short\'), \'170px\')"';

		} else {
			$list[$key]['allow_edit'] = false;
			$list[$key]['editOnclick'] = '';
		}

		// Записываем сформированные теги в массив
		$list[$key]['tags'] = $base->tagsLink($value['tags']);

		// Записываем в массив ссылку на аватар
		$list[$key]['avatar'] = $tplArr['theme'] . '/dleimages/noavatar.png';
		// А если у юзера есть фотка - выводим её, или граватар.
		if ($value['foto']) {
			$userFoto = $value['foto'];
			if (count(explode('@', $userFoto)) == 2) {
				$list[$key]['avatar'] = 'http://www.gravatar.com/avatar/' . md5(trim($userFoto)) . '?s=' . intval($user_group[$value['user_group']]['max_foto']);
			} else {
				$list[$key]['avatar'] = $base->dle_config['http_home_url'] . 'uploads/fotos/' . $userFoto;
			}
		}

		// Разбираемся с рейтингом
		$list[$key]['showRating'] = '';
		$list[$key]['showRatingCount'] = '';
		if($value['allow_rate']) {
			$list[$key]['showRatingCount'] = '<span data-vote-num-id="' . $value['id'] . '">' . $value['vote_num'] . '</span>';

			if( $base->dle_config['short_rating'] and $user_group[$member_id['user_group']]['allow_rating'] ) {
				$list[$key]['showRating'] = baseShowRating($value['id'], $value['rating'], $value['vote_num'], 1);
			}
			else {
				$list[$key]['showRating'] = baseShowRating($value['id'], $value['rating'], $value['vote_num'], 0);
			}
		}
		// Разбираемся с избранным
		$list[$key]['favorites'] = '';
		if($is_logged) {
			$fav_arr = explode(',', $member_id['favorites']);

			if(!in_array($value['id'], $fav_arr) || $base->dle_config['allow_cache']) {
				$list[$key]['favorites'] = '<img data-favorite-id="' . $value['id'] . '" data-action="plus" src="' . $tplArr['theme'] . '/dleimages/plus_fav.gif"  title="Добавить в свои закладки на сайте" alt="Добавить в свои закладки на сайте" />';
			} else {
				$list[$key]['favorites'] = '<img data-favorite-id="' . $value['id'] . '" data-action="minus" src="' . $tplArr['theme'] . '/dleimages/minus_fav.gif"  title="Удалить из закладок" alt="Удалить из закладок" />';
			}
		}
	}

	// Полученный массив с данными для обработки в шаблоне
	$tplArr['list'] = $list;

	// Определяем группу пользователя
	$tplArr['member_group_id'] = $member_id['user_group'];

	// Устанавливаем пустое значение для постранички по умолчанию.
	$tplArr['pages'] = '';

	if ($base->cfg['showNav']) {

		// Получаем общее количество новостей по заданным параметрам отбора
		$totalCount = $base->db->getOne('SELECT COUNT(*) as count FROM ?n ?p', PREFIX . '_post', $where);
		// Вычитаем переменную startFrom для корректного значения кол-ва новостей
		$totalCount = $totalCount - $base->cfg['startFrom'];

		// Формируем имя кеш-файла с конфигом
		$pageCahceName = $base->cfg;
		// Удаляем номер страницы для того, что бы не создавался новый кеш для каждого блока постранички
		unset($pageCahceName['pageNum']);
		// Сокращаем немного имя файла :)
		$pageCahceName = 'bpa_' . crc32(implode('_', $pageCahceName));

		// Включаем кеширование DLE принудительно
		$cashe_tmp = $base->dle_config['allow_cache'];
		$config['allow_cache'] = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.

		// Проверяем есть ли кеш с указанным именем
		$ajaxCache = dle_cache($pageCahceName);
		// Если кеша нет
		if (!$ajaxCache) {
			// Сериализуем конфиг для последующей записи в кеш
			$pageCacheText = serialize($base->cfg);
			// Создаём кеш
			create_cache($pageCahceName, $pageCacheText);
		}

		// Возвращаем значение кеша DLE обратно
		$config['allow_cache'] = $cashe_tmp;

		// Подключаем класс постранички
		// require_once(BASE_DIR . '/core/pagination.php');

		// Массив с конфигурацией для формирования постранички
		$pagerConfig = array(
			'block_id'       => $pageCahceName,
			'total_items'    => $totalCount,
			'items_per_page' => $base->cfg['limit'],
			'style'          => $base->cfg['navStyle'],
			'current_page'   => $base->cfg['pageNum'],
		);
		$pagination  = new Pager($pagerConfig);

		// Сформированный блок с постраничкой
		$tplArr['pages'] = $pagination->render();
		// Уникальный ID для вывода в шаблон
		$tplArr['block_id'] = $pagerConfig['block_id'];

	} else {
		// Устанавливаем уникальный ID для блока по умолчаниюы
		$tplArr['block_id'] = 'bp_' . crc32(implode('_', $base->cfg));
	}

	// Результат обработки шаблона
	try {
		$output = $base->tpl->fetch($base->cfg['template'] . '.tpl', $tplArr);
	} catch (Exception $e) {
		$output               = '<div style="color: red;">' . $e->getMessage() . '</div>';
		$base->cfg['nocache'] = true;
	}

	// Формируем данные о запросах для статистики, если требуется
	if ($base->cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {
		$stat  = $base->db->getStats();
		$statQ = array();

		foreach ($stat as $i => $q) {
			$statQ['q'] .= '<br>' . '<b>[' . ($i + 1) . ']</b> ' . $q['query'] . ' <br>[' . ($i + 1) . ' время:] <b>' . $q['timer'] . '</b>';
			$statQ['t'] += $q['timer'];
		}
		$dbStat = 'Запрос(ы): ' . $statQ['q'] . '<br>Время выполнения запросов: <b>' . $statQ['t'] . '</b><br>';
	}
	// Создаём кеш, если требуется
	if (!$base->cfg['nocache']) {
		create_cache($base->cfg['cachePrefix'], $output, $cacheName, $cacheSuffix);
	}

}
// Результат работы модуля
echo $output;

// Показываем стстаистику выполнения скрипта, если требуется
if ($cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {
	// Информация об оперативке
	$mem_usg = (function_exists("memory_get_peak_usage")) ? '<br>Расход памяти: <b>' . round(memory_get_peak_usage() / (1024 * 1024), 2) . 'Мб </b>' : '';
	// Вывод статистикик
	echo '<div class="bp-statistics" style="border: solid 1px red; padding: 5px; margin: 5pxx 0;">' . $dbStat . 'Время выполнения скрипта: <b>' . round((microtime(true) - $start), 6) . '</b> c.' . $mem_usg . '</div>';
}


?>
