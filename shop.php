<?php
require(dirname(__FILE__)."/includes/common.php");
class shop extends base
{
	function index()
	{
		//商品列表1
		$sql = "select goods_id,goods_name,shop_price,goods_img from ". $GLOBALS['ecs']->table('pocket_goods')."  where  is_show=1 and is_on_sale=1 order by goods_id desc limit 8,4";
		$more_list = $GLOBALS['db']->getAll($sql);
		foreach($more_list as $key=>$row)
		{
			$goods = parent::get_goods_info($row['goods_id']);
			$more_list[$key]['goods_info']=$goods;
		}
		$GLOBALS['smarty']->assign('list1',$more_list);
		//商品列表2
		$sql = "select goods_id,goods_name,shop_price,goods_img from ". $GLOBALS['ecs']->table('pocket_goods')."  where  is_show=1 and is_on_sale=1 order by goods_id desc limit 4,4";
		$more_list = $GLOBALS['db']->getAll($sql);
		foreach($more_list as $key=>$row)
		{
			$goods = parent::get_goods_info($row['goods_id']);
			$more_list[$key]['goods_info']=$goods;
		}
		$GLOBALS['smarty']->assign('list2',$more_list);
		//商品列表3
		$sql = "select goods_id,goods_name,shop_price,goods_img from ". $GLOBALS['ecs']->table('pocket_goods')."  where  is_show=1 and is_on_sale=1 order by goods_id desc limit 0,4";
		$more_list = $GLOBALS['db']->getAll($sql);
		foreach($more_list as $key=>$row)
		{
			$goods = parent::get_goods_info($row['goods_id']);
			$more_list[$key]['goods_info']=$goods;
		}
		$GLOBALS['smarty']->assign('list3',$more_list);
		$GLOBALS['smarty']->display('shop.htm');
	}
}
$act=(empty($_REQUEST['act'])) ? "index" : $_REQUEST['act'];
$shop = new shop();
$sign=@is_callable(array($shop,$act));
if($sign)
$shop->$act();
else
$shop->location_main();