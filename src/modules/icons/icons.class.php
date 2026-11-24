<?php
/**
 * Icons module: settings page for the local Font Awesome icon layer.
 */

include_once(dirname(__FILE__) . '/../../lib/icons_config.php');
include_once(dirname(__FILE__) . '/../../lib/icons_lang.php');

class icons extends module
{
    function __construct()
    {
        $this->name = 'icons';
        $this->title = 'Icons';
        $this->module_category = 'System';
    }

    function run()
    {
        global $mode;
        $out = array();

        if ($mode == 'save') {
            $config = icons_config();
            $config['groups'] = array(
                'core' => !empty($_POST['group_core']) ? 1 : 0,
                'devices' => !empty($_POST['group_devices']) ? 1 : 0,
                'modules' => !empty($_POST['group_modules']) ? 1 : 0,
                'admin' => !empty($_POST['group_admin']) ? 1 : 0,
            );
            $allowed = array_keys(icons_module_map());
            $modules = isset($_POST['modules']) && is_array($_POST['modules']) ? $_POST['modules'] : array();
            $config['modules'] = array_values(array_intersect($allowed, $modules));
            $config = icons_save_config($config);
            icons_build_css($config);
            $out['SAVED'] = 1;
        }

        $config = icons_config();
        $out['S'] = icons_lang();
        $out['GROUPS'] = array();
        $groupLabels = array(
            'core' => 'ICONS_GROUP_CORE',
            'devices' => 'ICONS_GROUP_DEVICES',
            'modules' => 'ICONS_GROUP_MODULES',
            'admin' => 'ICONS_GROUP_ADMIN',
        );
        foreach ($config['groups'] as $key => $value) {
            $out['GROUPS'][] = array(
                'KEY' => $key,
                'LABEL' => isset($groupLabels[$key]) ? icons_t($groupLabels[$key], $key) : $key,
                'ENABLED' => $value ? 1 : 0,
            );
        }

        $map = icons_module_map();
        $selected = $config['modules'];
        $out['MODULES'] = array();
        foreach (icons_available_modules() as $name) {
            $info = isset($map[$name]) ? $map[$name] : $map['default'];
            $out['MODULES'][] = array(
                'NAME' => $name,
                'TITLE' => ucfirst(str_replace('_', ' ', $name)),
                'ICON' => $info[0],
                'SELECTED' => in_array($name, $selected) ? 1 : 0,
            );
        }

        $out['CSS_FILE'] = $config['css_file'];
        $out['CSS_EXISTS'] = is_file(ROOT . $config['css_file']) ? 1 : 0;

        $this->data = $out;
        $parser = new parser(DIR_TEMPLATES . 'icons/icons.html', $this->data, $this);
        $this->result = $parser->result;
    }

    function uninstall()
    {
        include_once(dirname(__FILE__) . '/../../lib/icons_config.php');

        $header = ROOT . 'templates' . DIRECTORY_SEPARATOR . 'common_header.html';
        if (is_file($header)) {
            $content = file_get_contents($header);
            $content = preg_replace(
                '/^[ \t]*<!-- majordomo-icons:enabled-css -->\r?\n[^\r\n]*icons-enabled\.css[^\r\n]*\r?\n/m',
                '',
                $content
            );
            file_put_contents($header, $content);
        }

        $base = icons_base_path();
        @unlink($base . 'config.json');
        @unlink($base . 'icons-enabled.css');
        if (is_dir($base)) {
            @rmdir($base);
        }
        @unlink(ROOT . 'cms' . DIRECTORY_SEPARATOR . 'modules_installed' . DIRECTORY_SEPARATOR . 'icons.files');

        if (function_exists('SQLExec')) {
            SQLExec("DELETE FROM project_modules WHERE NAME='icons'");
        }
    }
}
