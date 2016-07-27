<?php

class DbdumpController extends StudipController
{

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args)
    {

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
//      PageLayout::setTitle('');
    }

    public function index_action()
    {
        $backupdir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backups';
        foreach (glob($backupdir . DIRECTORY_SEPARATOR . '*.zip') as $file) {
            $this->backups[] = array("name" => basename($file),
                "link" => $this->url_for('dbdump/restore/' . basename($file, ".zip")),
                "delete" => $this->url_for('dbdump/delete/' . basename($file, ".zip")),
                "size" => number_format(filesize($file) / 1024 / 1024, 2) . 'MB',
                "date" => strftime("%x %X", DateTime::createFromFormat('!Ymd-His', basename($file, ".zip"))->getTimestamp()));

        }
        
    }

    public function delete_action($id) {
        $backupdir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backups';
        unlink($backupdir . DIRECTORY_SEPARATOR . $id . '.tar.gz');
        $this->redirect('dbdump/index');
    }
/* TAR.GZ erstellt das Zip File im Ram. Das wird bei Live ziemlich blöde
    public function backup_action()
    {
        set_time_limit (2 * 60 * 60);
        ini_set('memory_limit', '2G');
        $time = time();
        $bname = date("Ymd-His");
        $backupdir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backups';
        @mkdir($backupdir);
        $filepath = $backupdir . DIRECTORY_SEPARATOR . $bname;
        @mkdir($filepath);
        $zipname = $backupdir . DIRECTORY_SEPARATOR . $bname . '.tar';
        $zip = new PharData($zipname);
        $tables = DBManager::get()->prepare("SHOW TABLES");
        $tables->execute();
        $db = DBManager::get();
        while ($table = $tables->fetchColumn()) {
            $filename = $filepath . DIRECTORY_SEPARATOR . $table;
            $db->exec("SELECT * INTO OUTFILE '$filename' FROM `$table`");
            $dumpfiles[$table] = $filename;
            //$zip->addFile($filename, $table);
            //unlink($filename);
        }
        $zip->buildFromDirectory($filepath);
        $zip->compress(Phar::GZ);
        unlink($zipname);
        foreach ($dumpfiles as $f) {
            unlink($f);
        }

        rmdir($filepath);
        $time = time() - $time;
        PageLayout::postMessage(MessageBox::success('Backup erstellt!', array('Name ' . $bname . '.tar.gz', 'Dauer ' . $time . ' Sekunden', 'Größe ' . number_format(filesize($zipname . '.gz') / 1024 / 1024, 2) . 'MB')));
        $this->redirect('dbdump/index');
    }
*/
    public function restore_action($id)
    {
        $time = time();
        $backupdir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backups';
        $archive = new PharData($backupdir . DIRECTORY_SEPARATOR . $id . '.zip');
        $db = DBManager::get();
        foreach ($archive as $file) {
            $table = basename($file);
            $restore = $backupdir . DIRECTORY_SEPARATOR . $table;
            file_put_contents($restore, file_get_contents($file));
            $db->exec("TRUNCATE TABLE $table");
            $db->exec("LOAD DATA INFILE '$restore' INTO TABLE `$table");
            unlink($backupdir . DIRECTORY_SEPARATOR . $table);
        }
        PageLayout::postMessage(MessageBox::success('Backup wiederhergestellt!', array('Name ' . $id . '.tar.gz', 'Dauer ' . (time() - $time) . ' Sekunden')));
        $this->redirect('dbdump/index');
    }


    public function backup_action() {
        set_time_limit (2 * 60 * 60);
        ini_set('memory_limit', '2G');
        $time = time();
        $dirname = date("Ymd-His");
        $zipname = $dirname.".zip";
        $backupdir = dirname(__DIR__).DIRECTORY_SEPARATOR.'backups';
        $fulldir = $backupdir.DIRECTORY_SEPARATOR.$dirname;
        @mkdir($backupdir);
        @unlink($fulldir);
        @mkdir($fulldir);
        $zipfile = $backupdir.DIRECTORY_SEPARATOR.$zipname;
        $zip = new ZipArchive();
        $zip->open($zipfile, ZipArchive::CREATE);
        $tables = DBManager::get()->prepare("SHOW TABLES");
        $tables->execute();
        while($table = $tables->fetchColumn()) {
            $filename = $fulldir.DIRECTORY_SEPARATOR.$table;
            DBManager::get()->exec("SELECT * INTO OUTFILE '$filename' FROM $table");
            $zip->addFile($filename, basename($filename));
            $unlink[] = $filename;
        }
        $zip->close();
        foreach ($unlink as $u) {
            unlink($u);
        }
        rmdir($fulldir);
        PageLayout::postMessage(MessageBox::success('Backup erstellt!', array('Name ' . $zipname , 'Dauer ' . $time . ' Sekunden', 'Größe ' . number_format(filesize($zipname) / 1024 / 1024, 2) . 'MB')));
        $this->redirect('dbdump/index');
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
