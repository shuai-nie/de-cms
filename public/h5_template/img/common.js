
//Device.os.weixin,phone
//Device.os.android
//Device.os.ios||Device.os.ipad
(function () {
    function device(ua) {
        var os = this.os = {}, browser = this.browser = {},
            webkit = ua.match(/Web[kK]it[\/]{0,1}([\d.]+)/),
            android = ua.match(/(Android);?[\s\/]+([\d.]+)?/),
            osx = !!ua.match(/\(Macintosh\; Intel /),
            ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
            ipod = ua.match(/(iPod)(.*OS\s([\d_]+))?/),
            iphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
            webos = ua.match(/(webOS|hpwOS)[\s\/]([\d.]+)/),
            touchpad = webos && ua.match(/TouchPad/),
            kindle = ua.match(/Kindle\/([\d.]+)/),
            silk = ua.match(/Silk\/([\d._]+)/),
            blackberry = ua.match(/(BlackBerry).*Version\/([\d.]+)/),
            bb10 = ua.match(/(BB10).*Version\/([\d.]+)/),
            //rimtabletos = ua.match(/(RIM\sTablet\sOS)\s([\d.]+)/),
            playbook = ua.match(/PlayBook/),
            //uc = ua.match(/UCBrowser\/([\w.\s]+)/),
            chrome = ua.match(/Chrome\/([\d.]+)/) || ua.match(/CriOS\/([\d.]+)/),
            firefox = ua.match(/Firefox\/([\d.]+)/),
            ie = ua.match(/MSIE\s([\d.]+)/) || ua.match(/Trident\/[\d](?=[^\?]+).*rv:([0-9.].)/),
            webview = !chrome && ua.match(/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/),
            safari = webview || ua.match(/Version\/([\d.]+)([^S](Safari)|[^M]*(Mobile)[^S]*(Safari))/),
            orientation = Math.abs(window.orientation),
            weixin = ua.match(/MicroMessenger/i) == 'MicroMessenger';

        if (browser.webkit = !!webkit) browser.version = webkit[1];

        if (android) os.android = true, os.version = android[2];
        if (iphone && !ipod) os.ios = os.iphone = true, os.version = iphone[2].replace(/_/g, '.');
        if (ipad) os.ios = os.ipad = true, os.version = ipad[2].replace(/_/g, '.');
        if (ipod) os.ios = os.ipod = true, os.version = ipod[3] ? ipod[3].replace(/_/g, '.') : null;
        if (webos) os.webos = true, os.version = webos[2];
        if (touchpad) os.touchpad = true;
        if (blackberry) os.blackberry = true, os.version = blackberry[2];
        if (bb10) os.bb10 = true, os.version = bb10[2];
        //if (rimtabletos) os.rimtabletos = true, os.version = rimtabletos[2];
        if (playbook) browser.playbook = true;
        //if (uc) os.uc = true, os.ucversion = uc[1];
        if (kindle) os.kindle = true, os.version = kindle[1];
        if (silk) browser.silk = true, browser.version = silk[1];
        if (!silk && os.android && ua.match(/Kindle Fire/)) browser.silk = true;
        if (orientation !== 90) os.protrait = true;
        if (orientation === 90) os.landscape = true;

        if (chrome) browser.chrome = true, browser.version = chrome[1];
        if (firefox) browser.firefox = true, browser.version = firefox[1];
        if (ie) browser.ie = true, browser.version = ie[1];
        if (safari && (osx || os.ios)) { browser.safari = true; if (osx) browser.version = safari[1] }
        if (webview) browser.webview = true;
        if (weixin) os.weixin = true;

        os.tablet = !!(ipad || playbook || (android && !ua.match(/Mobile/)) ||
            (firefox && ua.match(/Tablet/)) || (ie && !ua.match(/Phone/) && ua.match(/Touch/)))
        os.phone = !!(!os.tablet && !os.ipod && (android || iphone || webos || blackberry || bb10 ||
            (chrome && ua.match(/Android/)) || (chrome && ua.match(/CriOS\/([\d.]+)/)) ||
            (firefox && ua.match(/Mobile/)) || (ie && ua.match(/Touch/))))
    }

    window.Device = new device(navigator.userAgent);
    //console.log(navigator.userAgent);
    //console.log(window.Device);
})();


//sj常用方法
var sj = sj || {};
sj.checkBrowserCapability = function () {
    const isEdge = navigator.userAgent.indexOf('Edge') > -1;
    if (window.Device.browser.chrome && !isEdge) {
        let sVer = window.Device.browser.version;
        let s2 = sVer.substring(0, sVer.indexOf("."));
        let browserVersion = parseInt(s2);
        return browserVersion > 66;
    } else {
        return false;
    }
}
sj.checkChromuimVersion = function () {
    if (window.Device.browser.chrome &&
        window.Device.browser.version.substr(0, window.Device.browser.version.indexOf(".")) > 70)
        return true;
}
sj.showToast = function (msg, time) {
    var sjToast = $("#sjToast");
    if (sjToast[0]) {
        sjToast.stop();
        sjToast.remove();
        sjToast = null;
    }
    var sHtml = "<div id='sjToast' style='position:fixed;font-size:16px;z-index:99;top:" + (document.documentElement.scrollTop / document.documentElement.clientHeight * document.documentElement.clientHeight - 100) + "px;left:50%;transform:translate(-50%, 0);background-color:rgba(0,0,0,0.6);border-radius:10px;color:white;padding:5px 10px;word-wrap:break-word;word-break:normal; '>" + msg + "</div>"
    sjToast = $(sHtml);
    sjToast.appendTo($(document.body));
    sjToast.animate({ top: (document.documentElement.scrollTop / document.documentElement.clientHeight * document.documentElement.clientHeight - 200) + 'px' }, 500, function () {
        setTimeout(function () {
            sjToast.css("display", "none");
        }, time == undefined ? 1500 : time);
    })
}
//获取url中的参数
sj.getUrlParam = function (name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}
sj.loadImg = function (url, callback) {
    var img = new Image();
    img.src = url;
    if (img.complete)
        callback(img);
    img.onload = function () {
        img.onload = null;
        callback(img);
    }
}

//log
sj.logButton = function (pageName, buttonName, params1) { $.ajax({ url: "../api/logButton?pageName=" + pageName + "&buttonName=" + buttonName + "&params1=" + params1 }); }
sj.logErr = function (msg) { $.ajax({ url: "../api/logErr?msg=" + msg }); }
sj.logInfo = function (msg) { $.ajax({ url: "../api/logInfo?msg=" + msg }); }
sj.logErrDB = function (userID, errMessage, errParams, extend1) { $.ajax({ url: "../api/logErrDB?userID=" + userID + "&errMessage=" + errMessage + "&errParams=" + errParams + "&extend1=" + extend1 }); }
sj.logInfoDB = function (userID, tag, infoMessage, infoParams, extend1) { $.ajax({ url: "../api/logErrDB?userID=" + userID + "&tag=" + tag + "&infoMessage=" + infoMessage + "&infoParams=" + infoParams + "&extend1=" + extend1 }); }

//********神策
//初始化
sj.sensorsInit = function (isAutoTrackPage) {
    (function (para) {
        var p = para.sdk_url, n = para.name, w = window, d = document, s = 'script', x = null, y = null;
        if (typeof (w['sensorsDataAnalytic201505']) !== 'undefined') {
            return false;
        }
        w['sensorsDataAnalytic201505'] = n;
        w[n] = w[n] || function (a) { return function () { (w[n]._q = w[n]._q || []).push([a, arguments]); } };
        var ifs = ['track', 'quick', 'register', 'registerPage', 'registerOnce', 'trackSignup', 'trackAbtest', 'setProfile', 'setOnceProfile', 'appendProfile', 'incrementProfile', 'deleteProfile', 'unsetProfile', 'identify', 'login', 'logout', 'trackLink', 'clearAllRegister', 'getAppStatus'];
        for (var i = 0; i < ifs.length; i++) {
            w[n][ifs[i]] = w[n].call(null, ifs[i]);
        }
        if (!w[n]._t) {
            x = d.createElement(s), y = d.getElementsByTagName(s)[0];
            x.async = 1;
            x.src = p;
            x.setAttribute('charset', 'UTF-8');
            w[n].para = para;
            y.parentNode.insertBefore(x, y);
        }
    })({
        sdk_url: 'https://www.moguyouxi.cn/sensorsdata.min.js',
        heatmap_url: 'https://www.moguyouxi.cn/heatmap.min.js',
        name: 'sensors',
        server_url: 'https://sj.datasink.sensorsdata.cn/sa?token=8749cbadc8e370e7&project=production',
        //server_url: 'https://sj.datasink.sensorsdata.cn/sa?token=8749cbadc8e370e7&project=default',
        heatmap: {
            //default 表示开启，自动采集 $WebClick 事件，可以设置 'not_collect' 表示关闭。
            clickmap: isAutoTrackPage == true ? "default" : "not_collect",
            //default 表示开启，自动采集 $WebStay 事件，可以设置 'not_collect' 表示关闭。
            scroll_notice_map: isAutoTrackPage == true ? "default" : "not_collect",
        }
    });
    sensors.quick('isReady', function () {
        var cookieId = sensors.quick('getAnonymousID');
        var uid = getCookie("uid");
        //console.log("uid=" + uid);
        //console.log("cookieId=" + cookieId);
        if (uid !== undefined && uid.length > 0 && cookieId != uid) {
            sensors.identify(uid, true);
        }
    });
}
//自动采集 $pageview 事件
sj.sensorsQuick = function () {
    sensors.quick('autoTrack');
}
//注册公共属性（未登录）
sj.sensorsRegistPage = function (ps) {
    //静态属性
    ps.Platform_type = "PC";
    ps.ChannelName = "26001";
    ps.Deviceid = navigator.userAgent;
    sensors.registerPage(ps);
}
//采集界面
sj.sensorsTrack_VisitPage = function (pageName, fromPage) {
    sensors.track('VisitPage', {
        page_name: pageName,
        from_page_name: fromPage,
    }); //sc
}
//采集按钮点击
sj.sensorsTrack_ButtonClick = function (pageName, button_id, button_name) {
    sensors.track('ButtonClick', {
        page_name: pageName,
        button_id: button_id,
        button_name: button_name,
    }); //sc
}


//cookie
function setCookie(name, value, timeMinutes) {
    //设置名称为name,值为value的Cookie
    var expdate = new Date();   //初始化时间
    if (timeMinutes == undefined)
        timeMinutes = 30;
    expdate.setTime(expdate.getTime() + timeMinutes * 60 * 1000);   //时间单位毫秒
    document.cookie = name + "=" + value + ";expires=" + expdate.toGMTString() + ";path=/";
    //即document.cookie= name+"="+value+";path=/";  时间默认为当前会话可以不要，但路径要填写，因为JS的默认路径是当前页，如果不填，此cookie只在当前页面生效！
}
function getCookie(c_name) {
    //判断document.cookie对象里面是否存有cookie
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        //如果document.cookie对象里面有cookie则查找是否有指定的cookie，如果有则返回指定的cookie值，如果没有则返回空字符串
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1
            c_end = document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return ""
}
function delCookie() {
    var keys = document.cookie.match(/[^ =;]+(?==)/g)
    if (keys) {
        for (var i = keys.length; i--;) {
            document.cookie = keys[i] + '=0;path=/;expires=' + new Date(0).toUTCString() // 清除当前域名下的,例如：m.ratingdog.cn
            document.cookie = keys[i] + '=0;path=/;domain=' + document.domain + ';expires=' + new Date(0).toUTCString() // 清除当前域名下的，例如 .m.ratingdog.cn
            document.cookie = keys[i] + '=0;path=/;domain=ratingdog.cn;expires=' + new Date(0).toUTCString() // 清除一级域名下的或指定的，例如 .ratingdog.cn
        }
    }
}
//url
function UrlSearch() {
    var name, value;
    var str = location.href; //取得整个地址栏
    var num = str.indexOf("?")
    str = str.substr(num + 1); //取得所有参数   stringvar.substr(start [, length ]

    var arr = str.split("&"); //各个参数放到数组里
    var res = {};
    for (var i = 0; i < arr.length; i++) {
        num = arr[i].indexOf("=");
        if (num > 0) {
            name = arr[i].substring(0, num);
            value = arr[i].substr(num + 1);
            res[name] = value;
        }
    }
    return res;
}

function scrollGo(number, time) {
    if (!time) {
        document.body.scrollTop = document.documentElement.scrollTop = number;
        return number;
    }
    const spacingTime = 20; // 设置循环的间隔时间  值越小消耗性能越高
    let spacingInex = time / spacingTime; // 计算循环的次数
    let nowTop = document.body.scrollTop + document.documentElement.scrollTop; // 获取当前滚动条位置
    const everTop = (number - nowTop) / spacingInex; // 计算每次滑动的距离
    const scrollTimer = setInterval(() => {
        if (spacingInex > 0) {
            spacingInex--;
            scrollGo(nowTop += everTop);
        } else {
            clearInterval(scrollTimer); // 清除计时器
        }
    }, spacingTime);
}

/**
 * 日期格式化
 * 格式 YYYY/yyyy/YY/yy 表示年份
 * MM/M 月份
 * W/w 星期
 * dd/DD/d/D 日期
 * hh/HH/h/H 时间
 * mm/m 分钟
 * ss/SS/s/S 秒
 * @param {any} formatStr yyyy-MM-dd hh:mm:ss
 */
Date.prototype.Format = function (formatStr) {
    var str = formatStr
    var Week = ['日', '一', '二', '三', '四', '五', '六']

    str = str.replace(/yyyy|YYYY/, this.getFullYear())
    str = str.replace(/yy|YY/,
        (this.getYear() % 100) > 9 ? (this.getYear() % 100).toString() : '0' + (this.getYear() % 100))

    str = str.replace(/MM/, this.getMonth() + 1 > 9 ? (this.getMonth() + 1).toString() : '0' + (this.getMonth() + 1))
    str = str.replace(/M/g, this.getMonth() + 1)

    str = str.replace(/w|W/g, Week[this.getDay()])

    str = str.replace(/dd|DD/, this.getDate() > 9 ? this.getDate().toString() : '0' + this.getDate())
    str = str.replace(/d|D/g, this.getDate())

    str = str.replace(/hh|HH/, this.getHours() > 9 ? this.getHours().toString() : '0' + this.getHours())
    str = str.replace(/h|H/g, this.getHours())
    str = str.replace(/mm/, this.getMinutes() > 9 ? this.getMinutes().toString() : '0' + this.getMinutes())
    str = str.replace(/m/g, this.getMinutes())

    str = str.replace(/ss|SS/, this.getSeconds() > 9 ? this.getSeconds().toString() : '0' + this.getSeconds())
    str = str.replace(/s|S/g, this.getSeconds())

    return str
}

/**
 * 求两个时间的天数差 日期格式为 YYYY-MM-dd
 * @param {any} DateOne
 * @param {any} DateTwo
 */
function daysBetween(DateOne, DateTwo) {
    var OneMonth = DateOne.substring(5, DateOne.lastIndexOf('-'))
    var OneDay = DateOne.substring(DateOne.length, DateOne.lastIndexOf('-') + 1)
    var OneYear = DateOne.substring(0, DateOne.indexOf('-'))

    var TwoMonth = DateTwo.substring(5, DateTwo.lastIndexOf('-'))
    var TwoDay = DateTwo.substring(DateTwo.length, DateTwo.lastIndexOf('-') + 1)
    var TwoYear = DateTwo.substring(0, DateTwo.indexOf('-'))

    var cha = ((Date.parse(OneMonth + '/' + OneDay + '/' + OneYear) - Date.parse(TwoMonth + '/' + TwoDay + '/' + TwoYear)) / 86400000)
    return Math.abs(cha)
}

/**
 * 日期计算
 * @param {any} strInterval s: second n: minute h: hour d: day w: week q: quarter m: month y: year. 
 * @param {any} Number Number
 */
Date.prototype.DateAdd = function (strInterval, Number) {
    var dtTmp = this
    switch (strInterval) {
        case 's': return new Date(Date.parse(dtTmp) + (1000 * Number))
        case 'n': return new Date(Date.parse(dtTmp) + (60000 * Number))
        case 'h': return new Date(Date.parse(dtTmp) + (3600000 * Number))
        case 'd': return new Date(Date.parse(dtTmp) + (86400000 * Number))
        case 'w': return new Date(Date.parse(dtTmp) + ((86400000 * 7) * Number))
        case 'q': return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number * 3, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds())
        case 'm': return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds())
        case 'y': return new Date((dtTmp.getFullYear() + Number), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds())
    }
}

