ThinkPHP 6.0
===============
https://www.kancloud.cn/manual/thinkphp5/177530
水印
> 运行环境要求PHP7.1+，兼容PHP8.0。

[官方应用服务市场](https://market.topthink.com) | [`ThinkAPI`——官方统一API服务](https://docs.topthink.com/think-api)

ThinkPHPV6.0版本由[亿速云](https://www.yisu.com/)独家赞助发布。

## 主要新特性

* 采用`PHP7`强类型（严格模式）
* 支持更多的`PSR`规范
* 原生多应用支持
* 更强大和易用的查询
* 全新的事件系统
* 模型事件和数据库事件统一纳入事件系统
* 模板引擎分离出核心
* 内部功能中间件化
* SESSION/Cookie机制改进
* 对Swoole以及协程支持改进
* 对IDE更加友好
* 统一和精简大量用法

## 安装

~~~
composer create-project topthink/think tp 6.0.*
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~

## 文档

[完全开发手册](https://www.kancloud.cn/manual/thinkphp6_0/content)

## 参与开发

请参阅 [ThinkPHP 核心框架包](https://github.com/top-think/framework)。

## 版权信息

            <form method="POST" class="elegant-aero" enctype="multipart/form-data" action="{$archivesInfo.action}" onsubmit="return checkForm();">
<input type="hidden" name="action" value="post">
<input type="hidden" name="diyid" value="1">
<input type="hidden" name="do" value="2">
<table style="width:97%;" cellpadding="0" cellspacing="1">
<tbody><tr>
  <td align="right" valign="top">姓名：</td>
  <td><input class="form-control" type="text" id='attr_26' name='{$field.attr_26}' style="width:250px" class="intxt" value="" placeholder="(必填)" required>
</td>
</tr>
<tr>
  <td align="right" valign="top">电话：</td>
  <td><input class="form-control" type="text" id='attr_27' name='{$field.attr_27}' style="width:250px" class="intxt" value="" placeholder="(必填):" required>
</td>
</tr>
<tr>
  <td align="right" valign="top">QQ：</td>
  <td><input class="form-control" type="text" id='attr_28' name='{$field.attr_28}' style="width:250px" class="intxt" value="" placeholder="(必填):" required>
</td>
</tr>

<tr>
  <td align="right" valign="top">类别：</td>
  <td><input class="form-control" type="text" name="attr_29" id="{$field.attr_29}" style="width:250px" class="intxt" value="" placeholder="(必填，如个人/企业):" required>
</td>
</tr>
<tr>
  <td align="right" valign="top">申请资源：</td>
  <td><textarea class="form-control"  id='attr_30' name='{$field.attr_30}' cols="30" rows="5" style="width:90%;height:80" placeholder="(必填):身份证号码、有什么资源等" required></textarea>
</td>
</tr>
<input type="hidden" name="dede_fields" value="name,text;tel,text;text,multitext;kind,text;qq,text;time,datetime" />
<input type="hidden" name="dede_fieldshash" value="5ae3e5229be7cb608b22d0786d122bc0" /></table>

<div align="center" style="height:30px;padding-top:10px;">
<input type="submit" name="submit" value="提 交" class="coolbg">
&nbsp;
<input type="reset" name="reset" value="重 置" class="coolbg">
</div>
<input name="time" value="" type="hidden"  id="time"  />
{$field.hidden}
{/eyou:guestbookform}
<script type="text/javascript">
window.onload = function(){
var nowDate = new Date();
var str = nowDate.getFullYear()+"-"+(nowDate.getMonth() + 1)+"-"+nowDate.getDate()+" "+nowDate.getHours()+":"+nowDate.getMinutes()+":"+nowDate.getSeconds();
document.getElementById("time   ").value=str;
}
</script>
</form>

