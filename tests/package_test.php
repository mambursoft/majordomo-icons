<?php
/**
 * Package smoke test: layout, metadata, languages and archive contents.
 * Run from the package root: php tests/package_test.php
 */

$root = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$fail = 0;

function icons_test($name, $ok, $detail = '')
{
    global $fail;
    if (!$ok) {
        $fail++;
    }
    echo $name . '=' . ($ok ? 'OK' : 'FAIL') . ($detail !== '' ? ' (' . $detail . ')' : '') . "\n";
}

$required = array(
    'install.php',
    'modinfo.json',
    'module.xml',
    'README.md',
    'CHANGELOG.md',
    'LICENSE.md',
    'build_package.ps1',
    'template-icons.patch',
    'src/css/fontawesome-compat.css',
    'src/css/fontawesome-devices.css',
    'src/css/fontawesome-module-icons.css',
    'src/css/fontawesome-admin-icons.css',
    'src/3rdparty/fontawesome/css/all.min.css',
    'src/3rdparty/fontawesome/webfonts/fa-solid-900.woff2',
    'src/3rdparty/fontawesome/webfonts/fa-regular-400.woff2',
    'src/3rdparty/fontawesome/webfonts/fa-brands-400.woff2',
    'src/3rdparty/fontawesome/webfonts/fa-v4compatibility.woff2',
    'src/lib/icons_config.php',
    'src/lib/icons_lang.php',
    'src/modules/icons/icons.class.php',
    'src/templates/icons/icons.html',
    'languages/icons_default.php',
    'languages/icons_en.php',
    'languages/icons_ru.php',
    'languages/icons_ua.php',
    'languages/icons_uk.php',
    'docs/INSTALL.ru.md',
    'docs/CONFIG.ru.md',
    'docs/ICONS.ru.md',
    'docs/TROUBLESHOOTING.ru.md',
);
$missing = array();
foreach ($required as $file) {
    if (!is_file($root . $file)) {
        $missing[] = $file;
    }
}
icons_test('files', count($missing) === 0, implode(', ', $missing));

$info = json_decode(file_get_contents($root . 'modinfo.json'), true);
icons_test('modinfo', is_array($info) && isset($info['version'], $info['module_name'], $info['min_php']));

$xml = @simplexml_load_file($root . 'module.xml');
icons_test('module_xml', $xml !== false && isset($xml->version));

foreach (array('default', 'en', 'ru', 'ua', 'uk') as $lang) {
    $ICONS_STRINGS = array();
    include $root . 'languages/icons_' . $lang . '.php';
    icons_test('language_' . $lang, isset($ICONS_STRINGS['ICONS_TITLE']));
}

$archive = $root . 'dist' . DIRECTORY_SEPARATOR . 'icons.tgz';
if (is_file($archive)) {
    $entries = array();
    $output = array();
    $code = 0;
    @exec('tar -tzf ' . escapeshellarg($archive), $output, $code);
    if ($code === 0) {
        foreach ($output as $line) {
            $entries[] = trim($line, './');
        }
    }
    foreach (array('install.php', 'modules/icons/icons.class.php', 'templates/icons/icons.html', 'languages/icons_uk.php', 'css/fontawesome-compat.css') as $needle) {
        icons_test('archive:' . $needle, in_array($needle, $entries, true));
    }
} else {
    icons_test('archive', false, 'dist/icons.tgz не собран');
}

echo $fail === 0 ? "ALL OK\n" : "FAILED: $fail\n";
exit($fail === 0 ? 0 : 1);
