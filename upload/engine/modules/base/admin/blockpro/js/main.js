/*!
=============================================================================
BlockPro
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/

var doc = $(document);


doc
	.on('click', '.popup-modal-dismiss', function (e) {
		e.preventDefault();
		$.magnificPopup.close();
	})
	.on('click', '.modal-lose', function () {
		$.magnificPopup.close();
	})
	.on('click', '.code', function() {
		$(this).select();
	})
	.on('click touchstart', '[data-page-num]', function (event) {

		var $this = $(this),
			blockId = $this.parent().data('blockId'),
			pageNum = $this.data('pageNum'),
			$block = $('#' + blockId);

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
				console.log(data);
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

		$.get(dle_root + "engine/ajax/favorites.php", {
			fav_id: fav_id,
			action: action,
			skin: dle_skin
		}, function (data) {
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

	})
	.on('keyup input', '[data-defimage]', function(event) {
		event.preventDefault();
		var variable = ($('[data-id="img_defImage"]').val()) ? $('[data-id="img_defImage"]').val() : '$noimage',
			noimage = ($('[data-id="img_noimage"]').val()) ? $('[data-id="img_noimage"]').val() : '/images/noimage.png',
			text = '{*Определение картинки-заглушки*}\n'+
			'{var '+variable+'}\n'+
			'\t{$theme}'+noimage+'\n'+
			'{/var}';

		$('#img_code').val(text);

	})
	.on('change', '[data-img-type]', function(event) {
		event.preventDefault();
		var $other = $('[data-setimage]:not([data-defimage]):not([data-img-count])');
		if ($(this).val() == 'original') {
			$other.prop('disabled', true);
		} else {
			$other.prop('disabled', false);
		}
	})
	.on('keyup input change', '[data-setimage]', function(event) {
		event.preventDefault();
		var $arr = ($('[data-setimage]')),
			prefix = '{$el.short_story|image:',
			suffix = '}',
			returnArr = [],
			text;

		$.each($arr, function(index, val) {
			if (!$(this).prop('disabled')) {
				if ($(this).hasClass('checkbox')) {
					if ($(this).is(':checked')) {
						returnArr.push('true');
					} else {
						returnArr.push('false');
					}
				} else if ($(this).data('id') == 'img_defImage') {
					returnArr.push($(this).val());
				} else {
					returnArr.push('\''+$(this).val()+'\'');
				}
			}
			if ($(this).prop('disabled') && $(this).val() == 'original') {
				returnArr.push('\''+$(this).val()+'\'');
			};
		});
		text = returnArr.join(':');

		$('#img_code2').val(prefix + text + suffix);
	})
	.on('keyup input change', '[data-settext]', function(event) {
		event.preventDefault();
		var $arr = ($('[data-settext]')),
			prefix = '{$el.short_story|limit:',
			suffix = '}',
			returnArr = [],
			text;

		$.each($arr, function(index, val) {
			if ($(this).hasClass('checkbox')) {
				if ($(this).is(':checked')) {
					returnArr.push('true');
				}
			} else {
				returnArr.push('\''+$(this).val()+'\'');
			}
		});
		text = returnArr.join(':');

		$('#txt_code').val(prefix + text + suffix);
	})
	.on('change', '[data-setcatinfo]', function(event) {
		event.preventDefault();
		var catinfo = $(this).val(),
			noimage_w_icon = ($('[data-id="img_defImage"]').val()) ? ':\'icon\':' + $('[data-id="img_defImage"]').val() : ':\'icon\':$noimage',
			prefix = '{$el.category|catinfo',
			suffix = '}',
			text;

		console.log(catinfo);

		if (catinfo && catinfo != 'icon_noimage') {
			text = ':\'' + catinfo + '\'';
		} else if (catinfo == 'icon_noimage') {
			text = noimage_w_icon;

		} else {
			text = '';
		}

		$('#cat_code').val(prefix + text + suffix);
	})




jQuery(document).ready(function ($) {
	// Авторазмер для блоков с кодом
	$('.code').autosize();

	// Селекты

	var $select = $('.styler').selectize();

	// Табы с настройками
	$('#tab').easyResponsiveTabs();

	// Дефолтные настройки magnificpopup
	$.extend(true, $.magnificPopup.defaults, {
		tClose: 'Закрыть (Esc)', // Alt text on close button
		tLoading: 'Загрузка...', // Text that is displayed during loading. Can contain %curr% and %total% keys
		ajax: {
			tError: '<a href="%url%">Контент</a> не загружен.' // Error message when ajax request failed
		}
	});

	$('.mfp-open').magnificPopup();

	$('.mfp-open-ajax').magnificPopup({
		type: 'ajax'
	});

	// Инициализация Ladda
	var laddaLoad = $('.ladda-button').ladda();

	// Дефолтные настройки аякс формы
	var formOptions = {
		// dataType: 'json',
		beforeSubmit: processStart,
		success: processDone
	};

	$('#ajaxForm').ajaxForm(formOptions);


	/**
	 * [processStart description]
	 * @return {[type]} [description]
	 */
	function processStart() {
		laddaLoad.ladda('start')
	};

	/**
	 * [processDone description]
	 * @param  {[type]} data [description]
	 * @return {[type]}      [description]
	 */
	function processDone(data) {
		var progress = 0;
		var interval = setInterval(function () {
			progress = Math.min(progress + Math.random() * 0.2, 1);
			laddaLoad.ladda('setProgress', progress);

			if (progress === 1) {
				laddaLoad.ladda('stop');
				clearInterval(interval);
			}
		}, 100);

	};


});


/**
 * Простейшая функция для реализации эффекта загрузки блока
 * Добавляет/удаляет заданный класс для заданного блока
 * вся работа по оформлению ложится на css
 *
 * @author ПафНутиЙ <pafnuty10@gmail.com>
 *
 * @param  {str} id        ID блока
 * @param  {str} method    start/stop
 * @param  {str} className Имя класса, добавляемого блоку
 */
function base_loader (id, method, className) {
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
 * @param  {int} rate Значение рейтинга
 * @param  {int} id   ID новости
 *
 * @return {str}      Результат обработки рейтинга
 */
function base_rate(rate, id) {

	$.get(dle_root + "engine/ajax/rating.php", {
		go_rate: rate,
		news_id: id,
		skin: dle_skin
	}, function (data) {
		if (data.success) {
			var rating = data.rating;

			rating = rating.replace(/&lt;/g, "<");
			rating = rating.replace(/&gt;/g, ">");
			rating = rating.replace(/&amp;/g, "&");

			$('[data-rating-layer="'+id+'"]').html(rating);
			$('[data-vote-num-id="'+id+'"]').html(data.votenum);

			$("#ratig-layer-" + id).html(rating);
			$("#vote-num-id-" + id).html(data.votenum);
		}

	}, "json");
};


