<?php

if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die("Hacking attempt!");
}
if ($member_id['user_group'] != '1') {
	msg("error", $lang['index_denied'], $lang['index_denied']);
}

function getTemplatesList($dir) {
	global $cfg;

	$tplFiles = array();
	$tplFiles[] = '<option value="">Выберите шаблон</option>';
	$f = scandir($dir);
	foreach ($f as $file){
		$filename = str_replace('.tpl', '', $file);
		// $act = ('blockpro/' . $filename == $cfg['template']) ? 'selected' : '' ;
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

	$root_category = array ();

	if( $parentid == 0 ) {
		if( $nocat ) $returnstring .= '<option value="">Выберите категории</option>';
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

function showXFields($configname) {
	global $cfg;
	$arr = xfieldsload();
	$feldsArr = array();
	$feldsArr[] = '<option value="">Выберите допполе</option>';

	foreach ($arr as $field) {
		$act = (in_array($field[0], explode(',', $cfg[$configname]))) ? 'selected' : '';
		$feldsArr[] = '<option '.$act.' value="'.$field[0].'">'. $field[1].'</option>';
	}

	if (count($feldsArr) > 0) {
		return '<select multiple name="' . $configname . '[]" class="styler">'.implode('', $feldsArr).'</select>';
	} else {
		return;
	}
}


$optTemplates = getTemplatesList(ROOT_DIR . '/templates/'. $config['skin'] . '/blockpro/');

// echo "<pre class='dle-pre'>xfieldsdataload: "; print_r($_REQUEST); echo "</pre>";


?>

<form ation="<?=$PHP_SELF?>" id="ajaxForm1" method="post">
	<input type="hidden" name="mod" value="blockpro">
	<input type="hidden" name="setPreview" value="y">

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Новости на модерации
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="checkbox" type="checkbox" value="y" name="moderate" id="moderate"  <?=$moderate_checked?>> <label for="moderate"><span></span> показывать</label>
		</div>
	</div>
	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Шаблон блока <b>blockpro/</b>
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<?=$optTemplates?>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Время жизни кеша (мин)
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="cacheLive" value="<?=$cfg['cacheLive']?>" placeholder="например 180 - это 3 часа"> <br>
			<input class="checkbox" type="checkbox" value="y" name="nocache" id="nocache"  <?=$nocache_checked?>> <label for="nocache"><span></span> отключить кеширование блока</label>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			C какой новости начать вывод
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="startFrom" value="<?=($cfg['startFrom'] > 0) ? $cfg['startFrom'] : '';?>" placeholder="по умолчанию <?=$cfg['startFrom']?>">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Количество новостей в блоке
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="limit" value="<?=($cfg['limit'] != 10) ? $cfg['limit'] : '';?>" placeholder="по умолчанию <?=$cfg['limit']?>">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Обработка фиксированных новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="fixed" class="styler">
				<option value="yes" <? if($cfg['fixed'] == 'yes'):?> selected <?endif?>>Показывать вместе с обычными</option>
				<option value="only" <? if($cfg['fixed'] == 'only'):?> selected <?endif?> >Показывать только фиксированные</option>
				<option value="without" <? if($cfg['fixed'] == 'without'):?> selected <?endif?> >Показывать только обычные</option>
			</select>
		</div>
	</div>


	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Категории для показа
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="catId[]" class="styler" multiple>
				<?=base_get_category()?>
			</select>
			<input class="input" type="text" name="catId[]" value="<?=$cfg['catId']?>" placeholder="или так: 1-10,15,18,45-150"> <br>
			<input class="checkbox" type="checkbox" value="y" name="subcats" id="subcats"  <?=$subcats_checked?>> <label for="subcats"><span></span> Выводить подкатегории выбранных категорий</label>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Игнорируемые категории
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="notCatId[]" class="styler" multiple>
				<?=base_get_category()?>
			</select>
			<input class="input" type="text" name="notCatId[]" value="<?=$cfg['notCatId']?>" placeholder="или так: 1-10,15,18,45-150"> <br>
			<input class="checkbox" type="checkbox" value="y" name="notSubcats" id="notSubcats"  <?=$notSubcats_checked?>> <label for="notSubcats"><span></span> Игнорировать подкатегории выбранных категорий</label>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			ID новостей для вывода
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="postId" value="<?=$cfg['postId']?>" placeholder="можно так: 1-10,15,18,45-150">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			ID игнорируемых новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="notPostId" value="<?=$cfg['notPostId']?>" placeholder="можно так: 1-10,15,18,45-150">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Логины авторов, для показа их новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="author" value="<?=$cfg['author']?>" placeholder="Иван,admin,Петр">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Логины авторов, для показа их новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="notAuthor" value="<?=$cfg['notAuthor']?>" placeholder="Михаил,username">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Имена дополнительных полей для фильтрации новостей по ним <br>
			<small>Проверется только заполенность полей</small>
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<?=showXFields('xfilter')?>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Имена дополнительных полей для игнорирования показа новостей<br>
			<small>Проверется только заполенность полей</small>
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<?=showXFields('notXfilter')?>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Данные для фильтрации по значению допполей (не зависит от настроек выше)
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="xfSearch" value="<?=$cfg['xfSearch']?>" placeholder="имя_поля|значение||имя_поля|значение">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Данные для исключающей фильтрации по значению допполей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="notXfSearch" value="<?=$cfg['notXfSearch']?>" placeholder="имя_поля|значение||имя_поля|значение">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Логика фильтрации по значению допполей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="xfSearchLogic" class="styler">
				<option value="">Логика: И (OR)</option>
				<option value="AND" <? if($cfg['xfSearchLogic'] == 'AND'):?> selected <?endif?>>Логика: ИЛИ (AND)</option>
			</select>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Теги из облака тегов для показа новостей, содержащих их
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="tags" value="<?=$cfg['tags']?>" placeholder="tag1,tag2">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Теги из облака тегов для игнорирования новостей, содержащих их
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="notTags" value="<?=$cfg['notTags']?>" placeholder="tag1,tag2">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Временной период для отбора новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="day" value="<?=$cfg['day']?>" placeholder="например 14">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Временной интервал для отбора <br> <small>(т.е. к примеру выбираем новости за прошлую недею так: &day=14&dayCount=7)</small>
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="dayCount" value="<?=$cfg['dayCount']?>" placeholder="например 7">
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Сортировка новостей
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="sort" class="styler">
				<option value="">Стандартный топ</option>
				<option value="none"   <? if($cfg['sort'] == 'none'):?> selected <?endif?>>Без сортировки</option>
				<option value="date"   <? if($cfg['sort'] == 'date'):?> selected <?endif?>>По дате добавления</option>
				<option value="rating" <? if($cfg['sort'] == 'rating'):?> selected <?endif?>>По рейтингу</option>
				<option value="comms"  <? if($cfg['sort'] == 'comms'):?> selected <?endif?>>По кол-ву комментариев</option>
				<option value="views"  <? if($cfg['sort'] == 'views'):?> selected <?endif?>>По кол-ву просмотов</option>
				<option value="random" <? if($cfg['sort'] == 'random'):?> selected <?endif?>>В случаном порядке</option>
				<option value="title"  <? if($cfg['sort'] == 'title'):?> selected <?endif?>>По алфавиту</option>
				<option value="hit"    <? if($cfg['sort'] == 'hit'):?> selected <?endif?>>Хит (Правильный топ)</option>
			</select>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Направление сортировки
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="order" class="styler">
				<option value="">По убыванию</option>
				<option value="old" <? if($cfg['order'] == 'old'):?> selected <?endif?>>По возрастанию</option>
			</select>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Постраничная навигация
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="checkbox" type="checkbox" value="y" name="showNav" id="showNav"  <?=$showNav_checked?>> <label for="showNav"><span></span> Выводить навигацию (добавит 1 запрос на блок)</label>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Стиль постраничной навигации
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<select name="navStyle" class="styler">
				<option value="">classic</option>
				<option value="digg">digg</option>
				<option value="extended">extended</option>
				<option value="punbb">punbb</option>
			</select>
		</div>
	</div>

	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Начальная страница в блоке (при загрузке без AJAX)
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="pageNum" value="<?=$cfg['pageNum']?>" placeholder="например 7">
		</div>
	</div>


	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			Режим вывода похожих новостей <small>(Оставить поле пустым, если не требуется вывод похожих новостей)</small>
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="input" type="text" name="related" value="<?=$cfg['related']?>" placeholder="например this или 7">
		</div>
	</div>


	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			&nbsp;
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<input class="checkbox" type="checkbox" value="y" name="avatar" id="avatar"  <?=$avatar_checked?>> <label for="avatar"><span></span> Выводить аватарку автора новости</label> <br>
			<input class="checkbox" type="checkbox" value="y" name="showstat" id="showstat"  <?=$showstat_checked?>> <label for="showstat"><span></span> Показывать время и статистику по блоку</label> <br>
		</div>
	</div>





	<div class="content">
		<div class="col col-mb-12 col-5 col-dt-4 form-label">
			&nbsp;
		</div>
		<div class="col col-mb-12 col-7 col-dt-8 form-control">
			<button class="btn ladda-button" type="submit" data-style="expand-left"><span class="ladda-label">Применить</span></button>
			<a class="btn btn-link" href="<?=$PHP_SELF?>?mod=blockpro">Сбросить</a>
		</div>
	</div>

</form>
