<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 09:45:50
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-16 09:59:23
 */
Class Cookie {
  static public function set($name = null, $id = null, $expire = 0, $path = '/', $domain = '', $secure = false) {
    if($expire === 0) return;
    if(is_null($name)) {
      $name = session_name();
    }
    if(is_null($id)) {
      $id = session_id();
    }
    setcookie($name, $id, time() + $expire, $path, $domain, $secure);
  }

  static public function get($name = null) {
    $cookie = $_COOKIE;
    return is_null($name) ? $cookie : (isset($cookie[$name]) ? $cookie[$name] : null);
  }
}