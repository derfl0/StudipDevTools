<?php

class GitController extends StudipController {

    public function __construct($dispatcher) {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args) {

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
//      PageLayout::setTitle('');
    }

    public function index_action() {
        $git = Request::get('git');
        if ($git) {
            $tmpname = uniqid();
            $path = $GLOBALS['PLUGINS_PATH'];
            $folder = "$path/$tmpname";
            // Required for git to work
            $export = 'export DYLD_LIBRARY_PATH=/usr/lib/:$DYLD_LIBRARY_PATH;';
            $cmd = $export . "git clone $git $folder 2>&1;";
            // Clone
            $this->answer .= shell_exec($cmd);
            // Read manifest
            $manifest = parse_ini_file("$folder/plugin.manifest");
            // Create origin directory if doesnt exist
            @mkdir($path . DIRECTORY_SEPARATOR . $manifest['origin']);
            // And move
            $realpath = $path . DIRECTORY_SEPARATOR . $manifest['origin'] . DIRECTORY_SEPARATOR . $manifest['pluginclassname'];
            rename($folder, $realpath);
            $this->answer .= '<br>Folder moved to '.$realpath;
            // Register if required
            if (Request::get('register')) {
                require_once 'app/models/plugin_administration.php';
                $pa = new PluginAdministration();
                try {
                    $pa->registerPlugin($realpath);
                    $this->answer .= '<br>Plugin wurde erfolgreich registriert.';

                    if (Request::get('activate')) {
                        $plugin = PluginManager::getInstance()->getPluginInfo($manifest['pluginclassname']);
                        PluginManager::getInstance()->setPluginEnabled($plugin['id'], true);
                        $this->answer .= '<br>Plugin wurde erfolgreich aktiviert.';
                    }

                } catch (PluginInstallationException $ex) {
                    $this->flash['error'] = $ex->getMessage();
                }
            }

            $this->answer .= '<br>Done!';
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
