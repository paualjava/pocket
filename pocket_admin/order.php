<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class index extends base
{
	private $table_name_pocket_goods="pocket_goods";
	private $page_size = 20; //一页显示的行数
	private $type = ''; //类型
	function __construct()
	{
		global $_LANG;
		$GLOBALS['smarty']->assign('lang', $_LANG);
		$this->type=$_GET['type'];
		$GLOBALS['smarty']->assign('nav_order',$this->type);
		$GLOBALS['smarty']->assign('nav', "order");
		$GLOBALS['smarty']->assign('title', "订单概况");
		$GLOBALS['smarty']->assign('order_sn',trim($_GET['order_sn']));
		$GLOBALS['smarty']->assign('time_start',trim($_GET['time_start']));
		$GLOBALS['smarty']->assign('time_end',trim($_GET['time_end']));
		$GLOBALS['smarty']->assign('address',trim($_GET['address']));
		$GLOBALS['smarty']->assign('mobile',trim($_GET['mobile']));
		$GLOBALS['smarty']->assign('pay_id',trim($_GET['pay_id']));
		$GLOBALS['smarty']->assign('shipping_id',trim($_GET['shipping_id']));
		$GLOBALS['smarty']->assign('pay_status',trim($_GET['pay_status']));
		$GLOBALS['smarty']->assign('wx_nickname',trim($_GET['wx_nickname']));
		$GLOBALS['smarty']->assign('payment_list',$this->get_payment_list());
		$GLOBALS['smarty']->assign('shipping_list',$this->get_shipping_list());
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
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		$where=$this->get_search_count_where();
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " $where";

		$total = $GLOBALS['db']->getOne($sql);
		//echo $sql."<BR><BR><BR>";
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;

		$page = new pagination($total, $this->page_size, $curpage, $url, 2);
		$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		$where=$this->get_search_where();
		$sql = "SELECT o.order_id, o.order_sn,o.downloadid, o.add_time, o.order_status, o.shipping_status, o.order_amount, o.money_paid," .
		"o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, o.is_from_mobile_phone ,
		( o.goods_amount + o.tax + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee ) AS total_fee,o.pay_name,o.pay_id".
		" FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " . $where ." ORDER BY order_id desc ".
		" LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		//echo $sql;
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$row)
		{
			$goods_id = parent::table_get_one("order_goods","goods_id",$row['order_id'],"order_id");
			if($goods_id)
			{
				$goods = get_goods_info($goods_id);
				if(empty($goods))
				$goods = parent::get_goods_info($goods_id);
				$info[$key]['goods_info']=$goods;
				$info[$key]['order_info']=$this->get_order_info($row['order_id']);
				$sql = "SELECT remark FROM " . $GLOBALS['ecs']->table('pocket_order_remark') . " where order_id='".$row['order_id']."' order by  remark_id desc limit 0,1";
				$remark = $GLOBALS['db']->getOne($sql);
				$info[$key]['remark']=($remark) ? "卖价备注：".$remark : "";
				$outside_sn=$this->get_order_outside_sn($row['pay_id'],$row['order_sn']);
				$info[$key]['outside_sn']=$outside_sn;//外部订单号 out_trade_no,外部交易号 transid
			}
		}
		$GLOBALS['smarty']->assign('info',$info);
	}
	function get_search_count_where()
	{
		if($this->type=="wait_pay")
		return 'where pay_status=0';
		if($this->type=="wait_shipping")
		return 'where pay_status=2 and shipping_status=0';
		if($this->type=="aleady_shipping")
		return 'where shipping_status=1';
		if($_GET['search_type']=="search_order")
		{
			$where=" where order_id>0 ";
			if(!empty(trim($_GET['order_sn'])))
			$where.=" and order_sn='".trim($_GET['order_sn'])."'";
			if(!empty($_GET['time_start']))
			$where.=" and add_time>='".strtotime(trim($_GET['time_start']))."'";
			if(!empty($_GET['time_end']))
			$where.=" and add_time<='".strtotime(trim($_GET['time_end']))."'";
			if(!empty($_GET['mobile']))
			$where.=" and mobile='".trim($_GET['mobile'])."'";
			if(!empty($_GET['address']))
			$where.=" and address='".trim($_GET['address'])."'";
			if(!empty($_GET['pay_id']))
			$where.=" and pay_id='".trim($_GET['pay_id'])."'";
			if(!empty($_GET['shipping_id']))
			$where.=" and shipping_id='".trim($_GET['shipping_id'])."'";
			if(isset($_GET['pay_status']))
			{
				if($_GET['pay_status']==1)
				$where.=" and pay_status=0";
				elseif($_GET['pay_status']==2)
				$where.=" and pay_status='2'";
			}
			//微信昵称查找
			if(!empty($_GET['wx_nickname']))
			{
				$user_list = parent::table_get_list("users","where wx_nickname='".trim($_GET['wx_nickname'])."'","user_id");
				if(!empty($user_list))
				{
					$user_id="(";
					foreach($user_list as $user_v)
					{
						$user_id.=$user_v['user_id'].",";
					}
					$user_id=(!empty($user_id)) ? substr($user_id,0,strlen($user_id)-1).")" : "";
					$where.=(!empty($user_id)) ? " and user_id in ".$user_id : "";
				}
				else
				$where=' where user_id=-1';
			}
			return $where;
		}
	}
	function get_search_where()
	{
		if($this->type=="wait_pay")
		return 'where o.pay_status=0';
		if($this->type=="wait_shipping")
		return 'where o.pay_status=2 and o.shipping_status=0';
		if($this->type=="aleady_shipping")
		return 'where shipping_status=1';
		if($_GET['search_type']=="search_order")
		{
			$where=" where o.order_id>0 ";
			if(!empty($_GET['order_sn']))
			$where.=" and o.order_sn='".trim($_GET['order_sn'])."'";
			if(!empty($_GET['time_start']))
			$where.=" and o.add_time>='".strtotime(trim($_GET['time_start']))."'";
			if(!empty($_GET['time_end']))
			$where.=" and o.add_time<='".strtotime(trim($_GET['time_end']))."'";
			if(!empty($_GET['mobile']))
			$where.=" and o.mobile='".trim($_GET['mobile'])."'";
			if(!empty($_GET['address']))
			$where.=" and o.address='".trim($_GET['address'])."'";
			if(!empty($_GET['pay_id']))
			$where.=" and o.pay_id='".trim($_GET['pay_id'])."'";
			if(!empty($_GET['shipping_id']))
			$where.=" and o.shipping_id='".trim($_GET['shipping_id'])."'";
			if(isset($_GET['pay_status']))
			{
				if($_GET['pay_status']==1)
				$where.=" and pay_status=0";
				elseif($_GET['pay_status']==2)
				$where.=" and pay_status='2'";
			}
			//微信昵称查找
			if(!empty($_GET['wx_nickname']))
			{
				$user_list = parent::table_get_list("users","where wx_nickname='".trim($_GET['wx_nickname'])."'","user_id");
				if(!empty($user_list))
				{
					$user_id="(";
					foreach($user_list as $user_v)
					{
						$user_id.=$user_v['user_id'].",";
					}
					$user_id=(!empty($user_id)) ? substr($user_id,0,strlen($user_id)-1).")" : "";
					$where.=(!empty($user_id)) ? " and o.user_id in ".$user_id : "";
				}
				else
				$where=' where o.user_id=-1';
			}
			return $where;
		}
	}
	/**
	 * 外部订单号
	 *
	 * @param unknown_type $pay_id
	 * @param unknown_type $order_sn
	 * @return unknown
	 */
	function get_order_outside_sn($pay_id,$order_sn)
	{
		//微信支付
		$array=array();
		if($pay_id==26)
		{
			$pay_info = parent::table_get_row("weixin_pay_log",$order_sn,"order_sn");
			$array=array("out_trade_no"=>$pay_info['out_trade_no'],"transid"=>$pay_info['transid']);
		}
		return $array;
	}
	/**
	 * 获取备注
	 *
	 * @param unknown_type $order_id
	 * @return unknown
	 */
	function ajax_remark_get()
	{
		$order_id=$_POST['order_id'];
		$sql = "SELECT order_id,remark FROM " . $GLOBALS['ecs']->table('pocket_order_remark') . " where order_id='".$order_id."' order by  remark_id desc limit 0,1";
		$remark = $GLOBALS['db']->getRow($sql);
		$array=array("remark"=>$remark['remark'],"order_id"=>$order_id);
		echo json_encode($array);die();
	}
	function ajax_remark_save()
	{
		$order_id=$_POST['order_id'];
		$remark=$_POST['remark'];
		$data=array("remark"=>$remark,"order_id"=>$order_id,"time"=>gmtime());
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('pocket_order_remark'),$data);
		$array=array("remark"=>$remark,"order_id"=>$order_id);
		echo json_encode($array);die();
	}
	function get_order_info($order_id)
	{
		$order_row = parent::table_get_row("order_info",$order_id,"order_id");
		return $order_row;
	}
	/**
	 * 订单详情
	 *
	 */
	function info()
	{
		global $_CFG;
		require_once(ROOT_PATH . 'includes/lib_order.php');
		$order_id=$_GET['order_id'];
		$order=order_info($order_id);
		/* 取得区域名 */
		$sql = "SELECT concat(IFNULL(c.region_name, ''), '  ', IFNULL(p.region_name, ''), " .
		"'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS c ON o.country = c.region_id " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS p ON o.province = p.region_id " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS t ON o.city = t.region_id " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS d ON o.district = d.region_id " .
		"WHERE o.order_id = '$order[order_id]'";
		$order['region'] = $GLOBALS['db']->getOne($sql);
		/* 格式化金额 */
		if ($order['order_amount'] < 0)
		{
			$order['money_refund']          = abs($order['order_amount']);
			$order['formated_money_refund'] = price_format(abs($order['order_amount']));
		}
		/* 其他处理 */
		$order['order_time']    = local_date($_CFG['time_format'], $order['add_time']);
		$order['pay_time']      = $order['pay_time'] > 0 ? local_date($_CFG['time_format'], $order['pay_time']) : $_LANG['ps'][PS_UNPAYED];
		$order['shipping_time'] = $order['shipping_time'] > 0 ? local_date($_CFG['time_format'], $order['shipping_time']) : $_LANG['ss'][SS_UNSHIPPED];
		$order['status']        = $_LANG['os'][$order['order_status']] . ',' . $_LANG['ps'][$order['pay_status']] . ',' . $_LANG['ss'][$order['shipping_status']];
		$order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED || $order['shipping_status'] == SS_PREPARING ? $_LANG['ss'][SS_UNSHIPPED] : $order['invoice_no'];

		/* 此订单的发货备注(此订单的最后一条操作记录) */

		$sql = "SELECT action_note FROM " . $GLOBALS['ecs']->table('order_action').
		" WHERE order_id = '$order[order_id]' AND shipping_status = 1 ORDER BY log_time DESC";
		$order['invoice_note'] = $GLOBALS['db']->getOne($sql);
		//卖价备注
		$sql = "SELECT remark FROM " . $GLOBALS['ecs']->table('pocket_order_remark') . " where order_id='".$order_id."' order by  remark_id desc limit 0,1";
		$remark = $GLOBALS['db']->getOne($sql);
		$GLOBALS['smarty']->assign('remark',(($remark) ? $remark : ""));
		//是否使用优惠券
		$bonus_name='';
		if($order['bonus_id'])
		{
			$sql = "SELECT bonus_type_id FROM " . $GLOBALS['ecs']->table('user_bonus') . " where bonus_id='".$order['bonus_id']."' limit 0,1";
			$bonus_type_id = $GLOBALS['db']->getOne($sql);
			$sql = "SELECT type_name,type_money FROM " . $GLOBALS['ecs']->table('bonus_type') . " where type_id='".$bonus_type_id."' limit 0,1";
			$bonus_name = $GLOBALS['db']->getRow($sql);
			$bonus_name=$bonus_name['type_name'];
		}
		$sql = "SELECT remark FROM " . $GLOBALS['ecs']->table('pocket_order_remark') . " where order_id='".$order_id."' order by  remark_id desc limit 0,1";
		$remark = $GLOBALS['db']->getOne($sql);
		$GLOBALS['smarty']->assign('remark',(($remark) ? $remark : ""));
		/* 取得订单商品总重量 */
		$weight_price = order_weight_price($order['order_id']);
		$order['total_weight'] = $weight_price['formated_weight'];
		$GLOBALS['smarty']->assign('title', "订单详情");
		$GLOBALS['smarty']->assign('order', $order);
		$GLOBALS['smarty']->assign('bonus_name', $bonus_name);
		$GLOBALS['smarty']->display('order_info.htm');
	}
	function ajax_check_form()
	{
		$time_start=trim($_POST['time_start']);
		$time_end=trim($_POST['time_end']);
		$order_sn=trim($_POST['order_sn']);
		$mobile=trim($_POST['mobile']);
		$array=array("error"=>0);
		if(!empty($time_start) && empty($time_end))
		$array=array("error"=>1,"info"=>"请选择结束时间","column"=>"time_end");
		elseif(!empty($time_end) && empty($time_start))
		$array=array("error"=>1,"info"=>"请选择开始时间","column"=>"time_start");
		elseif(!empty($time_start) && !empty($time_end) && strtotime($time_start)>strtotime($time_end))
		$array=array("error"=>1,"info"=>"结束时间应该大于开始时间","column"=>"time_end");
		elseif(!empty($order_sn) && !preg_match("/^\d+$/is",$order_sn))
		$array=array("error"=>1,"info"=>"订单号有误","column"=>"order_sn");
		elseif(!empty($mobile) && !preg_match("/^\d{11}$/is",$mobile))
		$array=array("error"=>1,"info"=>"收货人手机号有误","column"=>"mobile");
		echo json_encode($array);die();
	}
	function get_payment_list()
	{
		$sql = "SELECT pay_id,pay_desc FROM " . $GLOBALS['ecs']->table('payment') . " where enabled=1 and pay_id not in(5,27)";
		$info = $GLOBALS['db']->getAll($sql);
		return $info;
	}
	function get_shipping_list()
	{
		$sql = "SELECT shipping_id,shipping_name FROM " . $GLOBALS['ecs']->table('shipping') . " where enabled=1 and length(shipping_name)>1";
		$info = $GLOBALS['db']->getAll($sql);
		return $info;
	}
	function overview()
	{
		$GLOBALS['smarty']->assign('title', "订单概述");
		$GLOBALS['smarty']->display('order_overview.htm');
	}

}
include(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/' . basename(PHP_SELF));
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$index = new index();
$sign=@is_callable(array($index,$act));
if($sign)
$index->$act();
else
ecs_header("Location:http://m.wm18.com/\n");