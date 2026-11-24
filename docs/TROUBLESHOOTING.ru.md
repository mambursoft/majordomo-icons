# Устранение неполадок

## Иконки не меняются

1. Проверьте, что `cms/icons/icons-enabled.css` существует и не пустой.
2. Убедитесь, что в `templates/common_header.html` есть строка с маркером `majordomo-icons:enabled-css`.
3. Очистите кэш браузера (Ctrl+F5).

## 404 на webfonts

Проверьте путь `3rdparty/fontawesome/webfonts/` относительно корня MajorDoMo. В архиве должны быть четыре файла woff2. Если вы копировали только `css/`, скопируйте весь каталог `3rdparty/fontawesome/`.

## Страница `?action=icons` пустая

Модуль не зарегистрирован. Выполните `php install.php` или добавьте запись в `project_modules` вручную:

```sql
INSERT INTO project_modules (NAME, TITLE, CATEGORY)
VALUES ('icons', 'Icons', 'System');
```

## Ошибка после удаления модуля

`icons::uninstall()` удаляет только строку подключения в `common_header.html` и файлы в `cms/icons/`. CSS и шрифты в корне остаются намеренно: если какие-то шаблоны всё ещё ссылаются на `glyphicon-*`, система продолжит работать. Удалите `css/fontawesome-*.css` и `3rdparty/fontawesome/` вручную, когда убедитесь, что они больше не нужны.
