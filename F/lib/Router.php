<?php

/**
 * @Author: LDF
 * @Date:   2018-11-15 14:29:01
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-22 09:36:30
 */
class Router {
  static public function parse() {
    if(is_null(PATHINFO)) return;
    $params = explode('/', trim(PATHINFO, '/'));
    if(count($params) < 3) throw new \Exception('参数错误');
    $gets = ['m', 'c', 'a'];
    foreach ($gets as $k => $getName) { // 设置 mca
      $_GET[$getName] = $params[$k];
      unset($params[$k]);
    }

    if(count($params) > 1) { // 设置参数a/1/b/2为a=1&b=2方式
      $tmp = [];
      foreach ($params as $k => $p) {
        if($k % 2) {
          $tmp[] = $p;
          unset($params[$k]);
        }
      }
      foreach (array_values($params) as $k => $p) {
        $_GET[$tmp[$k]] = $p;
      }
    }

    $_REQUEST = array_merge($_REQUEST, $_GET);
  }
}