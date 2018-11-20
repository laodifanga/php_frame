<?php
/**
 * @Author: [Dzero] <81951882@qq.com>
 * @Date:   2016-01-10 14:47:10
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-15 14:07:38
 */
Class Dir{
    public static function __callstatic($name,$arguments){
        die($name.'('.implode(',', $arguments).')方法不存在');
    }
    /**
     * 创建目录
     * @param  [type]  $dir  [目录/文件名]
     * @param  integer $auth [权限]
     * @return [type]        [description]
     */
    static public function create($dir,$auth=0755){
        $dir = str_ireplace('\\','/',$dir);
        $dir = explode('/',$dir);
        $d = '';
        foreach ($dir as $v) {
            $d.=$v.'/';
            is_dir($d) or mkdir($d,$auth);
        }
    }
    /**
     * 复制目录
     * @param  [type] $source [description]
     * @param  [type] $to     [description]
     * @return [type]         [description]
     */
    static public function icopy($source, $to){
        //目标目录不存在就创建
        is_dir($to) || mkdir($to,0755,true);
        if(is_file($source)){
             copy($source,$to.'/'.$source);
        }else{
            $source=str_replace('\\', '/', $source);
            $source = (strchr($source,'/'))=='/' ? $source : $source.'/';
            //判断源目录存在否
            if(!is_dir($source)) return false;
            //将源目录文件复制到目标目录
            foreach (glob($source.'*') as $f) {
                $todir = $to.'/'.basename($f);
                is_dir($f) ? self::icopy($f,$todir) : copy($f,$todir);
            }
        }
    }
    /**
     * 删除目录或文件
     * @param  [type] $file [目录/文件名]
     * @return [type]       [description]
     */
    static public function del($file){
        if(!file_exists($file)) return true;
        if(is_file($file)){
            return @unlink($file);
        }else{
            foreach (glob($file.DIRECTORY_SEPARATOR.'*') as $f) {
                is_file($f)?@unlink($f):self::del($f);
            }
            return rmdir($file);
        }
    }
    /**
     * 移动目录/文件夹
     * @param  [type] $res_dir [description]
     * @param  [type] $to_dir  [description]
     * @return [type]          [description]
     */
    static public function move($res_dir,$to_dir){
        self::icopy($res_dir,$to_dir);
        self::del($res_dir);
    }
}
