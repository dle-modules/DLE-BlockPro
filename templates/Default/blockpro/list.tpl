{*
	$block_id - это уникальный идентификатор блока (у блоков с разными настройками он разный) для того, что бы правильно организовать постраничную навигацию на ajax.
*}
<div id="{$block_id}">

	{$pages}

	{*
		Тут мы определяем количество новостей в блоке
		$list - переменная, содержащая массив с новостями.
	*}
	{var $newsCount = $list|length}
	В блоке {$newsCount} {$newsCount|declination:'новост|ь|и|ей'}
	{*Пробегаем по массиву с новостями*}
	{foreach $list as $key => $el}
		<div class="content content-border-bottom">
			<div class="col col-mb-12">
				рейтинг: {$el.showRating} {$el.showRatingCount}
				<h3>
					{$el.favorites}[{$el.id}] <a href="{$el.url}">{$el.title}</a> {if $el.allow_edit} <a href="#" {$el.editOnclick}>[редактировать]</a> {/if}
				</h3>
				<img src="{$el.avatar}" alt="{$el.name}" width="46">
				{$el.category|catinfo:'name'} | {$el.category|catinfo:'url'} | {$el.date|dateformat} | {$el.date|dateformat:"d F Y"}
				в новости: {$el.short_story|length} симв.
			</div>
		</div>
		{if $key == 1}
			<div class="content content-border-bottom">
				<div class="col col-mb-12">
					<h4>Выводим навигацию между новостями</h4>
					{$pages}
				</div>
			</div>
		{/if}
	{foreachelse}
		{*Если новостей нет - выведем информацию об этом*}
		<p>Новостей нет</p>
	{/foreach}

</div> <!-- #{$block_id} -->