/**
 * 比较日期差 dtEnd 格式为日期型或者有效日期格式字符串
 * @param {any} strInterval s: second n: minute h: hour d: day w: week m: month y: year.
 * @param {any} dtEnd 格式为日期型或者有效日期格式字符串
 */
Date.prototype.DateDiff = function (strInterval, dtEnd) {
    var dtStart = this
    if (typeof dtEnd == 'string')//如果是字符串转换为日期型  
    {
        dtEnd = StringToDate(dtEnd)
    }
    switch (strInterval) {
        case 's': return parseInt((dtEnd - dtStart) / 1000)
        case 'n': return parseInt((dtEnd - dtStart) / 60000)
        case 'h': return parseInt((dtEnd - dtStart) / 3600000)
        case 'd': return parseInt((dtEnd - dtStart) / 86400000)
        case 'w': return parseInt((dtEnd - dtStart) / (86400000 * 7))
        case 'm': return (dtEnd.getMonth() + 1) + ((dtEnd.getFullYear() - dtStart.getFullYear()) * 12) - (dtStart.getMonth() + 1)
        case 'y': return dtEnd.getFullYear() - dtStart.getFullYear()
    }
}

/**
 * 日期输出字符串，重载了系统的toString方法
 * @param {any} showWeek 星期(日~六)
 */
