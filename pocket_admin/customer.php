<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class customer extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_staff";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav', "customer");
		$GLOBALS['smarty']->assign('title',"基本信息筛选");
		$GLOBALS['smarty']->assign('search_department',trim($_GET['department']));
		$GLOBALS['smarty']->assign('search_tag', $_REQUEST['tag']);
		$GLOBALS['smarty']->assign('search_name', $_REQUEST['name']);
		$GLOBALS['smarty']->assign('time_start',trim($_GET['time_start']));
		$GLOBALS['smarty']->assign('time_end',trim($_GET['time_end']));
	}
	function main()
	{
		//部门
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_department')." order by sort_order desc, id asc";
		$department = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('department', $department);
		//标签
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_tag')." order by sort_order desc, id asc";
		$tag = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('tag', $tag);
		$GLOBALS['smarty']->display('customer.htm');
	}
	function ajax_check_form()
	{
		$time_start=trim($_POST['time_start']);
		$time_end=trim($_POST['time_end']);
		$array=array("error"=>0);
		if(!empty($time_start) && empty($time_end))
		$array=array("error"=>1,"info"=>"请选择结束时间","column"=>"time_end");
		elseif(!empty($time_end) && empty($time_start))
		$array=array("error"=>1,"info"=>"请选择开始时间","column"=>"time_start");
		elseif(!empty($time_start) && !empty($time_end) && strtotime($time_start)>strtotime($time_end))
		$array=array("error"=>1,"info"=>"结束时间应该大于开始时间","column"=>"time_end");
		echo json_encode($array);die();
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$customer = new customer();
$sign=@is_callable(array($customer,$act));
if($sign)
$customer->$act();
else
ecs_header("Location:http://m.wm18.com/\n");