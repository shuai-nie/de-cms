<?php
namespace pagehtml;

class Pagehtml{
    //分页url
    public $pageUrl;
    // 起始行数
    public $firstRow    ;
    // 列表每页显示行数
    public $listRows    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
    // 分页显示定制
    protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

    /**
    +----------------------------------------------------------
     * 架构函数
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
    +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$pageUrl='',$nowPage) {
        $this->pageUrl = $pageUrl;
        $this->totalRows = $totalRows;
        $this->rollPage = 5;
        $this->listRows = !empty($listRows)?$listRows:C('PAGE_LISTROWS');
        $this->totalPages = ceil((int)$this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = $nowPage;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
    +----------------------------------------------------------
     * 分页显示输出
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $this->pageUrl;
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            if($upRow==1){
                $upPage="<a href='".$url.".html'>".$this->config['prev']."</a>";
            }else{
                $upPage="<a href='".$url."/$upRow.html'>".$this->config['prev']."</a>";
            }
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="<a href='".$url."/$downRow.html'>".$this->config['next']."</a>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            if($preRow==1){
                $prePage = "";
            }else{
                $prePage = "<a href='".$url."/$preRow.html' >上".$this->rollPage."页</a>";
            }

            $theFirst = "<a href='".$url.".html' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            if($nextRow>= $this->totalPages){
                $nextPage = "";
            }else{
                $nextPage = "<a href='".$url."/$nextRow.html' >下".$this->rollPage."页</a>";
            }
            $theEnd = "<a href='".$url."/$theEndRow.html' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    if($page==1){
                        $linkPage .= " <a href='".$url.".html'> ".$page." </a>";
                    }else{
                        $linkPage .= " <a href='".$url."/$page.html'> ".$page." </a>";
                    }
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= " <span class='current'>".$page."</span>";
                }
            }
        }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
?>


