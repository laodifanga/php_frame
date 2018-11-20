<?php
/**
 * @Author: [Dzero] <81951882@qq.com>
 * @Date:   2016-01-10 11:13:34
 * @Last Modified by:   [Dzero] <81951882@qq.com>
 * @Last Modified time: 2016-05-10 21:55:38
 */
class Tree
{
  static public function getOption($cate, $pid=0, $cid = 'cid', $pidName = 'pid', $html = '&ensp;', $level = 0)
  {
    if (!is_array($cate) || empty($cate)) return array();
    $arr = self::getLevel($cate, $pid, $cid, $pidName, $html, $level);
    foreach ($arr as $k => $v) {
      $str = "";
      if ($v['_level'] > 2) {
        for ($i = 1; $i < $v['_level'] - 1; $i++) {
          $str .= "│&ensp;";
        }
      }
      if ($v['_level'] != 1) {
        if (isset($arr[$k + 1]) && $arr[$k + 1]['_level'] >= $arr[$k]['_level']) {
          $arr[$k]['_name'] = $str . "├─ " . $v['_html'];
        } else {
          $arr[$k]['_name'] = $str . "└─ " . $v['_html'];
        }
      }
    }
    //设置主键
    $data = array();
    foreach ($arr as $d) {
      $data[$d[$cid]] = $d;
    }
    return $data;
  }
  // 获取目录树
  static public function getTree($cate, $pid=0, $cid = 'cid', $pidName = 'pid', $child = '_child')
  {
    $arr = array();
    foreach($cate as $v){
      if($v[$pidName] == $pid){
        $d = self::getTree($cate, $v[$cid], $cid, $pidName, $child);
        $d && $v[$child] = $d;
        $arr[] = $v;
      }
    }
    return $arr;
  }
  // 按照等级排
  static public function getLevel($cate, $pid=0, $cid = 'cid', $pidName = 'pid', $html = '&emsp;&emsp;', $level = 0)
  {
    $data = self::_level($cate, $pid, $cid, $pidName, $html, $level);
    if (empty($data)) return $data;
    foreach ($data as $n => $m) {
      $data[$n]['_childs'] = self::_countChild($m, $data, $cid, $pidName);
      // $data[$n]['_total'] = self::_total($m, $data);
      // if ($m['_level'] == 1) continue; // 第一层跳过
      $data[$n]['_first'] = false;
      $data[$n]['_end'] = false;
      if (!isset($data[$n - 1]) || $data[$n - 1]['_level'] != $m['_level']) {
        $data[$n]['_first'] = true;
      }
      if (isset($data[$n + 1]) && $data[$n]['_level'] < $data[$n + 1]['_level']) {
        $data[$n]['_end'] = true;
      }
    }
    return $data;
  }
  static public function _countChild($field = array(), $data = array(), $cid, $pidName){ // 获取下属个数
    $count = 0;
    foreach ($data as $v) {
      if($v[$pidName] == $field[$cid] && $v['_level'] == $field['_level']+1) $count ++;
    }
    return $count;
  }
  static public function _total($field = array(), $data = array()){ // 获取同级个数
    $count = 0;
    foreach ($data as $v) {
      if($v['_level'] == $field['_level']) $count ++;
    }
    return $count;
  }
  // 按照等级排私有方法
  static public function _level($cate, $pid=0, $cid = 'cid', $pidName = 'pid', $html = '&emsp;&emsp;', $level = 0)
  {
    $arr = array();
    foreach($cate as $k=>$v){
      if($v[$pidName] == $pid){
        if($level == 0) $v['isShow'] = true;
        $v['_level'] = $level + 1;
        $v['_html'] = str_repeat($html, $level);
        $arr[] = $v;
        $arr = array_merge($arr, self::_level($cate, $v[$cid], $cid, $pidName, $html, $level+1));
      }
    }
    return $arr;
  }
  // 获取父级，用于面包屑
  static public function getParents($cate, $pid=0, $cid = 'cid', $pidName = 'pid')
  {
    $arr = array();
    foreach($cate as $v){
      if($v[$cid] == $pid){
        $arr[] = $v;
        $arr = array_merge(self::getParents($cate, $v[$pidName], $cid, $pidName), $arr);
      }
    }
    return $arr;
  }
  // 获取所有子分类id
  static public function getChildId($cate, $pid=0, $cid = 'cid', $pidName = 'pid')
  {
    $arr = array();
    foreach($cate as $v){
      if($v[$pidName] == $pid){
        $arr[] = $v[$cid];
        $arr = array_merge($arr, self::getChildId($cate, $v[$cid], $cid, $pidName));
      }
    }
    return $arr;
  }
  // 获取所有子分类
  static public function getChilds($cate, $pid=0, $cid = 'cid', $pidName = 'pid')
  {
    $arr = array();
    foreach($cate as $v){
      if($v[$pidName] == $pid){
        $arr[] = $v;
        $arr = array_merge($arr, self::getChilds($cate, $v[$cid], $cid, $pidName));
      }
    }
    return $arr;
  }
}