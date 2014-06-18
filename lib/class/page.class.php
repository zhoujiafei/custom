<?php

class page {
    
    protected $baseUrl = '';        //连接地址
    protected $totalNum = '';       //总条数
    protected $perPage = 20;        //每页条数
    protected $totalPage = '';      //总页数
    protected $currPage = '';       //当前页数
    protected $numPre = '3';    //当前页前面显示的页数
    protected $numNext  = '3';   //当前页后面显示的页数
    protected $firstPage = '第一页';   //第一页显示的文字
    protected $lastPage = '最后一页';   //第二页显示的文字
    protected $prePage = '上一页';   //上一页显示的页数
    protected $nextPage = '上一页';   //上一页显示的页数
    
    
    public function __construct($params = array()) {
        foreach ((array)$params as $key => $val)
        {
            if (isset($this->$key))
            {
                $this->$key = $val;
            }
        }        
    }
    
    
    public function built_page() {
        if (!$this->totalNum || !$this->perPage) {
            return '';
        }
        $this->totalPage = ceil($this->totalNum / $this->perPage);
        if ($this->totalPage == 1) {
            return '';
        }
        
        if ($this->currPage > $this->totalPage) {
            $this->currPage = 1;
        }
        
        $this->baseUrl = rtrim($this->baseUrl);
        $this->baseUrl .= (strpos($this->baseUrl, '?') !== FALSE) ? '&amp;' : '?';
        
        $output = '';
        if ($this->firstPage !== FALSE AND $this->currPage > $this->numPre) {
            $url = $this->baseUrl . 'page=1&perPage=' . $this->perPage;
            $output .=  '<span class="pagelink_next"><a class="page_bur" href="' . $url . '">' . $this->firstPage . '</a></span>';
        } 
        
        if ($this->prePage !== FALSE AND $this->currPage != 1) {
            $url = $this->baseUrl . 'page=' . ($this->currPage-1) . '&perPage=' . $this->perPage;
            $output .= '<span class="pagelink_next"><a class="page_bur" href="' . $url . '">' . $this->prePage . '</a></span>';
        }  
        
        $start_page = (($this->currPage - $this->numPre)) > 0 ? ($this->currPage - $this->numPre) : 1;
        $end_page = $this->currPage + $this->numNext;  
        $end_page = ($end_page < ($this->numPre + $this->numNext + 1)) ? ($this->numPre + $this->numNext + 1) : $end_page;  
        $end_page = ($end_page < $this->totalPage) ? $end_page : $this->totalPage;
        for ($i = $start_page; $i<= $end_page; $i++) {
            $url = $this->baseUrl . 'page=' . $i . '&perPage=' . $this->perPage;
            $class='class="page_bur"';
            if ($this->currPage == $i) {
                $class= 'class="page_curr"';
            }
            $output .= '<span class="pagelink_'.$i.'"><a '.$class.' href="' . $url . '">' . $i . '</a></span>';
        }
        
        if ($this->nextPage !== FALSE AND $this->currPage < $this->totalPage) {
            $url = $this->baseUrl . 'page=' . ($this->currPage+1) . '&perPage=' . $this->perPage;
            $output .= '<span class="pagelink_next"><a class="page_bur" href="' . $url . '">' . $this->nextPage . '</a></span>';            
        }  
        
        if ($this->lastPage !== FALSE AND ($this->curPage + $this->numNext) < $this->totalNum) {
            $url = $this->baseUrl . 'page=' . $this->totalPage . '&perPage=' . $this->perPage;
            $output .=  '<span class="pagelink_next"><a class="page_bur" href="' . $url . '">' . $this->lastPage . '</a></span>';
        }         
        
        $page_link = '<div id="page"><span class="page_all">共'.$this->totalPage.'页/计'.$this->totalNum.'条</span>'.$output.'</div>';
        
        return $page_link;            
        
    }
    
}
