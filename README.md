# Краткая информация о модуле
- **ВНИМАНИЕ!** Работа модуля в кодировке windows-1251 не гарантируется, не проверялась и проверяться когда-либо автором модуля не будет. Техническая поддержка кодировки windows-1251 оказывается с наименьшим приоритетом и только при наличии желания и времени. Это принципиальная позиция и меняться не будет. В общем рекомендую переходить на нормальную кодировку сайта :smile:
- На данный момент модуль находится в стадии бета тестирования.
- Краткая информация о шаблонных тега прописана в шаблоне **{THEME}/blockpro/blockpro.tpl**
- Более детальная информация по используемому шаблонизатору модуля находится в [документации по шаблонизатору](https://github.com/bzick/fenom/blob/master/docs/ru/readme.md)
- Все вопросы, баги и проблемы прошу писать в [посте о бетатестировании](http://pafnuty.name/main/154-blockpro4-beta.html), а если требуется предоставить доступ к закрытым данным (сайт, если он закрыт или нт желания светить, ftp и пр.), то пишем на [email](pafnuty10@gmail.com) или [в личку на сайте](http://pafnuty.name/index.php?do=pm&doaction=newpm&username=%CF%E0%F4%CD%F3%F2%E8%C9).
- Сообщаем о багах в следующем формате: 
    + Строка подключения.
    + Содержимое шаблона блока.
    + Наблюдаемый текст ошибки.
    + Данные о статистике блока (прописываем в строке подключения ``&showstat=y``)

# Зарезервированные шаблонные теги:

- ``{$block_id}`` - уникальный идентификатор блока, формируется из текущего конфига блока (не включая номер текущей страницы).
- ``{$theme}`` - аналог {THEME}
- ``{$list}`` - массив, с отобранными по параетрам строки подключения, новостями.
- ``{$member_group_id}`` - выводит id групы текущего пользователя.
- ``{$pages}`` - выводит постраничную навигацию.
- Теги, зарезервированные внутри цикла, где ``$el`` - элемент внутри цикла (можно менять на свой, по усмотреню):
    + ``{$el.favorites}`` - выводит favorites новости.
    + ``{$el.url}`` - сформированный url в соответствии с настройками ЧПУ.
    + ``{$el.showRating}`` - показывает рейтинг новости.
    + ``{$el.showRatingCount}`` - показывает счётчик рейтинга (обновляется при выставлении рейтинга), если нужно просто вывести счётчик - можно использовать ``{$el.rating_count}``.
    + ``{$el.editOnclick}`` - выводит атрибут onclick для фомирования "кнопки" редактирования. 
    + ``{$el.avatar}`` - выводит аватар пользователя, если в строке подключения указана переменная ``&avatar=y``
- Теги, которые формируются автоматически (выводят содержимое соответствующих даных из БД). 
    + ``{$el.id}``
    + ``{$el.autor}``
    + ``{$el.date}``
    + ``{$el.short_story}``
    + ``{$el.full_story}``
    + ``{$el.xfields}``
    + ``{$el.title}``
    + ``{$el.category}``
    + ``{$el.alt_name}``
    + ``{$el.allow_comm}``
    + ``{$el.comm_num}``
    + ``{$el.fixed}``
    + ``{$el.tags}``
    + ``{$el.news_read}``
    + ``{$el.allow_rate}``
    + ``{$el.rating}``
    + ``{$el.vote_num}``
    + ``{$el.votes}``
    + ``{$el.view_edit}``
    + ``{$el.editdate}``
    + ``{$el.editor}``
    + ``{$el.reason}``

- Модификаторы, используемые в шаблонах (можно применять к любым данным, но модификаторы не проверяют входящие данные, имейте ввиду):
    + [Список модификаторов по умолчанию](https://github.com/bzick/fenom/blob/master/docs/ru/syntax.md#%D0%9C%D0%BE%D0%B4%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%82%D0%BE%D1%80%D1%8B), используемых шаблонизатором.
    + ``{$foo|image}`` - выводит картинку новости, пример использования в шаблоне.
    + ``{$foo|limit:500}`` - ограничивает символы в тексте с учётом слов.
    + ``{$foo|catinfo}`` - выводит информацию о категориии новости.
    + ``{$foo|dateformat}`` - выводит отформатированную дату новости.
    + ``{$foo|declination}`` - выводит слово (без числа) с правильным склонением в зависимости от числа, к которому применяется.


# Установка Block.Pro.4
- Перекодировать файлы в windows-1251 при необходимости. 
- Залить содержимое папки upload в корень сайта. **ВНИМАНИЕ!** Папка с шаблонами модуля содержит шаблон blockpro.tpl, имя его совпадает с уже существующим, будьте осторожны.
- В css-файл добавить (стилизация навигации, можно доработать под себя):
~~~~ css
/* ==========================================================================
   Навигация blockpro */
/* ========================================================================== */

    .bp-pager:before,
    .bp-pager:after {
        content: " ";
        display: table;
    }
    .bp-pager:after {
        clear: both;
    }
    .bp-pager [data-page-num],
    .bp-pager .current {
        display: inline-block;
        color: #ffffff;
        margin-bottom: 0;
        font-weight: normal;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        background-image: none;
        background: #4a9fc5;
        border: 0;
        text-decoration: none;
        white-space: nowrap;
        padding: 10px 15px 8px;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-transition: all ease 0.3s;
        -moz-transition: all ease 0.3s;
        -o-transition: all ease 0.3s;
        transition: all ease 0.3s;
        -webkit-box-shadow: 0 2px 0 #3584a7;
        -moz-box-shadow: 0 2px 0 #3584a7;
        box-shadow: 0 2px 0 #3584a7;
        padding: 5px 8px 3px;
        font-size: 12px;
        line-height: 20px;
        border-radius: 3px;
        margin-bottom: 7px;
    }
    .bp-pager [data-page-num]:focus {
        outline: thin dotted #333;
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px;
    }
    .bp-pager [data-page-num]:hover,
    .bp-pager [data-page-num]:focus {
        color: #ffffff;
        background: #50bd98;
        text-decoration: none;
        -webkit-box-shadow: 0 2px 0 #3c9e7d;
        -moz-box-shadow: 0 2px 0 #3c9e7d;
        box-shadow: 0 2px 0 #3c9e7d;
    }
    .bp-pager [data-page-num]:active {
        outline: 0;
        -webkit-box-shadow: 0 2px 0 #50bd98;
        -moz-box-shadow: 0 2px 0 #50bd98;
        box-shadow: 0 2px 0 #50bd98;
    }

    .bp-pager .current {
        cursor: default;
        background: #c70000;
        -webkit-box-shadow: 0 2px 0 #940000;
        -moz-box-shadow: 0 2px 0 #940000;
        box-shadow: 0 2px 0 #940000;
    }
    /**
    * .base-loader - класс, добавляемый к блоку при аякс-загрузке
    */
    .base-loader {
        position: relative;
    }
    .base-loader:after {
        position: absolute;
        content: "";
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        background: rgba(255, 255, 255, 0.9) url(../blockpro/base-loader.gif) 50% 50% no-repeat;
        -webkit-transition: all ease 0.3s;
        -moz-transition: all ease 0.3s;
        -o-transition: all ease 0.3s;
        transition: all ease 0.3s;
    }

    /**
     * [data-favorite-id] - селектор favorites
     */

    [data-favorite-id] {
        cursor: pointer;
    }
~~~~

- В js-файл добавить (обработка навигации и favorites, можно поправить под себя):
~~~~ js
$(document)
    .on('click touchstart', '[data-page-num]', function (event) {
        event.preventDefault();
        console.log('ок');
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
~~~~