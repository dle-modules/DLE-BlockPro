<?php
/*
=============================================================================
Проверка совместимости модуля и сайта
=============================================================================
Автор:   ПафНутиЙ 
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

$moduleName = 'BlockPro';
include('engine/api/api.class.php');

/** @var array $config */
$arCheck = array(
	array(
		'name'  => 'Версия DLE',
		'req'   => '10.0',
		'check' => ($config['version_id'] < 10.0) ? '<span class="red">' . $config['version_id'] . '</span>' : $config['version_id'],
	),
	array(
		'name'  => 'Кодировка',
		'req'   => 'utf-8',
		'check' => ($config['charset'] != 'utf-8') ? '<span class="red">' . $config['charset'] . '</span>' : $config['charset'],
	),
	array(
		'name'  => 'Версия php',
		'req'   => '5.6 и выше',
		'check' => (phpversion() < 5.6) ? '<span class="red">' . phpversion() . '</span>' : phpversion(),
	)

);

function checkIonCube() {
	if (function_exists('ioncube_loader_version')) {
		return ioncube_loader_version();
	} else {
		return '<span class="red">не найдено</span>';
	}
}

function checkShortOpenTag() {
	if (ini_get('short_open_tag')) {
		return 'On';
	} else {
		return '<span class="red">Off</span>';
	}

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BlockPro Checker</title>
	<style>
		/*! normalize.css v3.0.2 | MIT License | git.io/normalize */
		html {
			font-family: sans-serif;
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%
		}

		body {
			margin: 0;
			font-size: 16px;
			line-height: 1.5;
		}

		a {
			background-color: transparent
		}

		a:active, a:hover {
			outline: 0
		}

		b, strong {
			font-weight: 700
		}

		h1 {
			font-size: 2em;
			margin: .67em 0
		}

		img {
			border: 0
		}

		svg:not(:root) {
			overflow: hidden
		}

		hr {
			-moz-box-sizing: content-box;
			box-sizing: content-box;
			height: 0
		}

		table {
			border-collapse: collapse;
			border-spacing: 0
		}

		td, th {
			padding: 0
		}

		.content {
			margin: 0 auto
		}

		.content:after, .content:before {
			content: " ";
			display: table
		}

		.content:after {
			clear: both
		}

		.content .content {
			margin-left: -10px;
			margin-right: -10px
		}

		.col {
			padding-left: 10px;
			padding-right: 10px;
			min-height: 1px;
			float: left;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box
		}

		.col-mb-12 {
			width: 100%
		}

		@media (min-width: 768px) {
			.content {
				max-width: 728px
			}
		}

		@media (min-width: 992px) {
			.content {
				max-width: 952px
			}
		}

		hr {
			display: block;
			height: 1px;
			border: 0;
			border-top: 1px solid #ccc;
			margin: 1em 0;
			padding: 0
		}

		h1 {
			font: normal 28px Arial, sns-serif;
			text-align: center;
			margin-top: 50px;
			color: #666;
		}

		a {
			color: #4a9fc5;
		}

		a:hover {
			text-decoration: none;
		}

		.table {
			max-width: 100%;
			background-color: transparent;
			border-collapse: collapse;
			border-spacing: 0;
			width: 100%;
			border: 1px solid #dddddd;
			border-left: 0;
		}

		.table tr:hover td,
		.table tr:hover th {
			background-color: #f5f5f5;
		}

		.table th {
			font-weight: bold;
			background-color: #f9f9f9;
			vertical-align: bottom;
		}

		.table th,
		.table td {
			padding: 8px;
			line-height: 18px;
			text-align: left;
			vertical-align: top;
			border-top: 1px solid #dddddd;
			border-left: 1px solid #dddddd;
		}

		.red {
			color: #f00;
		}

		.alert {
			border: 1px solid #f1c40f;
			background: rgba(241, 196, 15, .1);
			color: #796307;
			padding: 20px
		}

	</style>
<body>
<div class="container">
	<div class="content">
		<div class="col col-mb-12">
			<h1>Проверка совместимости текущего сайта и модуля BlockPro</h1>
			<hr>
		</div>
		<!-- .col col-mb-12 -->
		<div class="col col-mb-12">
			<table class="table">
				<tr>
					<th>Параметр</th>
					<th>Требование</th>
					<th>Наличие</th>
				</tr>
				<?php foreach ($arCheck as $key => $check): ?>
					<tr>
						<td><?php echo $check['name'] ?></td>
						<td><?php echo $check['req'] ?></td>
						<td><?php echo $check['check'] ?></td>
					</tr>
				<?php endforeach ?>
			</table>
			<p class="alert">
				Если один или несколько пунктов отмечены <span class="red">красным цветом</span> &mdash; модуль не
				запустится на вашем сайте при текущих настройках. Необходимо исправить несоответсвия.
			</p>

			<p class="alert">
				Если всё в порядке &mdash; можно смело <a href="http://bp.pafnuty.name/" target="_blank">устанавливать модуль</a>!
			</p>
		</div>
	</div>
	<!-- .content -->
</div>
<!-- .container -->
</body>
</html>
