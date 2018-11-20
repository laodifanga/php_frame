<?php

/**
 * @Author: LDF
 * @Date:   2018-11-14 16:10:34
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-20 10:18:49
 */
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');
define('VERSION', '1.0.0');
define('BEGIN_RUNTIME', microtime(true));
define('BEGIN_MEMORY', memory_get_usage());

defined('DEBUG') || define('DEBUG', false);
defined('APP_PATH') || define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');

define('EXT', '.php');
define('DS', DIRECTORY_SEPARATOR);
define('F_PATH', __DIR__. DS);

require F_PATH. 'base'. DS. 'error'. EXT; // 错误处理
require F_PATH. 'lib'. DS. 'Root'. EXT;
Root::start();



