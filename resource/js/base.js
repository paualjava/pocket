//头部---------------------------------
function header(font){
	var html =  ''
		html += '<header id="header">';
		html += '<div class="goHome" onClick="javascript:history.go(-1);"><img src="images/goHome.png" height="40" alt="返回上一页"><span>返回</span></div>';
		html += '<h2>'+font+'</h2>';
		html += '</header>';
	document.write(html);
}
//尾巴---------------------------------
/*
id
0,首页
1,特惠
2,购物车
3,我的积分
4,我的小店
5,我的
p
1,显示我的积分，否则显示我的小店
*/
function footer(id,p){
	var html =  ''
		html += '<div style="height:75px;"></div>';
		html += '<footer id="footer"><ul><li';
		if(id == 0){html += ' class="this"';}
		html += '><a href="shop.php" title="首页"><span class="footerIcon-1"></span>首页</a></li><li';
		if(id == 1){html += ' class="this"';}
		html += '><a href="#" title="特惠"><span class="footerIcon-2"></span>特惠</a></li><li';
		if(id == 2){html += ' class="this"';}
		html += '><a href="#" title="购物车" class="footerCart"><p></p></a></li><li';
		if(p == 1){
			if(id == 3){html += ' class="this"';}
			html += '><a href="#" title="我的积分"><span class="footerIcon-6"></span>我的积分</a></li><li';
		}else{
			if(id == 4){html += ' class="this"';}
			html += '><a href="myShop.html" title="我的小店"><span class="footerIcon-4"></span>我的小店</a></li><li';
		}
		if(id == 5){html += ' class="this"';}
		html += '><a href="setting.php" title="我的"><span class="footerIcon-5"></span>我的</a></li>';
		html += '</ul></footer>';
	document.write(html);
}
//提示框内容-----------------------------------------
/*
0,提现需知
1,您尚未有自己的小店哦，快去申请开店吧
2,进行开店才可以申请提现哦
3,需通过直销系统后台提现哦
4,密码设置成功
5,输入有误
*/
var promptBox = {
	font  : new Array(
		'<h2>提现须知</h2><p>一个月可提现4次</p><p>每7天可提现1次</p><p>每次取款不得超过800元</p>',
		'<p>您尚未有自己的小店哦!</p><p>快去申请开店吧</p>',
		'<p>进行开店才可以申请提现哦!</p>',
		'<p>需通过直销系统后台提现哦!</p>',
		'<div class="duihao"></div><p>密码设置成功!</p>',
		'<p>输入有误</p>'
	),
	input : new Array(
		'<li class="alone"><a href="#" class="this" onClick="close_(\'#prompt\')">已阅读</a></li>',
		'<li><a href="#" class="this">去开店</a></li><li><a href="#" onClick="close_(\'#prompt\')">确定</a></li>',
		'<li class="alone"><a href="#" onClick="close_(\'#prompt\')">确定</a></li>',
		'<li class="alone"><a href="#" onClick="close_(\'#prompt\')">确定</a></li>',
		'<li class="alone"><a href="#" class="this" onClick="close_(\'#prompt\')">确定</a></li>',
		'<li class="alone"><a href="#" class="this" onClick="close_(\'#prompt\')">确定</a></li>'
	)	
};
//提示框
function prompt_(id,text,pid){
	var html = '';
		html +='<div id="prompt">';
		html +='<div class="promptBox">';
		if(text){
			html +='<div class="promptBoxFont">'+text+'</div>';
		}else{
			html +='<div class="promptBoxFont">'+promptBox.font[id]+'</div>';
		}
		html +='<div class="promptBoxInput">';
		if(pid){
			html +='<ul><li class="alone"><a href="#" class="this" onClick="close_(\'#prompt\',\''+pid+'\')">确定</a></li></ul>';
		}else{
			html +='<ul>'+promptBox.input[id]+'</ul>';
		}
		html +='</div>';
		html +='</div>';
		html +='</div>';
	$('body').append(html); 
}
//关闭
function close_(id,url){
	if(url){
		setTimeout(function(){ 
			window.location.href=url;
		},1000)
	}else{
		$(id).remove();
	}
}
