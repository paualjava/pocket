<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class ad extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_ad";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"ad");
		$GLOBALS['smarty']->assign('nav', "staff");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"标签管理");
		$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
	}
	function main()
	{
		/* 显示列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		
		$keyword=$_GET['keyword'];
		if(!empty($keyword))
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name)." where name like '%".$keyword."%'  order by sort_order desc, id asc";
		else
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc, id asc";
		$total = $GLOBALS['db']->getOne($sql);
		
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;

		$page = new pagination($total, $this->page_size, $curpage, $url, 2);
		$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		
		if(!empty($keyword))
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where name like '%".$keyword."%'  order by sort_order desc, id asc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		else
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc, id asc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		$info = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('ad.htm');
	}
	/**
	 * 添加分组
	 *
	 */
	function add()
	{
		$GLOBALS['smarty']->display('ad_add.htm');
	}
	function ajax_add()
	{
		$cat_name=$_POST['cat_name'];
		if($cat_name=="")
		{
			$array=array("error"=>1,"info"=>"请输入标签名称");
			echo json_encode($array);die();
		}
		$sql = "select id from ". $GLOBALS['ecs']->table($this->table_name)." where name='".$cat_name."' limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"标签名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'name'             =>trim($_POST['cat_name']),
		'info'             =>trim($_POST['cat_desc']),
		'postdate'         =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data);
		$array=array("error"=>0,"info"=>"添加成功");
		echo json_encode($array);die();
	}
	function ajax_edit()
	{
		$cat_name=trim($_POST['cat_name']);
		$cat_id_post=$_POST['cat_id'];
		if($cat_name=="")
		{
			$array=array("error"=>1,"info"=>"请输入标签名称");
			echo json_encode($array);die();
		}
		$sql = "select id from ". $GLOBALS['ecs']->table($this->table_name)." where name='".$cat_name."' and id!=".$cat_id_post." limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"标签名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'name'         =>$cat_name,
		'info'         =>trim($_POST['cat_desc']),
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$cat_id_post." limit 1");
		$array=array("error"=>0,"info"=>"修改成功");
		echo json_encode($array);die();
	}
	function edit()
	{
		$cat_id=$_GET['tag_id'];
		$cate = parent::table_get_row($this->table_name,$cat_id,"id");
		$GLOBALS['smarty']->assign('cat_info', $cate);
		$GLOBALS['smarty']->display('ad_edit.htm');
	}
	/**
	 * 删除分类
	 *
	 */
	function delete()
	{
		$cat_id=$_POST['cat_id'];
		$cate = parent::table_get_row($this->table_name,$cat_id,"id");
		if(!empty($cate))
		{
			$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name). " where id=".$cat_id." limit 1";
			$GLOBALS['db']->query($sql);
		}
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$ad = new ad();
$sign=@is_callable(array($ad,$act));
if($sign)
$ad->$act();
else
ecs_header("Location:http://m.wm18.com/\n");