Date.prototype.toString = function (showWeek) {
    var myDate = this
    var str = myDate.toLocaleDateString()
    if (showWeek) {
        var Week = ['日', '一', '二', '三', '四', '五', '六']
        str += ' 星期' + Week[myDate.getDay()]
    }
    return str
}

/**
 * 日期合法性验证
 * @param {any} DateStr 格式为：YYYY-MM-DD或YYYY/MM/DD
 */
function IsValidDate(DateStr) {
    var sDate = DateStr.replace(/(^\s+|\s+$)/g, '') //去两边空格   
    if (sDate == '') return true
    //如果格式满足YYYY-(/)MM-(/)DD或YYYY-(/)M-(/)DD或YYYY-(/)M-(/)D或YYYY-(/)MM-(/)D就替换为''   
    //数据库中，合法日期可以是:YYYY-MM/DD(2003-3/21),数据库会自动转换为YYYY-MM-DD格式   
    var s = sDate.replace(/[\d]{ 4,4 }[\-/]{ 1 }[\d]{ 1,2 }[\-/]{ 1 }[\d]{ 1,2 }/g, '')
    if (s == '') //说明格式满足YYYY-MM-DD或YYYY-M-DD或YYYY-M-D或YYYY-MM-D   
    {
        var t = new Date(sDate.replace(/\-/g, '/'))
        var ar = sDate.split(/[-/:]/)
        if (ar[0] != t.getYear() || ar[1] != t.getMonth() + 1 || ar[2] != t.getDate()) {
            //alert('错误的日期格式！格式为：YYYY-MM-DD或YYYY/MM/DD。注意闰年。')   
            return false
        }
    }
    else {
        //alert('错误的日期格式！格式为：YYYY-MM-DD或YYYY/MM/DD。注意闰年。')   
        return false
    }
    return true
}

