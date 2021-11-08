<?php
namespace tagparse;
/**
 * Tag 标记的数据结构描述
 * function c____DedeTag();
 */
class Tag
{
    var $IsReplace = FALSE; //标记是否已被替代，供解析器使用
    var $TagName = "";      //标记名称
    var $InnerText = "";    //标记之间的文本
    var $StartPos = 0;      //标记起始位置
    var $EndPos = 0;        //标记结束位置
    var $CAttribute = "";   //标记属性描述,即是 Attribute
    var $TagValue = "";     //标记的值
    var $TagID = 0;

    /**
     *  获取标记的名称和值
     *
     * @access    public
     * @return    string
     */
    function GetName(){
        return strtolower($this->TagName);
    }

    /**
     *  获取值
     *
     * @access    public
     * @return    string
     */
    function GetValue(){
        return $this->TagValue;
    }

    //下面两个成员函数仅是为了兼容旧版
    function GetTagName(){
        return strtolower($this->TagName);
    }

    function GetTagValue(){
        return $this->TagValue;
    }

    //获取标记的指定属性
    function IsAttribute($str){
        return $this->CAttribute->IsAttribute($str);
    }

    function GetAttribute($str){
        return $this->CAttribute->GetAtt($str);
    }

    function GetAtt($str){
        return $this->CAttribute->GetAtt($str);
    }

    function GetInnerText(){
        return $this->InnerText;
    }


}