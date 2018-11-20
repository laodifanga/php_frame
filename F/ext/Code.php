<?php
/**
 * @Author: [Dzero] <81951882@qq.com>
 * @Date:   2016-01-10 14:36:23
 * @Last Modified by:   [Dzero] <81951882@qq.com>
 * @Last Modified time: 2016-08-29 11:30:54
 */
CLass Code{
    public $codeFont;
    public $codeFontSize;
    public function __construct($codeFont = null,$codeFontSize = null){
        $this->codeFont = is_null($codeFont) ? DATA_FONT_PATH.C('CODE_FONT') : $codeFont;
        $this->codeFontSize = is_null($codeFontSize) ? C('CODE_SIZE') : $codeFontSize;
        if (!is_file($this->codeFont)) {
            halt("验证码字体文件不存在");
        }
    }
    /**
     * 验证码类
     * @param  integer $d_w   [画布宽度]
     * @param  integer $d_h   [画布高度]
     * @param  integer $noise [是否添加干扰]
     * @return [type]         [GIF图片]
     */
    public function code(){
        $d_w = C('CODE_WIDTH') ? : 90;
        $d_h = C('CODE_HEIGHT') ? : 40;
        //取得随机字符
        $rand = $this->getCodeRand(1);
        // 取得字体尺寸
        $info = imagettfbbox($this->codeFontSize, 0, $this->codeFont, $rand);
        $w = $info[2] - $info[0];
        $h = $info[1] - $info[5];
        //画布大小为字体尺寸，此处可以关闭
        if(C('CODE_FOLLOW')){
            $d_w = $w + 20;
            $d_h = $h + 5;
        }
        // 创建画布
        $dst = imagecreatetruecolor($d_w, $d_h);
        //颜色
        $bg = explode(',',C('CODE_BG'));
        // $randColor = imagecolorallocate($dst, mt_rand(0,120), mt_rand(0,120), mt_rand(0,120));
        // $white = imagecolorallocate($dst, mt_rand(150,255), mt_rand(150,255), mt_rand(150,255));
        $white = imagecolorallocate($dst, $bg[0],$bg[1],$bg[2]);
        // 将画布填充白色
        imagefill($dst, 0, 0, $white);
        // imagecolortransparent($dst,$white); // 背景透明

        // 添加噪点
        C('CODE_NOISE') && $this->addNoise($dst,$d_w,$d_h);
        // 将随机数写入画布
        // foreach (str_split($this->getCodeRand()) as $k => $v) {
        //     imagettftext($dst, mt_rand($this->codeFontSize - rand(0,20), $this->codeFontSize+rand(0,10)), 0, 2 + $k * rand(2,20), $h, imagecolorallocate($dst, mt_rand(200,250), mt_rand(200,250), mt_rand(200,250)), $this->codeFont, $v);
        // }
        if(C('CODE_COLORRAND')) {
            $fcolor = array(mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
        } else {
            $fcolor = explode(',',C('CODE_COLOR'));
        }
        
        foreach (str_split($rand) as $k => $v) {
            imagettftext($dst, mt_rand($this->codeFontSize + C('CODE_SIZERAND'), $this->codeFontSize + C('CODE_SIZERAND')), 0, $k*C('CODE_PADDING'), $h + C('CODE_TOP'), imagecolorallocate($dst, $fcolor[0], $fcolor[1], $fcolor[2]), $this->codeFont, $v);
        }
        
        // imagettftext($dst, mt_rand($this->codeFontSize - 2, $this->codeFontSize), 0, 2, $h + 5, $randColor, $this->codeFont, $rand);
        //写入Session
        if(!isset($_SESSION)){
            session_start();
        }
        $_SESSION['code']=$rand;
        setcookie(session_name(),session_id(), time() + 300, '/'); // 保存5分钟
        //===================
        // 输出图像
        header("Content-type:image/gif");
        imagegif($dst);
        imagedestroy($dst);
    }
    /**
     * 添加干扰
     * @param [type] $dst [description]
     * @param [type] $d_w [description]
     * @param [type] $d_h [description]
     */
    private function addNoise($dst, $d_w, $d_h){
        // for ($i=0; $i < 100; $i++) { 
        //     //点坐标
        //     $x = mt_rand(0,$d_w);
        //     $y = mt_rand(0,$d_h);
        //     //点颜色
        //     $color = imagecolorallocate($dst, mt_rand(150,200), mt_rand(150,200), mt_rand(150,200));
        //     $bcolor = imagecolorallocate($dst, mt_rand(200,250), mt_rand(200,250), mt_rand(200,250));
        //     //画点
        //     imagesetpixel($dst, $x, $y, $color);
        //     //线宽度
        //     // $wid = mt_rand(0,15);
        //     // imagesetthickness($dst, $wid);
        //     // imagefilledellipse($dst, $x, $y, $y/5, $y/5, $color);
        //     // imagearc($dst, $x, $y, $x*2, $y*2, $x*$x, $y*$y, $bcolor);
        // }
        // 画线
        for ($i=0; $i < 10 ; $i++) { 
            //线起始位置
            $bx = mt_rand(0,$d_w);
            $by = mt_rand(0,$d_h);
            //结束位置
            $ex = mt_rand($d_w/2,$d_w);
            $ey = mt_rand($d_h/2,$d_h);
            //线颜色
            $color = imagecolorallocate($dst, mt_rand(150,200), mt_rand(150,200), mt_rand(150,200));
            //线宽度
            $wid = mt_rand(0,2);
            imagesetthickness($dst, $wid);
            //画点
            imageline($dst, $bx, $by, $ex, $ey, $color);
        }
    }
    /**
     * 获得随机字符
     * @param  [type] $type [0为数字，1为数字+字母]
     * @return [type]       [description]
     */
    private function getCodeRand($type=0){
        if(!$type){
            // 生成随机数
            $rand = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
        }else{
            // 生成随机字母+数字
            $str=C('CODE_SEED');
            $arr = str_split($str);
            $total = count($arr)-1;
            $rand='';
            for($i=1;$i<=C('CODE_COUNT');$i++){
                $rand .= $arr[mt_rand(0,$total)];
            }
        }
        return $rand;
    }
}