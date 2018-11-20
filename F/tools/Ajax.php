<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 14:26:10
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 11:23:09
 */
Class Ajax {
  static public function show($code = 0, $info = 'success', $data = [], $ext = []) {
    header('content-type:text/json; charset=utf-8');
    $res = ['code' => $code, 'info' => $info, 'data' => $data];
    $res = array_merge($res, $ext);
    if( Config::get('RETURN_TYPE') !== 'JSON' ) {
      exit(print_r($res, 1));
    }
    die(json_encode($res, 256));
  }
}