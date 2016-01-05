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

if (isset($_POST['bp_config'])) {
	$bp_config = $_POST['bp_config'];

	$handler = fopen(ENGINE_DIR . '/data/blockpro.php', "w");

	fwrite($handler, "<?php \n/**\n * Конфиг модуля BlockPro\n * @var array\n */\n\n\$bpConfig = [\n");

	foreach ($bp_config as $name => $value) {

		if ($name == 'approve_groups' || $name == 'postmoder_groups') {
			fwrite($handler, "\t'{$name}' => [ ");
			foreach ($value as $groupId) {
				fwrite($handler, "{$groupId}, ");
			}
			fwrite($handler, "],\n");
		} else {
			if (is_numeric($value)) {
				fwrite($handler, "\t'{$name}' => {$value},\n");
			} else {
				fwrite($handler, "\t'{$name}' => '{$value}',\n");
			}
		}

	}
	fwrite($handler, "];");
	fclose($handler);
}


?>
<?php if (isset($_POST['saveConfig']) && $_POST['saveConfig'] == 'y'): ?>
	<div class="content">
		<div class="col col-mb-12">
			<div class="alert alert-success">
				<p>Настройки модуля успешно сохранены.</p>
				<a href="?mod=blockpro" class="btn btn-small">Вернуться назад</a>
			</div>
		</div>
	</div>
<?php else: ?>
	<form method="post">
		<input type="hidden" name="mod" value="blockpro">
		<input type="hidden" name="saveConfig" value="y">
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">&nbsp;</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Настройка лицензии</h3>
			</div>
		</div>
		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">Ключ активации модуля</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input type="text" class="input" name="bp_config[activation_key]" value="<?php echo $bpConfig['activation_key'] ?>">
				<div class="alert alert-info">
					Ключ активации можно получить <a href="http://store.pafnuty.name/purchase/" target="_blank">в списке покупок</a>
				</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">&nbsp;</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Настройка tinyPNG <small>(при необходимости)</small></h3>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">API key</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input type="text" class="input" name="bp_config[tinypng_key]" value="<?php echo $bpConfig['tinypng_key'] ?>">
				<div class="alert alert-info">
					Получить API key можно на странице <a href="https://tinypng.com/developers" target="_blank">для разработчиков</a>. <br>
					Там же вы найдёте информацию о правилах использования сервиса и лимитах.
				</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">&nbsp;</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<h3 class="mb0 h2 text-muted">Настройка Kraken.io <small>(при необходимости)</small></h3>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">API Key</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input type="text" class="input" name="bp_config[kraken_key]" value="<?php echo $bpConfig['kraken_key'] ?>">
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">API Secret</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<input type="text" class="input" name="bp_config[kraken_secret]" value="<?php echo $bpConfig['kraken_secret'] ?>">
				<div class="alert alert-info">
					Получить ключи можно <a href="https://kraken.io/account/api-credentials" target="_blank">в профиле пользователя</a>. <br>
					Так же в личном кабинете вы найдёте необходимую информацию о лимитах и правилах работы сервиса.
				</div>
			</div>
		</div>

		<div class="content">
			<div class="col col-mb-12 col-5 col-dt-4 form-label">&nbsp;</div>
			<div class="col col-mb-12 col-7 col-dt-8 form-control">
				<button class="btn" type="submit">Сохранить</button>
			</div>
		</div>

	</form>
<?php endif ?>