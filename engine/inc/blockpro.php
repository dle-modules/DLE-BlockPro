<?php
/*
=============================================================================
BlockPro
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

/**
 * @global array  $config
 * @global array  $member_id
 * @global array  $lang
 * @global array  $config
 * @global string $PHP_SELF
 * @global array  $user_group
 */
if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die("Hacking attempt!");
}
if ($member_id['user_group'] != '1') {
	msg("error", $lang['index_denied'], $lang['index_denied']);
}

define('MODULE_DIR', ENGINE_DIR . '/modules/base/admin/blockpro/');

$moduleName    = 'blockpro';
$moduleVersion = '5.1.3';

$moderate              = $_REQUEST['moderate'];
$moderate_checked      = ($moderate) ? 'checked' : '';
$future                = $_REQUEST['future'];
$future_checked        = ($future) ? 'checked' : '';
$template              = $_REQUEST['template'];
$cachePrefix           = $_REQUEST['cachePrefix'];
$cacheSuffixOff        = $_REQUEST['cacheSuffixOff'];
$cacheNameAddon        = $_REQUEST['cacheNameAddon'];
$nocache               = $_REQUEST['nocache'];
$nocache_checked       = ($nocache) ? 'checked' : '';
$cacheLive             = $_REQUEST['cacheLive'];
$startFrom             = $_REQUEST['startFrom'];
$limit                 = $_REQUEST['limit'];
$fixed                 = $_REQUEST['fixed'];
$allowMain             = $_REQUEST['allowMain'];
$postId                = $_REQUEST['postId'];
$notPostId             = $_REQUEST['notPostId'];
$author                = $_REQUEST['author'];
$notAuthor             = $_REQUEST['notAuthor'];
$xfilter               = (is_array($_REQUEST['xfilter'])) ? implode(',', array_filter($_REQUEST['xfilter'])) : $_REQUEST['xfilter'];
$notXfilter            = (is_array($_REQUEST['notXfilter'])) ? implode(',', array_filter($_REQUEST['notXfilter'])) : $_REQUEST['notXfilter'];
$xfSearch              = $_REQUEST['xfSearch'];
$notXfSearch           = $_REQUEST['notXfSearch'];
$xfSearchLogic         = $_REQUEST['xfSearchLogic'];
$catId                 = (is_array($_REQUEST['catId'])) ? implode(',', array_filter($_REQUEST['catId'])) : $_REQUEST['catId'];
$subcats               = $_REQUEST['subcats'];
$subcats_checked       = ($subcats) ? 'checked' : '';
$notCatId              = (is_array($_REQUEST['notCatId'])) ? implode(',', array_filter($_REQUEST['notCatId'])) : $_REQUEST['notCatId'];
$notSubcats            = $_REQUEST['notSubcats'];
$notSubcats_checked    = ($notSubcats) ? 'checked' : '';
$tags                  = $_REQUEST['tags'];
$notTags               = $_REQUEST['notTags'];
$day                   = $_REQUEST['day'];
$dayCount              = $_REQUEST['dayCount'];
$sort                  = $_REQUEST['sort'];
$order                 = $_REQUEST['order'];
$avatar                = $_REQUEST['avatar'];
$avatar_checked        = ($avatar) ? 'checked' : '';
$showstat              = $_REQUEST['showstat'];
$showstat_checked      = ($showstat) ? 'checked' : '';
$related               = $_REQUEST['related'];
$showNav               = $_REQUEST['showNav'];
$showNav_checked       = ($showNav) ? 'checked' : '';
$pageNum               = $_REQUEST['pageNum'];
$navStyle              = $_REQUEST['navStyle'];
$cacheVars             = $_REQUEST['cacheVars'];
$xfSortType            = $_REQUEST['xfSortType'];
$symbols               = $_REQUEST['symbols'];
$notSymbols            = $_REQUEST['notSymbols'];
$saveRelated           = $_REQUEST['saveRelated'];
$fields                = $_REQUEST['fields'];
$navDefaultGet         = $_REQUEST['navDefaultGet'];
$navDefaultGet_checked = ($navDefaultGet) ? 'checked' : '';
$thisCatOnly           = $_REQUEST['thisCatOnly'];
$thisCatOnly_checked   = ($thisCatOnly) ? 'checked' : '';
$setFilter             = $_REQUEST['setFilter'];
$experiment            = $_REQUEST['experiment'];
$experiment_checked    = ($experiment) ? 'checked' : '';


