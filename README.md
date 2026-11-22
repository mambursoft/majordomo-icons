# Локальный слой иконок для MajorDoMo

Бесплатный Font Awesome Free 7.3.1 без внешних запросов (требование автономной
работы в DMZ, см. sergejey/majordomo#1321 и #1322).

## Что внутри

- `src/css/fontawesome-compat.css` — совместимость с существующими классами
  Bootstrap 3 `glyphicon-*`; разметку шаблонов менять не нужно;
- `src/3rdparty/fontawesome/` — CSS и четыре локальных woff2-шрифта;
- `template-icons.patch` — примеры добавления пиктограмм там, где они
  отсутствовали (вход, пользователи, кнопки управления, Connect).

## Установка

1. Скопируйте `src/css/fontawesome-compat.css` в `css/`.
2. Скопируйте `src/3rdparty/fontawesome/` в `3rdparty/fontawesome/`.
3. Подключите `css/fontawesome-compat.css` после Bootstrap CSS на всех страницах,
   где используются `glyphicon-*`.

Лицензия Font Awesome Free: CC BY 4.0 (иконки), SIL OFL 1.1 (шрифты), MIT (код).
