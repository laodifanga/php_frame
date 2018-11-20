<?php
/**
 * @Author: [LDF] <47121862@qq.com>
 * @Date:   2015-07-19 12:31:46
 * @Last Modified by:   [LDF] <47121862@qq.com>
 * @Last Modified time: 2015-11-24 17:22:22
 * 分页类
 */
class Page
{
	public $total; 				// 总条数
	public $limit = 10; 	// 每页显示条数
	public $currentPage; 	// 当前页
	public $url; 					// 链接
	public $totalPage; 		// 总页数
	public $pages; 				// 显示页数
	public $offset; 			// 偏移量
	public $style = 0; 		// 分页风格 0 完整 1简单 2简单带页数
	public function __construct($total = 0, $limit = 10, $pages = 5)
	{
		$this->total       = $total;
		$this->limit       = $limit;
		$this->totalPage   = ceil($total/$limit);
		$this->pages       = $pages;
		$this->offset      = floor($this->pages/2);
		$this->currentPage = isset($_GET['p']) ? $_GET['p'] : 1;
		$this->getUrl();
	}
	function getUrl()
	{
		$get = $_GET;
		if(isset($get['p'])) unset($get['p']);
		$str = '';
		foreach($get as $n=>$g) {
			if(in_array($n, array('m','c','a'))) continue;
			$str .= "$n/$g/";
		}
		// $this->url = __WEB__."?{$str}p=";
		$this->url = __WEB__.'/'.strtolower(MODULE).'/'.strtolower(CONTROL).'/'.strtolower(ACTION)."/{$str}p/";
		// $this->url = preg_replace('/p\/\d+/isU', 'p/'.$this->currentPage, __SELF__);
	}
	function prePage()
	{
		$page = $this->currentPage - 1;
		return $this->currentPage > 1 ? "<a href='{$this->url}1'>首页</a> <a href='{$this->url}{$page}'>上一页</a>" : "<span class='disabled'>首页</span><span class='disabled'>上一页</span>";
	}
	function nextPage()
	{
		$page = $this->currentPage + 1;
		return ($this->currentPage < $this->totalPage) ? "<a href='{$this->url}{$page}'>下一页</a> <a href='{$this->url}{$this->totalPage}'>尾页</a>" : "<span class='disabled'>下一页</span><span class='disabled'>尾页</span>";
	}
	function limit()
	{
		$page = ($this->currentPage-1)*$this->limit;
		return "$page,$this->limit";
	}
	// 带有页码等分页
	function getPage()
	{
		$str   = '';
		$start = 1;
		$end   = $this->totalPage;
		if($this->totalPage > $this->pages){
			if($this->currentPage > $this->offset+1) $str .= '<span class="more">...</span>';
			if($this->currentPage > $this->offset){
				$start = $this->currentPage-$this->offset;
				$end = min($this->totalPage, ($this->currentPage+$this->offset));
			}else{
				$start = 1;
				$end = min($this->totalPage, $this->pages);
			}
			if($this->currentPage + $this->offset > $this->totalPage){
				$start -= $this->currentPage + $this->offset - $end;
			}
		}
		for($i = $start; $i <= $end; $i++){
			$str .= ($this->currentPage == $i) ? "<span class='current'>$i</span>" : "<a href='{$this->url}{$i}'>$i</a>";
		}
		if($end < $this->totalPage) $str .= '<span class="more">...</span>';
		return $str;
	}
	// 分页风格
	function style()
	{
		switch ($this->style) {
			case 1:
				return "{$this->prePage()}{$this->nextPage()}";
			case 2:
				return "{$this->prePage()}{$this->nextPage()} <span>{$this->currentPage}/{$this->totalPage}页</span>";
			default:
				return "{$this->prePage()}{$this->getPage()}{$this->nextPage()} <span>{$this->currentPage} / {$this->totalPage} 页</span> <span>共 {$this->total} 条</span>";
		}
	}

	function show()
	{
		return $this->style();
	}
}