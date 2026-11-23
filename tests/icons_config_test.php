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

foreach ($checks as $name => $value) {
    echo $name . '=' . (is_bool($value) ? ($value ? 'OK' : 'FAIL') : $value) . "\n";
}
