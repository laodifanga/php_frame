<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 09:08:52
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-16 09:45:13
 */
class Session {
  /**
   * [set 获取session]
   * @param [type] $name [session名称]
   * @param [type] $val  [null时为删除]
   */
  public static function set($name = null, $val = null) {
    if(is_null($name)) return;

    if(is_null($val)) {
      unset($_SESSION[$name]);
      return;
    }

    $_SESSION[$name] = $val;
  }

  public static function get($name = null) {
    $session = $_SESSION;
    return is_null($name) ? $session : (isset($session[$name]) ? $session[$name] : null);
  }

  public static function destory() {
    session_unset();
    session_destroy();
  }
}