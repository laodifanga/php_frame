<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 11:39:24
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-16 16:38:10
 */
class Request {
  static public function get($name = null, $val = null, $filter = 'trim|addslashes') {
    // $req = &$_REQUEST;
    $req = $_REQUEST; // 保留原始 _REQUEST
    $input = json_decode(file_get_contents('php://input'), true);
    if(!empty($req) && !empty($input)) {
      $req = array_merge($req, $input);
    }

    if(is_null($name)) {
      foreach ($req as $n => &$r) {
        if(in_array($n, ['m', 'c', 'a'])) { // 去除mca
          unset($req[$n]);
        }
        $r = self::filter($r, $filter);
      }
      return $req;
    }
    if(isset($req[$name])) return self::filter($req[$name], $filter);
    return $val;
  }

  static public function set($name = null, $val = null) {
    $_REQUEST[$name] = $val;
    return $val;
  }

  static public function filter($name = null, $filter = 'trim|addslashes') {
    if(is_null($name)) return $name;
    $filterArr = is_array($filter) ? $filter : explode('|', $filter);
    foreach ($filterArr as $f) {
      if(is_array($name)) continue;
      $name = $f($name);
    }
    return $name;
  }
}