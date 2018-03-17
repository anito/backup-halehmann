<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');
App::uses('ConnectionManager', 'Model');
App::uses('Utility', 'File');


class MysqlController extends AppController {

    var $name = 'Mysql';
    var $helpers = array();
    var $uses = array();

    function beforeFilter() {
        $this->autoRender = FALSE;
        define('USE_X_SEND', FALSE);
        $this->disableCache();
        parent::beforeFilter();
    }
    
    function exec() {
        $allowed_actions = array('dump', 'restore', 'connect');

        $action = array_splice($this->passedArgs, 0, 1);
        $action = $action[0];
        $args = implode(' ', $this->passedArgs);

        if (!in_array($action, $allowed_actions)) {
            $message = 'command not in list of allowed commands';
            header("Location: http://" . $_SERVER['HTTP_HOST'] . str_replace('//', '/', '/' . BASE_URL . '/pages/response?m=' . $message . '&c=error'));
            die;
        }

        $mysql = $this->mysql($action, $args);
    }

    function mysql($action, $args = '') {
        $ds = ConnectionManager::getDataSource('default');
        $dsc = $ds->config;
        $db = $dsc['database'];
        $message = '';

        if ($action == 'dump') {
            $postfix = MYSQL_CMD_PATH . 'mysqldump';
            $io = '>';
            $message = 'Datenbank erfolgreich gesichert';
            $fn = 'file_' . md5(date(time())) . '.sql';
        } elseif ($action == 'restore') {
            if (!empty($this->request->named['fn']) && !empty($this->request->ext) && $this->request->ext == 'sql') {
                $fn = $this->request->named['fn'] . '.' . $this->request->ext;
                $postfix = MYSQL_CMD_PATH . 'mysql';
                $io = '<';
                $message .= 'Datenbank erfolgreich wiederhergestellt';
            } else {
                $message .= 'wrong file';
                $result = 'error';
            }
        } else {
            $cmd = 'mysql connect localhost 2>&1';
            $op = `$cmd`;
            return $op;
        }

        $cmd = sprintf('%1s --defaults-extra-file=' . MYSQLCONFIG . DS . 'my.cnf ' . $db . ' %2s ' . MYSQLUPLOAD . DS . $fn . ' 2>&1', $postfix, $io);
        
        exec($cmd, $output, $return_var);# execute the command
        
        if($return_var) {
            $message = "Sorry - irgendwas ist schief gelaufen :(";
            $result = "error";
            $exists = file_exists(MYSQLUPLOAD . '/file.sql');
            if(!$exists) {
                $message .= 'No MySQL dump file found';
            } else {
                $chars = array("\n", "\r");
                $file = new File(MYSQLUPLOAD . '/file.sql');
                $message .= str_replace($chars, "", $file->read());
            }
        } else {
            $result = "success";
        }
        
        if ($action == "dump") {
            c(MAX_DUMPS); #cleanup older dump files
        }
        header("Location: http://" . $_SERVER['HTTP_HOST'] . str_replace('//', '/', '/' . BASE_URL . '/pages/response?m=' . $message . '&c=' . $result));
        die;
    }
    
    public function uri() {
        $json = array();
        $fn = '*.*';
        if ($this->Auth->user('id')) {
            $uid = $this->Auth->user('id');
            if (!empty($this->data)) {
                foreach ($this->data as $data) {
                    if(!empty($data['fn'])) {
                        $fn = $data['fn'];
                    }
                }
            }
            $path = MYSQLUPLOAD . DS . $fn;
            $files = glob($path);
            $fn = basename($files[0]);
            if(!empty($files[0])) {
                $options = compact(array('uid', 'fn'));
                $file = p($options);
            } else {
                $message = 'kein Download verfügbar';
                $result = 'error';
                header("Location: http://" . $_SERVER['HTTP_HOST'] . str_replace('//', '/', '/' . BASE_URL . '/pages/response?m=' . $message . '&c=' . $result));
                die;
            }
        } else {
            header('HTTP/1.1 403 Forbidden');
            die;
        }
        header("Location: $file" );
        die;
    }
    
    function getFile() {
        $this->autoRender = false;
        $this->layout = false;
        
        $val = $this->request->params['named']['a'];
        
        if (strpos($val, 'http://') !== false || substr($val, 0, 1) == '/') {
            header('Location: ' . $val);
            exit;
        } else {
            $val = str_replace(' ', '.2B', $val);
        }

        App::import('Component', 'Salt');

        $salt = new SaltComponent();

        $val = str_replace(' ', '.2B', $val);
        $crypt = $salt->convert($val, false);
        $a = explode(',', $crypt);
        $file = $fn = basename($a[1]);

        // Make sure supplied filename contains only approved chars
        if (preg_match("/[^A-Za-z0-9._-]/", $file)) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        define('PATH', MYSQLUPLOAD );

        $file = PATH . DS . $file;
        $disabled_functions = explode(',', ini_get('disable_functions'));
        
        if (USE_X_SEND) {
            header("X-Sendfile: $file");
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        } else {
            header('Content-type: application/octet-stream');
            header('Content-length: ' . filesize($file));
            header('Cache-Control: public');
            header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 year')));
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)));
            if (is_callable('readfile') && !in_array('readfile', $disabled_functions)) {
                readfile($file);
            } else {
                die(file_get_contents($file));
            }
        }
    }

}
