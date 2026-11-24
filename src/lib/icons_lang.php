<?php
/**
 * Language loader for the icons module.
 *
 * Resolves the active MajorDoMo language to one of the bundled dictionaries.
 * 'uk' is treated as an alias of 'ua', matching the core language convention.
 */

function icons_current_language()
{
    $lang = 'default';
    if (isset($_SESSION['lang']) && $_SESSION['lang']) {
        $lang = $_SESSION['lang'];
    } elseif (defined('SETTINGS_SITE_LANGUAGE') && SETTINGS_SITE_LANGUAGE) {
        $lang = SETTINGS_SITE_LANGUAGE;
    }
    $lang = strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', (string)$lang));
    if ($lang === 'uk') {
        $lang = 'ua';
    }
    if (!in_array($lang, array('en', 'ru', 'ua'), true)) {
        $lang = 'default';
    }
    return $lang;
}

function icons_lang()
{
    static $strings = null;
    if (is_array($strings)) {
        return $strings;
    }

    $file = ROOT . 'languages' . DIRECTORY_SEPARATOR . 'icons_' . icons_current_language() . '.php';
    if (!is_file($file)) {
        $file = ROOT . 'languages' . DIRECTORY_SEPARATOR . 'icons_default.php';
    }

    $ICONS_STRINGS = array();
    if (is_file($file)) {
        include $file;
    }
    $strings = is_array($ICONS_STRINGS) ? $ICONS_STRINGS : array();
    return $strings;
}

function icons_t($key, $default = '')
{
    $strings = icons_lang();
    return isset($strings[$key]) ? $strings[$key] : $default;
}