/**
 * 日期时间检查
 * @param {any} str 格式为：YYYY-MM-DD HH:MM:SS
 */
function CheckDateTime(str) {
    var reg = /^(\d+)-(\d{ 1,2 })-(\d{ 1,2 }) (\d{ 1,2 }):(\d{ 1,2 }):(\d{ 1,2 })$/
    var r = str.match(reg)
    if (r == null) return false
    r[2] = r[2] - 1
    var d = new Date(r[1], r[2], r[3], r[4], r[5], r[6])
    if (d.getFullYear() != r[1]) return false
    if (d.getMonth() != r[2]) return false
    if (d.getDate() != r[3]) return false
    if (d.getHours() != r[4]) return false
    if (d.getMinutes() != r[5]) return false
    if (d.getSeconds() != r[6]) return false
    return true
}

/**把日期分割成数组 */
Date.prototype.toArray = function () {
    var myDate = this
    var myArray = Array()
    myArray[0] = myDate.getFullYear()
    myArray[1] = myDate.getMonth()
    myArray[2] = myDate.getDate()
    myArray[3] = myDate.getHours()
    myArray[4] = myDate.getMinutes()
    myArray[5] = myDate.getSeconds()
    return myArray
}

/**
 * 取得日期数据信息
 * @param {any} interval s: second n: minute h: hour d: day w: weekday ww: week of year m: month y: year.
 */
