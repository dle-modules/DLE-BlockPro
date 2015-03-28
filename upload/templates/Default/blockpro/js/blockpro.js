$(document)
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
		console.log(data);
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