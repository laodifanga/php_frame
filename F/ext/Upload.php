<?php
/**
 * @Author: [LDF] <47121862@qq.com>
 * @Date:   2015-07-20 21:46:49
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-18 19:09:24
 */
class Upload
{
	public static $limitSize = 2097152; // 2M
	public static $limitType = 'jpg|jpeg|gif|png|'; // 文件格式
	public static $error = array();
	public static $dir = './upload/';
	public static function save($dir = './upload/')
	{
		$data = array();
		$dir  = (strrchr($dir,'/') == '/') ? $dir : $dir.'/';
		$dir .= date('Y-m-d').'/';
		is_dir($dir) || mkdir($dir, 0755, true);
		$files = self::fileHandle();
		foreach ($files as $f) {
			$data[] = self::move($f, $dir);
		}
		$data = count($data) > 1 ? $data : current($data);
		return $data;
	}
	// 移动文件
	private static function move($file, $dir)
	{
		$to = $dir.time().mt_rand(1, 9999). '.'. $file['extension'];
		$file['time'] = time();
		$file['date'] = date('Y-m-d H:i:s');
		$file['file'] = $to;
		move_uploaded_file($file['tmp_name'], $to);
		return $file;
	}
	// 处理表单转换为统一数组
	private static function fileHandle()
	{
		$files = array();
		foreach ($_FILES as $k => $v) {
			if(is_array($v['name'])){
				foreach ($v['name'] as $key => $n) {
					$arr = array();
					$arr['name']     = $n;
					$arr['type']     = $v['type'][$key];
					$arr['tmp_name'] = $v['tmp_name'][$key];
					$arr['error']    = $v['error'][$key];
					$arr['size']     = $v['size'][$key];
					$files[] = $arr;
				}
			}else{
				$files[] = $v;
			}
		}

		return self::filter($files);
	}
	private static function getFileErrCode($file){
		switch ($file['error']) {
			 case 1:
				self::$error[] = array('name'=>$file['name'], 'info'=>'文件大小超出了服务器的空间大小');
				break;
			 case 2:
				self::$error[] = array('name'=>$file['name'], 'info'=>'要上传的文件大小超出浏览器限制');
				break;
			 case 3:
				self::$error[] = array('name'=>$file['name'], 'info'=>'文件仅部分被上传');
				break;
			 case 4:
				self::$error[] = array('name'=>$file['name'], 'info'=>'没有找到要上传的文件');
				break;
			 case 5:
				self::$error[] = array('name'=>$file['name'], 'info'=>'服务器临时文件夹丢失');
				break;
			 case 6:
				self::$error[] = array('name'=>$file['name'], 'info'=>'文件写入到临时文件夹出错');
				break;
		}
	}
	// 过滤数组 并记录错误信息
	private static function filter($files = array())
	{
		$result = array();
		$limitType = explode('|', self::$limitType);

		foreach ($files as $k => $v) {
			if( $v['error'] > 0) {
				self::getFileErrCode($v);
				continue;
			}
			if( !is_uploaded_file($v['tmp_name']) ) {
				self::$error[] = array('name'=>$v['name'], 'info'=>'非法上传');
				continue;
			}
			if( $v['size'] > self::$limitSize) {
				self::$error[] = array('name'=>$v['name'], 'info'=>'超过大小');
				continue;
			}
			$type = pathinfo($v['name']);
			if(!isset($type['extension']) || !in_array($type['extension'], $limitType)) {
				self::$error[] = array('name'=>$v['name'], 'info'=>'格式错误');
				continue;
			}
			$v['extension'] = $type['extension'];
			$result[] = $v;
		}
		return $result;
	}
	// 获取错误信息
	public static function getError ()
	{
		return self::$error;
	}
	// 删除图片
	public static function del($file = null){
		if(is_null($file)) return false;
		return unlink('../upload/'.$file);
	}
}