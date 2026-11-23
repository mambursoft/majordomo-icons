# Локальный слой иконок для MajorDoMo

Бесплатный Font Awesome Free 7.3.1 без внешних запросов (требование автономной
работы в DMZ, см. sergejey/majordomo#1321 и #1322).

![Результат замены иконок](screenshots/icons-demo.png)

## Что внутри

- `src/css/fontawesome-compat.css` — совместимость с существующими классами
  Bootstrap 3 `glyphicon-*`; разметку шаблонов менять не нужно;
- `src/css/fontawesome-devices.css` — цветные шрифтовые иконки для «Простых
  устройств» (`device-icon.temperature`, `.humidity`, `.light` и др.);
- `src/css/fontawesome-module-icons.css` — база для замены растровых иконок
  модулей (`img/modules/*.png`);
- `src/css/fontawesome-admin-icons.css` — замена старых служебных `ico_*.gif`;
- `src/3rdparty/fontawesome/` — CSS и четыре локальных woff2-шрифта;
- `template-icons.patch` — примеры добавления пиктограмм там, где они
  отсутствовали (вход, пользователи, кнопки управления, Connect);
- интерфейс `?action=icons` — включение нужных групп и выбор модулей списком.

## Управление

![Интерфейс управления](screenshots/icons-settings.png)

1. Скопируйте `src/lib/icons_config.php` в `lib/`, `src/modules/icons/` в
   `modules/icons/`, `src/templates/icons/` в `templates/icons/`.
2. Откройте `?action=icons` и отметьте, что переопределять:
   - базовые `glyphicon` ядра и шаблонов;
   - иконки простых устройств;
   - иконки модулей — можно выбрать конкретные модули из списка;
   - служебные `ico_*.gif` админки.
3. Нажмите «Сохранить и пересоздать CSS» — будет создан файл
   `cms/icons/icons-enabled.css`, который содержит только выбранные стили.
4. Подключите этот файл в `common_header.html` после Bootstrap CSS. Для групп
   «модули», «устройства» и «админка» добавьте соответствующий класс
   (`fa-module-icons`, `fa-devices`, `fa-admin-icons`) на контейнер меню/страницы.

## Установка базового слоя совместимости

1. Скопируйте `src/css/fontawesome-compat.css` в `css/`.
2. Скопируйте `src/3rdparty/fontawesome/` в `3rdparty/fontawesome/`.
3. Подключите `css/fontawesome-compat.css` после Bootstrap CSS на всех страницах,
   где используются `glyphicon-*`.

## Проверка

`php tests/icons_config_test.php` — проверяет, что сгенерированный CSS содержит
только включённые группы и выбранные модули.

Лицензия Font Awesome Free: CC BY 4.0 (иконки), SIL OFL 1.1 (шрифты), MIT (код).
