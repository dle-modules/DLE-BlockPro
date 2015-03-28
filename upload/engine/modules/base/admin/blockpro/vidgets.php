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
$arVidgets = $db->super_query("SELECT * FROM " . PREFIX ."_blockpro_blocks ORDER BY id ASC", true);
$showDeleteInfo = false;
if (isset($_POST['blockId']) && $_POST['widgetDelete'] == 'Y') {
	$widgetId = $db->safesql($_POST['blockId']);
	$widgetToDelete = $db->super_query("SELECT id FROM " . PREFIX . "_blockpro_blocks WHERE id = '{$widgetId}'" );
	if ($widgetToDelete['id'] > 0) {
		$db->super_query("DELETE FROM " . PREFIX . "_blockpro_blocks WHERE id = '{$widgetId}'" );
		$showDeleteInfo = '<script>jQuery(document).ready(function($) {
			$.magnificPopup.open({
				items: {
					src: \'<div class="col-mb-12 col-8 col-dt-6 col-ld-5 center-block"><div class="content"><div class="modal-white"><span class="modal-close popup-modal-dismiss">&times;</span><div class="modal-header"><p>Виджет успешно удалён.</p></div><div class="modal-content p10"><div class="ta-center mb10"><span class="btn modal-close">Отлично!</span></div></div></div></div></div>\'
				},
				type: \'inline\',
				callbacks: {close: function () {location.reload() } } 
			});
		});</script>';
	}
}
?>
<div class="content">
	<div class="col col-mb-12">
		<h2>Виджеты для вывода на сторонних сайтах</h2>
		<?if (count($arVidgets) > 0):?>
			<?foreach ($arVidgets as $key => $vidgetItem):?>
				<div class="widget-item content">
					<div class="col col-mb-9">
						<b class="text-muted">[<?=$vidgetItem['id']?>]</b> <h3 class="m0 mr10 fz20 d-ib"><a href="/blockpro.php?block=<?=$vidgetItem['block_id']?>" target="_blank"><?=$vidgetItem['name']?></a></h3> (создан <?=$vidgetItem['date']?>)
					</div>
					<div class="col col-mb-3 ta-right">
						<span class="btn btn-small delete-widget" data-widget-action="<?$PHP_SELF?>?mod=blockpro" data-widget-id="<?=$vidgetItem['id']?>" data-widget-name="<?=$vidgetItem['name']?>">удалить</span>
					</div>
					<div class="col col-mb-12">
						<h4>Код для вставки через javascript:</h4>
						<textarea readonly class="input input-block-level code"><script type="text/javascript" async defer src="<?=$config['http_home_url']?>blockpro.php?block=<?=$vidgetItem['block_id']?>"></script>
<div id="<?=$vidgetItem['block_id']?>"></div></textarea>
						
						<h4>Код для вывода RSS:</h4>
						<textarea rows="1" readonly class="input input-block-level code"><?=$config['http_home_url']?>blockpro.php?channel=<?=$vidgetItem['block_id']?></textarea>						
					</div>
				</div>
			<?endforeach?>
		<?else:?>
			<div class="alert alert-info">Вы пока не создали ни одного виджета. Если вы только что его создали - нажмите F5 т.к. виджет создаётся без перезагрузки страницы.</div>
		<?endif?>
	</div>
</div> <!-- .content -->
<?=$showDeleteInfo?>

