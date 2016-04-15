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
		$goods_id=$_GET['goods_id'];
		$type=(in_array($_GET['type'],array(1,2))) ? $_GET['type'] : 1;
		if(preg_match("/^\d+/is",$pid) && $pid>0)
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_goods_preview')."  where pid='".$pid."' limit 0,1";
		elseif(preg_match("/^\d+/is",$goods_id) && $goods_id>0)
		$sql = "select * from ". $GLOBALS['ecs']->table('goods')."  where goods_id='".$goods_id."' limit 0,1";
		else 
		parent::location_main();
		$info = $GLOBALS['db']->getRow($sql);
		if($info)
		{
			$GLOBALS['smarty']->assign('goods_info', $info);
			$GLOBALS['smarty']->assign('pid', $pid);
			$GLOBALS['smarty']->assign('type', $type);
			$GLOBALS['smarty']->assign('goods_id', $goods_id);
			//图片
			$sql = "select img_url from ". $GLOBALS['ecs']->table('goods_gallery')."  where goods_id='".$info['goods_id']."' limit 0,5";
			$pic_list = $GLOBALS['db']->getAll($sql);
			$GLOBALS['smarty']->assign('pic_list', $pic_list);
			//更多商品
			$sql = "select goods_id,goods_name,shop_price,goods_img from ". $GLOBALS['ecs']->table('pocket_goods')."  where goods_id!='".$info['goods_id']."' and is_show=1 and is_on_sale=1 order by rand() limit 0,4";
			$more_list = $GLOBALS['db']->getAll($sql);
			foreach($more_list as $key=>$row)
			{
				$goods = parent::get_goods_info($row['goods_id']);
				$more_list[$key]['goods_info']=$goods;
			}

			$GLOBALS['smarty']->assign('more_list', $more_list);
			if($type!=2)
			$GLOBALS['smarty']->display('goods_preview.htm');
			else
			$GLOBALS['smarty']->display('goods_preview2.htm');
		}
		else
		parent::location_main();
	}
	
	function preview_qrcode()
	{
		//url=goods.php?act=detail&goods_id=2323
		$pid=$_GET['pid'];
		$type=$_GET['type'];
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_goods_preview')."  where pid='".$pid."' limit 0,1";
		$info = $GLOBALS['db']->getRow($sql);
		if($info)
		{
			$GLOBALS['smarty']->assign('goods_info', $info);
			$GLOBALS['smarty']->assign('pid', $pid);
			//图片
			$sql = "select img_url from ". $GLOBALS['ecs']->table('goods_gallery')."  where goods_id='".$info['goods_id']."' limit 0,5";
			$pic_list = $GLOBALS['db']->getAll($sql);
			$GLOBALS['smarty']->assign('pic_list', $pic_list);
			//更多商品
			$sql = "select goods_id,goods_name,shop_price,goods_img from ". $GLOBALS['ecs']->table('pocket_goods')."  where goods_id!='".$info['goods_id']."' and is_show=1 and is_on_sale=1 order by rand() limit 0,4";
			$more_list = $GLOBALS['db']->getAll($sql);
			foreach($more_list as $key=>$row)
			{
				$goods = parent::get_goods_info($row['goods_id']);
				$more_list[$key]['goods_info']=$goods;
			}

			$GLOBALS['smarty']->assign('more_list', $more_list);
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