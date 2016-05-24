<?php

class SormformController extends StudipController {

    public function __construct($dispatcher) {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args) {

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
//      PageLayout::setTitle('');
    }

    public function index_action() {
        
        // Set includepath
        if (Request::get('path')) {
            StudipAutoloader::addAutoloadPath(Request::get('path'));
        }
        
        $classname = Request::get('sorm');
        if (class_exists($classname)) {
            if (is_subclass_of($classname, 'SimpleORMap')) {
            $sorm = new $classname();
            $this->metadata = $sorm->getTableMetadata();
            
            // Load edits
            $this->fulltext = Request::getArray('fulltext');
            $this->placeholder = Request::getArray('placeholder');
            $this->type = Request::getArray('type');
            
            // Set info
            $this->info = 'Dont forget! $sorm->setData(Request::getArray("'.(Request::get('request') ? : strtolower(Request::get('sorm'))).'")); $sorm->store(); will save this form in a controller!';
            
            } else {
                $this->info = "Klasse ist keine Sorm";
            }
        } else {
            $this->info = "Klasse nicht gefunden";
        }
    }

    // customized #url_for for plugins
    function url_for($to) {
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
