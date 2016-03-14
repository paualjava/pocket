<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class index extends base
{
	private $table_name_pocket_goods="pocket_goods";
	private $page_size = 20; //一页显示的行数
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav', "goods");
	}
	function main()
	{
		$sql = "SELECT goods_id, goods_name, goods_type, goods_sn, style_sn, product_sn, shop_price,market_price, integral_price, is_on_sale, is_best, is_new, is_hot, sort_order, goods_number, integral FROM ". $GLOBALS['ecs']->table('goods')." AS g WHERE  is_delete='0'  AND is_real='1' and is_pocket=1 and is_on_sale=1 ORDER BY goods_id DESC";
		$info = $GLOBALS['db']->getAll($sql);
		foreach ($info as $key => $val)
		{
			$sql = "SELECT count(*) FROM ".$GLOBALS ['ecs']->table($this->table_name_pocket_goods) . " WHERE goods_id = " .$val['goods_id'];
			$count=$GLOBALS ['db']->getOne ( $sql );
			if ($count == 0)
			{
				$data=array("goods_id"=>$val['goods_id'],"goods_name"=>$val['goods_name'],"shop_price"=>$val['shop_price'],"market_price"=>$val['market_price'],"sort_order"=>0,"is_show"=>1,"postdate"=>gmtime());
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name_pocket_goods),$data);
			}
		}
		/**如果产品的is_pocket=0那么删除此产品*/
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods);
		$info = $GLOBALS['db']->getAll($sql);
		foreach ($info as $key => $val)
		{
			$goods_id=$val['goods_id'];
			$sql = "select * from ". $GLOBALS['ecs']->table('goods')." where goods_id=".$goods_id;
			$info2 = $GLOBALS['db']->getRow($sql);
			if($info2['is_pocket']!=1)
			{
				$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name_pocket_goods). " where goods_id=".$goods_id." limit 1";
				$GLOBALS['db']->query($sql);
			}

		}
		/* 显示商品列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?keyword=".$keyword."&page={page}";
		$where='';
		$sql = "select count(*) from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods)."  where ".$where." is_show=1";
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		if ($total > $this->page_size) {//总记录数大于每页显示数，显示分页
			$page = new pagination($total, $this->page_size, $curpage, $url, 2);
			$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		}
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods)."  where ".$where." is_show=1 order by id desc LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		//分页
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$row)
		{
			$goods = get_goods_info($row['goods_id']);
			if(empty($goods))
			$goods = parent::get_goods_info($row['goods_id']);
			$info[$key]['goods_info']=$goods;
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('goods.htm');
	}
	function edit()
	{
		$goods_id=$_GET['goods_id'];
		$goods = get_goods_info($goods_id);
		if(empty($goods))
		$goods = parent::get_goods_info($goods_id);
		$info[$key]['goods_info']=$goods;
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('goods_edit.htm');
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$index = new index();
$sign=@is_callable(array($index,$act));
if($sign)
$index->$act();
else
ecs_header("Location:http://m.wm18.com/\n");