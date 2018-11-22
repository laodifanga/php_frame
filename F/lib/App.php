<?php

/**
 * @Author: LDF
 * @Date:   2018-11-15 13:43:27
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-22 09:32:58
 */
final class App {
  static public function start() {
    date_default_timezone_set( Config::get('timezone') );

    self::setDefine();
    self::loadBaseFile();

    if( !is_dir(APP_PATH) ) { // 自动创建基本文件
      Dir::icopy(TEMPLATE_PATH, APP_PATH);
    }
    self::setTmp();
    self::run();

    if(DEBUG) Debug::fileList(File::get());
  }

  // 设置常量
  static private function setDefine() {
    define('POST', strtolower($_SERVER['REQUEST_METHOD']) === 'post');
    define('GET', strtolower($_SERVER['REQUEST_METHOD']) === 'get');
    define('AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    $http = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) ? 'https' : 'http';
    define('HOST', $http.'://'. $_SERVER['HTTP_HOST']);

    define('VIEW_PATH', MODULE_PATH. M. DS. Config::get('dirname')['view']. DS);
    define('TPL_PATH', VIEW_PATH. C. DS);
  }

  // 加载配置文件&公共函数
  static private function loadBaseFile() {
    File::load(APP_PATH. 'base'. DS. Config::get('function_name'). EXT);
  }

  static private function setTmp() { // session在不同modules下不通用
    define('TMP_PATH', APP_PATH. 'tmp'. DS);
    define('SESSION_PATH', TMP_PATH. M. DS. 'session'. DS);

    is_dir(SESSION_PATH) || mkdir(SESSION_PATH, 0755, true);

    session_save_path(SESSION_PATH);
    session_name('F_SESSION_'. date('Y'));
    @session_start();
  }

  static private function run() {
    $ctrlName = ucfirst(C).'C';
    $Ctrl = new $ctrlName();
    if( !method_exists($Ctrl, A) ) throw new Exception(M. DS. $ctrlName.EXT. ' 方法'. A. '不存在。');

    $Ctrl->data = Request::get();
    if(!empty($Ctrl->rules)) { // 入参检查
      Rule::check($Ctrl->data, $Ctrl->rules, A);
    }

    $autoMethods = ['__init', '__auto', A];
    foreach ($autoMethods as $auto) { // 自动运行方法
      if(method_exists($Ctrl, $auto)) self::_parseData($Ctrl->$auto());
    }
  }

  // 处理返回数据为JSON
  static private function _parseData($data) {
    if( !is_null($data) ) {
      $res = ['data' => $data, 'code' => 0, 'info' => 'success'];
      if(is_array($data) && isset($data['data'])) {
        $res = array_merge($res, $data);
      }
      die(json_encode($res, true, 256));
    }
  }
}