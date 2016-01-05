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
/** @var array $member_id */
if ($member_id['user_group'] != '1') {
	/** @var array $lang */
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
		<?php if (count($arVidgets) > 0):?>
			<?php foreach ($arVidgets as $key => $vidgetItem):?>
				<?php 
				
					$params = unserialize($vidgetItem['params']);
					$vidgetModuleUrl = 'include file="engine/modules/base/blockpro.php';
					$vidgetParamsUrl = [];

					// Удаляем значения по умолчанию за ненадобностью.
					if ($params['template'] == 'blockpro/blockpro') {												
						unset($params['template']);
					}
					if ($params['cachePrefix'] == 'news') {												
						unset($params['cachePrefix']);
					}
					if ($params['limit'] == 10) {												
						unset($params['limit']);
					}
					if ($params['fixed'] == 'yes') {												
						unset($params['fixed']);
					}
					if ($params['allowMain'] == 'yes') {												
						unset($params['allowMain']);
					}
					if ($params['xfSearchLogic'] == 'on') {												
						unset($params['xfSearchLogic']);
					}
					if ($params['sort'] == 'top') {												
						unset($params['sort']);
					}
					if ($params['order'] == 'new') {												
						unset($params['order']);
					}
					if ($params['pageNum'] == 1) {												
						unset($params['pageNum']);
					}
					if ($params['navStyle'] == 'classic') {												
						unset($params['navStyle']);
					}
					if ($params['xfSortType'] == 'int') {												
						unset($params['xfSortType']);
					}

					$filteredparams = array_filter($params);
					if (count($filteredparams) > 0) {
						$vidgetModuleUrl .='?';
					}
					foreach (array_filter($params) as $value) {
						$vidgetParamsUrl[] = $key . '=' . $value;
					}
					$vidgetModuleUrlParams = implode('&amp;', $vidgetParamsUrl);

					$vidgetModuleUrl .= $vidgetModuleUrlParams;
				?>
		
				<div class="widget-item content">
					<div class="col col-mb-9">
						<b class="text-muted">[<?php echo $vidgetItem['id']?>]</b> <h3 class="m0 mr10 fz20 d-ib"><a href="/blockpro.php?block=<?php echo $vidgetItem['block_id']?>" target="_blank"><?php echo $vidgetItem['name']?></a></h3> (создан <?php echo $vidgetItem['date']?>) <a href="?mod=blockpro&setPreview=y&<?php echo $vidgetModuleUrlParams?>#tab2" class="btn btn-small" title="Предпросмотр виджета или создание нового на основе текущего">Предпросмотр</a>
					</div>
					<div class="col col-mb-3 ta-right">
						<span class="btn btn-small delete-widget" data-widget-action="<?$PHP_SELF?>?mod=blockpro" data-widget-id="<?php echo $vidgetItem['id']?>" data-widget-name="<?php echo $vidgetItem['name']?>">удалить</span>
					</div>
					<div class="col col-mb-12">
						<h4>Код для вставки через javascript:</h4>
						<textarea readonly class="input input-block-level code"><script type="text/javascript" async defer src="<?php echo $config['http_home_url']?>blockpro.php?block=<?php echo $vidgetItem['block_id']?>"></script>
<div id="<?php echo $vidgetItem['block_id']?>"></div></textarea>
						
						<h4>Код для вывода RSS:</h4>
						<textarea rows="1" readonly class="input input-block-level code"><?php echo $config['http_home_url']?>blockpro.php?channel=<?php echo $vidgetItem['block_id']?></textarea>

						<h4>Строка подключения:</h4>
						<textarea rows="1" class="input input-block-level code">{<?php echo $vidgetModuleUrl?>"}</textarea>	
										
					</div>
				</div>
			<?php endforeach?>
		<?php else:?>
			<div class="alert alert-info">Вы пока не создали ни одного виджета. Если вы только что его создали - нажмите F5 т.к. виджет создаётся без перезагрузки страницы.</div>
		<?php endif?>
	</div>
</div> <!-- .content -->
<?php echo $showDeleteInfo?>