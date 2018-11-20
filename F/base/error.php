<?php
/**
 * @Author: LDF
 * @Date:   2018-11-19 16:04:24
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-20 09:54:55
 */
error_reporting(0);
if(!DEBUG) return;
register_shutdown_function('shutdown_handler'); // 系统级错误
set_error_handler('error_handler'); // try catch之外
set_exception_handler('exception_handler'); // try catch

function exception_handler(Exception $e) {
  // $e->getTrace();
  // print_r($e->getTrace());
  showErrorHtml($e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode(), $e->getTrace());
}

function error_handler($type, $message, $file, $line) {
  throw new Exception($message);
}

function shutdown_handler() {
  $error = error_get_last();
  !is_null($error) && showErrorHtml($error['message'], $error['file'], $error['line'], $error['type']);
}

function showErrorHtml($msg = '', $file = '', $line = 0, $code = 0, $trace = []) {
  extract([$msg, $file, $line, $code, $trace]);
  require_once(__DIR__. DS. 'error.html');
}


