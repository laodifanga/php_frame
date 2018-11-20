<?php
// 配置文件 转为Config
// function C($name = null, $val = null) {
//   static $config = [];
//   $config = array_change_key_case($config, CASE_UPPER);
//   if(is_null($name)) return $config;
//   if(is_array($name)) return $config = array_merge($config, $name);
//   if(is_null($val))  return isset($config[strtoupper($name)]) ? $config[strtoupper($name)] : null;
//   $config[$name] = $val;
// }

// 带缓存引入文件 转为File
// function import($file = null, $vars = null) {
//   static $files = [];
//   if(is_null($file)) return $files;

//   if(!isset($files[$file]) && is_file($file)) {
//     $files[$file] = $file;
//     if(!is_null($vars)) {
//       extract($vars);
//     }
//     return require_once($file);
//   }
//   return false;
// }

// 打印
function P($str = null) {
  if(is_null($str) || is_bool($str)) {
    return var_dump($str);
  }
  $args = func_get_args();
  foreach ($args as $arg) {
    echo '<pre style="line-height: 1; background: #eee;">'. print_r($arg, 1) .'</pre>';
  }
}

// 获取所有自定义常量
function getAllDefine($print = null) {
  $user = get_defined_constants(1)['user'];
  if($print) return $user;
  P($user);
}


function runtime($start = null, $end = null) { // 运行时间
  static $t = array();
  if(is_null($end)){
    $t[$start] = microtime(1);
  }else{
    return round((microtime(1) - $t[$start]), 4);
  }
}
/**
 * 运行时间
 * @param [type] $end  [description]
 * @param string $name [description]
 */
function R($end = null, $name = 'start') { // 运行时间
  $totalTime = runtime($name, $end);
  return $end ? "运行花费时间共 {$totalTime} 毫秒" : $totalTime;
}

