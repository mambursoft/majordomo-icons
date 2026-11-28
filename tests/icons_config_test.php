<?php
// Smoke-тест генерации CSS. Запуск: php tests/icons_config_test.php
$root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'majordomo-icons-test' . DIRECTORY_SEPARATOR;
define('ROOT', $root);
if (!is_dir($root . 'img/modules')) {
    mkdir($root . 'img/modules', 0755, true);
}
foreach (array('devices.png', 'settings.png', 'scenes.png') as $file) {
    file_put_contents($root . 'img/modules/' . $file, 'x');
}
include dirname(__FILE__) . '/../src/lib/icons_config.php';

$config = icons_config();
$config['groups']['core'] = 1;
$config['groups']['modules'] = 1;
$config['modules'] = array('devices', 'settings');
$config = icons_save_config($config);
$path = icons_build_css($config);
$css = file_get_contents($path);

$checks = array();
$checks['core_import'] = strpos($css, 'fontawesome-compat.css') !== false;
$checks['module_import'] = strpos($css, 'fontawesome-module-icons.css') !== false;
$checks['devices_rule'] = strpos($css, 'action=devices') !== false;
$checks['settings_rule'] = strpos($css, 'action=settings') !== false;
$checks['scenes_absent'] = strpos($css, 'action=scenes') === false;
$checks['module_count'] = count(icons_available_modules());

// Современное ядро подключает compat CSS самостоятельно: @import дублировать не нужно.
$coreCss = $root . 'css' . DIRECTORY_SEPARATOR . 'fontawesome-compat.css';
$coreTpl = $root . 'templates' . DIRECTORY_SEPARATOR;
if (!is_dir(dirname($coreCss))) {
    mkdir(dirname($coreCss), 0755, true);
}
if (!is_dir($coreTpl)) {
    mkdir($coreTpl, 0755, true);
}
file_put_contents($coreCss, '/* core copy */');
file_put_contents($coreTpl . 'common_header.html', '<link href="css/fontawesome-compat.css" rel="stylesheet">');
$cssModern = file_get_contents(icons_build_css($config));
$checks['core_compat_no_duplicate'] = strpos($cssModern, 'fontawesome-compat.css') === false;
unlink($coreCss);
unlink($coreTpl . 'common_header.html');
$cssLegacy = file_get_contents(icons_build_css($config));
$checks['legacy_compat_import'] = strpos($cssLegacy, 'fontawesome-compat.css') !== false;

// Совместимость с ядром: только mapping-классы, без catch-all для всех glyphicon.
$compat = file_get_contents(dirname(__FILE__) . '/../src/css/fontawesome-compat.css');
$checks['compat_mapped_only'] = strpos($compat, '.glyphicon::before { content') === false
    && strpos($compat, '.glyphicon,' . "\n.glyphicon::before") === false;
$checks['compat_important'] = preg_match_all('/\.glyphicon-[a-z-]+::before \{ content: "[^"]+" !important; \}/', $compat) >= 65;
$checks['compat_new_mappings'] = strpos($compat, 'glyphicon-bell') !== false
    && strpos($compat, 'glyphicon-print') !== false
    && strpos($compat, 'glyphicon-user-plus') !== false;

// Иконки устройств: классы актуальных шаблонов и сохранение inline-картинок.
$devices = file_get_contents(dirname(__FILE__) . '/../src/css/fontawesome-devices.css');
$checks['devices_real_classes'] = strpos($devices, '.device-icon.light-sensor') !== false
    && strpos($devices, '.device-icon.power-sensor') !== false
    && strpos($devices, '.device-icon.group_state') !== false;
$checks['devices_preserve_inline'] = strpos($devices, ':not([style*="background-image"])') !== false;

foreach ($checks as $name => $value) {
    echo $name . '=' . (is_bool($value) ? ($value ? 'OK' : 'FAIL') : $value) . "\n";
}
