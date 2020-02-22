<?php
/*
=============================================================================
BlockPro - Конфиг строки подключения модуля
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
email:   pafnuty10@gmail.com
=============================================================================
*/

$cfg = [
    // Показывать только новости на модерации
    'moderate' => !empty($moderate) ? $moderate : false,

    // Название шаблона (без расширения)
    'template' => !empty($template) ? $template : 'blockpro/blockpro',

    // Префикс кеша
    'cachePrefix'    => !empty($cachePrefix) ? $cachePrefix : 'news',

    // Отключить суффикс кеша (будет создаваться один кеш-файл для всех пользователей). По умолчанию включен, т.е. для каждой группы пользователей будет создаваться свой кеш (на случай разного отображения контента разным юзерам).
    'cacheSuffixOff' => !empty($cacheSuffixOff) ? true : false,

    // Назначаем дополнение к имени кеша, если имеются переменные со значениями this, они будут дописаны в имя кеша, иначе для разных мест будет создаваться один и тот же файл кеша
    'cacheNameAddon' => '',

    // Не использовать кеш
    'nocache'   => !empty($nocache) ? $nocache : false,

    // Время жизни кеша в минутах
    'cacheLive' => (!empty($cacheLive) && !$mcache) ? $cacheLive : false,

    // C какой новости начать вывод
    'startFrom' => !empty($startFrom) ? $startFrom : '0',

    // Количество новостей в блоке
    'limit'     => !empty($limit) ? $limit : '10',

    // Обработка фиксированных новостей (yes/only/without показ всех/только фиксированных/только обычных новостей)
    'fixed'     => !empty($fixed) ? $fixed : 'yes',

    // Обработка новостей, опубликованных на главной (yes/only/without показ всех/только на главной/только не на главной)
    'allowMain' => !empty($allowMain) ? $allowMain : 'yes',

    // ID новостей для вывода в блоке (через запятую, или черточку)
    'postId'    => !empty($postId) ? $postId : '',

    // ID игнорируемых новостей (через запятую, или черточку)
    'notPostId' => !empty($notPostId) ? $notPostId : '',

    // Логины авторов, для показа их новостей в блоке (через запятую)
    'author'    => !empty($author) ? $author : '',

    // Логины игнорируемых авторов (через запятую)
    'notAuthor' => !empty($notAuthor) ? $notAuthor : '',

    // Имена дополнительных полей для фильтрации новостей по ним (через запятую)
    'xfilter'    => !empty($xfilter) ? $xfilter : '',

    // Имена дополнительных полей для игнорирования показа новостей (через запятую)
    'notXfilter' => !empty($notXfilter) ? $notXfilter : '',

    // синтаксис передачи данных: &xfSearch=имя_поля|значение||имя_поля|значение
    'xfSearch'      => !empty($xfSearch) ? $xfSearch : false,

    // синтаксис передачи данных: &notXfSearch=имя_поля|значение||имя_поля|значение
    'notXfSearch'   => !empty($notXfSearch) ? $notXfSearch : false,

    // Принимает OR или AND (по умолчанию OR)
    'xfSearchLogic' => !empty($xfSearchLogic) ? $xfSearchLogic : 'OR',

    // Категории для показа	(через запятую, или черточку)
    'catId'       => !empty($catId) ? $catId : '',

    // Выводить подкатегории указанных категорий (&subcats=y), работает и с диапазонами.
    'subcats'     => !empty($subcats) ? $subcats : false,

    // Игнорируемые категории (через запятую, или черточку)
    'notCatId'    => !empty($notCatId) ? $notCatId : '',

    // Игнорировать подкатегории игнорируемых категорий (&notSubcats=y), работает и с диапазонами.
    'notSubcats'  => !empty($notSubcats) ? $notSubcats : false,

    // Показывать новости ТОЛЬКО из текущей категории (имеет смысл при выводе похожих новостей и использоваии мультикатегорий).
    'thisCatOnly' => !empty($thisCatOnly) ? $thisCatOnly : false,

    // Теги из облака тегов для показа новостей, содержащих их (через запятую)
    'tags'    => !empty($tags) ? $tags : '',

    // Игнорируемые теги (через запятую)
    'notTags' => !empty($notTags) ? $notTags : '',

    // Временной период для отбора новостей
    'day'        => !empty($day) ? $day : false,

    // Интервал для отбора (т.е. к примеру выбираем новости за прошлую недею так: &day=14&dayCount=7 )
    'dayCount'   => !empty($dayCount) ? $dayCount : false,

    // Сортировка (top, date, comms, rating, views, title, hit, random, randomLight, download, symbol, editdate или xf|xfieldname где xfieldname - имя дополнительного поля, field|p.name где p.name — кастомное поле)
    'sort'       => !empty($sort) ? $sort : 'top',

    // Тип сортировки по допполю (string, int) - для корректной сортировки по строке используем string, по умолчанию сортируется как число (для цен полезно).
    'xfSortType' => !empty($xfSortType) ? $xfSortType : 'int',

    // Направление сортировки (new, old, asis)
    'order'      => !empty($order) ? $order : 'new',

    // Вывод аватарки пользователя (немного усложнит запрос).
    'avatar' => !empty($avatar) ? $avatar : false,

    // Показывать время и статистику по блоку
    'showstat' => !empty($showstat) ? $showstat : false,

    // Включить режим вывода похожих новостей (по умолчанию нет)
    'related'       => !empty($related) ? $related : false,

    // Включить запись похожих новостей в БД
    'saveRelated'   => !empty($saveRelated) ? $saveRelated : false,

    // Включить постраничную навигацию
    'showNav'       => !empty($showNav) ? $showNav : false,

    // Слушать дефолтный get-параметр постраничной навигации
    'navDefaultGet' => !empty($navDefaultGet) ? $navDefaultGet : false,

    // Текущая страница при постраничной конфигурации
    'pageNum'       => !empty($pageNum) ? $pageNum : '1',

    /**
     * Стиль навигации. Возможны следующие стили:
     * classic:	    << Первая  < 1 [2] 3 >  Последняя >>
     * digg:		<< Назад  1 2 ... 5 6 7 8 9 [10] 11 12 13 14 ... 25 26  Вперёд >>
     * extended:	<< Назад | Страница 2 из 11 | Показаны новости 6-10 из 52 | Вперёд >>
     * punbb:		1 ... 4 5 [6] 7 8 ... 15
     * arrows:	    << Назад Вперёд >>
     */
    'navStyle'      => !empty($navStyle) ? $navStyle : 'classic',
    /**
     * @todo  реализовать функционал с options и notOptions
     * Опции, публикации новости для показа (Публиковать на главной, Разрешить рейтинг статьи, Разрешить комментарии, Запретить индексацию страницы для поисковиков, Зафиксировать новость) main|rating|comments|noindex
     */
    'options'       => !empty($options) ? $options : false,

    // Опции, публикации новости для исключения (Публиковать на главной, Разрешить рейтинг статьи, Разрешить комментарии, Запретить индексацию страницы для поисковиков, Зафиксировать новость) main|rating|comments|noindex
    'notOptions'    => !empty($notOptions) ? $notOptions : false,

    // Выводить только новости, дата публикации которых не наступила (Полезно для афиш) &future=y
    'future'     => !empty($future) ? $future : false,

    // Значимые переменные в формировании кеша блока на случай разного вывода в зависимости от условий расположения модуля. Сюда можно передавать ключи, доступные через $_REQUEST или значения переменной $dle_module
    'cacheVars'  => !empty($cacheVars) ? $cacheVars : false,

    // Символьные коды для фильтрации по символьному каталогу. Перечисляем через запятую.
    'symbols'    => !empty($symbols) ? $symbols : false,

    // Символьные коды для исключающей фильтрации по символьному каталогу. Перечисляем через запятую или пишем this для текущего символьного кода
    'notSymbols' => !empty($notSymbols) ? $notSymbols : false,

    /**
     * Дополнение к выборке полей из БД (p.field,e.field)
     * setFilter=p.full_story|SEARCH|dle_media_begin|OR|p.full_story|SEARCH|dle_video_begin
     * setFilter=e.news_read|+|100||p.comm_num|-|20
     */
    'fields'     => !empty($fields) ? $fields : false,

    // Собственная фильтрация полей БД
    'setFilter'  => !empty($setFilter) ? $setFilter : '',

    // Включить экспериментальные функции
    'experiment' => !empty($experiment) ? $experiment : false,

    // Включить поддержку модуля MultiLanguage от japing.pw
    'multiLang' => !empty($multiLang) ? $multiLang : false,

    // Список языков, добавленных в модуль MultiLanguage (из модуля без запросов в БД эту информацию получить нельзя)
    'langList' => !empty($langList) ? $langList : 'en',
];