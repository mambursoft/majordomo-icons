<?php
/**
 * Installation of the MajorDoMo icons package.
 *
 * The Market includes this file from the unpacked archive BEFORE copying the
 * files to the MajorDoMo root, so every path below is relative to __DIR__.
 * The module class is loaded from the staged directory to register it in the
 * database; the Market copies the remaining files afterwards.
 */

if (!defined('ROOT')) {
    return;
}

$iconsStagedModule = __DIR__ . '/modules/icons/icons.class.php';
if (is_file($iconsStagedModule)) {
    if (!class_exists('module', false)) {
        include_once(ROOT . 'lib/module.class.php');
    }
    include_once(__DIR__ . '/lib/icons_config.php');
    include_once(__DIR__ . '/lib/icons_lang.php');

    if (class_exists('module') && !class_exists('icons', false)) {
        include_once($iconsStagedModule);
        $iconsInstaller = new icons();
    }

    // Register the module so the settings page appears in the admin menu.
    if (function_exists('SQLSelectOne') && function_exists('SQLInsert')) {
        $iconsRecord = SQLSelectOne("SELECT ID FROM project_modules WHERE NAME='icons'");
        if (empty($iconsRecord['ID'])) {
            $iconsRecord = array(
                'NAME' => 'icons',
                'TITLE' => 'Icons',
                'CATEGORY' => 'System',
                'PARENT_NAME' => '',
                'DATA' => '',
                'HIDDEN' => 0,
                'PRIORITY' => 0,
            );
            SQLInsert('project_modules', $iconsRecord);
        }
    }

    // Give the current administrator access to the new module.
    if (isset($GLOBALS['session']) && function_exists('SQLSelectOne') && function_exists('SQLUpdate')) {
        $iconsUser = SQLSelectOne("SELECT * FROM admin_users WHERE LOGIN='" . DBSafe($GLOBALS['session']->data['USER_NAME']) . "'");
        if (!empty($iconsUser['ID']) && strpos($iconsUser['ACCESS'], 'icons') === false) {
            $iconsUser['ACCESS'] = $iconsUser['ACCESS'] != '' ? $iconsUser['ACCESS'] . ',icons' : 'icons';
            SQLUpdate('admin_users', $iconsUser);
        }
    }

    // Register the files owned by the package (used when downloading a module).
    $iconsFiles = array(
        'cms/icons/config.json',
        'cms/icons/icons-enabled.css',
        'css/fontawesome-compat.css',
        'css/fontawesome-devices.css',
        'css/fontawesome-module-icons.css',
        'css/fontawesome-admin-icons.css',
        '3rdparty/fontawesome/css/all.min.css',
        'lib/icons_config.php',
        'lib/icons_lang.php',
        'modules/icons/icons.class.php',
        'templates/icons/icons.html',
        'languages/icons_default.php',
        'languages/icons_en.php',
        'languages/icons_ru.php',
        'languages/icons_ua.php',
        'languages/icons_uk.php',
    );
    $iconsInstalledDir = ROOT . 'cms' . DIRECTORY_SEPARATOR . 'modules_installed' . DIRECTORY_SEPARATOR;
    if (!is_dir($iconsInstalledDir)) {
        @mkdir($iconsInstalledDir, 0755, true);
    }
    @file_put_contents($iconsInstalledDir . 'icons.files', implode("\n", $iconsFiles));

    // Generate the default CSS from the staged library.
    icons_build_css(icons_config_default());

    // Connect the generated CSS from a common template, idempotently.
    $iconsHeader = ROOT . 'templates' . DIRECTORY_SEPARATOR . 'common_header.html';
    if (is_file($iconsHeader)) {
        $content = file_get_contents($iconsHeader);
        if (strpos($content, 'majordomo-icons:enabled-css') === false) {
            $block = "<!-- majordomo-icons:enabled-css -->\n"
                . '<link rel="stylesheet" href="<#ROOTHTML#>cms/icons/icons-enabled.css" type="text/css">' . "\n";
            $anchor = '<link rel="stylesheet" href="<#ROOTHTML#>css/theme-tokens.css" type="text/css">';
            if (strpos($content, $anchor) !== false) {
                $content = str_replace($anchor, $anchor . "\n" . $block, $content);
            } else {
                // Line-based match: the placeholder <#ROOTHTML#> contains ">",
                // so a generic <link[^>]*> pattern would stop too early.
                $content = preg_replace(
                    '/(^[^\r\n]*css\/devices\.css[^\r\n]*$\r?\n)/mi',
                    '$1' . $block,
                    $content,
                    1
                );
            }
            file_put_contents($iconsHeader, $content);
        }
    }
}
