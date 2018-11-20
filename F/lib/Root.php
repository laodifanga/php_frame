<?php

/**
 * @Author: LDF
 * @Date:   2018-11-14 16:27:01
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-20 10:49:13
 */
final class Root {
  static public function start() {
    header('content-type:text/html;charset=utf8');

    self::setDefine();
    self::loadBaseFile();

    File::load(LIB_PATH. 'Router'. EXT); // 路由
    Router::parse();

    self::setMCA();

    spl_autoload_register('self::autoload');
    App::start();

  }

  // 加载函数与基础配置
  static public function loadBaseFile() {
    $baseFn = BASE_PATH. 'fn'. EXT;
    $baseFile = TOOLS_PATH. 'File'. EXT; // 文件加载类
    $baseConfig = TOOLS_PATH. 'Config'. EXT; // 配置类
    require_once($baseFile);

    File::load([$baseFn, $baseConfig, $baseFile]);
    Config::set( File::load(BASE_PATH. 'config'. EXT) ); // 加载 默认config文件

    if(is_dir(APP_PATH)) { // 加载 app内config文件
      Config::set( File::load(APP_PATH. 'base'. DS. 'config'. EXT) );
      $extendFiles = Config::get('EXTEND_FILES') ? explode(',', Config::get('EXTEND_FILES')) : [];
      foreach ($extendFiles as $f) { // 扩展文件
        Config::set(File::load(APP_PATH. 'base'. DS. $f. EXT));
      }
    }
  }

  // 设置常量
  static public function setDefine() {
    define('BASE_PATH', F_PATH. 'base'. DS);
    define('LIB_PATH', F_PATH. 'lib'. DS);
    define('TOOLS_PATH', F_PATH. 'tools'. DS);
    define('TEMPLATE_PATH', F_PATH. 'template'. DS);
    define('EXT_PATH', F_PATH. 'ext'. DS);
    define('ASSETS_PATH', F_PATH. 'assets'. DS);
    define('PATHINFO', isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null);
  }

  // 设置 M C A目录
  static public function setMCA() {
    $m  = isset($_GET['m']) ? $_GET['m'] : Config::get('module_default');
    $c = isset($_GET['c']) ? $_GET['c'] : Config::get('controller_default');
    $a  = isset($_GET['a']) ? $_GET['a'] : Config::get('action_default');
    define('M', strtolower($m));
    define('C', $c);
    define('A', strtolower($a));

    define('MODULE_PATH', APP_PATH. Config::get('dirname')['module']. DS);
    define('M_PATH', MODULE_PATH. M. DS);
  }

  // 自动加载
  static public function autoload($name = '') {
    $dirs = [];
    $frameDirs = ['ext', 'tools', 'lib'];
    $appDirs = explode(',', Config::get('dirname')['autoload']);
    $appDirs[] = Config::get('dirname')['module']. DS. M. DS . Config::get('dirname')['controller'];
    $appDirs[] = Config::get('dirname')['model'];
    $appDirs[] = Config::get('dirname')['module']. DS. M. DS . Config::get('dirname')['model'];

    foreach ($appDirs as $a) {
      array_unshift($dirs, APP_PATH. $a. DS);
    }

    foreach ($frameDirs as $f) {
      array_unshift($dirs, F_PATH. $f. DS);
    }

    // p($dirs);

    $re = false;
    foreach ($dirs as $dir) {
      $file = $dir. $name. EXT;
      if($re = File::load($file)) break; // 找到文件为止
    }

    // echo $name;

    if(!$re) throw new \Exception(M. DS .$name. EXT. ' 丢失。');
  }
}