Date.prototype.DatePart = function (interval) {
    var myDate = this
    var partStr = ''
    var Week = ['日', '一', '二', '三', '四', '五', '六']
    switch (interval) {
        case 'y': partStr = myDate.getFullYear(); break
        case 'm': partStr = myDate.getMonth() + 1; break
        case 'd': partStr = myDate.getDate(); break
        case 'w': partStr = Week[myDate.getDay()]; break
        case 'ww': partStr = myDate.WeekNumOfYear(); break
        case 'h': partStr = myDate.getHours(); break
        case 'n': partStr = myDate.getMinutes(); break
        case 's': partStr = myDate.getSeconds(); break
    }
    return partStr
}

/**取得当前日期所在月的最大天数 */
Date.prototype.MaxDayOfDate = function () {
    var myDate = this
    var ary = myDate.toArray()
    var date1 = (new Date(ary[0], ary[1] + 1, 1))
    var date2 = date1.dateAdd(1, 'm', 1)
    var result = dateDiff(date1.Format('yyyy-MM-dd'), date2.Format('yyyy-MM-dd'))
    return result
}

/**取得当前日期所在周是一年中的第几周 */
Date.prototype.WeekNumOfYear = function () {
    var myDate = this
    var ary = myDate.toArray()
    var year = ary[0]
    var month = ary[1] + 1
    var day = ary[2]
    //document.write('< script language=VBScript\> \n');
    //document.write('myDate = Datue(''+month+' - '+day+' - '+year+'') \n');
    //document.write('result = DatePart('ww', myDate) \n');
    //document.write(' \n');
    return result
}

/**
 * 字符串转成日期类型
 * @param {any} DateStr 格式 MM/dd/YYYY MM-dd-YYYY YYYY/MM/dd YYYY-MM-dd
 */
function StringToDate(DateStr) {

    var converted = Date.parse(DateStr)
    var myDate = new Date(converted)
    if (isNaN(myDate)) {
        //var delimCahar = DateStr.indexOf('/')!=-1?'/':'-'  
        var arys = DateStr.split('-')
        myDate = new Date(arys[0], --arys[1], arys[2])
    }
    return myDate
}
