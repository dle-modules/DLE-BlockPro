# Описание ошибки:

- При каких условиях воспроизводится ошибка
- Страница, на которой наблюдается ошибка
- Вносились ли модификации в движок
- Версия DLE: 10.3
- Версия PHP: 5.6


## Строка подключения с включенным параметром showstat:

```php
{include file="engine/modules/base/blockpro.php?showstat=y"}
```

## Статистика генерации:
_Содержимое блока в красной рамке в конце блока с новостями_

```sql
Запрос(ы): 
[1] SELECT p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, p.allow_comm, p.comm_num, p.fixed, p.allow_main, p.symbol, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.related_ids, e.view_edit, e.editdate, e.editor, e.reason FROM `dle_post` p LEFT JOIN `dle_post_extras` e ON (p.id=e.news_id) WHERE approve AND p.date < "2017-09-20 21:57:16" ORDER BY fixed DESC, e.rating DESC, p.comm_num DESC, e.news_read DESC LIMIT 0, 10 
[1 время:] 0.0032839775085449
Время выполнения запросов: 0.0032839775085449
Время выполнения скрипта: 1505933836.7845 c.
Расход памяти: 2.97Мб
```

## Код используемого шаблона:

```smarty
<div id="{$block_id}">
    {foreach $list as $el}
        {$el.id}
    {/foreach}
</div> <!-- #{$block_id} -->
```