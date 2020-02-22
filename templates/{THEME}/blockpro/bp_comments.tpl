{* 
	Условие, которое отобразит информацию о корректном вызове этого шаблона, если такое не произошло.
	Например когда в строке подключения указан этот шаблон
*}
{if $postId > 0}
	
	{* 
		Определяем сколько комментариев получить.
		Если передан параметр limit и он больше нуля, позьмём его
		Если нет - поставим 10.

	*}
	{set $commentsLimit = ($limit > 0) ? $limit*1 : 10 }

	{*
		Получаем комментарии прямым запросом в БД, через класс SafeMySQL
		http://phpfaq.ru/safemysql
	*}

	{set $comments = $.blockPro->db->getAll('SELECT * FROM ?n WHERE post_id=?i LIMIT 0, ?p', 'dle_comments', $postId, $commentsLimit)}

	{* Пробегаем по полученным комментариям *}
	{foreach $comments as $comment}
		<p>
			Автор: <b>{$comment.autor}</b> | {$comment.date|dateformat}
		</p>
		<p>
	        {$comment.text}
		</p>
	    {foreachelse}
		<p>Комментариев у новости нет</p>
	{/foreach}
{else}
 <div class="alert">
 	Этот шаблон следует подключать внутри цикла, используя такую конструкцию:
	<pre>{ignore}{include '/blockpro/bp_comments.tpl' postId=$el.id limit=5}{/ignore}</pre>
 </div>		
{/if}
