<?php

/**
 * @Author: LDF
 * @Date:   2018-11-15 17:41:38
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-20 10:32:16
 */
class Debug {
  public static function fileList($cacheFiles = []){
    $str = '<style>
      .debugOut { position: fixed; bottom: 1em; right: 1em; background: #fafafa; line-height: 1.4; color: gray; font-size: 12px; box-shadow: .25em .25em .5em rgba(0,0,0,.3); width: 50em; max-height: 30em; overflow: auto; border: 1px solid #198599; }
      .debugOut.hide {width: 0; height: 0; background: none; border: 1.5em solid transparent; border-right-color: #198599; box-shadow: none; bottom: 0; right: 0;}
      .debugOut.hide .tools{background: none;}
      .debugOut .tools {background: #198599; color: #eee; box-shadow: 0 .25em .5em rgba(0,0,0,.25); position: relative;}
      .debugOut .tools span{display: inline-block; padding: .5em .75em; cursor: pointer; }
      .debugOut .tools span.act{background: #fff; color: #198599;}
      .debugOut .cont {height: 25em; overflow: auto; display: none; }
      .debugOut .cont.act {display: block;}
      .debugOut .cont .wrap {padding: .5em; border-bottom: 1px solid #eee; font-weight: 400; }
      .debugOut .cont .wrap.app {font-weight: 700; }
      .debugOut .cont .wrap:nth-child(2n) {background: #fff; }
      .debugOut .cont .wrap .key{color: #fff; display: inline-block; background: #198599; border-radius: .2em; padding: .25em; line-height: 1.2; margin-right: .25em;}

      .debugOut .cont::-webkit-scrollbar-track-piece {background:#eee; }
      .debugOut .cont::-webkit-scrollbar {width: .5em;}
      .debugOut .cont::-webkit-scrollbar-thumb {background:rgba(0,0,0,.25);}
      .debugOut .cont::-webkit-scrollbar-thumb:hover {background:#198599; }
    </style>';
    $cacheFiles = get_included_files();
    $cacheFile = '';
    foreach ($cacheFiles as $i => $file) {
      $app = strstr($file, F_PATH) ? '' : 'app';
      $cacheFile .= '<div class="wrap '. $app .'">['. ($i+1). '] '. $file .'</div>';
    }
    // $cacheFile = '';
    // foreach ($cacheFiles as $file => $c) {
    //   $app = $c ? 'app' : '';
    //   $cacheFile .= '<div class="wrap '. $app .'">'. $file .'</div>';
    // }

    $allDefine = getAllDefine(true);
    $defines = '';
    foreach ($allDefine as $name => $c) {
      $defines .= '<div class="wrap"><span class="key">'. $name. '</span>'. $c .'</div>';
    }

    $allConfig = Config::get();
    $config = '';
    foreach ($allConfig as $name => $c) {
      $c = is_array($c) ? print_r($c, 1) : $c;
      $config .= '<div class="wrap"><span class="key">'. $name. '</span>'. $c .'</div>';
    }


    $session = '';
    foreach (Session::get() as $name => $c) {
      $session .= '<div class="wrap"><span class="key">'. $name. '</span>'. $c .'</div>';
    }

    $base = [
      '请求信息' => date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['REQUEST_METHOD'].' '.__FILE__,
      '运行时间' => round(microtime(true) - BEGIN_RUNTIME, 6). 'ms',
      '内存开销' => round((memory_get_usage() - BEGIN_MEMORY)/1024, 4). 'K',
      '文件加载' => count(get_included_files()). '个',
      '会话信息' => 'session_id='. session_id(). 'session_name='. session_name(),
      '通信协议' => $_SERVER['SERVER_PROTOCOL'],
      '云端信息' => $_SERVER['SERVER_SOFTWARE'],
      '页面大小' =>  number_format(filesize(__FILE__)/1024,2).'KB',
      '用户代理' => $_SERVER['HTTP_USER_AGENT'],
    ];

    $trace = '';
    foreach ($base as $name => $c) {
      $trace .= '<div class="wrap"><span class="key">'. $name. '</span>'. $c .'</div>';
    }


    echo $str."<div class=\"debugOut hide\">
      <div class=\"tools\" id=\"debugTools\"><span class=\"act\" >已载文件</span><span>系统常量</span><span>配置信息</span><span>SESSION</span><span>TRACE</span></div>
      <div class=\"cont act\">{$cacheFile}</div>
      <div class=\"cont\">{$defines}</div>
      <div class=\"cont\">{$config}</div>
      <div class=\"cont\">{$session}</div>
      <div class=\"cont\">{$trace}</div>
    </div>";
    echo '<script>
      let debugOut = document.querySelector(".debugOut")
      let debugTools = document.querySelector("#debugTools");
      let spans = debugTools.querySelectorAll("span");
      let conts = debugOut.querySelectorAll(".cont");
      debugOut.onclick = function() {
        this.classList.toggle("hide");
      }
      spans.forEach((span, i) => {
        span.onclick = function(e) {
          e.stopPropagation();
          spans.forEach(c => c.classList.remove("act"));
          this.classList.add("act");
          conts.forEach(c => c.classList.remove("act"));
          conts[i].classList.add("act");
        }
      })
      conts.forEach(c => {
        c.onclick = function(e) {
          e.stopPropagation();
        }
      })
    </script>';
  }
}