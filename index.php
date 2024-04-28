<?php
//后台正文开始 
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE);
//默认时区设置
date_default_timezone_set('PRC');
$confile = './images/~vvv7590-config.php';
$default = array(
	'isLog' => '0',
	'type' => '2',
	'sort' => '2',
	'urls' => "",
);
if(!is_dir(dirname($confile)))mkdir(dirname($confile));
if(!isset($_GET['vip'])){
	if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
		$ip = getenv("REMOTE_ADDR");
	}else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
		$ip = $_SERVER['REMOTE_ADDR'];
	}else{
		$ip = '0.0.0.0';
	}
	if(strpos($ip,','))$ip = current(explode(',',$ip));
	$config = getConfig($confile);
	$urls = preg_split('/\s*\n\s*/ui',trim($config['urls']));
	if(isset($_GET['type']) && 'jsonp'==$_GET['type']){
		exit('var urlList = '.json_encode($urls,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).';');
	}
	if(2==$config['sort'] && count($urls)>1){
		$iText = is_file($ifile =dirname($confile).'/~vvv7590-index.log')?abs(intval(@file_get_contents($ifile))+1):0;
		file_put_contents($ifile,$iText);
		$url = myChat($urls[$iText%count($urls)]);
	}elseif(3==$config['sort'] && count($urls)>1){
		$ipIndex = abs(intval(crc32(md5($ip))))+1;
		$url = myChat($urls[$ipIndex%count($urls)]);
	}else{
		$url = myChat($urls[array_rand($urls)]);
	}
	if(!empty($config['isLog'])){
		file_put_contents(dirname($confile).'/~vvv7590-log-'. date('Y-m') . '.log',"\r\n###### ".date('Y-m-d H:i:s')."\t{$url}\t{$ip}\t{$_SERVER['HTTP_REFERER']}\thttp://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\t{$_SERVER['HTTP_USER_AGENT']}\t######",FILE_APPEND);
	}
	if(empty($url)){
		header("Content-Type:text/html;charset=utf-8");
		echo '参数无效';
	}elseif('20' == $config['type']){
		echo FromCharEval("location.replace('{$url}');");
	}elseif('1' == $config['type']){
		header("HTTP/1.1 302 Found"); 
		header("Location: {$url}",true,302);
	}elseif('2' == $config['type']){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: {$url}");
	}elseif('3' == $config['type']){
		echo '<script>'.FromCharEval("location.replace('{$url}');").'</script>';
	}elseif('7' == $config['type']){
		echo "<!DOCTYPE html>\n<html><head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n<title>请使用浏览器打开</title>\n<meta name=\"viewport\" content=\"width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no\">\n<meta name=\"format-detection\" content=\"telephone=no\">\n<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n<meta name=\"wap-font-scale\" content=\"no\">\n<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">\n<script type=\"text/javascript\">\nvar system ={};  \n   var p = navigator.platform;       \n   system.win = p.indexOf(\"Win\") == 0;  \n   system.mac = p.indexOf(\"Mac\") == 0;  \n   system.x11 = (p == \"X11\") || (p.indexOf(\"Linux\") == 0);     \n   if(system.win||system.mac||system.xll){\n\t\twindow.location.href=\"http://weishi.qq.com\";  \n   }\n</script> \n<script>\nvar url = '{$url}';\nif(!/MicroMessenger|QQ\\//gi.test(navigator.userAgent)){\n\tlocation.href=url;\n}\n</script>\n<style type=\"text/css\">body,html{font-family:sans-serif}</style>\n</head>\n<body oncontextmenu=\"return false\" onselectstart=\"return false\">\n<div style=\"text-align:center\">\n\t<div style=\"background-image:url(https://external-30160.picsz.qpic.cn/632d2c7051d7d9d8764fb3f8983bce4c);height:100%;\">\n\t\t<img src=\"https://external-30160.picsz.qpic.cn/f5c4cd695818494d727d8715bcfe7239\" width=\"100%\"/>\n\t</div>\n\t<div style=\"width:100%;position:fixed;bottom: 0;font-size: 12px;display:-ms-flexbox;display:flex;-ms-flex-direction:row;margin:0 auto auto auto;padding:5%;background:#f9f9f9;border-radius:4px;-ms-flex-align:center;align-items:center;right:0;left:0;\" class=\"bomArea\">\n\t\t<img src=\"https://external-30160.picsz.qpic.cn/60d9f46afebf468646b2e008a020d1a2\" style=\"width:15%;margin-right:5%\">\n\t\t<div> \n\t\t\t<div style=\"text-align:left;font-size:14px;color:#fb7299\">管家检测正常，请按上图提示打开。</div>\n\t\t\t<div>您所访问的地址：http://pc.qq.com/</div>\n\t\t</div>\n\t</div>\t\n</div>\t\n</body>\n</html>";
	}else{
		echo json_encode(array('err'=>0,'url'=>$url),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	}
}else{
    $config = is_file($confile) ? array_merge($default, include($confile)) : $default;
    setcookie('DFDOG', md5('staticValue' . $_SERVER['HTTP_USER_AGENT']), $_SERVER['REQUEST_TIME'] + 864000, '/');
    $json['tip'] = '已经登录成功，请按需求配置';
    };

	//修改配置
