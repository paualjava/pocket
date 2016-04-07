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
	function preview()
	{
		$pid=$_GET['pid'];
		$type=$_GET['type'];
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_goods_preview')."  where pid='".$pid."' limit 0,1";
		$info = $GLOBALS['db']->getRow($sql);
		var_dump($info);die();
		if($info)
		{
			$GLOBALS['smarty']->assign('goods_info', $info);
			$GLOBALS['smarty']->assign('pid', $pid);
			//图片
			$sql = "select img_url from ". $GLOBALS['ecs']->table('goods_gallery')."  where goods_id='".$pid."' limit 0,1";
			$info = $GLOBALS['db']->getRow($sql);
			if($type!=2)
			$GLOBALS['smarty']->display('goods_preview.htm');
			else
			$GLOBALS['smarty']->display('goods_preview2.htm');
		}
		else
		ecs_header("Location:http://m.wm18.com/\n");
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$goods = new goods();
$sign=@is_callable(array($goods,$act));
if($sign)
$goods->$act();
else
ecs_header("Location:http://m.wm18.com/\n");