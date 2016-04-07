<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class goods_cat extends base
{
	private $table_name="pocket_goods_cat";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"goods_cat");
		$GLOBALS['smarty']->assign('nav', "goods");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"商品分组");
	}
	function main()
	{
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc, cat_id desc";
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$v)
		{
			$info[$key]['time']=($v['time']>0) ? date("Y-m-d H:i:s",$v['time']) : "";
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('goods_cat.htm');
	}
	/**
	 * 添加分组
	 *
	 */
	function add()
	{
		$GLOBALS['smarty']->display('goods_cat_add.htm');
	}
	function ajax_add()
	{
		$cat_name=$_POST['cat_name'];
		if($cat_name=="")
		{
			$array=array("error"=>1,"info"=>"请输入分组名称");
			echo json_encode($array);die();
		}
		$sql = "select cat_id from ". $GLOBALS['ecs']->table($this->table_name)." where cat_name='".$cat_name."' limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"分组名称已存在");
			echo json_encode($array);die();
		}
		$data = array(
		'cat_name'         =>trim($_POST['cat_name']),
		'cat_desc'         =>trim($_POST['cat_desc']),
		'time'             =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data);
		$array=array("error"=>0,"info"=>"添加成功");
		echo json_encode($array);die();
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$goods_cat = new goods_cat();
$sign=@is_callable(array($goods_cat,$act));
if($sign)
$goods_cat->$act();
else
ecs_header("Location:http://m.wm18.com/\n");