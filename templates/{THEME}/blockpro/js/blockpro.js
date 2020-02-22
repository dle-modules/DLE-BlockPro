var thisUrl = window.location.pathname;

$(document)
	.on('click touchstart', '[data-page-num]', function () {
		var $this   = $(this),
		    blockId = $this.parent().data('blockId'),
		    pageNum = $this.data('pageNum'),
		    $block  = $('#' + blockId);

		base_loader(blockId, 'start');

		$.ajax({
			url: dle_root + 'engine/ajax/controller.php',
			dataType: 'html',
			data: {
				mod: 'blockpro',
				pageNum: pageNum,
				blockId: blockId,
				thisUrl: thisUrl
			}
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
		var $this  = $(this),
		    fav_id = $this.data('favoriteId'),
		    action = $this.data('action');

		ShowLoading('');
		$.get(dle_root + 'engine/ajax/controller.php', {
			fav_id: fav_id,
			action: action,
			skin: dle_skin,
			mod: 'favorites',
			alert: 1,
			user_hash: dle_login_hash || ''
		}, function (data) {
			HideLoading('');
			DLEalert(data, dle_info);
		});

	});

/**
 * Простейшая функция для реализации эффекта загрузки блока
 * Добавляет/удаляет заданный класс для заданного блока
 * вся работа по оформлению ложится на css
 *
 * @author ПафНутиЙ <pafnuty10@gmail.com>
 *
 * @param id
 * @param method
 * @param className
 */
function base_loader(id, method, className) {
	var $block = $('#' + id),
	    cname  = (className) ? className : 'base-loader';
	if (method === 'start') {
		$block.addClass(cname);
	}

	if (method === 'stop') {
		$block.removeClass(cname);
	}
}

/**
 * Выставление рейтинга
 * @see base_rate
 * @param rate
 * @param id
 */
function base_rate(rate, id) {
	ShowLoading('');
	$.get(dle_root + 'engine/ajax/controller.php?mod=rating', {
		go_rate: rate,
		news_id: id,
		skin: dle_skin,
		user_hash: dle_login_hash || ''
	}, function(data){

		HideLoading('');

		if (data.success) {
			var rating = data.rating;

			rating = rating.replace(/&lt;/g, '<');
			rating = rating.replace(/&gt;/g, '>');
			rating = rating.replace(/&amp;/g, '&');

			$('[data-rating-layer="' + id + '"]').html(rating);
			$('[data-vote-num-id="' + id + '"]').html(data.votenum);

			$('#ratig-layer-' + id).html(rating);
			$('#vote-num-id-' + id).html(data.votenum);

			$('#likes-id-' + id).html(data.likes);
			$('#dislikes-id-' + id).html(data.dislikes);

		} else if (data.error) {
			DLEalert(data.errorinfo, dle_info);
		}

	}, "json");
}