<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class income extends base
{
	private $table_name_pocket_goods="pocket_goods";
	private $page_size = 20; //一页显示的行数
	private $type = ''; //类型
	private $title = '收入/提现'; //关键词
	function __construct()
	{
		$this->type=$_GET['type'];
		$GLOBALS['smarty']->assign('nav_order',$this->type);
		$GLOBALS['smarty']->assign('nav', "order");
		$GLOBALS['smarty']->assign('title',$this->title);
	}
	function main()
	{
		$GLOBALS['smarty']->display('income.htm');
	}
	//交易明细
	function trade()
	{
		$GLOBALS['smarty']->display('income_trade.htm');
	}
	//收支明细
	function inoutdetail()
	{
		$GLOBALS['smarty']->display('income_inoutdetail.htm');
	}
	//提现记录
	function withdraw()
	{
		$GLOBALS['smarty']->display('income_withdraw.htm');
	}
	//保证金记录
	function deposit()
	{
		$GLOBALS['smarty']->display('income_deposit.htm');
	}
	//不可用余额
	function freeze()
	{
		$GLOBALS['smarty']->display('income_freeze.htm');
	}
	//发票管理
	function invoice()
	{
		$GLOBALS['smarty']->display('income_invoice.htm');
	}
	function getMenu($act)
	{
		$str="";
		$array=array(
		"main"=>"我的收入",
		"trade"=>"交易明细",
		"inoutdetail"=>"收支明细",
		"withdraw"=>"提现记录",
		"deposit"=>"保证金记录",
		"freeze"=>"不可用余额",
		"invoice"=>"发票管理"
		);
		foreach($array as $key=>$val)
		{
			$current=($key==$act) ? 'class="current"' : "";
			$str.='<li><a href="income.php?act='.$key.'" '.$current.'>'.$val.'</a></li>';
			$str.="\r\n";
		}
		if($act!="main")
		$GLOBALS['smarty']->assign('title', $array[$act]." - ".$this->title);
		$GLOBALS['smarty']->assign('menu',$str);
	}

}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$income = new income();
$sign=@is_callable(array($income,$act));
if($sign)
{
	$income->getMenu($act);
	$income->$act();
}
else
ecs_header("Location:http://m.wm18.com/\n");