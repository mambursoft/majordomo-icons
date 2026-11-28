# Доступные иконки

## Базовый слой

`fontawesome-compat.css` сопоставляет старые классы Bootstrap 3 с глифами Font Awesome Free. Например:

| Класс MajorDoMo | Глиф |
|---|---|
| `glyphicon-home` | `house` |
| `glyphicon-cog` | `gear` |
| `glyphicon-plus` | `plus` |
| `glyphicon-pencil` | `pencil` |
| `glyphicon-trash` | `trash` |
| `glyphicon-log-in` | `right-to-bracket` |
| `glyphicon-log-out` | `right-from-bracket` |
| `glyphicon-user-plus` | `user-plus` |

Полный список соответствий — в самом CSS. Разметку шаблонов менять не требуется.

## Устройства

`fontawesome-devices.css` использует классы `device-icon`, которые реально встречаются в актуальных шаблонах ядра:

`temperature`, `thermostat`, `humidity`, `moisture`, `leak`, `light-sensor`, `motion`, `openclose`, `power-sensor`, `button`, `current-sensor`, `voltage-sensor`, `pressure`, `smoke`, `group_state`, `ac`, `camera`, `general-sensor`.

Старые имена `light`, `power`, `current`, `voltage`, `group`, `general` в шаблонах ядра больше не используются. Замена применяется только к иконкам **без** собственного `background-image` в атрибуте `style`: растровые спрайты с пользовательским изображением и partial-обновление виджета остаются нетронутыми. Состояния `on`/`off` имеет приоритет над типом устройства.

## Модули

Карта модулей задаётся функцией `icons_module_map()` в `lib/icons_config.php`. Для неизвестного модуля используется нейтральный глиф `house`. Список модулей берётся из `img/modules/*.png` и таблицы `project_modules`.

Чтобы добавить новый модуль, добавьте строку в карту:

```php
'my_module' => array('gear', 'f013', '#7f8c8d'),
```

где `gear` — имя глифа, `f013` — код, `#7f8c8d` — цвет.
