<?php
/**
 * Интерфейс управления заменой иконок.
 */
include_once(dirname(__FILE__) . '/../../lib/icons_config.php');

class icons extends module
{
    function __construct()
    {
        $this->name = 'icons';
        $this->title = 'Иконки';
        $this->module_category = 'Система';
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
            $config['modules'] = isset($_POST['modules']) && is_array($_POST['modules']) ? $_POST['modules'] : array();
            $config = icons_save_config($config);
            icons_build_css($config);
            $out['SAVED'] = 1;
        }

        $config = icons_config();
        $out['GROUPS'] = array();
        foreach ($config['groups'] as $key => $value) {
            $out['GROUPS'][] = array('KEY' => $key, 'ENABLED' => $value ? 1 : 0);
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
                'COLOR' => $info[1],
                'SELECTED' => in_array($name, $selected) ? 1 : 0,
            );
        }
        $out['CSS_FILE'] = $config['css_file'];
        $out['CSS_EXISTS'] = is_file(ROOT . $config['css_file']) ? 1 : 0;

        $this->data = $out;
        $parser = new parser(DIR_TEMPLATES . 'icons/icons.html', $this->data, $this);
        $this->result = $parser->result;
    }
}
