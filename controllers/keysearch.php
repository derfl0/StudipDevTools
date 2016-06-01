<?php
class KeysearchController extends StudipController {

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
        if (Request::submitted('query')) {
            $time = microtime(1);
        $db = DBManager::get();
        foreach ($db->fetchFirst('SHOW TABLES') as $table) {
            //$pri = $db->fetchFirst("SHOW COLUMNS FROM abschluss WHERE `Key` = 'PRI'");
            
            foreach ($db->fetchAll("SHOW COLUMNS FROM $table WHERE Type = 'char(32)' OR Type = 'varchar(32)'") as $hit) {
                $columns[] = array(
                    'table' => $table,
                    'column' => $hit['Field']
                );
                $sql[] = "(SELECT '$table' as `table`, '{$hit['Field']}' as `column`, CAST({$hit['Field']} AS char(32)) as id  FROM $table WHERE {$hit['Field']} LIKE :query)";
            }
        }
        $fullsql = join(' UNION ', $sql);
        $stmt = $db->prepare($fullsql);
        $query = Request::get('query').'%';
        $stmt->bindParam(':query', $query);
        $stmt->execute();
        $this->data = $stmt->fetchAll();
            $this->time = microtime(1) - $time;
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