if (!empty($_POST['conf'])) {
    $configChanged = false;
    $json = array();
    
    if (isset($_POST['back']) && $_POST['back'] == '恢复默认' && is_file($confile)) {
        unlink($confile);
        $config = $default;
        $json['tip'] = '已经恢复默认，请重新配置';
        $configChanged = true;
    } elseif (isset($_POST['conf']) && is_array($_POST['conf'])) {
        $newConfig = array_merge($config, $_POST['conf']);
        if ($newConfig !== $config) {
            $config = $newConfig;
            $configChanged = true;
        }

        if (!empty($_POST['add_jump'])) {
            $_POST['add_jump'] = array_reverse($_POST['add_jump']);
            foreach ($_POST['add_jump'] as $jv) {
                if (!empty($jv[0]) && !empty($jv[1]) && !in_array($jv, $config['jumps'])) {
                    array_unshift($config['jumps'], $jv);
                    $configChanged = true;
                }
            }
        }
    }

    if ($configChanged && file_put_contents($confile, '<?php return ' . var_export($config, true) . ';')) {
        $json['tip'] = '保存成功';
    } elseif (!$configChanged) {
        $json['tip'] = '配置未变更，无需保存。';
    } else {
        $json['tip'] = '文件无法写入，请设置根目录的写入权限';
    }

    exit(json_encode($json));
}
	$conf = $config;
	$GLOBALS['json']['home'] =  'http://'.$_SERVER['HTTP_HOST'].current(explode('?',$_SERVER['REQUEST_URI']));	
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Cache-Control" content="no-transform">
<title>阿枫-短域名管理后台</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{margin:0;padding:200px 0 0;color:#fff;background-color:#000;background-size:20px;height:100vh;}
.head{padding:8px 3px;font-size:30px;border-top:none !important;}
.form{margin:auto;font-size:13px;width:100%;display:block;margin:0 auto;width:100%;max-width:800px;}
.form .form_table{margin-bottom:100px;}
.form span{color:#90caec;display:block;padding-top:3px;font-size:12px;width:92%;}
.form td{border-top:#3e3d3d solid 1px;padding:8px 3px;}
.form a{cursor:pointer;text-decoration:none;color:#aad2f1;}
.form .text{width: 95%;border:1px solid #efd5d5;padding:6px;font-size:14px;line-height:1.2em;border-radius:5px;background-color:rgba(255, 255, 255, 0.86);color:#333;}
.form .checkbox{vertical-align:-2px;margin-right:7px;}
.form .left{text-align:right;min-width:78px;width: 13%;font-size:14px;padding-right:8px;}
.form .butt{color:#FFF;border:none;height:30px;font-size:14px;margin:4px 5px 4px 0px;cursor:pointer;line-height:1;font-weight:bold;border-radius:5px;background:#4899E0;width: 212px;}
.form .info{font-size:12px;color:#ef9c67;margin:0;padding:3px 15px;}
.tr_more{display:none;}
.msg_btn{padding:0px;font-size:14px;font-weight:100;color:#fff;border-style:none;border-color:initial;height:40px;line-height:40px;width:100px;max-width:20vw;cursor:pointer;border-radius:5px;background:#4899E0;margin:0 3px 20px;}
@media(max-width:750px){.form .butt{width:75px;max-width: 159px;}}
</style>
</head>
<body>
<form id="form" class="form" name="form" method="post" enctype="multipart/form-data">
	<table class="form_table" width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" class="head" align="center">阿枫-短域名管理后台</td>
		</tr>
		<!--<tr>-->
		<!--	<td class="left">前台推广地址</td>-->
		<!--	<td><a target="_blank" href="<?php echo $GLOBALS['json']['home']; ?>" ><?php echo preg_replace('/(.{30}).{5,}(.{15})/','$1**$2', $GLOBALS['json']['home']); ?></a></td>-->
		<!--</tr>-->
		<tr>
			<td class="left">后台管理地址</td>
			<td><a href="<?php echo $GLOBALS['json']['home'].'?vip=on'; ?>" ><?php echo preg_replace('/(.{30}).{5,}(.{15})/','$1**$2', $GLOBALS['json']['home'].'?vip=on'); ?></a></td>
		</tr>
		<tr>
			<td class="left">目标地址</td>
			<td><textarea class="text" id="urls" name="conf[urls]" style="min-height:7.2em;" ><?php echo($conf['urls']); ?></textarea>
				<span>填写目标地址，可选一行一个(填写多个进行随机跳转)</span>
			</td>
		</tr>
		<tr>
			<td class="left">跳转形式</td>
			<td >
				<select class="text" id="type" name="conf[type]">
					<option <?php if($conf['type']==1)echo 'selected'; ?> value="1">302跳转</option>
					<option <?php if($conf['type']==2)echo 'selected'; ?> value="2">301跳转</option>
					<option <?php if($conf['type']==3)echo 'selected'; ?> value="3">JS 跳转</option>
					<option <?php if($conf['type']==7)echo 'selected'; ?> value="7">跳出浏览器（QQ，微信）</option>
				</select>
				<span>选择跳转形式</span>
			</td>
		</tr>
		<tr class="tr_more">
			<td class="left">轮询方式</td>
			<td >
				<select class="text" id="sort" name="conf[sort]">
					<option <?php if($conf['sort']==1)echo 'selected'; ?> value="1">随机打开目标地址</option>
					<option <?php if($conf['sort']==2)echo 'selected'; ?> value="2">顺序打开目标地址</option>
					<option <?php if($conf['sort']==3)echo 'selected'; ?> value="3">用户固定(自测)</option>
				</select>
				<span>按顺序依次打开链接</span>
			</td>
		</tr>
		
		<!--<tr class="tr_more">-->
		<!--	<td class="left">记录日志</td>-->
		<!--	<td >-->
		<!--	<input type="hidden" name="conf[isLog]" value="0">-->
		<!--	<input class="checkbox" type="checkbox" id="isLog" name="conf[isLog]" <?php if($conf['isLog']=='0')echo 'checked'; ?> value="1">在根目录写访问日志<br>-->
		<!--	</td>-->
		<!--</tr>-->
		
		<tr class="tr_more">
			<td class="left">温馨提醒</td>
			<td>
				<ol class="info">
					<!--<li>后台入口是 ?vip=on，<?php echo $confile; ?> 是配置文件</li>-->
					<li>草料二维码生成美化 <a class="line_act" target="_blank" href="https://cli.im/">打开网址</a>
					<!--验证跳转抓取工具 <a class="line_act" target="_blank" href="http://tools.qedns.cn/code/?t=fetch&url=<?php echo urlencode($GLOBALS['json']['home']);?>">打开网址</a>-->
					</li>
					<li>当参数设置混乱导致功能障碍无法使用时，请点击恢复默认配置</li>
				</ol>
			</td>
		</tr>
		<tr >
			<td class="left">操作保存</td>
			<td>
				<input class="butt tr_more" onclick="saveAct(1)" type="button" value="退出系统" >
				<input class="butt tr_more" onclick="saveAct(2)" type="button" formnovalidate value="恢复默认配置" >
				<input class="butt tr_more" onclick="saveAct(3)" type="button" formnovalidate value="官网" >
				<br class="tr_more">
				<input class="butt" onclick="saveAct(0)" type="button" value="保存设置" style="width:433px;background:#6FB934;" >
				<input class="butt" onclick="saveAct(4)" type="button" formnovalidate value="更多设置" >
			</td>
		</tr>
	</table>
</form>
<script src="//libs.baidu.com/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
// 初始化全局 JSON 变量，存储服务器端传递的信息
$json = <?php echo json_encode($GLOBALS['json']);?>;

// 为每个 textarea 元素自动调整高度以适应其内容
$('textarea').each(textareaAutoHeight);

// 页面加载完毕后的操作
$(function(){
	// 绑定输入事件到 textarea，调用自动高度调整函数
	$('textarea').on('input', textareaAutoHeight);
	// 绑定点击事件到设置用户的按钮，显示更多设置
	$('.set_user').click(function(e){
		$('.tr_more').show();
	});
	// 如果用户修改了用户名或密码，设置全局变量 changePass 为 true
	$('#user,#pass').on('input change', function(){
		window.changePass = true;
	});
	// 如果服务器传回消息，则显示提示信息
	if($json.msg) tip($json.msg);
})

// 动态调整 textarea 高度的函数
function textareaAutoHeight(e){
	var val = $(this).val().replace(/^\s+/g,'');
	var arr = val.split(/\n/);
	$(this).css('height', (arr.length * 1.2) + 'em');
}

// 根据动作代码保存表单或执行其他动作
function saveAct(act){
	var val = $('#form').serialize();  // 序列化表单数据为字符串
	if (1 == act) {
		// 显示确认退出的弹窗
		return msg('您确认要退出吗!', function(){
			document.cookie = 'DFDOG=NULL; path=/; expires=' + new Date(0).toUTCString();
			location.href = '?vip=on';
		});
	} else if (2 == act) {
		// 显示确认恢复默认设置的弹窗
		return msg('您确认恢复原始吗?重置后无法恢复!!', function(){
			saveAct(-2)
		});
	} else if (-2 == act) {
		window.changePass = true;
		val += '&back=恢复默认&';
	} else if (3 == act) {
		// 打开新窗口链接
		return open('http://ok.vvv7590.shop/', '_blank');
	} else if (4 == act) {
		// 显示更多设置
		return $('.tr_more').show();
	}
	// 发送 POST 请求
	$.post('', val, function(d){
		d = dejson(d);  // 解析返回的 JSON 数据
		if (window.changePass) {
			// 如果密码被修改，1秒后刷新页面
			setTimeout(function(){
				location.reload();
			}, 1000);
		}
	});
	return false;
}

// 解析 JSON 数据，并根据数据显示提示、警告或执行重定向
function dejson(d) {
	try {
		if (typeof(d) == 'string') d = JSON.parse(d);
		if (d.tip) tip(d.tip);
		if (d.alert) alert(d.alert);
		if (d.reload) location.reload();
		if (d.location) location.href = d.location;
	} catch (e) {
		return {d: d, e: e};
	}
	return d;
}
// 显示一个自定义的消息弹窗，提供确认操作
function msg(text, fun) {
    var d = document.createElement('div');
    d.id = 'tmsg';
    // 设置弹窗样式，覆盖全屏，黑色半透明背景
    d.style.cssText = 'background:rgba(0,0,0,0.6);position:fixed;left:0;top:0;width:100%;height:100%;z-index:19891015';
    d.innerHTML = '<div style="margin:300px auto 0;background:#fff;border:solid #666 1px;border-radius:5px;width:90%;max-width:500px;text-align:center;">' +
                  '<div style="padding:32px 10px;color:#333;font-size:16px;line-height:2;">' + text + '</div>' +
                  '<div onclick="document.body.removeChild(window.tmsg)"><button class="msg_btn" style="background:#ccc;">取消</button>' +
                  '<button class="msg_btn" onclick="tfun();">确定</button></div></div>';
    window.tfun = fun;
    window.tmsg && document.body.removeChild(window.tmsg); // 如果已存在弹窗，则先移除
    document.body.appendChild(d); // 添加新的弹窗到文档中
}

// 创建一个提示消息弹窗，会自动消失
function tip(text, time) {
    var div = $('<div id="tmsg" style="top:300px;left:20%;right:20%;color:#000;margin:0 auto;opacity:1;padding:5px;font-size:15px;max-width:300px;position:fixed;text-align:center;border-radius:8px;background-color:#fff;border:1px solid #666;box-shadow:rgba(0,0,0,0.2) 0px 0px 16px 5px;">' + text + '</div>');
    $('#tmsg').remove(); // 移除已存在的提示消息
    $('body').append(div); // 将新的提示消息添加到文档中
    setTimeout(function() {
        div.animate({opacity: 0}, function() { div.remove(); }); // 指定时间后渐隐并移除提示消息
    }, (time || 2) * 1000); // 默认显示时间为2秒，可通过参数调整
}

</script>
</body>
</html>
<?php	
function getConfig($file){
    header("Content-Type: text/html; charset=utf-8");

    if (!is_file($file)) {
        header("Location: https://www.baidu.com/");
        exit;
    }

    if (strpos($_SERVER['HTTP_HOST'], '.qedns.cn') !== false) {
        throw new Exception('此测试不能正式使用！');
    }

    return include $file;
}

function myChat($url) {
    return preg_replace_callback('/\{([NWD]+)\}/iu', function ($matches) {
        $result = '';
        foreach (str_split($matches[1]) as $char) {
            switch (strtoupper($char)) {
                case 'N':
                    $result .= mt_rand(0, 9);
                    break;
                case 'D':
                    $result .= chr(mt_rand(65, 90));
                    break;
                case 'W':
                    $result .= chr(mt_rand(97, 122));
                    break;
            }
        }
        return $result;
    }, $url);
}

function FromCharEval($txt){
	$txt=iconv('UTF-8', 'UCS-2BE', $txt);
	$str	 = '["ec.v1"][\'\x66\x69\x6c\x74\x65\x72\'][\'\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f\x72\']';
	$str	.= '(((["ec.v1"]+[])[\'\x63\x6f\x6e\x73\x74\x72\x75\x63\x74\x6f\x72\']["\x66\x72\x6f\x6d\x43\x68\x61\x72\x43\x6f\x64\x65"]["\x61\x70\x70\x6c\x79"](null,\'';
	for($i = 0; $i < strlen($txt) - 1; $i = $i + 2)$str .= ($i>0?chr(mt_rand(97,122)):'').(ord($txt[$i])*256+ord($txt[$i + 1]));		
	$str	.= '\'["\x73\x70\x6c\x69\x74"](/[a-zA-Z]{1,}/))))("ec.v1");';
	return $str;
}
/*
*	技术支持：我们的技术范围有：PHP，JAVASCRIPT，JQUERY，MYSQL，HTML，H5，CSS，THINKPHP，APACHE，NGINX，IIS，图片处理，UI设计，SEO技术顾问等电话：18729480012，企鹅：3379530015。
*/