<?php
class DatabaseController extends StudipController {

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
        if (Request::submitted('dbname')) {
            $path = $GLOBALS['ABSOLUTE_PATH_STUDIP'].$this->plugin->getPluginPath().DIRECTORY_SEPARATOR.'db';
            mkdir($path);
            $file_db = new PDO("sqlite:".$path.DIRECTORY_SEPARATOR.Request::get('dbname').".sqlite3");
            // Set errormode to exceptions
            $file_db->setAttribute(PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION);

            $sql = file_get_contents($GLOBALS['STUDIP_BASE_PATH'].DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'studip.sql');
            $stmts = explode(';', $sql);
            //var_dump($stmts);die;
            foreach ($stmts as $stmt) {
                try {
                    $stmt = preg_replace('/\s((ENGINE)|(DEFAULT CHARSET)|(ROW_FORMAT)|(AUTOINCREMENT)|(COLLATE))=\S*/', '', $stmt);
                    $stmt = preg_replace('/,\s*(UNIQUE\s)?KEY\s+\S*\s?\(\S*\)/', '', $stmt);
                    $stmt = preg_replace('/enum\(.*\)/', 'TEXT', $stmt);
                    $stmt = str_replace('AUTO_INCREMENT', '', $stmt);
                    $stmt = str_replace('unsigned', '', $stmt);
                $qr = $file_db->exec($stmt);
                } catch (PDOException $e) {
                    var_dump($stmt, $e->errorInfo);
                }
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
