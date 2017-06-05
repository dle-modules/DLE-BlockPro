<?php
/*
=============================================================================
BLockPro
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die("Hacking attempt!");
}
if ($member_id['user_group'] != '1') {
	msg("error", $lang['index_denied'], $lang['index_denied']);
}

function getTemplatesList($dir) {
	global $cfg, $template;

	$tplFiles = [];
	$tplFiles[] = '<option value="">Выберите шаблон</option>';
	$f = scandir($dir);
	foreach ($f as $file){
		$filename = str_replace('.tpl', '', $file);
		$act = (('blockpro/' . $filename) == $template) ? 'selected' : '' ;

		if(preg_match('/\.(tpl)/', $file)){
			$tplFiles[] = '<option '.$act.' value="blockpro/'.$filename.'">'. $filename.'</option>';
		}
	}

	if (count($tplFiles) > 0) {
		return '<select name="template" class="styler">'.implode('', $tplFiles).'</select>';
	} else {
		return;
	}

}

function base_get_category($categoryid = 0, $parentid = 0, $nocat = TRUE, $returnstring = '') {
	global $cat_info;

	$root_category = [];

	if( $parentid == 0 ) {
		// if( $nocat ) $returnstring .= '<option value="">Выберите категории</option>';
	}

	if( count( $cat_info ) ) {

		foreach ( $cat_info as $cats ) {
			if( $cats['parentid'] == $parentid ) $root_category[] = $cats['id'];
		}

		if( count( $root_category ) ) {
			foreach ( $root_category as $id ) {
				$returnstring .= '<option value="' . $id . '">' . $cat_info[$id]['name'] . '</option>';
			}

			$returnstring = base_get_category( $categoryid, $id, $nocat, $returnstring );

		}
	}

	return $returnstring;
}

function showXFields($configname, $prefix = false, $inOptGroup = false) {
	global $cfg;
	$arr = xfieldsload();
	$feldsArr = [];

	foreach ($arr as $field) {
		
		$act = (in_array($prefix . $field[0], explode(',', $cfg[$configname]))) ? 'selected' : '';
		$feldsArr[] = '<option '.$act.' value="' . $prefix . $field[0] . '">' . $field[1] . ' (' . $field[0] . ')</option>';
	}

	if (count($feldsArr) > 0) {
		if ($inOptGroup) {
			return '<optgroup label="По значению допполя">' . implode('', $feldsArr) . '</optgroup>';
		} else {
			return '<select data-placeholder="Выберите допполя" multiple name="' . $configname . '[]" class="styler">'.implode('', $feldsArr).'</select>';
		} 
	} else {
		return;
	}
}


$optTemplates = getTemplatesList(ROOT_DIR . '/templates/'. $config['skin'] . '/blockpro/');

?>

<form id="ajaxForm1" method="get">
	<input type="hidden" name="mod" value="blockpro">
	<input type="hidden" name="setPreview" value="y">
	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Базовые параметры</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Шаблон блока <b>blockpro/</b>
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<?php echo $optTemplates?>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Количество новостей в блоке
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input input-short mr10" type="number" name="limit" value="<?php echo ($cfg['limit'] != 10) ? $cfg['limit'] : '';?>" placeholder="<?php echo $cfg['limit']?>"> начать с <input class="input input-short ml10" type="number" name="startFrom" value="<?php echo ($cfg['startFrom'] > 0) ? $cfg['startFrom'] : '';?>" placeholder="<?php echo $cfg['startFrom']?>">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Сортировка новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select name="sort" class="styler">
					<option value="">Стандартный топ</option>
					<option value="none"        <?php if($cfg['sort'] == 'none'):?> selected <?php endif?>>Без сортировки</option>
					<option value="date"        <?php if($cfg['sort'] == 'date'):?> selected <?php endif?>>По дате добавления</option>
					<option value="rating"      <?php if($cfg['sort'] == 'rating'):?> selected <?php endif?>>По рейтингу</option>
					<option value="comms"       <?php if($cfg['sort'] == 'comms'):?> selected <?php endif?>>По кол-ву комментариев</option>
					<option value="views"       <?php if($cfg['sort'] == 'views'):?> selected <?php endif?>>По кол-ву просмотов</option>
					<option value="random"      <?php if($cfg['sort'] == 'random'):?> selected <?php endif?>>В случаном порядке</option>
					<option value="randomLight" <?php if($cfg['sort'] == 'randomLight'):?> selected <?php endif?>>В случаном порядке (Light)</option>
					<option value="title"       <?php if($cfg['sort'] == 'title'):?> selected <?php endif?>>По алфавиту</option>
					<option value="hit"         <?php if($cfg['sort'] == 'hit'):?> selected <?php endif?>>Хит (Правильный топ)</option>
					<option value="download"    <?php if($cfg['sort'] == 'download'):?> selected <?php endif?>>По кол-ву скачиваний</option>
					<option value="symbol"      <?php if($cfg['sort'] == 'symbol'):?> selected <?php endif?>>По символьному коду</option>
					<option value="editdate"    <?php if($cfg['sort'] == 'editdate'):?> selected <?php endif?>>По дате редактирования</option>
					<?php echo showXFields('sort', 'xf|', true)?>
				</select>
				<p>
					<input class="radio" id="orderNEW" type="radio" name="order" value="new" <?php if($cfg['order'] != 'old'):?> checked <?php endif?>>
					<label for="orderNEW" class="mr10"><span></span>По убыванию</label>
					<input class="radio" id="orderOLD" type="radio" name="order" value="old" <?php if($cfg['order'] == 'old'):?> checked <?php endif?>>
					<label for="orderOLD" class="mr10"><span></span>По возрастанию</label>
					<input class="radio" id="orderASIS" type="radio" name="order" value="asis" <?php if($cfg['order'] == 'asis'):?> checked <?php endif?>>
					<label for="orderASIS"><span></span>Как есть</label>
				</p>
				<div class="alert alert-info">Для правильной сортировки "Как есть" не забывайте указывать конкретные ID новостей (или диапазон)</div>
				<p>
					<input class="checkbox" id="xfSortTypeCheckbox" type="checkbox" name="xfSortType" value="string" <?php if($cfg['xfSortType'] == 'string'):?> checked <?php endif?>>
					<label for="xfSortTypeCheckbox" class="mr10"><span></span>Сортировать по значению допполя как по строке</label>
				</p>
				<div class="alert alert-info">Опция имеет смысл только при сортировке по значению допполя и если в допполе хранится строка, а не число. К примеру фамилия режиссёра фильма, а не цена или год. <br>Так же рекомендуется добавлять в фильтрацию по именам допполей выбранное допполе для исключения из вывода новостей с пустыми полями.</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Период и интервал для отбора новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				Период: <input class="input input-short mr10" type="number" name="day" value="<?php echo $cfg['day']?>" placeholder="day">
				Интервал: <input class="input input-short mr10" style="width: 100px;" type="number" name="dayCount" value="<?php echo $cfg['dayCount']?>" placeholder="dayCount">
				<div class="alert alert-info">
					<p>К примеру нужно вывести новости за прошлую неделю. Код: <code>&day=14&dayCount=7</code> выведет новости за период 14 дней с интервалом в 7 дней, что и есть прошлая неделя. </p>
					<p>
						Для вывода новостей только за сегодня необходимо вписать в оба поля '-1' (<code>&day=-1&dayCount=-1</code>)
					</p>
					<p>
						<a href="engine/modules/base/admin/blockpro/images/days.png" title="Красным полупрозрачным блоком выделены дни, новости которых попадут в вывод." class="open-img">Пояснение по временным параметрам</a>
					</p>
				</div>
			</div>
		</div>
		
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Постраничная навигация
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="showNav" id="showNav"  <?php echo $showNav_checked?>> <label for="showNav"><span></span> Выводить навигацию</label>
				<div class="alert">Добавит 1 лёгкий запрос на блок</div>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="navDefaultGet" id="navDefaultGet"  <?php echo $navDefaultGet_checked?>> <label for="navDefaultGet"><span></span> Отслеживать стандартную навигацию DLE</label>
				<div class="alert alert-info">При указании этого параметра модуль будет брать значение текущей страницы и формировать постраничную навигацию так же как это делается в DLE. Переход между страницами новостей будет осуществляться с перезагрузкой страницы.</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Стиль постраничной навигации
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select name="navStyle" class="styler">
					<option value=""        <?php if($cfg['navStyle'] == 'classic'):?> selected <?php endif?>>classic</option>
					<option value="digg"    <?php if($cfg['navStyle'] == 'digg'):?> selected <?php endif?>>digg</option>
					<option value="extended"<?php if($cfg['navStyle'] == 'extended'):?> selected <?php endif?>>extended</option>
					<option value="punbb"   <?php if($cfg['navStyle'] == 'punbb'):?> selected <?php endif?>>punbb</option>
					<option value="arrows"  <?php if($cfg['navStyle'] == 'arrows'):?> selected <?php endif?>>arrows</option>
				</select>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Начальная страница в блоке
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input input-short mr10" type="number" name="pageNum" value="<?php echo $cfg['pageNum']?>"> (при загрузке без AJAX)
			</div>
		</div>


	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Режимы работы модуля</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Режим вывода похожих новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input input-short mr10" type="text" name="related" value="<?php echo $cfg['related']?>"> (например this или 7)
				<p>
					<input class="checkbox" id="saveRelated" type="checkbox" name="saveRelated" value="y" <?php if($cfg['saveRelated']):?> checked <?php endif?>>
					<label for="saveRelated"><span></span>Записывать похожие новости в БД (ускоряет работу)</label>
				</p>
				<div class="alert alert-info">
					<div>Оставить поле пустым, если не требуется вывод похожих новостей.</div>
					<div>Для корректного вывода похожих новостей не забудьте выставить сортировку "без сортировки"</div>
				</div>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Режим афиши
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="future" id="future"  <?php echo $future_checked?>> <label for="future"><span></span> включить</label>
				<div class="alert alert-info">
					<div>При включении режима афиши дни будут прибавляться, а не вычитаться.</div>
					<div><a href="engine/modules/base/admin/blockpro/images/days.png" title="Красным полупрозрачным блоком выделены дни, новости которых попадут в вывод." class="open-img">Пояснение по временным параметрам</a></div>
				</div>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Экспериментальные функции
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="experiment" id="experiment"  <?php echo $experiment_checked?>> <label for="experiment"><span></span> включить</label>
				<div class="alert alert-info">
					<div>
						Что произойдёт <small>(список функций будет расширяться)</small>:
						<ul>
							<li>Начиная с DLE 11.0 фильтрация по значению допполей должна происходить быстрее за счёт получения ID новостей из отдельной таблицы с допполями.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Кеширование и отладка</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Время жизни кеша (мин)
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input input-short mr10" type="text" name="cacheLive" value="<?php echo $cfg['cacheLive']?>"> например 180 — это 3 часа, а 50s — 50 секунд
				<p>
					<input class="checkbox" type="checkbox" value="y" name="nocache" id="nocache"  <?php echo $nocache_checked?>> <label for="nocache"><span></span> отключить кеширование блока</label>
				</p>
				<div class="alert alert-info">Не забывайте, что шаблон модуля кешируется отдельно, поэтому на время работы с шаблоном лучше отключать кеширование блока. <br>Время жизни кеша работает только с файловым кешем.</div>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="showstat" id="showstat"  <?php echo $showstat_checked?>> <label for="showstat"><span></span> Показывать статистику по блоку</label>
			</div>
		</div>
	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Фильтрация по категориям и ID новостей</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Категории для показа
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select data-placeholder="Выберите категории" name="catId[]" class="styler" multiple>
					<?php echo base_get_category()?>
				</select>
				<br>
				<input class="input mt10" type="text" name="catId[]" value="<?php echo $cfg['catId']?>" placeholder="или так: 1-10,15,18,45-150 или this"> <br>
				<input class="checkbox" type="checkbox" value="y" name="subcats" id="subcats"  <?php echo $subcats_checked?>> <label for="subcats"><span></span> Выводить подкатегории выбранных категорий</label>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Игнорируемые категории
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select data-placeholder="Выберите категории" name="notCatId[]" class="styler" multiple>
					<?php echo base_get_category()?>
				</select>
				<br>
				<input class="input mt10" type="text" name="notCatId[]" value="<?php echo $cfg['notCatId']?>" placeholder="или так: 1-10,15,18,45-150 или this"> <br>
				<input class="checkbox" type="checkbox" value="y" name="notSubcats" id="notSubcats"  <?php echo $notSubcats_checked?>> <label for="notSubcats"><span></span> Игнорировать подкатегории выбранных категорий</label>
				
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">	
				<input class="checkbox" type="checkbox" value="y" name="thisCatOnly" id="thisCatOnly"  <?php echo $thisCatOnly_checked?>> <label for="thisCatOnly"><span></span> Выводить/игнорировать новости только из текущей категории</label>
				<div class="alert alert-info">
					Вывод новостей только из текущей категории имеет смысл при выводе похожих новостей, если используются мультикатегории и если нужно вывести или исключить из вывода новости, принадлежащие только к просматриваемой категории. 
				</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				ID новостей для вывода
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="postId" value="<?php echo $cfg['postId']?>" placeholder="можно так: 1-10,15,18,45-150 или this">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				ID игнорируемых новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="notPostId" value="<?php echo $cfg['notPostId']?>" placeholder="можно так: 1-10,15,18,45-150 или this">
			</div>
		</div>
	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Обработка свойств новостей</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Новости на модерации
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="moderate" id="moderate"  <?php echo $moderate_checked?>> <label for="moderate"><span></span> показывать</label>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Обработка фиксированных новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select name="fixed" class="styler">
					<option value="yes" <?php if($cfg['fixed'] == 'yes'):?> selected <?php endif?>>Показывать вместе с обычными</option>
					<option value="only" <?php if($cfg['fixed'] == 'only'):?> selected <?php endif?> >Показывать только фиксированные</option>
					<option value="without" <?php if($cfg['fixed'] == 'without'):?> selected <?php endif?> >Показывать только обычные</option>
				</select>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Обработка новостей, опубликованных на главной
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<select name="allowMain" class="styler">
					<option value="yes" <?php if($cfg['allowMain'] == 'yes'):?> selected <?php endif?>>Показывать все</option>
					<option value="only" <?php if($cfg['allowMain'] == 'only'):?> selected <?php endif?> >Показывать только опубликованные на главной</option>
					<option value="without" <?php if($cfg['allowMain'] == 'without'):?> selected <?php endif?> >Показывать только неопубликованные на главной</option>
				</select>
			</div>
		</div>

	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Фильтрация по допполям</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Имена дополнительных полей для фильтрации новостей по ним <br>
				<small>Проверется только заполенность полей</small>
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<?php echo showXFields('xfilter')?>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Имена дополнительных полей для игнорирования показа новостей<br>
				<small>Проверется только заполенность полей</small>
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<?php echo showXFields('notXfilter')?>
				<div class="alert alert-info">
					Можно использовать <code>&amp;xfilter=this</code> и <code>&amp;notXfilter=this</code> для показа новостей, содержащих текущее 	допполе при просмотре страниц /xfsearch/
				</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Данные для фильтрации по значению допполей (не зависит от настроек выше)
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="xfSearch" value="<?php echo $cfg['xfSearch']?>" placeholder="имя_поля|значение||имя_поля|значение">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Данные для исключающей фильтрации по значению допполей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="notXfSearch" value="<?php echo $cfg['notXfSearch']?>" placeholder="имя_поля|значение||имя_поля|значение">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Логика фильтрации по значению допполей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="radio" id="xfSearchLogicOR" type="radio" name="xfSearchLogic" <?php if($cfg['xfSearchLogic'] != 'AND'):?> checked <?php endif?>>
				<label for="xfSearchLogicOR"><span></span>ИЛИ (OR)</label>
				<input class="radio" id="xfSearchLogicAND" type="radio" name="xfSearchLogic" value="AND" <?php if($cfg['xfSearchLogic'] == 'AND'):?> checked <?php endif?>>
				<label for="xfSearchLogicAND" class="ml10"><span></span>И (AND)</label>

			</div>
		</div>

	</div> <!-- .logic-block -->
	
	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Фильтрация по авторам и тегам</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="checkbox" type="checkbox" value="y" name="avatar" id="avatar"  <?php echo $avatar_checked?>> <label for="avatar"><span></span> Выводить аватарку автора новости</label>
				<div class="alert">Слегка утяжелит запрос в БД</div>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Логины авторов, для показа их новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="author" value="<?php echo $cfg['author']?>" placeholder="Иван,admin,Петр">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Логины авторов, для исключения их новостей
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="notAuthor" value="<?php echo $cfg['notAuthor']?>" placeholder="Михаил,username">
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Теги из облака тегов для показа новостей, содержащих их
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="tags" value="<?php echo $cfg['tags']?>" placeholder="tag1,tag2">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Теги из облака тегов для игнорирования новостей, содержащих их
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="notTags" value="<?php echo $cfg['notTags']?>" placeholder="tag1,tag2">
			</div>
		</div>
	</div> <!-- .logic-block -->

	<div class="logic-block">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				&nbsp;
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Собственная фильтрация и колонки БД</h3>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Дополнительные колонки, отбираемые из БД
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="fields" value="<?php echo $cfg['fields']?>" placeholder="p.custom,e.extra">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">
				Собственные условия фильтрации
			</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input class="input" type="text" name="setFilter" value="<?php echo $cfg['setFilter']?>" placeholder="e.news_read|+|100||p.comm_num|-|20">
				<p class="alert">
					<b>ВНИМАНИЕ!</b> Корректность фильтрации не проверяется.
				</p>
				<p class="alert alert-info">Для удаления ненужных стандартных параметров фильтрации рекомендуется выбирать параметр сортировки "без сортировки".</p>
			</div>
		</div>

	</div> <!-- .logic-block -->








	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			&nbsp;
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<button class="btn ladda-button" type="submit" data-style="expand-left"><span class="ladda-label">Применить</span></button>
			<a class="btn btn-link" href="<?php echo $PHP_SELF?>?mod=blockpro">Сбросить</a>
		</div>
	</div>

</form>