$cfg = [
	'moderate'       => !empty($moderate) ? $moderate : false,
	'template'       => !empty($template) ? $template : 'blockpro/blockpro',
	'cachePrefix'    => !empty($cachePrefix) ? $cachePrefix : 'news',
	'cacheSuffixOff' => !empty($cacheSuffixOff) ? true : false,
	'cacheNameAddon' => '',
	'nocache'        => !empty($nocache) ? $nocache : false,
	'cacheLive'      => (!empty($cacheLive) && !$mcache) ? $cacheLive : false,
	'startFrom'      => !empty($startFrom) ? $startFrom : '0',
	'limit'          => !empty($limit) ? $limit : '10',
	'fixed'          => !empty($fixed) ? $fixed : 'yes',
	'allowMain'      => !empty($allowMain) ? $allowMain : 'yes',
	'postId'         => !empty($postId) ? $postId : '',
	'notPostId'      => !empty($notPostId) ? $notPostId : '',
	'author'         => !empty($author) ? $author : '',
	'notAuthor'      => !empty($notAuthor) ? $notAuthor : '',
	'xfilter'        => !empty($xfilter) ? $xfilter : '',
	'notXfilter'     => !empty($notXfilter) ? $notXfilter : '',
	'xfSearch'       => !empty($xfSearch) ? $xfSearch : false,
	'notXfSearch'    => !empty($notXfSearch) ? $notXfSearch : false,
	'xfSearchLogic'  => !empty($xfSearchLogic) ? $xfSearchLogic : 'OR',
	'catId'          => !empty($catId) ? $catId : '',
	'subcats'        => !empty($subcats) ? $subcats : false,
	'notCatId'       => !empty($notCatId) ? $notCatId : '',
	'notSubcats'     => !empty($notSubcats) ? $notSubcats : false,
	'thisCatOnly'    => !empty($thisCatOnly) ? $thisCatOnly : false,
	'tags'           => !empty($tags) ? $tags : '',
	'notTags'        => !empty($notTags) ? $notTags : '',
	'day'            => !empty($day) ? $day : false,
	'dayCount'       => !empty($dayCount) ? $dayCount : false,
	'sort'           => !empty($sort) ? $sort : 'top',
	'xfSortType'     => !empty($xfSortType) ? $xfSortType : 'int',
	'order'          => !empty($order) ? $order : 'new',
	'avatar'         => !empty($avatar) ? $avatar : false,
	'showstat'       => !empty($showstat) ? $showstat : false,
	'related'        => !empty($related) ? $related : false,
	'saveRelated'    => !empty($saveRelated) ? $saveRelated : false,
	'showNav'        => !empty($showNav) ? $showNav : false,
	'navDefaultGet'  => !empty($navDefaultGet) ? $navDefaultGet : false,
	'pageNum'        => !empty($pageNum) ? $pageNum : '1',
	'navStyle'       => !empty($navStyle) ? $navStyle : 'classic',
	'options'        => !empty($options) ? $options : false,
	'notOptions'     => !empty($notOptions) ? $notOptions : false,
	'future'         => !empty($future) ? $future : false,
	'cacheVars'      => !empty($cacheVars) ? $cacheVars : false,
	'symbols'        => !empty($symbols) ? $symbols : false,
	'notSymbols'     => !empty($notSymbols) ? $notSymbols : false,
	'fields'         => !empty($fields) ? $fields : false,
	'setFilter'      => !empty($setFilter) ? $setFilter : '',
	'experiment'     => !empty($experiment) ? $experiment : false,

];

/**
 * var array $bpConfig
 */
include ENGINE_DIR . '/data/blockpro.php';

// Объединяем массивы конфигов
$cfg = array_merge($cfg, $bpConfig);

$mcache = false;

if ($config['cache_type']) {

	if (class_exists('Memcache')) {
		$mcache = new Memcache();
	} elseif (class_exists('Memcached')) {
		$mcache = new Memcached();
	}
	if ($mcache !== false) {
		$memcache_server = explode(":", $config['memcache_server']);
		if ($memcache_server[0] == 'unix') {
			$memcache_server = [$config['memcache_server'], 0];
		}

		if (!$mcache->addServer($memcache_server[0], $memcache_server[1])) {
			$mcache = false;
		}

		if ($mcache->getStats() === false) {
			$mcache = false;
		}
	}
}

