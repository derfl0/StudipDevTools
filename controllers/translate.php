<?php

class TranslateController extends StudipController
{

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function index_action()
    {
        // Pluginselection
        $select = new SelectWidget('Plugin', $this->url_for('translate/index'), 'pluginid');
        foreach(PluginManager::getInstance()->getPluginInfos() as $plugin) {
            if (($plugin['id'] == Request::get('pluginid') || (!Request::get('pluginid') && !$this->currentPlugin))) {
                $this->currentPlugin = $plugin;
            }
            $select->addElement(new SelectElement($plugin['id'], $plugin['name'], $plugin['id'] == Request::get('pluginid')));
        }
        Sidebar::Get()->addWidget($select);
        $actions = new ActionsWidget();
        $actions->addLink('Compile', $this->url_for('translate/compile/'.$this->currentPlugin['id']));
        Sidebar::Get()->addWidget($actions);
    }

    public function extract_action($pluginid) {
        $plugin = PluginManager::getInstance()->getPluginInfoById($pluginid);
        $path = $GLOBALS['PLUGINS_PATH'].DIRECTORY_SEPARATOR.$plugin['path'];
        $filename = trim(Request::get('filename'));
        $lang = 'en';
        $file = $path.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'LC_MESSAGES'.DIRECTORY_SEPARATOR.$filename.'.po';
        $langpath = $path.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'LC_MESSAGES';

        // Create dir if not existing
        @mkdir($langpath, 0744, true);

        // Rename old file
        if (file_exists($file)) {
            rename($file, $file.'.old');
        }

        // Need PATH variable
        $ex = 'export PATH="/usr/local/bin/";';

        $phpfiles = ($this->getPhpFiles($path));

        // Create new file
        file_put_contents($langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po', '', FILE_APPEND);
        file_put_contents($langpath.DIRECTORY_SEPARATOR.$filename.'.po', '', FILE_APPEND);

        // Load php strings
        foreach ($phpfiles as $phpfile) {
            $cmd = $ex.'xgettext --from-code=ISO-8859-1 -j -n --language=PHP -o "'.$langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po" '.$phpfile.' 2>&1';
            echo shell_exec($cmd);
        }

        // Set charset to utf8
        file_put_contents($langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po',str_replace('charset=CHARSET\n"','charset=UTF-8\n"',file_get_contents($langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po')));

        echo shell_exec($ex.'msgconv --to-code=iso-8859-1 "'.$langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po" -o "'.$file.'" 2>&1');
        if (file_exists($file.'.old')) {
            shell_exec($ex.'msgmerge "'.$file.'.old" "'.$file.'" --output-file="'.$file.'"');
        }
        @unlink($langpath.DIRECTORY_SEPARATOR.$filename.'.UTF-8.po');
        $this->redirect('translate/index?pluginid='.$pluginid);
    }

    public function compile_action($pluginid) {
        $plugin = PluginManager::getInstance()->getPluginInfoById($pluginid);
        $path = $GLOBALS['PLUGINS_PATH'].DIRECTORY_SEPARATOR.$plugin['path'];
        $lang = 'en';
        $ex = 'export PATH="/usr/local/bin/";';
        $langpath = $path.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'LC_MESSAGES';
        $pos = glob($langpath.DIRECTORY_SEPARATOR.'*.po');
        $filename = basename($pos[0], '.po');
        $file = $langpath.DIRECTORY_SEPARATOR.$filename.'.mo';

        // Rename old create new
        if (file_exists($file)) {
            rename($file, $file.'.old');
        }
        file_put_contents($file, '', FILE_APPEND);


        echo shell_exec($ex.'msgfmt "'.$langpath.DIRECTORY_SEPARATOR.$filename.'.po'.'" --output-file="'.$file.'" 2>&1');
        $this->redirect('translate/index?pluginid='.$pluginid);
    }

    function getPhpFiles($dir) {
        $result = glob($dir.DIRECTORY_SEPARATOR.'*.php');
        foreach (glob($dir.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $newdir) {
            $result = array_merge($result, $this->getPhpFiles($newdir));
        }
        $result = array_filter($result);
        return $result;
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
