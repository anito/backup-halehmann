<?php

/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 */
/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 */
/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); // Loads a single plugin named DebugKit
 */
/**
 * To prefer app translation over plugin translation, you can set
 *
 * Configure::write('I18n.preferApp', true);
 */
/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 * 		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 * 		'MyCacheFilter' => array('prefix' => 'my_cache_'), //  will use MyCacheFilter class from the Routing/Filter package in your app with settings array.
 * 		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 * 		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));

/**
 * Configures Google Analytics
 * you can disable anlytics by initilize it by false ,see here
 */
//Configure::write('google-analytics.tracker-code', 'UA-88792343-1'); // Google Analytics Enabled
Configure::write('google-analytics.tracker-code', false); //  Google Analytics disabled

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
    'engine' => 'File',
    'types' => array('notice', 'info', 'debug'),
    'file' => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'File',
    'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
    'file' => 'error',
));

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $protocol = 'https://';
} else {
    $protocol = 'http://';
}
define('DIR_REL_HOST', str_replace('/index.php?', '', Configure::read('App.baseUrl')));
define('DIR_HOST', $protocol . preg_replace('/:80$/', '', env('HTTP_HOST')) . DIR_REL_HOST);
define('BASE_URL', Configure::read('App.baseUrl'));
define('WEB_URL', '/' . APP_DIR . '/' . WEBROOT_DIR);
define('UPLOADS', ROOT . DS . 'uploads');
define('PHOTOS', UPLOADS . DS . 'photos');
if(!defined('MYSQLCONFIG')) {
    define('MYSQLCONFIG', ROOT . DS . 'mysqlconf');
    if (!is_dir(MYSQLCONFIG)) {
        $parent_perms = substr(sprintf('%o', fileperms(dirname(dirname(MYSQLCONFIG)))), -4);
        $old = umask(0);
        mkdir(MYSQLCONFIG, octdec($parent_perms));
        umask($old);
    }
}
if(!defined('MYSQLUPLOAD')) {
    define('MYSQLUPLOAD', ROOT . DS . 'mysql');
    if (!is_dir(MYSQLUPLOAD)) {
        $parent_perms = substr(sprintf('%o', fileperms(dirname(dirname(MYSQLUPLOAD)))), -4);
        $old = umask(0);
        mkdir(MYSQLUPLOAD, octdec($parent_perms));
        umask($old);
    }
}
define('STATCONFIG', '../../' . WEBROOT_DIR . DS . 'stat' . DS . 'config' . DS);
define('DEFAULT_USER', 'angela');
if (!defined('MYSQL_CMD_PATH')) {
    $a = explode('.', DIR_HOST);
    $last = count($a) - 1;
    $tl = $a[$last];
    $path = '';
    if ($tl == 'dev' || $tl == 'local') {
        $path = '/usr/local/mysql/bin/';
    }
    define('MYSQL_CMD_PATH', $path);
}
if (!defined('TOPLEVEL')) {
    $a = explode('.', DIR_HOST);
    $last = count($a) - 1;
    define('TOPLEVEL', $a[$last]);
}
//if (!defined('HOST')) {
//  $a = explode('.', DIR_HOST);
//  $domain = count($a)-2;
//  define('HOST', $a[$domain]);
//}
if (!defined('SIMPLE_JSON')) {
    define('SIMPLE_JSON', '/Elements/simple_json');
}
if (!defined('FLASH_JSON')) {
    define('FLASH_JSON', '/Elements/flash_json');
}
if (!defined('SALT')) {
    define('SALT', 'urrasjksdjkbsdakbjvgikjbgfiabrg');
}
if (!defined('MAGICK_PATH')) {
    define('MAGICK_PATH_FINAL', 'convert');
} else if (strpos(strtolower(MAGICK_PATH), 'c:\\') !== false) {
    define('MAGICK_PATH_FINAL', '"' . MAGICK_PATH . '"');
} else {
    define('MAGICK_PATH_FINAL', MAGICK_PATH);
}
if (!defined('MAX_DUMPS')) {
    define('MAX_DUMPS', 5);
}

function pre() {
    $args = func_get_args();
    foreach ($args as $arg) {
        pr($arg);
    }
}

function returnExt($file) {
    $pos = strrpos($file, '.');
    return strtolower(substr($file, $pos + 1, strlen($file)));
}

function l($sort, $fullpath = FALSE, $human = TRUE) {
    App::uses('CakeTime', 'Utility');
    $time = array();
    $path = MYSQLUPLOAD . DS . '*.*';
    $files = glob($path);
    if(!is_array($files)) $files = [];
    foreach ($files as $key => $val) {
        $timestamp = filemtime($val);
        if(!$fullpath) {
            $parts = explode(DS, $val);
            $val = array_pop($parts);
        }
        $time[$val] = $timestamp;
    }
    array_multisort($time, $sort);
    if(!$human) {
        return $time;
    } else {
        foreach ($time as $key => $val) {
            $time[$key] = CakeTime::format($val, '%d. %b %Y %H:%M');
        }
        return $time;
    }
}

function c($max = 5) {
    App::uses('File', 'Utility');
    $files = l(SORT_ASC, TRUE);
    reset($files);
    if(count($files) > $max) {
        $file = new File(key($files));
        $file->delete();
        $file->close();
        c($max);
    }
}

function p($options) {
    $defaults = array(
        'fn' => 'file.sql'
    );
    $o = array_merge($defaults, $options);
    $args = join(',', array($o['uid'], $o['fn']));
    include_once(ROOT . DS . 'app' . DS . 'Controller' . DS . 'Component' . DS . 'SaltComponent.php');

    $salt = new SaltComponent();
    $crypt = $salt->convert($args);
    $path = MYSQLUPLOAD . DS . $o['fn'];
    $m = filemtime($path);
    $x = returnExt($path);
//    $timestamp = gmdate('D, d M Y H:i:s');
    $timestamp = date('Ymd:His');
    return BASE_URL . '/q/a:' . $crypt . '/dump:' . $timestamp . '_' . $m . '.' . $x;
}
/*
 * get difference of 2 times
 * 
 */
function get_time_diff( $time, $time_unit = "d" ) {

	$now = date_create();

	if ( !isset( $time ) )
		$time = $now;

	$lst = date_create( date( "Y-m-d h:i:s", $time ) );
	$diff = date_diff( $lst, $now );
	switch( $time_unit ) {
		case "y":
			$total = $diff->y + $diff->m / 12 + $diff->d / 365.25;
			$unit_name = sprintf( 'Jahr%s', 1 !== $total ? 'en' : ''  );
			break;
		case "m":
			$total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
			$unit_name = sprintf( 'Monat%s', 1 !== $total ? 'en' : ''  );
			break;
		case "d":
			$total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i/60;
			$unit_name = sprintf( 'Tag%s', 1 !== $total ? 'en' : ''  );
			break;
		case "h":
			$total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
			$unit_name = sprintf( 'Stunde%s', 1 !== $total ? 'n' : ''  );
			break;
		case "i":
			$total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
			$unit_name = sprintf( 'Minute%s', 1 !== $total ? 'n' : ''  );
			break;
	}
	return array( 'total' => round($total, 0, PHP_ROUND_HALF_DOWN), 'name' => $unit_name );
}
