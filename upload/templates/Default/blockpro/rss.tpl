<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
	<channel>
		{* Устанавливаем заголовок канала *}
		<title>{$dleConfig['home_title']}</title>
		{* Устанавливаем ссылку на канал *}
		<link>{$dleConfig['http_home_url']}</link>
		{* Определяем язык канала *}
		<language>ru</language>
		{* Устанавливаем описание канала *}
		<description>{$dleConfig['short_title']}</description>
		{* Служебная запись о генераторе канала *}
		<generator>BlockPro for DLE</generator>

		{* Пробегаем по массиву с новосями *}
		{foreach $list as $el}		
			<item>
				{* Опреедеяем заголовок элемента *}
				<title>{$el.title}</title>
				{* Определяем ссылки на элемент *}
				<guid isPermaLink="true">
					{$el.url}
				</guid>
				<link>
					{$el.url}
				</link>
				{* Описание элемента *}
				<description>
					<![CDATA[ 
						{$el.short_story}
					]]>
				</description>
				{* Категория *}
				{* Т.к. категория не может содержать пустое значение
				вынесем определение имени категории в переменную *}
				{set $catName = $el.category|catinfo:'name'} {* https://github.com/fenom-template/fenom/blob/master/docs/ru/tags/set.md#set *}
				{if $catName}
					{* Если переменная определена (есть категория у новости) — выведем эту категорию *}
					{set $showCatName = $catName}
				{else}
					{* Если категории у новости нет — выведем три черточки :) *}
					{set $showCatName = '---'}						
				{/if}
				<category>
					<![CDATA[{$showCatName}]]>
				</category>
				{* Выводим автора новости *}
				<dc:creator>{$el.autor}</dc:creator>
				{* Выводим дату публикации (лучше не менять этот формат т.к. будут ошибки валидации) *}
				<pubDate>{$el.date|date:"r"}</pubDate> {* Модификатор date — недокументированный модификатор шаблонизатора, но тем не менее его использование разрешено *}
			</item>
		{/foreach}
	</channel>
</rss>