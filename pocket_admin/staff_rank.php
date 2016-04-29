<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class staff_rank extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_staff_rank";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav', "staff");
		$GLOBALS['smarty']->assign('title',"等级管理");
		$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
	}
	function main()
	{
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc, id asc";
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$val)
		{
			$info[$key]['rule']=(!empty($val['rule'])) ? "累计采购金额达到".$val['rule']."元" : "";
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('staff_rank.htm');
	}
	function ajax_edit()
	{
		$discount=trim($_POST['discount']);
		$rule=$_POST['rule'];
		$note=$_POST['note'];
		$id=$_POST['id'];
		$data = array(
		'discount'         =>$discount,
		'rule'             =>$rule,
		'note'             =>$note,
		);
		if($id)
		{
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
			$array=array("error"=>0,"info"=>"修改成功");
			echo json_encode($array);die();
		}
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$staff_rank = new staff_rank();
$sign=@is_callable(array($staff_rank,$act));
if($sign)
$staff_rank->$act();
else
ecs_header("Location:http://m.wm18.com/\n");