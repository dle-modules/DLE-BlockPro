<?php
/*
=============================================================================
BLockPro - основной модуль
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
email:   pafnuty10@gmail.com
=============================================================================
 */

if (!defined('DATALIFEENGINE')) {
    header("HTTP/1.1 403 Forbidden");
    header('Location: ../../');
    die("Hacking attempt!");
}

/**
 * @global array $config
 * @global array $member_id
 * @global array $lang
 * @global array $category_id
 * @global array $user_group
 */

/** @var bool $showstat */
if ($showstat) {
    $start  = microtime(true);
    $dbStat = '';
}
/**
 * Конфиг модуля
 *
 * @global array $isAjaxConfig
 */
if ($isAjaxConfig) {
    /** @var array $ajaxConfigArr */
    $cfg = $ajaxConfigArr;
} else {
    include ENGINE_DIR.'/modules/base/core/config.php';
}

include ENGINE_DIR.'/data/blockpro.php';

// Объединяем массивы конфигов
/** @var array $bpConfig */
$cfg = array_merge($cfg, $bpConfig);

// Получаем id текущей категории при AJAX навигации
if ($isAjaxConfig && ($cfg['catId'] == 'this' || $cfg['notCatId'] == 'this')) {
    /**
     * @var string $thisUrl
     * @see engine/ajax/blockpro.php
     */
    if (substr($thisUrl, -1, 1) == '/') {
        $thisUrl = substr($thisUrl, 0, -1);
    }
    $arThisUrl   = explode('/', $thisUrl);
    $thisCatName = end($arThisUrl);
    if (trim($thisCatName) != '') {
        $category_id = get_ID($cat_info, $thisCatName);
    }
}

// Сохраняем текущее значение переменной, если она задана (fix #142)
$startCacheNameAddon = $cfg['cacheNameAddon'];

$cfg['cacheNameAddon']   = [];
$cfg['cacheNameAddon'][] = $startCacheNameAddon;

