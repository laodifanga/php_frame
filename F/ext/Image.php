<?php
/**
 * @Author: [Dzero] <81951882@qq.com>
 * @Date:   2016-01-10 14:24:12
 * @Last Modified by:   [Dzero] <81951882@qq.com>
 * @Last Modified time: 2016-01-10 14:35:39
 */
/**
 * 图片处理类
 * $img = new Image();
 * $img->code();
 * $img->verify();
 */
class Image{
    public function __call($name,$arguments){
        half($name.'('.implode(',', $arguments).')方法不存在');
    }
    public $water_fontSize; //水印字体大小 0为随机大小
    public $water_fontTtf;  //水印字体
    public $water_fontText; //水印文字内容
    public $water_color;    //  水印文字颜色0白色，1为开启随机色
    public $water_pic;  //水印图片地址
    public $water_picOpa;   //水印是否透明，默认不透明
    /**
     * 构造函数
     * @param [type] $water_fontSize [水印字体大小 0为随机大小]
     * @param [type] $water_color    [水印文字颜色0白色，1为开启随机色]
     * @param [type] $water_fontText [水印文字内容]
     * @param [type] $water_fontTtf  [水印字体]
     * @param [type] $water_pic      [水印图片地址]
     * @param [type] $water_picOpa   [水印图片透明度]
     */
    public function __construct($water_fontSize=null,$water_color=null,$water_fontText=null,$water_fontTtf=null,$water_pic=null,$water_picOpa=null){
        $this->water_fontSize = $water_fontSize ? $water_fontSize : C('WATER_SIZE');
        $this->water_color = $water_color ? $water_color : C('WATER_COLOR');
        $this->water_fontText = $water_fontText ? $water_fontText : C('WATER_TEXT');
        $this->water_fontTtf = $water_fontTtf ? $water_fontTtf : DATA_FONT_PATH.C('water_font');
        $this->water_pic = $water_pic ? $water_pic : DATA_IMAGE_PATH.C('WATER_PIC');
        $this->water_picOpa = $water_picOpa ? $water_picOpa : C('WATER_OPA');
    }
    /**
     * 缩略图调用函数
     * @param  [type] $img [description]
     * @param  [type] $t_w [description]
     * @param  [type] $t_h [description]
     * @return [type]      [description]
     */
    public function thumb($img,$t_w,$t_h){
        //环境与文件检测
        if(!$this->check($img)) return false;
        //取得图片尺寸
        $info = $this->getImageInfo($img,$t_w,$t_h);
        //定义画布
        $dst = imageCreateTrueColor($t_w,$t_h); //创建截取画布
        $src = $info['creatFun']($img);         //创建需要截取图片画布
        //生成缩略图
        imagecopyresized($dst, $src,0,0,0,0,$t_w,$t_h,$info['cut_w'],$info['cut_h']);
        //保存图像
        $name = pathinfo($img);
        $to = $name['dirname'].'/'.$name['filename'].'_thumb'.$t_w.$t_h.'.'.$name['extension'];
        // return $info['mime']($dst,$to);
        return $info['mime']($dst,$to)?$to:false; //返回缩略图名称
        imagedestroy($dst); //释放资源
    }
    
