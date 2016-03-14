<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class index extends base
{
	private $table_name_pocket_goods="pocket_goods";
	private $page_size = 20; //一页显示的行数
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav', "order");
	}
	function main()
	{
		$this->order_list();
		$GLOBALS['smarty']->display('order.htm');
	}
	function order_list()
	{
		/* 显示商品列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?keyword=".$keyword."&page={page}";
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " $where";
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		if ($total > $this->page_size) {//总记录数大于每页显示数，显示分页
			$page = new pagination($total, $this->page_size, $curpage, $url, 2);
			$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		}
		$sql = "SELECT o.order_id, o.order_sn,o.downloadid, o.add_time, o.order_status, o.shipping_status, o.order_amount, o.money_paid," .
		"o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, o.is_from_mobile_phone ,
		( o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee ) AS total_fee".
		" FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " . $where ." ORDER BY order_id desc ".
		" LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		$info = $GLOBALS['db']->getAll($sql);
		/*foreach($info as $key=>$row)
		{
			$goods = get_goods_info($row['goods_id']);
			if(empty($goods))
			$goods = parent::get_goods_info($row['goods_id']);
			$info[$key]['goods_info']=$goods;
		}*/
		$GLOBALS['smarty']->assign('info',$info);
	}

}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$index = new index();
$sign=@is_callable(array($index,$act));
if($sign)
$index->$act();
else
ecs_header("Location:http://m.wm18.com/\n");