{filter|strip|replace:["\n","\t"]:""|replace:"> <":"><"|strip} 

{var $noimage}{$theme}/blockpro/noimage.png{/var}
<!DOCTYPE html>
<html>
<head>
	<style>
		.{$block_id}-block {
			padding: 0 20px;
			margin: 20px 0;
			background: #fff;
			border: solid 1px #ccc;
		}
		.{$block_id}-element {
			border-bottom: solid 1px #ccc;
		}
		.{$block_id}-element:last-child {
			border-bottom: 0;
		}
		.{$block_id}-clearfix:before, 
		.{$block_id}-clearfix:after {
			content: "";
			display: table;
		}
		.{$block_id}-clearfix:after {
			clear: both;
		}
		.{$block_id}-clearfix {
			*zoom: 1;
		}
		.{$block_id}-header {
			margin-top: 20px;
			margin-bottom: 10px;
		}
		.{$block_id}-header-text {
			font: normal 16px/1.4 Arial, sana-serif;
			color: #4a9fc5;
			float: left;
		}
		.{$block_id}-header-text a {
			text-decoration: none;
			color: #4a9fc5;
		}
		.{$block_id}-header-text a:hover {
			color: #c70000;
			text-decoration: underline;
		}
		.{$block_id}-text {
			padding-bottom: 20px;
		}
		.{$block_id}-date {
			color: rgba(0,0,0,.8);
			text-align: right;
			font-style: italic;
			float: right;
		}
		.{$block_id}-image {
			float: left;
			margin: 0 10px 20px 0;
		}
	</style>
</head>
<body>
	<div class="{$block_id}-block">
		{foreach $list as $el}	
			{* Не забываем прописать те же префиксы перед именами классов *}
			<div class="{$block_id}-element">
				<div class="{$block_id}-header {$block_id}-clearfix">
					<div class="{$block_id}-header-text">
						<a href="{$el.url}">{$el.title}</a>
					</div>
					<div class="{$block_id}-date">
						{$el.date|dateformat:"d F Y"}
					</div>
				</div>
				<div class="{$block_id}-clearfix">
					<div class="{$block_id}-image">
						<img src="{$el.short_story|image:$noimage:'small':'1':'200':'90':'':true:false}" alt="">
					</div>
					<div class="{$block_id}-text">
						{$el.short_story|limit:"140"}
					</div>
				</div>
			</div>		
		{/foreach}
	</div>
</body>
</html>
{/filter}