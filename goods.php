<?php
/*
1.前台显示的程序
后台
1.后台商品管理
2.后台订单管理管理
其他
建表 pocket_goods

*/
require(dirname(__FILE__)."/includes/common.php");
class goods extends base
{
	function main()
	{
		$row=parent::table_get_row("users",1467933,"user_id");
		var_dump($row);
	}
	function detail()
	{
		
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$goods = new goods();
$sign=@is_callable(array($goods,$act));
if($sign)
$goods->$act();
else
ecs_header("Location:http://m.wm18.com/\n");