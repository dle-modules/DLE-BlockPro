{set $get = $.request}
<ul class="topnavi">
    {foreach $list as $key => $el}
        <li>
        	{if $get.newsid == $el.id} Текущая новость {/if}
            <a href="{$el.url}" title="{$el.title}">{$el.title}</a>
        </li>
    {/foreach}
</ul> <!-- .topnavi -->