    /**
     * 图片加水印调用函数
     * @param  [type]  $img [description]
     * @param  integer $type [1文字 2图片 ]
     * @param  integer $pos  [位置 9宫格]
     * @return [type]        [description]
     */
    public function water($img,$type=1,$pos=9){
        //检测环境
        if(!$this->check($img)) return false;
        //获取图片信息
        $info = $this->getImageInfo($img);
        $dst = $info['creatFun']($img); //  创建图片画布
        if($type==1){   //文字水印
            //文字颜色
            $col = $this->water_color?mt_rand(50,255).','.mt_rand(50,255).','.mt_rand(50,255):'240,240,240';
            //文字大小
            $fontSize = $this->water_fontSize?$this->water_fontSize:mt_rand(12,32);
            $c = explode(',',$col);
            $color = imagecolorallocate($dst, $c[0],$c[1],$c[2]);   //随机颜色
            $textinfo = imagettfbbox($fontSize, 0, $this->water_fontTtf, $this->water_fontText);    //获得字体尺寸信息
            $w = $textinfo[2] - $textinfo[0] +20;
            $h= $textinfo[1] - $textinfo[5] +20;
            //获取文字在图片中的位置
            $pos = $this->get_pos($info['w'],$info['h'],$w,$h,$pos);
            if(C('WATER_ISOPA')){ //是否开启文字透明
                // 定义一个画布===文字透明
                $res = imagecreatetruecolor($w, $h);
                //定义随机颜色
                $rand = imagecolorallocate($res, mt_rand(0,100),mt_rand(0,100),mt_rand(0,00));
                //填充画布颜色为随机颜色
                imagefill($res, 0, 0, $rand);
                //写入文字
                imagettftext($res, $fontSize, 0, 5,$h-22, $color, $this->water_fontTtf, $this->water_fontText);
                //合并图像
                imagecopymerge($dst, $res, $pos[0], $pos[1], 0, 0, $w, $h, C('WATER_TEXTOPA'));
            }else{
                //文字写到画布
                imagettftext($dst, $fontSize, 0, $pos[0], $pos[1], $color, $this->water_fontTtf, $this->water_fontText);    
            }
        }else{  //图片水印
            $srcinfo = $this->getImageInfo($this->water_pic);
            $src = $srcinfo['creatFun']($this->water_pic);
            $pos = $this->get_pos($info['w'],$info['h'],$srcinfo['w'],$srcinfo['h'],$pos);
            //合并图像，并判断是否透明
            $this->water_picOpa?imagecopymerge($dst, $src, $pos[0], $pos[1], 0, 0, $srcinfo['w'], $srcinfo['h'], $this->water_picOpa):imagecopy($dst, $src, $pos[0], $pos[1], 0, 0, $srcinfo['w'], $srcinfo['h']);
            imagedestroy($src); //释放资源
        }
        // imagesavealpha($dst, true);
        //存储图片
        return $info['mime']($dst,$img);
        imagedestroy($dst); //释放资源
    }
    /**
     * 获得图片信息
     * @param  [type] $img [图片地址]
     * @param  [type] $t_w [截取宽度]
     * @param  [type] $t_h [截取高度]
     * @return [type]    返回可用数组
     */
    private function getImageInfo($img,$t_w=null,$t_h=null){
        $data = null;
        $info = getimagesize($img);
        $data['w'] = $info[0];
        $data['h'] = $info[1];
        $data['cut_w'] = $info[0];
        $data['cut_h'] = $info[1];
        //输出图片的函数
        $data['mime'] = str_ireplace('/','',$info['mime']);
        if($t_w&&$t_h){
            //裁剪规则：裁减掉比例大的
            if($info[0]/$t_w>$info[1]/$t_h){
            $data['cut_w'] = $t_w*($info[1]/$t_h);
            }else{
                $data['cut_h'] = $t_h*($info[0]/$t_w);
            }
        }
        //判断打开的图像文件该用哪一个函数创建画布
        switch ($info[2]) {
            case 1: $data['creatFun'] = 'imageCreateFromGif'; break;
            case 2: $data['creatFun'] = 'imageCreateFromJpeg'; break;
            case 3: $data['creatFun'] = 'imageCreateFromPng'; break;
            case 6: $data['creatFun'] = 'imageCreateFromBmp'; break;
            default: $data['creatFun'] = 'imageCreateFromGd';
        }
        return $data;
    }
    /**
     * 检测环境
     * @param  [type] $img [判断文件是否存在]
     * @return [type]      [description]
     */
    private function check($img){
        return extension_loaded("GD") && is_file($img) && getimagesize($img);
    }
    /**
     * 获取水印的位置(图片水印都可以操作)
     * @param  [type] $img_w   [原图宽度]
     * @param  [type] $img_h   [原图高度]
     * @param  [type] $water_w [水印宽度]
     * @param  [type] $water_h [水印高度]
     * @param  [type] $pos     [位置9宫格，默认为右下角10为随机位置]
     * @return [type]          [description]
     */
    private function get_pos($img_w,$img_h,$water_w,$water_h,$pos){
        $x = $y = 20;
        switch ($pos) {
            case 1: break;
            case 2: $x = ($img_w-$water_w)/2; break;
            case 3: $x = $img_w-$water_w; break;
            case 4: $y = ($img_h-$water_h)/2; break;
            case 5:
                $x = ($img_w-$water_w)/2;
                $y = ($img_h-$water_h)/2;
                break;
            case 6:
                $x = $img_w-$water_w;
                $y = ($img_h-$water_h)/2;
                break;
            case 7: $y = $img_h-$water_h; break;
            case 8:
                $x = ($img_w-$water_w)/2;
                $y = $img_h-$water_h;
                break;
            case 9:
                $x = $img_w-$water_w-10; 
                $y = $img_h-$water_h-10; 
                break;
            default:
                $x = mt_rand(10,$img_w-$water_w-10); 
                $y = mt_rand(10,$img_h-$water_h-10); 
                break;
        }
        return array($x,$y);
    }
}