if ($_REQUEST['setPreview']) {
	// Формируем имя кеш-файла с конфигом
	$pageCacheName = $cfg;
	// Удаляем номер страницы для того, что бы не создавался новый кеш для каждого блока постранички
	unset($pageCacheName['pageNum']);
	// Сокращаем немного имя файла :)
	$pageCacheName = 'bpa_' . crc32(implode('_', $pageCacheName));

	// Включаем кеширование DLE принудительно
	$cache_tmp             = $config['allow_cache'];
	$config['allow_cache'] = '1';

	// Проверяем есть ли кеш с указанным именем
	$ajaxCache = base_dle_cache($pageCacheName);
	// Если кеша нет
	if (!$ajaxCache) {
		// Сериализуем конфиг для последующей записи в кеш
		$pageCacheText = serialize($cfg);
		// Создаём кеш
		base_create_cache($pageCacheName, $pageCacheText);
	}

	// Возвращаем значение кеша DLE обратно
	$config['allow_cache'] = $cache_tmp;

}


function base_dle_cache($prefix, $cache_id = false, $member_prefix = false) {
	global $config, $is_logged, $member_id, $mcache;

	if (!$config['allow_cache']) {
		return false;
	}

	$config['clear_cache'] = (intval($config['clear_cache']) > 1) ? intval($config['clear_cache']) : 0;

	if ($is_logged) {
		$end_file = $member_id['user_group'];
	} else {
		$end_file = "0";
	}

	if (!$cache_id) {

		$key = $prefix;

	} else {

		$cache_id = md5($cache_id);

		if ($member_prefix) {
			$key = $prefix . "_" . $cache_id . "_" . $end_file;
		} else {
			$key = $prefix . "_" . $cache_id;
		}

	}

	if ($mcache !== false) {

		return $mcache->get(md5(DBNAME . PREFIX . md5(SECURE_AUTH_KEY) . $key));

	} else {

		$buffer = @file_get_contents(ENGINE_DIR . "/cache/" . $key . ".tmp");

		if ($buffer !== false AND $config['clear_cache']) {

			$file_date = @filemtime(ENGINE_DIR . "/cache/" . $key . ".tmp");
			$file_date = time() - $file_date;

			if ($file_date > ($config['clear_cache'] * 60)) {
				$buffer = false;
				@unlink(ENGINE_DIR . "/cache/" . $key . ".tmp");
			}

			return $buffer;

		} else {
			return $buffer;
		}

	}
}

