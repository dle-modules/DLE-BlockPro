{*
	$block_id - это уникальный идентификатор блока (у блоков с разными настройками он разный) для того, что бы правильно организовать постраничную навигацию на ajax. Этот блок необходимо располагать в начале шаблона, иначе есть риск отломить ajax навигацию.
*}
<div id="{$block_id}">
	{* В этом шаблоне собрано всё, что может быть выведено модулем. или почти всё *}
	{* Стили *}
	{ignore} {* Используем тег {ignore} для вывода данных как есть *}
		<style>
			.box {
				padding: 10px;
				margin: 10px 0;
				border: solid 1px #ccc;
			}
			.box:before, 
			.box:after { content: ""; display: table; }
			.box:after { clear: both; }
			.box-hide {
				display: none;
			}
			.box-header {
				font: normal 20px/30px Arial, sans-serif;
				color: #398dd8;
				cursor: pointer;
			}
			.box-header:hover {
				color: #c9143d;
			}

			.btn {
				cursor: pointer;
				display: inline-block;
				padding: 5px;
				min-width: 16px;
				border-radius: 5px;
				background: #398dd8;
				color: #fff;
				width: 10px;
				text-align: center;
				transition: all ease .3s;
			}
			.btn:hover {
				background: #c9143d;
			}
			.text-red {color: #c9143d;}
		</style>
	{/ignore}

	{* .box-header+.box.box-hide *}
	
	{$pages}

	<div class="box-header">Конфиг DLE</div>
	<div class="box box-hide">
		{foreach $dleConfig as $key=>$value }
			{$key} : {$value} <br>
		{/foreach}
	</div>


	
	{*
		Тут мы определяем количество новостей в блоке
		$list - переменная, содержащая массив с новостями.
	*}
	{var $newsCount = $list|length}

	<p><b>В блоке {$newsCount} {$newsCount|declination:'новост|ь|и|ей'}</b></p>
	{*Пробегаем по массиву с новостями*}
	{foreach $list as $el}	

		<div class="box-header">Рейтинг {if $dleConfig.rating_type}({$dleConfig.rating_type} тип){/if}</div>
		<div class="box box-hide">		
			{if $dleConfig.rating_type == '1'}
				{* Если рейтинг 1го типа (только лайк) *}
				<span class="btn" {$el.ratingOnclickPlus}>+</span>
			{elseif $dleConfig.rating_type == '2'}					
				{* Если рейтинг 2го типа (лайк и дизлайк) *}
				<span class="btn" {$el.ratingOnclickMinus}>-</span>
				<span class="btn" {$el.ratingOnclickPlus}>+</span>
			{/if}
			{* Показываем кол-во голосов *}
			{$el.showRatingCount}
			{* Цифровое значение рейтинга *}
			{$el.showRating}
		</div>

		<div class="box-header">Все доступные теги новости ID={$el.id} и их значения</div>
		<div class="box box-hide">
			{foreach $el as $key=>$val }
				<b class="text-red">{'{$el.'}{$key}{'}'}</b> : {$val} <br>
			{/foreach}
		</div>
		<div class="box-header">Вся информация о новости через функцию print_r</div>
		<div class="box box-hide">
			<pre><code>Новость: {$el|dump} <br>Категория: {$el.category|catinfo|dump} <br>Картинки: {$el.full_story|image:$noimage:'small':'all':'150':'85':'crop':true:true:'/uploads/myfolder/'|dump}</code></pre>
		</div>

		<div class="box-header">Все дополнительные поля</div>
		<div class="box box-hide">
			{foreach $el.xfields as $name=>$field}
				<b class="text-red">{$name}</b> : {$field} <br>
			{/foreach}
		</div>
		{* Проверяем, есть ли чтонибудь в допполе *}
		{if $el.xfields.link!} {* https://github.com/fenom-template/fenom/blob/964f20a9148f2b218087b2126dbe4ef62eb709df/docs/ru/operators.md#%D0%9E%D0%BF%D0%B5%D1%80%D0%B0%D1%82%D0%BE%D1%80%D1%8B-%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%BA%D0%B8 *}
			<div class="box-header">Поля-перекрёстные ссылки</div>
			<div class="box box-hide">
				{* Для начала разобъём строку с текстом допполя (поле и имененм link) на массив *}
				{set $arField = $el.xfields.link|split} {* https://github.com/fenom-template/fenom/blob/8ce6779119c098562d2bafba9167fbb4e2a222be/docs/ru/mods/split.md *}
				{* Пробежимся по полученному массиву *}
				{foreach $arField as $k=>$link}
					{* И завёрём каждый элемент массива в ссылку *}
					{set $links[$k]|strip} {* https://github.com/fenom-template/fenom/blob/8ce6779119c098562d2bafba9167fbb4e2a222be/docs/ru/mods/strip.md *}
						<a href="/xfsearch/{$link|escape:'url'}">{$link}</a> {* https://github.com/fenom-template/fenom/blob/8ce6779119c098562d2bafba9167fbb4e2a222be/docs/ru/mods/escape.md *}
					{/set} 
				{/foreach}
				{* Выведем сформированные перекрестные ссылки через запятую, или через любой другой символ по вашему желаню *}
				{$links|join:', '} {* https://github.com/fenom-template/fenom/blob/8ce6779119c098562d2bafba9167fbb4e2a222be/docs/ru/mods/join.md *}
				{* Ну и не забываем удалить созданные переменные на случай, если в других новостях таковых нет *}
				{unset $links $arField}
			</div>
		{/if}

		<div class="box-header">Все картинки из полной новости</div>
		<div class="box box-hide">
			{* Установим картинку-заглушку *}
			{set $noimage}
				{$theme}/blockpro/noimage.png
			{/set}
			
			{* Собирём и изменим размер для всех картинок из полной новости, сложим в папку /uploads/myfolder/[размер картинки] и назначим массив в качестве переменной $arImages*}
			{set $arImages = $el.full_story|image:$noimage:'small':'all':'150':'85':'crop':true:true:'/uploads/myfolder/'}
			
			{* И дополнительно соберём оригинальные картинки из новости, для того, что бы подсунуть их в ссылку *}
			{set $arImagesOriginal = $el.full_story|image:$noimage:'original':'all':'':'':'':false:true:''}
			
			{* Пройдёмся в цикле по полученному массиву картинок *}
			{foreach $arImages as $key=>$image}
				{* Ссылка будет вести на оригинальную картинку, соответствующую уменьшенной *}
				<a href="{$arImagesOriginal[$key]}" rel="highslide" class="highslide"><img src="{$image}" alt=""></a>
			{/foreach}
		</div>
		<div class="box-header">Аналог {'{image-1}'}</div>
		<div class="box box-hide">
			{$el.full_story|image:$noimage:'intext':'1':'':'':'':false:true:''}
		</div>
		<div class="box-header">Уменьшенная первая картинка</div>
		<div class="box box-hide">
			<img src="{$el.full_story|image:$noimage:'small':'1':'':'':'':false:true:''}" alt="">
		</div>
		 
		<h3>
			{$el.favorites}[{$el.id}] <a href="{$el.url}">{$el.title}</a> {if $el.allow_edit} <a href="#" {$el.editOnclick}>[редактировать]</a> {/if}
		</h3>
		<img src="{$el.avatar}" alt="{$el.autor}" width="46">
		
		{* Формируем ссылку на автора новости с посмотром попап-профиля *}
		{set $urlUser = $el.autor|escape:'url'} {* https://github.com/fenom-template/fenom/blob/8ce6779119c098562d2bafba9167fbb4e2a222be/docs/ru/mods/escape.md *}
		<a href="/user/{$urlUser}" onclick="ShowProfile('{$urlUser}', '/user/{$urlUser}/', '1'); return false;">{$el.autor}</a>

		{$el.category|catinfo:'name'} | {$el.category|catinfo:'url'} | {$el.date|dateformat} | {$el.date|dateformat:"d F Y"}
		в новости: {$el.short_story|length} симв.
		<hr>
		<hr>
		<hr>

	{foreachelse}
		{*Если новостей нет - выведем информацию об этом*}
		<p>Новостей нет</p>
	{/foreach}

	{$pages}


	{* Скрипт для реализации спойлера *}
	{ignore} {* Используем тег {ignore} для вывода данных как есть *}
		<script>
			$(document).on('click', '.box-header', function(event) {
				event.preventDefault();
				var $this = $(this)
					$next = $this.next('.box'),
					className = 'box-hide';
				if ($next.stop().hasClass(className)) {
					$next.slideDown('300', function() {
						$next.removeClass(className);
					});
				} else {
					$next.stop().slideUp('300', function() {
						$next.addClass(className);
					});
				}

			});
		</script>
	{/ignore}

</div> <!-- #{$block_id} -->