// Если имеются переменные со значениями this, изменяем значение переменной cacheNameAddon
if ($cfg['catId'] == 'this') {
    $cfg['cacheNameAddon'][] = $category_id.'cId_';
}
if ($cfg['notCatId'] == 'this') {
    $cfg['cacheNameAddon'][] = $category_id.'nCId_';
}
if ($cfg['postId'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['newsid'].'pId_';
}
if ($cfg['notPostId'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['newsid'].'nPId_';
}
if ($cfg['author'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['user'].'a_';
}
if ($cfg['notAuthor'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['user'].'nA_';
}
if ($cfg['tags'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['tag'].'t_';
}
if ($cfg['notTags'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['tag'].'nT_';
}
if ($cfg['symbols'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['catalog'].'s_';
}
if ($cfg['notSymbols'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['catalog'].'nS_';
}
if ($cfg['related'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['newsid'].'r_';
}

if ($cfg['xfilter'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['xf'].'xf_';
}
if ($cfg['notXfilter'] == 'this') {
    $cfg['cacheNameAddon'][] = $_REQUEST['xf'].'nXf_';
}

if ($cfg['navDefaultGet']) {
    $cfg['cacheNameAddon'][] = $_REQUEST['cstart'].'cs_';
}

if ($cfg['cacheVars']) {
    // Если установлена переменная, добавим в имя кеша требуемые дополнения
    // Убираем пробелы, на всякий пожарный
    $cfg['cacheVars'] = str_replace(' ', '', $cfg['cacheVars']);
    // Разбиваем строку на массив
    $arCacheVars = explode(',', $cfg['cacheVars']);
    foreach ($arCacheVars as $cacheVar) {
        // Сверяем данные из массива с данными, доступными на странице
        if (isset($_REQUEST[$cacheVar])) {
            $cfg['cacheNameAddon'][] = $_REQUEST[$cacheVar].$cacheVar.'_';
        }
        if ($dle_module == $cacheVar) {
            $cfg['cacheNameAddon'][] = $dle_module.'_';
        }
    }


}

// Поддержка модуля multiLang
$multiLangEnabled = false;
// По умолчанию язык пустой
$langVariant = '';
// Задаём пустой массив для конфига модуля MultiLanguage
$lang_config = [];
// Получаем список доступных языков в виде массива
$langList                  = explode('|', $cfg['langList']);
$multiLangAdditionalFields = $multiLangNewsFields = [];

if ($cfg['multiLang'] && isset($_REQUEST['lang'])) {
    $requestLang = trim($_REQUEST['lang']);

    // Если язык доступен, работаем
    if (in_array($requestLang, $langList)) {
        // Импортируем конфиг модуля MultiLanguage
        include ENGINE_DIR.'/data/multilanguage_config.php';

        // Если модуль включен, работаем
        if ($lang_config['mod_on']) {
            // Добавляем параметры в кеш
            $cfg['cacheNameAddon'][] = 'multiLang_'.$_REQUEST['lang'].'_';
            // Устанавливаем нужный язык для дальнейшего использования в запросах
            $langVariant      = $requestLang;
            $multiLangEnabled = true;

            $multiLangAdditionalFields = [
                '1' => 'p.title_'.$langVariant,
                '2' => 'p.short_story_'.$langVariant,
                '3' => 'p.full_story_'.$langVariant,
            ];

            $multiLangNewsFields = [
                '1' => [0 => 'title', 1 => 'title_'.$langVariant],
                '2' => [0 => 'short_story', 1 => 'short_story_'.$langVariant],
                '3' => [0 => 'full_story', 1 => 'full_story_'.$langVariant],
            ];

            $multiLngEnabledFields = explode(',', $lang_config['fields_news']);
        }
    }
}

$cfg['cacheNameAddon'] = array_filter($cfg['cacheNameAddon']);
// Удаляем дублирующиеся значения кеша. Может возникать при AJAX вызове с &catId=this
$cfg['cacheNameAddon'] = array_unique($cfg['cacheNameAddon']);
$cfg['cacheNameAddon'] = implode('_', $cfg['cacheNameAddon']);

if ($cfg['cacheLive']) {
    // Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
    $cfg['cachePrefix'] = 'base';
}

// Определяемся с шаблоном сайта
// Проверим куку пользователя и наличие параметра skin в реквесте.
$currentSiteSkin = (isset($_COOKIE['dle_skin'])) ? trim(totranslit($_COOKIE['dle_skin'], false, false))
    : ((isset($_REQUEST['skin'])) ? trim(totranslit($_REQUEST['skin'], false, false)) : $config['skin']);

// Если  итоге пусто — назначим опять шаблон из конфига.
if ($currentSiteSkin == '') {
    $currentSiteSkin = $config['skin'];
}
// Если парки с шаблоном нет — дальше не работаем.
if (!@is_dir(ROOT_DIR.'/templates/'.$currentSiteSkin)) {
    die('no_skin');
}

// Формируем имя кеша
$cacheName = implode('_', $cfg).$currentSiteSkin;

// Определяем необходимость создания кеша для разных групп
$cacheSuffix      = ($cfg['cacheSuffixOff']) ? false : true;
$clear_time_cache = false;
// Если установлено время жизни кеша
if ($cfg['cacheLive']) {
    // Формируем имя кеш-файла в соответствии с правилами формирования такового стандартными средствами DLE, для последующей проверки на существование этого файла.
    $_end_file = (!$cfg['cacheSuffixOff']) ? ($is_logged) ? '_'.$member_id['user_group'] : '_0' : false;
    $filedate  = ENGINE_DIR.'/cache/'.$cfg['cachePrefix'].'_'.md5($cacheName).$_end_file.'.tmp';

    // Определяем в чём измеять время жизни кеша, в минутах или секундах
    $cacheLiveTimer = (strpos($cfg['cacheLive'], 's')) ? (int)$cfg['cacheLive'] : $cfg['cacheLive'] * 60;

    if (@file_exists($filedate)) {
        $cache_time = time() - @filemtime($filedate);
    } else {
        $cache_time = $cacheLiveTimer;
    }
    if ($cache_time >= $cacheLiveTimer) {
        $clear_time_cache = true;
    }
}

$output = false;

// Массив для записи возникающих ошибок
$outputLog = [
    'errors' => [],
    'info'   => [],
];

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
    include_once 'core/base.php';

    // Вызываем ядро
    $base = new base();

    // Назначаем конфиг модуля
    $base->cfg = $base->setConfig($cfg);

    // Назначаем текущий шаблон сайта
    $base->dle_config['skin'] = $currentSiteSkin;

    // Пустой массив для конфга шаблонизатора.
    $tplOptions = [];

    // Если кеширование блока отключено - будем автоматически проверять скомпилированный шаблон на изменения.
    if ($base->cfg['nocache']) {
        $tplOptions['auto_reload'] = true;
    }

    // Подключаем опции шаблонизатора
    $base->tplOptions = $base->setConfig($tplOptions);
    // Подключаем шаблонизатор
    $base->getTemplater($base->tplOptions);


    // Добавляем глобавльный тег $.blockPro
    $base->tpl->addAccessorSmart('blockPro', 'block_pro', Fenom::ACCESSOR_PROPERTY);
    $base->tpl->block_pro = $base;

    // Определяем начало сегодняшнего дня и дату "прямо вот сейчас".
    if ($base->dle_config['version_id'] > 10.2) {
        date_default_timezone_set($base->dle_config['date_adjust']);
        $today    = date('Y-m-d H:i:s', (mktime(0, 0, 0) + 86400));
        $rightNow = date('Y-m-d H:i:s', time());
    } else {
        $today    = date('Y-m-d H:i:s', (mktime(0, 0, 0) + 86400 + $base->dle_config['date_adjust'] * 60));
        $rightNow = date('Y-m-d H:i:s', (time() + $base->dle_config['date_adjust'] * 60));
    }

    if ($base->cfg['navDefaultGet']) {
        $base->cfg['pageNum'] = (isset($_GET['cstart']) && (int)$_GET['cstart'] > 0) ? (int)$_GET['cstart'] : 1;
    }

    // Массив с условиями запроса
    $wheres = [];

    // По умолчанию имеем пустые дополнения в запрос.
    $ext_query_fields = $ext_query = '';

    // По умолчанию группировки нет (она нам понадобится при фильтрации по аттачментам).
    $groupBy = '';

    // Условие для отображения только постов, прошедших модерацию или находящихся на модерации
    $wheres[] = ($base->cfg['moderate']) ? 'approve = 0' : 'approve';

    // Добавляем пользовательскую фильтрацию
    if ($base->cfg['setFilter'] != '') {

        $arFilter = [];

        if (strpos($base->cfg['setFilter'], '||')) {
            $arFilter = explode('||', $base->cfg['setFilter']);
        } else {
            $arFilter[] = $base->cfg['setFilter'];
        }


        if (!empty($arFilter)) {
            foreach ($arFilter as $strItem) {
                $queryItem = $base->prepareFilterQuery($strItem);

                if ($queryItem) {
                    $wheres[] = $queryItem;
                }

            }
        }
    }

    // Определяем в какую сторону направлена сортировка
    $ordering = ($base->cfg['order'] == 'new') ? 'DESC' : 'ASC';

    // Если без сортировки - сбрасываем направление
    if ($base->cfg['sort'] == 'none') {
        $ordering = false;
    }
    // Массив, куда будем записывать сортировки
    $orderArr = [];

    // Учёт фиксированных новостей
    switch ($base->cfg['fixed']) {
        case 'only':
            $wheres[] = 'fixed = 1';
            break;

        case 'without':
            $wheres[] = 'fixed = 0';
            break;

        case 'ignore':
            $wheres[] = '';
            break;

        default:
            // Если включен режим афиши и установлена сортировка по убыванию даты, фиксированные новости должны отображаться в начале списка.
            $fixedNewsOrdering = ($base->cfg['future'] && $base->cfg['sort'] != 'new') ? 'DESC' : $ordering;

            if ($base->cfg['sort'] != 'random' && $base->cfg['sort'] != 'randomLight' && $base->cfg['sort'] != 'none'
                && $base->cfg['sort'] != 'editdate'
            ) {
                $orderArr[] = 'fixed '.$fixedNewsOrdering;
            }
            break;
    }

    // Учёт новостей на главной
    switch ($base->cfg['allowMain']) {
        case 'only':
            $wheres[] = 'allow_main = 1';
            break;

        case 'without':
            $wheres[] = 'allow_main = 0';
            break;

        default:
            // по умолчанию показываем все
            break;
    }
    // Зададим произвольное имя для несуществующей сортировки
    $xfSortName = 'blockpro_undefined_sort_xfields';
    if (strpos($base->cfg['sort'], 'xf|') !== false) {
        // Если задана сортировка по значению допполя - то присвоим переменной имя этого поля и подкорректируем данные для swich
        $xfSortName = str_replace('xf|', '', $base->cfg['sort']);
    }

    $fieldSortName = 'blockpro_undefined_sort_field';

    if (strpos($base->cfg['sort'], 'field|') !== false) {
        // Если задана сортировка по кастомном полю - то присвоим переменной имя этого поля и подкорректируем данные для swich
        $fieldSortName = str_replace('field|', '', $base->cfg['sort']);
    }

    // Определяем тип сортировки
    switch ($base->cfg['sort']) {
        case 'none':    // Не сортировать (можно использовать для вывода похожих новостей, аналогично стандарту DLE)
            // $orderArr[] = false;
            break;

        case 'date':    // Дата
            $orderArr[] = 'p.date '.$ordering;
            break;

        case 'rating':    // Рейтинг
            $orderArr[] = 'e.rating '.$ordering;
            break;

        case 'comms':    // Комментарии
            $orderArr[] = 'p.comm_num '.$ordering;
            break;

        case 'views':    // Просмотры
            $orderArr[] = 'e.news_read '.$ordering;
            break;

        case 'random':    // Случайные
            $orderArr[] = 'RAND()';
            // randomLight ниже т.к. у него отдельный алгоритм
            break;

        case 'title':    // По алфавиту
            $orderArr[] = 'p.title '.$ordering;
            break;

        case 'download':    // По количеству скачиваний
            $orderArr[] = 'dcount '.$ordering;
            break;

        case 'symbol':    // По символьному коду
            $orderArr[] = 'p.symbol '.$ordering;
            break;

        case 'hit':    // Правильный топ
            $wheres[]   = 'e.rating > 0';
            $orderArr[] = '(e.rating*100+p.comm_num*10+e.news_read) '.$ordering;
            break;

        case 'top':    // Топ как в DLE (сортировка по умолчанию)
            $orderArr[] = 'e.rating '.$ordering.', p.comm_num '.$ordering.', e.news_read '.$ordering;
            break;

        case 'editdate':    // По дате редактрования
            $orderArr[] = 'e.editdate '.$ordering;
            $wheres[]   = 'e.editdate > 0';
            break;

        case 'xf|'.$xfSortName:    // Сортировка по значению дополнительного поля
            if ($base->cfg['xfSortType'] == 'string') {
                $orderArr[] = 'sort_xfield '.$ordering;
            } else {
                $orderArr[] = 'CAST(sort_xfield AS DECIMAL(12,2)) '.$ordering;
            }
            $ext_query_fields .= ", SUBSTRING_INDEX(SUBSTRING_INDEX(p.xfields,  '{$xfSortName}|', -1 ) ,  '||', 1 ) as sort_xfield ";
            break;

        case 'field|'.$fieldSortName: // Сортировка по кастомному полю
            $orderArr[] = $fieldSortName.' '.$ordering;
            break;

    }

    // Необходимо учитывать категорию для вывода похожих новостей, если категорию не задал пользователь.
    // https://github.com/dle-modules/DLE-BlockPro/issues/155
    if (!$base->cfg['catId'] && $base->cfg['related'] && $base->dle_config['related_only_cats']) {
        $base->cfg['catId'] = 'this';
    }

    // Эти переменные потребуются ниже, что бы корректно сформировать имя кеша, когда переданы
    // &catId=this или notCatId=this
    $isCatIdThis    = false;
    $isNotCatIdThis = false;

    // Фильтрация КАТЕГОРИЙ по их ID
    if ($base->cfg['catId'] == 'this' && $category_id) {
        $isCatIdThis = true;
        /**
         * @see https://github.com/dle-modules/DLE-BlockPro/issues/159
         */
        $base->cfg['catId'] = $category_id;
        if ($base->cfg['subcats']) {
            $base->cfg['catId'] = get_sub_cats($category_id);
        } elseif ($base->cfg['thisCatOnly']) {
            $base->cfg['catId'] = (int)$category_id;
        }
    }
    if ($base->cfg['notCatId'] == 'this' && $category_id) {
        $isNotCatIdThis = true;
        /**
         * @see https://github.com/dle-modules/DLE-BlockPro/issues/159
         */
        $base->cfg['notCatId'] = $category_id;
        if ($base->cfg['notSubcats']) {
            $base->cfg['notCatId'] = get_sub_cats($category_id);
        } elseif ($base->cfg['thisCatOnly']) {
            $base->cfg['notCatId'] = (int)$category_id;
        }
    }
    // Дублирование кода вызвано необходимостью сочетания параметра notCatId и catId
    // Например: catId=this&notCatId=3
    if ($base->cfg['notCatId']) {
        $notCatArr = $base->getDiapazone($base->cfg['notCatId'], $base->cfg['notSubcats']);
        if ($notCatArr[0] > 0) {
            if ($base->dle_config['allow_multi_category'] && !$base->cfg['thisCatOnly']) {
                $notCatQPart = 'category regexp "[[:<:]]('.str_replace(',', '|', $notCatArr).')[[:>:]]"';
            } else {
                $notCatQPart = 'category IN (\''.str_replace(',', "','", $notCatArr).'\')';
            }

            $wheres[] = 'NOT '.$notCatQPart;
        }
    }

    if ($base->cfg['catId']) {
        $catArr = $base->getDiapazone($base->cfg['catId'], $base->cfg['subcats']);
        if ($catArr[0] > 0) {
            if ($base->dle_config['allow_multi_category'] && !$base->cfg['thisCatOnly']) {
                $catQPart = 'category regexp "[[:<:]]('.str_replace(',', '|', $catArr).')[[:>:]]"';
            } else {
                $catQPart = 'category IN (\''.str_replace(',', "','", $catArr).'\')';
            }

            $wheres[] = $catQPart;
        }
    }

    // Фильтрация НОВОСТЕЙ по их ID
    if ($base->cfg['postId'] == 'this' && $_REQUEST['newsid']) {
        $base->cfg['postId'] = $_REQUEST['newsid'];
    }
    if ($base->cfg['notPostId'] == 'this' && $_REQUEST['newsid']) {
        $base->cfg['notPostId'] = $_REQUEST['newsid'];
    }

    if ($base->cfg['notPostId']) {
        $notPostsArr = $base->getDiapazone($base->cfg['notPostId']);
        if ($notPostsArr !== '0') {
            $wheres[] = 'id NOT IN ('.$notPostsArr.')';
        }
    }

    if ($base->cfg['postId'] && $base->cfg['related'] == '') {
        $postsArr = $base->getDiapazone($base->cfg['postId']);
        if ($postsArr !== '0') {
            $wheres[] = 'id IN ('.$postsArr.')';
        }
    }

    // Фильтрация новостей по АВТОРАМ
    if ($base->cfg['author'] == 'this' && isset($_REQUEST['user'])) {
        $base->cfg['author'] = $_REQUEST['user'];
    }
    if ($base->cfg['notAuthor'] == 'this' && isset($_REQUEST['user'])) {
        $base->cfg['notAuthor'] = $_REQUEST['user'];
    }
    if ($base->cfg['author'] || $base->cfg['notAuthor']) {
        $ignoreAuthors = ($base->cfg['notAuthor']) ? 'NOT ' : '';
        $authorsArr    = ($base->cfg['notAuthor']) ? $base->cfg['notAuthor'] : $base->cfg['author'];
        if ($authorsArr !== 'this') {
            // Если в строке подключения &author=this и мы просматриваем страницу юзера, то сюда уже попадёт логин пользователя
            $authorsArr = explode(',', $authorsArr);
            $wheres[]   = (count($authorsArr) === 1) ? $ignoreAuthors.'autor = '.$base->db->parse('?s', $authorsArr[0])
                : $ignoreAuthors.'autor regexp "[[:<:]]('.implode('|', $authorsArr).')[[:>:]]"';
        }
    }

    // Фильтрация новостей по ДОПОЛНИТЕЛЬНЫМ ПОЛЯМ (проверяется только на заполненность)
    $_currentXfield = false;
    if ($base->cfg['xfilter'] == 'this' && isset($_REQUEST['xf'])) {
        $base->cfg['xfilter'] = $base->db->parse('?s', '%'.$_REQUEST['xf'].'%');
        $_currentXfield       = true;
    }
    if ($base->cfg['notXfilter'] == 'this' && isset($_REQUEST['xf'])) {
        $base->cfg['notXfilter'] = $base->db->parse('?s', '%'.$_REQUEST['xf'].'%');
        $_currentXfield          = true;
    }

    if ($base->cfg['xfilter'] || $base->cfg['notXfilter']) {
        $ignoreXfilters = ($base->cfg['notXfilter']) ? 'NOT ' : '';
        $xfiltersArr    = ($base->cfg['notXfilter']) ? $base->cfg['notXfilter'] : $base->cfg['xfilter'];

        if ($xfiltersArr !== 'this') {
            // Если в строке подключения &xfilter=this и мы просматриваем страницу допполя, то сюда уже попадёт имя этого поля
            $wheres[] = ($_currentXfield) ? $ignoreXfilters.'xfields LIKE '.$xfiltersArr
                : $ignoreXfilters.'p.xfields regexp "[[:<:]]('.str_replace(',', '|', $xfiltersArr).')[[:>:]]"';
        }
    }

    // Фильтрация по ЗНАЧЕНИЮ ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ
    if ($base->cfg['xfSearch'] || $base->cfg['notXfSearch']) {

        // Массив для составления подзапроса
        $xfWheres = [];

        // Защита логики построения запроса от кривых рук (если прописать неправильно - будет логика OR)
        $_xfSearchLogic = (strtolower($base->cfg['xfSearchLogic']) == 'and') ? ' AND ' : ' OR ';

        // Определяем масивы с данными по фильтрации
        $xfSearchArray    = ($base->cfg['xfSearch']) ? explode('||', $base->cfg['xfSearch']) : [];
        $notXfSearchArray = ($base->cfg['notXfSearch']) ? explode('||', $base->cfg['notXfSearch']) : [];

        $bXfNotResult = true;
        if ($base->dle_config['version_id'] >= '11' && $base->cfg['experiment']) {
            // Если версия DLE 11 и более и включена экспериментальная функция, то для увеличения скорости выборки запросим данные по допполям из отдельной таблицы.

            // Пробегаем по сформированным массивам
            foreach ($xfSearchArray as $xf) {
                $_xf        = explode('|', $xf);
                $xfWheres[] = $base->db->parse('(tagname=?s AND tagvalue=?s)', $_xf[0], $_xf[1]);
            }
            foreach ($notXfSearchArray as $xf) {
                $_xf        = explode('|', $xf);
                $xfWheres[] = $base->db->parse('(tagname=?s AND NOT tagvalue=?s)', $_xf[0], $_xf[1]);
            }
            // Подготавливаем запрос.
            $xfSearchQuery = implode($_xfSearchLogic, $xfWheres);

            // Получаем ID новостей
            $xfSearchIDs = $base->db->getCol('SELECT news_id FROM ?n  WHERE ?p', PREFIX.'_xfsearch', $xfSearchQuery);
            // Если запрос вернул ID новостей — работаем.
            if (count($xfSearchIDs)) {
                // Оставляем только уникальные
                $xfSearchIDs = array_unique($xfSearchIDs);

                // Добавляем полученные данные в основной массив, формирующий запрос
                $wheres[] = 'p.id IN ('.implode(',', $xfSearchIDs).')';

                $bXfNotResult = false;
            } else {
                // Если ничего не найдено — сбросим массив с условиями т.к. строка подключения возможно содержит дополнительные условия выборки.
                $xfWheres = [];
            }
        }

        if ($bXfNotResult) {
            // Пробегаем по сформированным массивам
            // str_replace('|', '|%', $xf) необходимо для случаев, когда значение допполя идёт не первым в списке
            foreach ($xfSearchArray as $xf) {
                $xfWheres[] = $base->db->parse('p.xfields LIKE ?s', '%'.str_replace('|', '|%', $xf).'%');
            }
            foreach ($notXfSearchArray as $xf) {
                $xfWheres[] = $base->db->parse('p.xfields NOT LIKE ?s', '%'.str_replace('|', '|%', $xf).'%');
            }

            // Добавляем полученные данные (и логику) в основной массив, формирующий запрос
            $wheres[] = '('.implode($_xfSearchLogic, $xfWheres).')';
        }
    }

    // Фильтрация новостей по ТЕГАМ
    $_currentTag = false;
    if ($base->cfg['tags'] == 'this' && isset($_REQUEST['tag']) && $_REQUEST['tag'] != '') {
        $base->cfg['tags'] = $base->db->parse('?s', $_REQUEST['tag']);
        $_currentTag       = true;
    }
    if ($base->cfg['notTags'] == 'this' && isset($_REQUEST['tag']) && $_REQUEST['tag'] != '') {
        $base->cfg['notTags'] = $base->db->parse('?s', $_REQUEST['tag']);
        $_currentTag          = true;
    }

    // Фильтрация новостей по тегам текущей новости, когда в строке подключения прописано &tags=thisNewsTags и мы просматриваем полную новость
    if ((int)$_REQUEST['newsid'] > 0) {
        if ($base->cfg['tags'] == 'thisNewsTags' || $base->cfg['notTags'] == 'thisNewsTags') {
            $curTagNewsId = $base->db->getRow('SELECT tags FROM ?n WHERE id=?i', PREFIX.'_post', $_REQUEST['newsid']);
            if (!empty($curTagNewsId['tags'])) {
                if ($base->cfg['tags'] == 'thisNewsTags') {
                    // Заменяем запятую и пробел на просто запятую, иначе будет ошибка.
                    $base->cfg['tags'] = str_replace(', ', ',', $curTagNewsId['tags']);
                }
                if ($base->cfg['notTags'] == 'thisNewsTags') {
                    // Заменяем запятую и пробел на просто запятую, иначе будет ошибка.
                    $base->cfg['notTags'] = str_replace(', ', ',', $curTagNewsId['notTags']);
                }
            }
        }
    }

    if ($base->cfg['tags'] || $base->cfg['notTags']) {
        $ignoreTags = ($base->cfg['notTags']) ? 'NOT ' : '';
        $tagsArr    = ($base->cfg['notTags']) ? $base->cfg['notTags'] : $base->cfg['tags'];


        if ($tagsArr !== 'this') {
            // Если в строке подключения &tags=this и мы просматриваем страницу тегов, то сюда уже попадёт название тега
            $wherTag = ($_currentTag) ? $ignoreTags.'tag = '.$tagsArr
                : $ignoreTags.'tag regexp "[[:<:]]('.str_replace(',', '|', $tagsArr).')[[:>:]]"';
            // Делаем запрос на получение ID новостей, содержащих требуемые теги
            $tagNews = $base->db->getCol('SELECT news_id FROM ?n  WHERE ?p', PREFIX.'_tags', $wherTag);
            $tagNews = array_unique($tagNews);

            if (count($tagNews)) {
                $wheres[] = 'id '.$ignoreTags.' IN ('.implode(',', $tagNews).')';
            } else {
                // Fix #160
                $wheres[] = 'id = 0';
            }

        }
    }

    // Фильтрация новостей по символьным кодам
    $_currentSymbol = false;
    if ($base->cfg['symbols'] == 'this' && isset($_REQUEST['catalog'])) {
        $base->cfg['symbols'] = $base->db->parse('?s', $_REQUEST['catalog']);
        $_currentSymbol       = true;
    }
    if ($base->cfg['notSymbols'] == 'this' && isset($_REQUEST['catalog'])) {
        $base->cfg['notSymbols'] = $base->db->parse('?s', $_REQUEST['catalog']);
        $_currentSymbol          = true;
    }

    if ($base->cfg['symbols'] || $base->cfg['notSymbols']) {
        $ignoreSymbols = ($base->cfg['notSymbols']) ? 'NOT ' : '';
        $symbolsArr    = ($base->cfg['notSymbols']) ? $base->cfg['notSymbols'] : $base->cfg['symbols'];
        if ($symbolsArr !== 'this') {
            // Если в строке подключения &symbols=this и мы просматриваем страницу буквенного каталога, то сюда уже попадёт название буквы
            $wheres[] = ($_currentSymbol) ? $ignoreSymbols.'symbol = '.$symbolsArr
                : $ignoreSymbols.'symbol regexp "[[:<:]]('.str_replace(',', '|', $symbolsArr).')[[:>:]]"';
        }
    }

    // Если включен режим вывода похожих новостей:
    $reltedFirstShow = false;
    if ($base->cfg['related']) {
        if ($base->cfg['related'] == 'this' && $_REQUEST['newsid'] == '') {
            $outputLog['errors'][]
                = 'Переменная related=this работает только в полной новости и не работает с ЧПУ 3 типа.';
        } else {

            $relatedId       = ($base->cfg['related'] == 'this') ? $_REQUEST['newsid'] : $base->cfg['related'];
            $relatedRows     = 'p.title, p.short_story, p.full_story, p.xfields';
            $relatedIdParsed = $base->db->parse('id = ?i', $relatedId);

            $relatedBody = $base->db->getRow('SELECT id, ?p FROM ?n p LEFT JOIN ?n e ON (p.id=e.news_id) WHERE ?p',
                'p.title, p.short_story, p.full_story, p.xfields, e.related_ids', PREFIX.'_post', PREFIX.'_post_extras',
                $relatedIdParsed);
            // Фикс https://github.com/dle-modules/DLE-BlockPro/issues/78
            if ($relatedBody['id']) {
                /** @var bool $saveRelated */
                if ($relatedBody['related_ids'] && $saveRelated) {
                    // Если есть запись id похожих новостей — добавим в условие запроса эти новости.
                    $wheres[] = 'id IN('.$relatedBody['related_ids'].')';
                    // Отсортируем новости в том порядке, в котором они записаны в БД
                    $orderArr = ['FIELD (p.id, '.$relatedBody['related_ids'].')'];
                } else {
                    // Если похожие новости не записывались — отберём их.
                    $reltedFirstShow = true;
                    $bodyToRelated   = (dle_strlen($relatedBody['full_story'], $base->dle_config['charset'])
                        < dle_strlen($relatedBody['short_story'], $base->dle_config['charset']))
                        ? $relatedBody['short_story'] : $relatedBody['full_story'];

                    $bodyToRelated = strip_tags(stripslashes($relatedBody['title'].' '.$bodyToRelated));

                    // Фикс для https://github.com/pafnuty/BlockPro/issues/79
                    // @see /engine/modules/show.full.php
                    if (dle_strlen($bodyToRelated, $base->dle_config['charset']) > 1000) {
                        $bodyToRelated = dle_substr($bodyToRelated, 0, 1000, $base->dle_config['charset']);
                    }

                    $bodyToRelated = $base->db->parse('?s', $bodyToRelated);

                    // Добавляем улучшенный алгоритм поиска похожих новостей из DLE 13
                    $ext_query_fields .= ', MATCH (p.title, p.short_story, p.full_story, p.xfields) AGAINST ('
                        .$bodyToRelated.') as score';
                    $orderArr         = ['score DESC'];

                    // Формируем условие выборки
                    $wheres[] = 'MATCH ('.$relatedRows.') AGAINST ('.$bodyToRelated.') AND id !='.$relatedBody['id'];
                }
            } else {
                $outputLog['errors'][] = 'Новость с ID '.$relatedIdParsed.' не найдена в базе данных.';
            }
        }


    }

    // Определяем переменные, чтоб сто раз не писать одно и тоже
    $bDay      = (int)$base->cfg['day'];
    $bDayCount = (int)$base->cfg['dayCount'];

    // Если в bDay и bDayCount передано '-1', значит требуется вывести новости только за сегодня.
    // Это обработка случая, когда включен вывод новостей на ненаступившую дату и надо вывести за сегодня
    // https://pafnuty.omnidesk.ru/staff/cases/record/181-466935/
    if ($bDay === -1 && $bDayCount === -1) {
        // Формируем вывод новостей только за сегодня
        $wheres[] = 'p.date >= "'.$today.'" - INTERVAL 1 DAY';
        $wheres[] = 'p.date < "'.$today.'"';
    } else {
        // Если future задан, то интервал не вычитаем, а прибавляем к текущему началу дня
        $intervalOperator = ($base->cfg['future']) ? ' + ' : ' - ';

        // Если режим афиши включен - выводим новости, дата которых ещё не наступила.
        if ($base->cfg['future'] && (!$bDay && !$bDayCount)) {
            $wheres[] = 'p.date > "'.$rightNow.'"';
        }

        // Если включен вывод новостей на ненаступившую дату в настройках DLE
        // и режим афиши не используется, то нельзя выводить новости дата которых не наступила до текущей секунды
        if (!$base->cfg['future'] && ($base->dle_config['news_future'] && $base->dle_config['news_future'] !== 'no')) {
            $wheres[] = 'p.date <= "'.$rightNow.'"';
        }

        // Разбираемся с временными рамками отбора новостей, если кол-во дней указано - ограничиваем выборку, если нет - выводим без ограничения даты
        if ($bDay) {
            $wheres[] = 'p.date >= "'.$today.'" '.$intervalOperator.' INTERVAL '.(($base->cfg['future']) ? ($bDay
                    - $bDayCount) : $bDay).' DAY';
        }
        // Если задана переменная dayCount и day, а так же day больше dayCount - отбираем новости за указанный интервал от указанного периода
        if ($bDay && $bDayCount && ($bDayCount <= $bDay)) {
            $wheres[] = 'p.date < "'.$today.'" '.$intervalOperator.' INTERVAL '.(($base->cfg['future']) ? $bDay
                    : ($bDay - $bDayCount)).' DAY';
        } else {
            // Условие для отображения только тех постов, дата публикации которых уже наступила
            $wheres[] = ($base->dle_config['no_date'] && !$base->dle_config['news_future'] && !$base->cfg['future'])
                ? 'p.date < "'.$rightNow.'"' : '';
        }

    }

    // Подчистим массив от пустых значений
    $wheres = array_filter($wheres);

    // Когда выбран вариант вывода случайных новостей (Лёгкий режим)
    if ($base->cfg['sort'] == 'randomLight') {

        // Складываем условия выборки для рандомных новостей
        $randWhere = (count($wheres)) ? ' WHERE '.implode(' AND ', $wheres) : '';
        // Получим массив с id новостей
        $randDiapazone = $base->db->getCol('SELECT id FROM ?n AS p ?p', PREFIX.'_post', $randWhere);
        // Перемешаем
        shuffle($randDiapazone);
        // Возьмём только нужное количество элементов
        $randIds = array_slice($randDiapazone, 0, $base->cfg['limit']);
        // Удалим из памяти ненужное
        unset($randDiapazone);
        unset($randWhere);
        // Если вдруг не получится набрать элементы в принципе
        if (count($randIds)) {
            $randIds = implode(',', $randIds);
            // Сбрасываем ненужные условия выборки
            $wheres = [];
            // Задаём условие выборки по предварительно полученным ID
            $wheres[] = 'id IN ('.$randIds.')';
            // И выводим в том порядке, в ктором сформировались ID
            $orderArr = ['FIELD (p.id, '.$randIds.')'];
        }

    }

    // Добавлем условия выборки для новостей на другом языке при включении поддержки MultiLanguage
    if ($multiLangEnabled) {
        if ($lang_config['hide_news_on']) {
            foreach ($multiLngEnabledFields as $enabledField) {
                $wheres[] = $multiLangAdditionalFields[$enabledField].' <> \'\'';
            }
        }
    }

    // Складываем условия
    $where = (count($wheres)) ? ' WHERE '.implode(' AND ', $wheres) : '';

    // Если нужен вывод аватарок - добавляем дополнительные условия в запрос
    if ($base->cfg['avatar']) {
        $ext_query_fields .= ', u.name, u.user_group, u.foto ';
        $ext_query        .= $base->db->parse(' LEFT JOIN ?n u ON (p.autor=u.name) ', USERPREFIX.'_users');
    }

    // Если выбрана сортировка по кол-ву скачиваний прикрепленных файлов
    if ($base->cfg['sort'] == 'download') {
        $ext_query_fields .= ', d.news_id, sum(d.dcount) as dcount ';
        $ext_query        .= $base->db->parse(' LEFT JOIN ?n d ON (p.id=d.news_id) ', PREFIX.'_files');

        // Группируем новости по ID т.к. иначе появятся дубликаты при нескольких атачметах в новости.
        $groupBy = ' GROUP BY p.id ';
    }

    $customFields = '';
    if ($base->cfg['fields']) {
        $customFields = ', '.trim($base->cfg['fields']);
    }

    // Добавляем дополнительные поля в запрос при включении поддержки MultiLanguage
    if ($multiLangEnabled) {
        $customFields = ', '.implode(', ', $multiLangAdditionalFields);
    }

    // Поля, выбираемые из БД
    $selectRows
        = 'p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, p.allow_comm, p.comm_num, p.fixed, p.allow_main, p.symbol, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.related_ids, e.view_edit, e.editdate, e.editor, e.reason'
        .$customFields.$ext_query_fields;

    /** @var array $postsArr */
    if ($base->cfg['order'] == 'asis' && $base->cfg['postId'] && $postsArr) {
        $orderArr = ['FIELD (p.id, '.$postsArr.')'];
    }
    // Определяем необходимость и данные для сортировки
    $orderBy = (count($orderArr)) ? 'ORDER BY '.implode(', ', $orderArr) : '';

    // Запрос в БД (данные фильтруются в классе для работы с БД, так что можно не переживать), главное правильно сконструировать запрос.
    $query = 'SELECT ?p FROM ?n p LEFT JOIN ?n e ON (p.id=e.news_id) ?p '.$groupBy.' '.$orderBy.' LIMIT ?i, ?i';

    // Определяем с какой страницы начинать вывод (при постраничке, или если указано в строке).
    $_startFrom = ($base->cfg['pageNum'] >= 1) ? ($base->cfg['limit'] * $base->cfg['pageNum'] - $base->cfg['limit']
        + $base->cfg['startFrom']) : 0;

    // Получаем новости
    $list = $base->db->getAll($query, $selectRows, PREFIX.'_post', PREFIX.'_post_extras', $ext_query.$where,
        $_startFrom, $base->cfg['limit']);

    // Обрабатываем данные функцией stripslashes рекурсивно.
    $list = stripSlashesInArray($list);

    // Путь к папке с текущим шаблоном
    $tplArr['theme'] = $base->dle_config['http_home_url'].'templates/'.$base->dle_config['skin'];

    // Делаем доступным конфиг DLE внутри шаблона
    $tplArr['dleConfig'] = $base->dle_config;

    // Делаем доступной переменную $dle_module в шаблоне
    $tplArr['dleModule'] = $dle_module;

    // Делаем доступной переменную $lang в шаблоне
    $tplArr['lang']        = $lang;
    $tplArr['cacheName']   = $cacheName;
    $tplArr['category_id'] = $category_id;
    $tplArr['cfg']         = $cfg;
    $tplArr['langVariant'] = $langVariant;
    // Массив для аттачей и похожих новостей.
    $attachments = $relatedIds = [];

    // Обрабатываем данные в массиве.
    foreach ($list as $key => &$newsItem) {
        // Плучаем обработанные допполя.
        $newsItem['xfields'] = stripSlashesInArray(xfieldsdataload($newsItem['xfields']));
        // Собираем массив вложений
        $attachments[] = $relatedIds[] = $newsItem['id'];

        // Массив данных для формирования ЧПУ
        $urlArr = [
            'category' => $newsItem['category'],
            'id'       => $newsItem['id'],
            'alt_name' => $newsItem['alt_name'],
            'date'     => $newsItem['date'],
        ];
        // Записываем сформированный URL статьи в массив
        $newsItem['url'] = $base->getPostUrl($urlArr, $langVariant);

        // Присваиваем полям необходимые значения в зависимости от языка
        if ($multiLangEnabled) {
            foreach ($multiLngEnabledFields as $enabledField) {
                $newsItem[$multiLangNewsFields[$enabledField][0]] = $newsItem[$multiLangNewsFields[$enabledField][1]];
            }
        }

        // Добавляем тег edit
        if ($is_logged and (($member_id['name'] == $newsItem['autor']
                    and $user_group[$member_id['user_group']]['allow_edit'])
                or $user_group[$member_id['user_group']]['allow_all_edit'])
        ) {
            $_SESSION['referrer']    = $_SERVER['REQUEST_URI'];
            $newsItem['allow_edit']  = true;
            $newsItem['editOnclick'] = 'onclick="return dropdownmenu(this, event, MenuNewsBuild(\''.$newsItem['id']
                .'\', \'short\'), \'170px\')"';

        } else {
            $newsItem['allow_edit']  = false;
            $newsItem['editOnclick'] = '';
        }

        // Записываем сформированные теги в массив
        $newsItem['tags'] = $base->tagsLink($newsItem['tags']);

        // Записываем в массив ссылку на аватар
        $newsItem['avatar'] = $tplArr['theme'].'/dleimages/noavatar.png';
        // А если у юзера есть фотка - выводим её, или граватар.
        if ($newsItem['foto']) {
            $userFoto = $newsItem['foto'];
            if (count(explode('@', $userFoto)) == 2) {
                $newsItem['avatar'] = '//www.gravatar.com/avatar/'.md5(trim($userFoto)).'?s='
                    .intval($user_group[$newsItem['user_group']]['max_foto']);
            } else {
                $userFotoWHost = (strpos($userFoto, '//') === 0) ? 'http:'.$userFoto : $userFoto;
                $arUserFoto    = parse_url($userFotoWHost);
                if ($arUserFoto['host']) {
                    $newsItem['avatar'] = $userFoto;
                } else {
                    $newsItem['avatar'] = $base->dle_config['http_home_url'].'uploads/fotos/'.$userFoto;
                }
                unset($arUserFoto, $userFotoWHost);
            }
        }

        // Разбираемся с рейтингом
        $newsItem['showRating']      = '';
        $newsItem['showRatingCount'] = '';
        if ($newsItem['allow_rate']) {
            $newsItem['showRatingCount'] = '<span class="ignore-select" data-vote-num-id="'.$newsItem['id'].'">'
                .$newsItem['vote_num'].'</span>';
            $jsRAteFunctionName          =  'base_rate';

            if ($base->dle_config['short_rating'] and $user_group[$member_id['user_group']]['allow_rating']) {
                $newsItem['showRating'] = baseShowRating($newsItem['id'], $newsItem['rating'], $newsItem['vote_num'],
                    1);

                $newsItem['ratingOnclickPlus']  = 'onclick="'.$jsRAteFunctionName.'(\'plus\', \''.$newsItem['id']
                    .'\'); return false;"';
                $newsItem['ratingOnclickMinus'] = 'onclick="'.$jsRAteFunctionName.'(\'minus\', \''.$newsItem['id']
                    .'\'); return false;"';

            } else {
                $newsItem['showRating'] = baseShowRating($newsItem['id'], $newsItem['rating'], $newsItem['vote_num'],
                    0);

                $newsItem['ratingOnclickPlus']  = '';
                $newsItem['ratingOnclickMinus'] = '';
            }
        }
        // Разбираемся с избранным
        $newsItem['favorites'] = '';
        if ($is_logged) {
            $fav_arr = explode(',', $member_id['favorites']);

            if (!in_array($newsItem['id'], $fav_arr) || $base->dle_config['allow_cache']) {
                $newsItem['favorites'] = '<img data-favorite-id="'.$newsItem['id'].'" data-action="plus" src="'
                    .$tplArr['theme']
                    .'/dleimages/plus_fav.gif"  title="Добавить в свои закладки на сайте" alt="Добавить в свои закладки на сайте">';
            } else {
                $newsItem['favorites'] = '<img data-favorite-id="'.$newsItem['id'].'" data-action="minus" src="'
                    .$tplArr['theme']
                    .'/dleimages/minus_fav.gif"  title="Удалить из закладок" alt="Удалить из закладок">';
            }
        }
    }

    // Полученный массив с данными для обработки в шаблоне
    $tplArr['list'] = $list;

    // Определяем группу пользователя
    $tplArr['member_group_id'] = $member_id['user_group'];

    // Устанавливаем пустое значение для постранички по умолчанию.
    $tplArr['pages'] = '';
    // Общее кол-во новостей без постранички.
    $tplArr['totalCount'] = count($list);

    if ($base->cfg['showNav']) {
        // Получаем общее количество новостей по заданным параметрам отбора
        $totalCount = $base->db->getOne('SELECT COUNT(*) as count FROM ?n as p LEFT JOIN ?n e ON (p.id=e.news_id) ?p',
            PREFIX.'_post', PREFIX.'_post_extras', $where);
        // Вычитаем переменную startFrom для корректного значения кол-ва новостей
        $totalCount = $totalCount - $base->cfg['startFrom'];

        // Общее кол-во новостей с постраничкой.
        $tplArr['totalCount'] = $totalCount;

        // Меняем для кеша id категории на this если параметр catId или notCatId равен this
        if ($isCatIdThis) {
            $base->cfg['catId'] = 'this';
        }
        if ($isNotCatIdThis) {
            $base->cfg['notCatId'] = 'this';
        }

        // Формируем имя кеш-файла с конфигом
        $pageCacheName = $base->cfg;
        // Удаляем номер страницы для того, что бы не создавался новый кеш для каждого блока постранички
        unset($pageCacheName['pageNum']);

        // Сокращаем немного имя файла :)
        $pageCacheName = 'bpa_'.crc32(implode('_', $pageCacheName));

        // Включаем кеширование DLE принудительно
        $cache_tmp = $base->dle_config['allow_cache'];
        $config['allow_cache']
                   = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.

        // Проверяем есть ли кеш с указанным именем
        $ajaxCache = dle_cache($pageCacheName);
        // Если кеша нет
        if (!$ajaxCache) {
            // Сериализуем конфиг для последующей записи в кеш
            $pageCacheText = serialize($base->cfg);
            // Создаём кеш
            create_cache($pageCacheName, $pageCacheText);
        }

        // Возвращаем значение кеша DLE обратно
        $config['allow_cache'] = $cache_tmp;

        // Массив с конфигурацией для формирования постранички
        $pagerConfig = [
            'block_id'       => $pageCacheName,
            'total_items'    => $totalCount,
            'items_per_page' => $base->cfg['limit'],
            'style'          => $base->cfg['navStyle'],
            'current_page'   => $base->cfg['pageNum'],
        ];
        if ($base->cfg['navDefaultGet']) {
            $pagerConfig['is_default_dle_get'] = true;
            $pagerConfig['query_string']       = 'cstart';
            $pagerConfig['link_tag']           = '<a href=":link">:name</a>';
            $pagerConfig['current_tag']        = '<span class="current">:name</span>';
            $pagerConfig['prev_tag']           = '<a href=":link" class="prev">&lsaquo; Назад</a>';
            $pagerConfig['prev_text_tag']      = '<span class="prev">&lsaquo; Назад</span>';
            $pagerConfig['next_tag']           = '<a href=":link" class="next">Далее &rsaquo;</a>';
            $pagerConfig['next_text_tag']      = '<span class="next">Далее &rsaquo;</span>';
            $pagerConfig['first_tag']          = '<a href=":link" class="first">Первая &laquo;</a>';
            $pagerConfig['last_tag']           = '<a href=":link" class="last">&raquo; Последняя</a>';
            $pagerConfig['extended_pageof']    = 'Страница :current_page из :total_pages';
            $pagerConfig['extended_itemsof']
                                               = 'Показаны новости :current_first_item &mdash; :current_last_item из :total_items';
        }
        // Если более 1 страницы
        if ($totalCount > $base->cfg['limit']) {
            $pagination = new Pager($pagerConfig);

            // Сформированный блок с постраничкой
            $tplArr['pages'] = $pagination->render();
            // Уникальный ID для вывода в шаблон
        }
        $tplArr['block_id'] = $pagerConfig['block_id'];

    } else {
        // Устанавливаем уникальный ID для блока по умолчанию
        $tplArr['block_id'] = 'bp_'.crc32(implode('_', $base->cfg));
    }

    // Результат обработки шаблона
    try {
        $output = $base->tpl->fetch($base->cfg['template'].'.tpl', $tplArr);
    } catch (Exception $e) {
        $outputLog['errors'][] = $e->getMessage();
        $base->cfg['nocache']  = true;
    }

    // Записываем в БД id похожих новостей, если требуется
    if ($reltedFirstShow && $saveRelated) {
        /** @var integer $relatedId */
        $base->db->query('UPDATE ?n SET related_ids=?s WHERE news_id=?i', PREFIX.'_post_extras',
            implode(',', $relatedIds), $relatedId);
    }

    // Если есть ошбки и включен вывод статистики — оключаем кеш.
    if (count($outputLog['errors']) > 0 && $cfg['showstat']) {
        $base->cfg['nocache'] = true;
    }

    // Формируем данные о запросах для статистики, если требуется
    if ($base->cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {
        $stat  = $base->db->getStats();
        $statQ = [];

        foreach ($stat as $i => $q) {
            $statQ['q'] .= '<br>'.'<b>['.($i + 1).']</b> '.$q['query'].' <br>['.($i + 1).' время:] <b>'.$q['timer']
                .'</b>';
            $statQ['t'] += $q['timer'];
        }
        $dbStat = 'Запрос(ы): '.$statQ['q'].'<br>Время выполнения запросов: <b>'.$statQ['t'].'</b><br>';

        unset($stat);
    }

    // Создаём кеш, если требуется
    if (!$base->cfg['nocache']) {
        create_cache($base->cfg['cachePrefix'], $output, $cacheName, $cacheSuffix);
    }


}

// Обрабатываем вложения
/** @var $base */
if ($base->dle_config['files_allow']) {
    if (strpos($output, '[attachment=') !== false) {
        /** @var array $attachments */
        $output = show_attach($output, $attachments);
    }
} else {
    $output = preg_replace("'\[attachment=(.*?)\]'si", '', $output);
}

if ($user_group[$member_id['user_group']]['allow_hide']) {
    $output = str_ireplace('[hide]', '', str_ireplace('[/hide]', '', $output));
} else {
    $output = preg_replace('#\[hide\](.+?)\[/hide\]#ims', '', $output);
}

// Результат работы модуля
/** @var boolean $external */
if (!$external) {
    // Если блок не является внешним - выводим на печать
    if (count($outputLog['errors']) > 0) {
        // Выводим ошибки, если они есть
        $outputErrors = [];
        $outputErrors[]
                      = '<ul class="bp-errors" style="border: solid 1px red; padding: 5px; margin: 5px 0; list-style: none; background: rgba(255,0,0,0.2)">';

        foreach ($outputLog['errors'] as $errorText) {
            $outputErrors[] = '<li>'.$errorText.'</li>';
        }
        $outputErrors[] = '</ul>';

        $outputErrors = implode('', $outputErrors);

        echo $outputErrors;
    } else {
        // Если нет ошибок - выводим результат аботы модуля
        echo $output;
    }
}

// Показываем стстаистику выполнения скрипта, если требуется
if ($cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {

    // Информация об оперативке
    $mem_usg = (function_exists('memory_get_peak_usage')) ? '<br>Расход памяти: <b>'.round(memory_get_peak_usage()
            / (1024 * 1024), 2).'Мб </b>' : '';
    // Вывод статистики
    /** @var integer $start */
    /** @var string $dbStat */
    echo '<div class="bp-statistics" style="border: solid 1px red; padding: 5px; margin: 5px 0;">'.$dbStat
        .'Время выполнения скрипта: <b>'.round((microtime(true) - $start), 6).'</b> c.'.$mem_usg.'</div>';
}