function base_create_cache($prefix, $cache_text, $cache_id = false, $member_prefix = false) {
	global $config, $is_logged, $member_id, $mcache;

	if (!$config['allow_cache']) {
		return false;
	}

	if ($is_logged) {
		$end_file = $member_id['user_group'];
	} else {
		$end_file = "0";
	}

	if (!$cache_id) {

		$key = $prefix;

	} else {

		$cache_id = md5($cache_id);

		if ($member_prefix) {
			$key = $prefix . "_" . $cache_id . "_" . $end_file;
		} else {
			$key = $prefix . "_" . $cache_id;
		}

	}

	if ($mcache !== false) {

		$config['clear_cache'] = (intval($config['clear_cache']) > 1) ? intval($config['clear_cache']) : 0;

		if ($config['clear_cache']) {
			$set_time = $config['clear_cache'] * 60;
		} else {
			$set_time = 86400;
		}

		if (class_exists('Memcache')) {

			$mcache->set(md5(DBNAME . PREFIX . md5(SECURE_AUTH_KEY) . $key), $cache_text, MEMCACHE_COMPRESSED, $set_time);

		} else {

			$mcache->set(md5(DBNAME . PREFIX . md5(SECURE_AUTH_KEY) . $key), $cache_text, $set_time);

		}

	} else {

		file_put_contents(ENGINE_DIR . "/cache/" . $key . ".tmp", $cache_text, LOCK_EX);

		@chmod(ENGINE_DIR . "/cache/" . $key . ".tmp", 0666);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php echo $config['charset'] ?>">
	<title>BlockPro</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="engine/modules/base/admin/blockpro/css/main.css?v=<?php echo $moduleVersion ?>">
	<script>
		var dle_root = '<?php echo $config['http_home_url']?>';
		var dle_skin = '<?php echo $config['skin']?>';
	</script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/autosize.js/1.18.1/jquery.autosize.min.js"></script>

	<script src="engine/modules/base/admin/blockpro/js/jquery.form.min.js?v=<?php echo $moduleVersion ?>"></script>
	<script src="engine/modules/base/admin/blockpro/js/jquery.ladda.min.js?v=<?php echo $moduleVersion ?>"></script>
	<script
			src="engine/modules/base/admin/blockpro/js/jquery.easyResponsiveTabs.min.js?v=<?php echo $moduleVersion ?>"></script>
	<script
			src="engine/modules/base/admin/blockpro/js/jquery.magnificpopup.min.js?v=<?php echo $moduleVersion ?>"></script>
	<script src="engine/modules/base/admin/blockpro/js/jquery.chosen.min.js?v=<?php echo $moduleVersion ?>"></script>
	<script src="engine/modules/base/admin/blockpro/js/main.js?v=<?php echo $moduleVersion ?>"></script>
</head>
<body>
	<div class="container container-blue">
		<header class="content">
			<div class="col col-mb-12 col-6">
				<a href="<?php echo $PHP_SELF ?>?mod=main"
				   class="btn btn-small btn-white"><?php echo $lang['skin_main'] ?></a>
				<a class="btn btn-small btn-white" href="<?php echo $PHP_SELF ?>?mod=options&amp;action=options"
				   title="Список всех разделов">Список всех разделов</a>
				<a href="<?php echo $config['http_home_url'] ?>" target="_blank"
				   class="btn btn-small btn-white"><?php echo $lang['skin_view'] ?></a>
			</div>
			<div class="col col-mb-12 col-6 ta-right">
				<?php echo $member_id['name'] . ' <small class="hide-phone">(' . $user_group[$member_id['user_group']]['group_name'] . ')</small> ' ?>
				<a href="<?php echo $PHP_SELF ?>?action=logout"
				   class="btn btn-small btn-red"><?php echo $lang['skin_logout'] ?></a>
			</div>
		</header>
	</div>
	<div class="container">
		<div class="content">
			<div class="col col-mb-12 col-12">
				<h1 class="ta-center mb10">BlockPro
					<small class="text-muted fz20">(<?php echo $moduleVersion ?>)</small>
				</h1>
				<hr>
			</div> <!-- .col col-mb-12 col-12 -->
		</div> <!-- .content -->
		<div id="tab">
			<div class="content">
				<div class="col col-mb-12">
					<ul class="resp-tabs-list">
						<li>Параметры</li>
						<li>Результат</li>
						<li>Виджеты</li>
						<li>Хелперы</li>
						<li>Настройки</li>
						<li>Документация и техподдержка</li>
					</ul>

					<div class="resp-tabs-container">
						<div>
							<?php include(MODULE_DIR . 'generator.php'); ?>
						</div>
						<div>
							<div class="content">
								<div class="col col-mb-12">
									<?php if ($_REQUEST['setPreview']): ?>
										<?php
										$moduleUrl = 'include file="engine/modules/base/blockpro.php';
										$paramsUrl = [];

										if ($cfg['template'] == 'blockpro/blockpro') {
											unset($cfg['template']);
										}
										if ($cfg['cachePrefix'] == 'news') {
											unset($cfg['cachePrefix']);
										}
										if ($cfg['limit'] == 10) {
											unset($cfg['limit']);
										}
										if ($cfg['fixed'] == 'yes') {
											unset($cfg['fixed']);
										}
										if ($cfg['allowMain'] == 'yes') {
											unset($cfg['allowMain']);
										}
										if ($cfg['xfSearchLogic'] == 'on') {
											unset($cfg['xfSearchLogic']);
										}
										if ($cfg['xfSearchLogic'] == 'OR') {
											unset($cfg['xfSearchLogic']);
										}
										if ($cfg['sort'] == 'top') {
											unset($cfg['sort']);
										}
										if ($cfg['order'] == 'new') {
											unset($cfg['order']);
										}
										if ($cfg['pageNum'] == 1) {
											unset($cfg['pageNum']);
										}
										if ($cfg['navStyle'] == 'classic') {
											unset($cfg['navStyle']);
										}
										if ($cfg['xfSortType'] == 'int') {
											unset($cfg['xfSortType']);
										}

										$filteredCfg = array_filter($cfg);
										if (count($filteredCfg) > 0) {
											$moduleUrl .= '?';
										}
										foreach (array_filter($cfg) as $key => $value) {
											$paramsUrl[] = $key . '=' . $value;
										}
										$moduleUrl .= implode('&amp;', $paramsUrl);
										?>
										<h2>Ваша строка подключения: <span
													class="btn btn-green btn-small btn-external-save"
													data-mfp-src="engine/ajax/base/save_block_pro.php?blockId=<?php echo $pageCacheName ?>">Создать виджет</span>
										</h2>
										<textarea rows="1" class="input input-block-level code">{<?php echo $moduleUrl ?>"}</textarea>
										<h2>Предпросмотр блока:</h2>
										<hr>
										<div class="content">
											<script>
												jQuery(document).ready(function ($) {
													var blockId = '<?php echo $pageCacheName?>',
													    pageNum = '<?php echo $cfg['pageNum']?>',
													    $block  = $('#preview_tab');
													$block.addClass('base-loader loading');
													$.ajax({
														url: dle_root + 'engine/ajax/blockpro.php',
														dataType: 'html',
														data: {
															pageNum: pageNum,
															blockId: blockId
														},
													})
														.done(function (data) {
															$block.html(data);
														})
														.fail(function () {
															console.error("error");
														})
														.always(function () {
															$block.removeClass('base-loader loading');
														});
												});
											</script>
										</div>
									<?php else: ?>
										<div class="alert">
											Нужно для начала задать параметры строки подключения на предыдущей вкладке
										</div>
									<?php endif ?>
									<div id="preview_tab">
									</div>
								</div>
							</div>
						</div>
						<div>
							<?php include(MODULE_DIR . 'vidgets.php'); ?>
						</div>
						<div>
							<h2>Картинки</h2>

							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Сервис для обработки картинок <br>
									<small>
										Для корректной работы сервисов необходима их <a href="?mod=blockpro#tab5">настройка</a>
									</small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input class="radio" type="radio" name="service" value="image" id="service_local"
									       checked data-setimage>
									<label for="service_local"><span></span>Встроенный механизм</label>
									<br>
									<input class="radio" type="radio" name="service" value="tinypng"
									       id="service_tinypng" data-setimage>
									<label for="service_tinypng"><span></span>TinyPNG</label>
									<br>
									<input class="radio" type="radio" name="service" value="kraken" id="service_kraken"
									       data-setimage>
									<label for="service_kraken"><span></span>Kraken.io</label>
								</div>
							</div>

							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Название переменной для картинки-заглушки
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_defImage" data-id="img_defImage"
									       data-defimage data-setimage value="$noimage" placeholder="например $noimage">
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Путь к картинке-заглушке <br>
									<small>Относительно текущего шаблона сайта <b>{$theme}</b></small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_noimage" data-id="img_noimage"
									       data-defimage
									       value="/images/noimage.png" placeholder="например /images/noimage.png">
								</div>
							</div>
							<hr>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Тип выводимой картинки
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<select name="img_type" class="styler" data-setimage data-img-type>
										<option value="small">Уменьшенная копия</option>
										<option value="original">Оригинальная картинка</option>
										<option value="intext">Как есть</option>
									</select>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Номер картинки в контенте
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_number" value="1"
									       placeholder="например 3 или all" data-setimage data-img-count>

									<div class="alert alert-info">
										Можно прописать "<b>all</b>" для вывода массива со всеми картинками выбраного
										поля.
									</div>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Размер картики <br>
									<small>по умолчанию не определён, выводится картинка без ресайза</small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_size" value=""
									       placeholder="например 100 или 100x150" data-setimage>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Качество картинки <br>
									<small>по умолчанию не определено, картинка будет выведена в исходном качестве
									</small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_quality" value="" placeholder="0-100"
									       data-setimage>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Тип ресайза
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<select id="img_resize_type" name="img_resize_type" class="styler" data-setimage>
										<option value="">вписать в рамки (авто)</option>
										<option value="exact">точный размер (возможны деформации пропорций)</option>
										<option value="landscape">уменьшение по ширине</option>
										<option value="portrait">уменьшение по высоте</option>
										<option value="crop">crop (уменьшение и обрезка лишнего)</option>
									</select> <br>
									<input class="checkbox" type="checkbox" value="true" name="img_grab" id="img_grab"
									       checked data-setimage> <label for="img_grab"><span></span> Грабить сторонние
										картинки к себе</label> <br>
									<input class="checkbox" type="checkbox" value="true" name="img_get_small"
									       id="img_get_small" data-setimage> <label for="img_get_small"><span></span>
										Обрабатывать уменьшенную копию, если есть</label> <br>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Подпапка для картинок <br>
									<small>иногда бывает нужно</small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="img_subfolder" value=""
									       placeholder="например subfolder" data-setimage>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Код для картинки-заглушки
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
										<textarea id="img_code" class="input code">{*Определение картинки-заглушки*}
{var $noimage}
	{$theme}/images/noimage.png
{/var}</textarea>
								</div>
							</div>

							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Код для вставки в шаблон (внутрь цикла) <br>
									<span class="text-red">Не забывайте сменить short_story на нужное поле</span>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<textarea id="img_code2" class="input code">{$el.short_story|image:$noimage:'small':'1':'':'':'':true:false:''}</textarea>
								</div>
							</div>
							<hr>
							<h2>Текст</h2>

							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Максимальное кол-во символов <br>
									<small>по умолчанию не определено, текст будет просто очищаться от html-тегов
									</small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="txt_limit" value=""
									       placeholder="например 150"
									       data-settext data-text-limit>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Символы на окончании текста <br>
									<small>по умолчанию <b class="text-red">&amp;hellip;</b></small>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<input type="text" class="input" name="txt_etc" value=""
									       placeholder="например &amp;hellip;" data-settext data-text-etc> <br>
									<input class="checkbox" type="checkbox" value="true" name="txt_wordcut"
									       id="txt_wordcut"
									       data-settext data-text-checkbox> <label for="txt_wordcut"><span></span>
										Жесткое
										ограничение символов</label>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Код для вставки в шаблон (внутрь цикла) <br>
									<span class="text-red">Не забывайте сменить short_story на нужное поле</span>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<textarea id="txt_code" rows="1"
									          class="input code">{$el.short_story|limit:'150'}</textarea>
								</div>
							</div>
							<hr>


							<h2>Категории</h2>

							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Выводимая информация из категории
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<select id="cat_info" name="cat_info" class="styler" data-setcatinfo>
										<option value="">Весь массив с информацией</option>
										<option value="name">Название категории</option>
										<option value="link">Ссылка на категорию</option>
										<option value="url">URL категории</option>
										<option value="icon">Иконка категории</option>
										<option value="icon_noimage">Иконка категории (с заглушкой)</option>
									</select>
								</div>
							</div>
							<div class="content">
								<div class="col col-mb-12 col-5 col-dt-4 form-label">
									Код для вставки в шаблон (внутрь цикла) <br>
								</div>
								<div class="col col-mb-12 col-7 col-dt-8 form-control">
									<textarea id="cat_code" rows="1"
									          class="input code">{$el.category|catinfo}</textarea>
								</div>
							</div>


						</div>
						<div>
							<?php include(MODULE_DIR . 'config.php'); ?>
						</div>
						<div>
							<div class="content">
								<div class="col col-mb-12 col-2 form-label">
									&nbsp;
								</div>
								<div class="col col-mb-12 col-10 form-control">
									<h2 class="m0">Документация к модулю</h2>
								</div>
								<div class="col col-mb-12 mb10">
									Документация к модулю всегда доступна на официальном сайте, в разделе <a
											href="http://bp.pafnuty.name/documentation/" target="_blank">документация</a>.
								</div>
							</div>
							<hr>
							<div class="content">
								<div class="col col-mb-12 col-2 form-label">
									&nbsp;
								</div>
								<div class="col col-mb-12 col-10 form-control">
									<h2 class="m0">Техподдержка </h2>
								</div>
								<div class="col col-mb-12 mb10">
									<div class="alert">
										<p>
											Начиная с версии 5.0.0 техническая поддержка по модулю оказывается ТОЛЬКО в рамках тиккетов на GitHub 
										</p>
									</div>
									<a class="btn omni-email-widget" href="https://github.com/dle-modules/DLE-BlockPro/issues/new" target="_blank">Создать тиккет</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- #tab -->
	</div> <!-- .container -->

<div class="container">
	<div class="content">
		<div class="col col-mb-12">
			<hr class="mt0">
			Контакты для связи и техподдержки:<br>
			<a href="https://github.com/dle-modules/DLE-BlockPro" target="_blank" title="Сайт поддержки">BlockPro</a> —
			техподдержка <br>
			<a href="http://bp.pafnuty.name/" target="_blank" title="Официальный сайт модуля">bp.pafnuty.name</a> —
			документация <br>
		</div>
	</div>
</div>
<?php 
		
?>

</body>
</html>
