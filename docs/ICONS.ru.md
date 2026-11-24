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

`fontawesome-devices.css` использует модификаторы `device-icon`:

`temperature`, `thermostat`, `humidity`, `moisture`, `leak`, `light`, `motion`, `openclose`, `power`, `button`, `current`, `voltage`, `pressure`, `smoke`, `group`, `ac`, `camera`, `general`.

## Модули

Карта модулей задаётся функцией `icons_module_map()` в `lib/icons_config.php`. Для неизвестного модуля используется нейтральный глиф `house`. Список модулей берётся из `img/modules/*.png` и таблицы `project_modules`.

Чтобы добавить новый модуль, добавьте строку в карту:

```php
'my_module' => array('gear', 'f013', '#7f8c8d'),
```

где `gear` — имя глифа, `f013` — код, `#7f8c8d` — цвет.
