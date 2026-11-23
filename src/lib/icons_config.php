<?php
/**
 * Настройки опциональной замены иконок и генерация включённого CSS.
 */

function icons_config_default()
{
    return array(
        'groups' => array('core' => 1, 'devices' => 0, 'modules' => 1, 'admin' => 0),
        'modules' => array('dashboard', 'devices', 'classes', 'scenes', 'settings', 'control_access', 'control_modules', 'saverestore', 'market', 'xray', 'system_errors', 'connect'),
        'css_file' => 'cms/icons/icons-enabled.css',
    );
}

function icons_base_path()
{
    return ROOT . 'cms' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
}

function icons_config()
{
    $config = icons_config_default();
    $file = icons_base_path() . 'config.json';
    if (!is_file($file)) {
        return $config;
    }
    $saved = json_decode(@file_get_contents($file), true);
    if (!is_array($saved)) {
        return $config;
    }
    foreach ($config as $key => $value) {
        if (array_key_exists($key, $saved)) {
            $config[$key] = $saved[$key];
        }
    }
    return $config;
}

function icons_save_config($config)
{
    $defaults = icons_config_default();
    $clean = array(
        'groups' => array(),
        'modules' => isset($config['modules']) && is_array($config['modules']) ? array_values(array_unique($config['modules'])) : $defaults['modules'],
        'css_file' => $defaults['css_file'],
    );
    foreach ($defaults['groups'] as $key => $value) {
        $clean['groups'][$key] = !empty($config['groups'][$key]) ? 1 : 0;
    }
    if (!is_dir(icons_base_path())) {
        @mkdir(icons_base_path(), 0755, true);
    }
    file_put_contents(icons_base_path() . 'config.json', json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $clean;
}

function icons_module_map()
{
    return array(
        'dashboard' => array('gauge-high', 'f625', '#2980b9'),
        'devices' => array('microchip', 'f2db', '#e67e22'),
        'classes' => array('layer-group', 'f5fd', '#8e44ad'),
        'objects' => array('layer-group', 'f5fd', '#8e44ad'),
        'scenes' => array('table-cells', 'f00a', '#16a085'),
        'settings' => array('gear', 'f013', '#7f8c8d'),
        'control_access' => array('shield-halved', 'f3ed', '#c0392b'),
        'control_modules' => array('puzzle-piece', 'f12e', '#2980b9'),
        'saverestore' => array('database', 'f1c0', '#27ae60'),
        'market' => array('box-open', 'f49e', '#d35400'),
        'xray' => array('circle-info', 'f05a', '#3498db'),
        'system_errors' => array('triangle-exclamation', 'f071', '#e74c3c'),
        'connect' => array('cloud', 'f0c2', '#3f8fd0'),
        'commands' => array('terminal', 'f120', '#2c3e50'),
        'patterns' => array('list-check', 'f0ae', '#16a085'),
        'app_player' => array('music', 'f001', '#9b59b6'),
        'app_mediabrowser' => array('image', 'f03e', '#e67e22'),
        'calendar' => array('calendar', 'f133', '#2980b9'),
        'pinghosts' => array('wifi', 'f1eb', '#00acc1'),
        'locations' => array('map', 'f279', '#16a085'),
        'charts' => array('chart-bar', 'f080', '#2980b9'),
        'scripts' => array('code', 'f121', '#34495e'),
        'soundfiles' => array('volume', 'f028', '#9b59b6'),
        'security_rules' => array('shield-halved', 'f3ed', '#c0392b'),
        'shoutbox' => array('comment', 'f075', '#3498db'),
        'terminals' => array('terminal', 'f120', '#2c3e50'),
        'layouts' => array('layer-group', 'f5fd', '#8e44ad'),
        'events' => array('bell', 'f0f3', '#e67e22'),
        'default' => array('house', 'f015', '#607d8b'),
    );
}

function icons_available_modules()
{
    $modules = array();
    $dir = ROOT . 'img' . DIRECTORY_SEPARATOR . 'modules';
    if (is_dir($dir)) {
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*.png') as $file) {
            $modules[] = basename($file, '.png');
        }
    }
    if (function_exists('SQLSelect')) {
        $rows = @SQLSelect("SELECT NAME FROM project_modules ORDER BY NAME");
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['NAME']) && !in_array($row['NAME'], $modules)) {
                    $modules[] = $row['NAME'];
                }
            }
        }
    }
    sort($modules);
    return $modules;
}

function icons_build_css($config = null)
{
    if ($config === null) {
        $config = icons_config();
    }
    $groups = $config['groups'];
    $css = "/* MajorDoMo icons: сгенерировано интерфейсом ?action=icons */\n";
    if (!empty($groups['core'])) {
        $css .= "@import url(\"../../css/fontawesome-compat.css\");\n";
    }
    if (!empty($groups['devices'])) {
        $css .= "@import url(\"../../css/fontawesome-devices.css\");\n";
    }
    if (!empty($groups['admin'])) {
        $css .= "@import url(\"../../css/fontawesome-admin-icons.css\");\n";
    }
    if (!empty($groups['modules'])) {
        $css .= "@import url(\"../../css/fontawesome-module-icons.css\");\n";
        $map = icons_module_map();
        foreach ($config['modules'] as $module) {
            if (!isset($map[$module])) {
                continue;
            }
            $info = $map[$module];
            $name = preg_replace('/[^A-Za-z0-9_-]/', '', $module);
            $css .= '.fa-module-icons a[href*="action=' . $name . '"]::before { content: "\\' . $info[1] . '"; --fa-icon-color: ' . $info[2] . "; }\n";
        }
    }
    if (!is_dir(icons_base_path())) {
        @mkdir(icons_base_path(), 0755, true);
    }
    $path = ROOT . $config['css_file'];
    file_put_contents($path, $css);
    return $path;
}
