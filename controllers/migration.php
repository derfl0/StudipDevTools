<?php

class MigrationController extends StudipController
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
        if (Request::submitted('create')) {
            $db = DBManager::get();
            $tables = $db->fetchFirst('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = SCHEMA() AND TABLE_NAME  LIKE ?', array(Request::get('prefix').'%'));

            $create = $delete = array();
            foreach ($tables as $table) {
                $sql = $db->fetchColumn("SHOW CREATE TABLE $table" , array(), 1);
                $sql = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $sql);
                $sql = preg_replace('/\sCOLLATE\s\w*/', '', $sql);
                $sql = preg_replace('/\s((ENGINE)|(DEFAULT CHARSET)|(COLLATE))=\w*/', '', $sql);
                $create[] = 'DBManager::get()->exec("'.$sql.'");';
                $delete[] = 'DBManager::get()->exec("DROP TABLE IF EXISTS `'.$table.'`");';
            }

            $file[] = "<?php";
            $file[] = "/**";
            $file[] = " * This file was generated with Studip Dev Tools";
            $file[] = " */";
            $file[] = "";
            $file[] = "class ".str_replace(' ', '', ucwords(str_replace('_', ' ',preg_replace('/\A\d*_/', '', Request::get('migration')))))." extends DBMigration {";
            $file[] = "    function up() {";
            $file[] = join(PHP_EOL.'    ', $create);
            $file[] = "    }";
            $file[] = "    function down() {";
            $file[] = join(PHP_EOL.'    ', $delete);
            $file[] = "    }";
            $file[] = "}";

            $plugin = PluginManager::getInstance()->getPluginById(Request::get('plugin_id'));
            $path = $GLOBALS['ABSOLUTE_PATH_STUDIP'].$plugin->getPluginPath().DIRECTORY_SEPARATOR."migrations";
            $filename = Request::get('migration').".php";
            @mkdir($path, 0744, true);
            $filepath = $path.DIRECTORY_SEPARATOR.$filename;
            $content = join(PHP_EOL, $file);

            file_put_contents($filepath, $content);

            if (file_exists($filepath) && file_get_contents($filepath) == $content) {
                PageLayout::postMessage(MessageBox::success('Migration erfolgreich erstellt'));
            } else {
                PageLayout::postMessage(MessageBox::error('Migration konnte nicht erstellt werden'));
            }
        }
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
