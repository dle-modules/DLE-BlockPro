/*!https://github.com/Mikhus/jsurl/blob/master/url.js*/
var Url=function(){"use strict";var t={protocol:"protocol",host:"hostname",port:"port",path:"pathname",query:"search",hash:"hash"},r={ftp:21,gopher:70,http:80,https:443,ws:80,wss:443},o=function(o,e){var a=document,i=a.createElement("a"),e=e||a.location.href,n=e.match(/\/\/(.*?)(?::(.*?))?@/)||[];i.href=e;for(var h in t)o[h]=i[t[h]]||"";if(o.protocol=o.protocol.replace(/:$/,""),o.query=o.query.replace(/^\?/,""),o.hash=o.hash.replace(/^#/,""),o.user=n[1]||"",o.pass=n[2]||"",o.port=r[o.protocol]==o.port||0==o.port?"":o.port,o.protocol||/^([a-z]+:)?\/\//.test(e))o.path=o.path.replace(/^\/?/,"/");else{var p=new Url(a.location.href.match(/(.*\/)/)[0]),c=p.path.split("/"),f=o.path.split("/");c.pop();for(var h=0,u=["protocol","user","pass","host","port"],l=u.length;l>h;h++)o[u[h]]=p[u[h]];for(;".."==f[0];)c.pop(),f.shift();o.path=("/"!=e.substring(0,1)?c.join("/"):"")+"/"+f.join("/")}s(o)},e=function(t){return t=t.replace(/\+/g," "),t=t.replace(/%([ef][0-9a-f])%([89ab][0-9a-f])%([89ab][0-9a-f])/gi,function(t,r,o,e){var s=parseInt(r,16)-224,a=parseInt(o,16)-128;if(0==s&&32>a)return t;var i=parseInt(e,16)-128,n=(s<<12)+(a<<6)+i;return n>65535?t:String.fromCharCode(n)}),t=t.replace(/%([cd][0-9a-f])%([89ab][0-9a-f])/gi,function(t,r,o){var e=parseInt(r,16)-192;if(2>e)return t;var s=parseInt(o,16)-128;return String.fromCharCode((e<<6)+s)}),t=t.replace(/%([0-7][0-9a-f])/gi,function(t,r){return String.fromCharCode(parseInt(r,16))})},s=function(t){var r=t.query;t.query=new function(t){for(var r,o=/([^=&]+)(=([^&]*))?/g;r=o.exec(t);){var s=decodeURIComponent(r[1].replace(/\+/g," ")),a=r[3]?e(r[3]):"";null!=this[s]?(this[s]instanceof Array||(this[s]=[this[s]]),this[s].push(a)):this[s]=a}this.clear=function(){for(s in this)this[s]instanceof Function||delete this[s]},this.toString=function(){var t="",r=encodeURIComponent;for(var o in this)if(!(this[o]instanceof Function))if(this[o]instanceof Array){var e=this[o].length;if(e)for(var s=0;e>s;s++)t+=t?"&":"",t+=r(o)+"="+r(this[o][s]);else t+=(t?"&":"")+r(o)+"="}else t+=t?"&":"",t+=r(o)+"="+r(this[o]);return t}}(r)};return function(t){this.toString=function(){return(this.protocol&&this.protocol+"://")+(this.user&&this.user+(this.pass&&":"+this.pass)+"@")+(this.host&&this.host)+(this.port&&":"+this.port)+(this.path&&this.path)+(this.query.toString()&&"?"+this.query)+(this.hash&&"#"+this.hash)},o(this,t)}}();

$(document)
	.on('click touchstart browserNavigation', '[data-page-num]', function (event) {
		var $this = $(this),
			blockId = $this.parent().data('blockId'),
			pageNum = $this.data('pageNum'),
			$block = $('#' + blockId),
			u = new Url,
			url;

		u.query.bid = blockId;
		u.query.p = pageNum;
		url = u;

		base_loader(blockId, 'start');

		$.ajax({
				url: dle_root + 'engine/ajax/blockpro.php',
				dataType: 'html',
				data: {
					pageNum: pageNum,
					blockId: blockId
				},
			})
			.done(function (data) {
				$block.html($(data).html());
				if (history.pushState && event.type != 'browserNavigation') {
					window.history.pushState(null, null, url);
				}
			})
			.fail(function () {
				base_loader(blockId, 'stop');
				console.log("error");
			})
			.always(function () {
				base_loader(blockId, 'stop');
			});

	})
	.on('click touchstart', '[data-favorite-id]', function (event) {
		event.preventDefault();
		var $this = $(this),
			fav_id = $this.data('favoriteId'),
			action = $this.data('action');

		ShowLoading('');
		$.get(dle_root + "engine/ajax/favorites.php", {
			fav_id: fav_id,
			action: action,
			skin: dle_skin
		}, function (data) {
			HideLoading('');
			var $img = $(data),
				src = $img.prop('src'),
				title = $img.prop('title'),
				imgAction = (action == 'plus') ? 'minus' : 'plus',
				l = src.split(imgAction).length;
			if (l == 2) {
				$('[data-favorite-id=' + fav_id + ']')
					.prop({
						alt: title,
						title: title,
						src: src
					})
					.data({
						action: imgAction,
						favoriteId: fav_id
					});
			};
		});

	});

$(window).on('popstate', function (e) {
	browseTrigger();
});

jQuery(document).ready(function ($) {
	browseTrigger();
});

/**
 * Переход к нужной странице навигации блока модуля blockpro
 */
function browseTrigger() {
	var u = new Url(location.href);
	if (u.query.bid) {
		$('#' + u.query.bid).find('[data-page-num="' + u.query.p + '"]:first').trigger('browserNavigation');
	}
}

/**
 * Простейшая функция для реализации эффекта загрузки блока
 * Добавляет/удаляет заданный класс для заданного блока
 * вся работа по оформлению ложится на css
 *
 * @author ПафНутиЙ <pafnuty10@gmail.com>
 *
 * @param  string id        ID блока
 * @param  string method    start/stop
 * @param  string className Имя класса, добавляемого блоку
 */
function base_loader(id, method, className) {
	var $block = $('#' + id),
		cname = (className) ? className : 'base-loader';
	if (method == 'start') {
		$block.addClass(cname);
	};

	if (method == 'stop') {
		$block.removeClass(cname);
	};
}

/**
 * Функция выставления рейтинга в модуле blockpro
 *
 * @author ПафНутиЙ <pafnuty10@gmail.com>
 *
 * @param  integer rate Значение рейтинга
 * @param  integer id   ID новости
 *
 * @return string       Результат обработки рейтинга
 */
function base_rate(rate, id) {
	ShowLoading('');

	$.get(dle_root + "engine/ajax/rating.php", {
		go_rate: rate,
		news_id: id,
		skin: dle_skin
	}, function (data) {
		HideLoading('');
		if (data.success) {
			var rating = data.rating;

			rating = rating.replace(/&lt;/g, "<");
			rating = rating.replace(/&gt;/g, ">");
			rating = rating.replace(/&amp;/g, "&");

			$('[data-rating-layer="' + id + '"]').html(rating);
			$('[data-vote-num-id="' + id + '"]').html(data.votenum);

			$("#ratig-layer-" + id).html(rating);
			$("#vote-num-id-" + id).html(data.votenum);
		}

	}, "json");
};