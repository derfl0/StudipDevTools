<?php
require 'bootstrap.php';

/**
 * StudipDevTools.class.php
 *
 * ...
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 0.1a
 */

class StudipDevTools extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();

        $navigation = new AutoNavigation(_('StudipDevTools'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'migration/index'));
        $navigation->setImage(Assets::image_path('blank.gif'));
        Navigation::addItem('/studipdevtools', $navigation);
        $navigation->addSubNavigation('migration', new AutoNavigation(_('MigrationGenerator'), PluginEngine::GetURL($this, array(), 'migration/index')));
        $navigation->addSubNavigation('sormform', new AutoNavigation(_('Sorm2Form'), PluginEngine::GetURL($this, array(), 'sormform/index')));
        $navigation->addSubNavigation('less', new AutoNavigation(_('Less'), PluginEngine::GetURL($this, array(), 'color/index')));

        shell_exec('cd '.$GLOBALS['STUDIP_BASE_PATH'].';make less');
    }

    public function initialize () {

    }

    public function perform($unconsumed_path)
    {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload()
    {
        if (class_exists('StudipAutoloader')) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }
}
