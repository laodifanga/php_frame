<?php

/**
 * @Author: LDF
 * @Date:   2018-11-19 10:55:23
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 11:21:13
 */
class Config {
  static public $data = [];
  static public function set($name = null, $val = null) {
    if(is_null($name)) return;
    if(is_array($name)) {
      return self::$data = array_merge(array_change_key_case(self::$data, CASE_LOWER), array_change_key_case($name, CASE_LOWER));
    }
    return self::$data[strtolower($name)] = $val;
  }

  static public function get($name = null) {
    if(is_null($name)) return self::$data;
    if(!isset(self::$data[strtolower($name)])) return;
    return self::$data[strtolower($name)];
  }

  static public function del($name = null) {
    if(is_null($name) || isset(self::$data[$name]) ) return;
    unset(self::$data[$name]);
    return true;
  }
}