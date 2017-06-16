{*
	Наследуемся от общего шаблона
	https://github.com/fenom-template/fenom/blob/master/docs/ru/tags/extends.md
*}
{extends '/blockpro/template_wrapper.tpl'}

{*
	Определяем блок, 'content' контент которого будет передан в наследуемый шаблон
*}
{block 'content'}
    {*Пробегаем по массиву с новостями*}
    {foreach $list as $el}
        <p>
            <a href="{$el.url}">{$el.title} | {$el.category}</a>
        </p>
    {foreachelse}
        {*Если новостей нет - выведем информацию об этом*}
        <p>Новостей нет</p>
    {/foreach}
{/block}

{*
    Раскомментируйте код, что бы увидеть результат:

    {block 'header'}
        <p>
            Порядок следования блоков в дочернем шаблоне не играет роли
        </p>
    {/block}
*}

