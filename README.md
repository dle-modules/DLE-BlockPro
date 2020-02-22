# DLE-BlockPro — тот самый модуль для вывода новостей!
![version](https://img.shields.io/badge/version-6.0.2-red.svg?style=flat-square "Version")
![DLE](https://img.shields.io/badge/DLE-13.x-green.svg?style=flat-square "DLE Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/dle-modules/DLE-BlockPro/blob/master/LICENSE)

- Краткая информация о шаблонных тега прописана в шаблоне **{THEME}/blockpro/blockpro.tpl**, расширенный пример **{THEME}/blockpro/fullexample.tpl**
- Более детальная информация по используемому шаблонизатору модуля находится в [документации по шаблонизатору](https://github.com/bzick/fenom/blob/master/docs/ru/readme.md)
- Официальный сайт: [bp.pafnuty.name](http://bp.pafnuty.name/)
- [Техническая поддержка](https://github.com/dle-modules/DLE-BlockPro/issues)
- [История изменений](https://github.com/dle-modules/DLE-BlockPro/blob/master/CHANGELOG.md)

## Установка модуля

1. Устанавливаем как обычный плагин, файл **blockpro_plugin.zip** содержит всё необходимое для автоматической установки.
2. Открыть файл `/templates/Default/main.tpl`
3. Добавить после `{AJAX}` или после `{jsfiles}`:
`<link href="{THEME}/blockpro/css/blockpro.css" rel="stylesheet" />`
4. Добавить после `{AJAX}` или после `{jsfiles}`:
`<script src="{THEME}/blockpro/js/blockpro.js"></script>`

