<?php

class ColorController extends StudipController
{

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox.php'));
//      PageLayout::setTitle('');
    }

    public function index_action()
    {
        $file = $GLOBALS['STUDIP_BASE_PATH'].DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'stylesheets'.DIRECTORY_SEPARATOR.'mixins'.DIRECTORY_SEPARATOR.'colors.less';
        $current = file_get_contents($file);

        $newcolor = Request::get('color');
        if (Request::submitted('reset')) {
            $newcolor = '#28497c';
        }

        if ($newcolor) {
            $current = preg_replace('/@base-color: (#[0-9a-fA-F]{3,6});/', '@base-color: '.$newcolor.';', $current);
            file_put_contents($file, $current);
            shell_exec('cd '.$GLOBALS['STUDIP_BASE_PATH'].';make less');
        }


        preg_match('/@base-color: (#[0-9a-fA-F]{3,6});/', $current, $color);
        $this->color = $color[1];
    }

    // customized #url_for for plugins
    function url_for($to)
    {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }
}
