<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class ad_position extends base
{
	private $page_size = 80; //一页显示的行数
	private $table_name="pocket_ad_position";
	function __construct()
	{
		@ini_set('display_errors',"On");
		$GLOBALS['smarty']->assign('nav_left',"ad_position");
		$GLOBALS['smarty']->assign('nav', "other");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"广告位置管理");
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
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name)." where name like '%".$keyword."%'  order by position_id desc";
		else
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name)."  order by position_id desc";
		$total = $GLOBALS['db']->getOne($sql);

		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;

		$page = new pagination($total, $this->page_size, $curpage, $url, 2);
		$GLOBALS['smarty']->assign('page_bar',$page->myde_write());

		if(!empty($keyword))
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where name like '%".$keyword."%'  order by sort_order desc, position_id desc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		else
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc,position_id desc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$val)
		{
			$info[$key]['postdate']=local_date("Y-m-d H:i",$info[$key]['postdate']);
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('ad_position.htm');
	}
	/**
	 * 添加分组
	 *
	 */
	function add()
	{
		$GLOBALS['smarty']->display('ad_position_add.htm');
	}
	function ajax_add()
	{
		$cat_name=$_POST['position_name'];
		if($cat_name=="")
		{
			$array=array("error"=>1,"info"=>"请输入广告位名称");
			echo json_encode($array);die();
		}
		$sql = "select position_id from ". $GLOBALS['ecs']->table($this->table_name)." where position_name='".$cat_name."' limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"广告位名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'position_name'     =>trim($_POST['position_name']),//广告位名称
		'ad_width'          =>trim($_POST['ad_width']),//广告位宽度
		'ad_height'         =>trim($_POST['ad_height']),//广告位高度
		'position_desc'     =>trim($_POST['position_desc']),//广告位描述
		'position_style'    =>trim($_POST['position_style']),//广告位模板
		'postdate'          =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data);
		$array=array("error"=>0,"info"=>"添加成功");
		echo json_encode($array);die();
	}
	function ajax_edit()
	{
		$cat_name=trim($_POST['position_name']);
		$cat_id_post=$_POST['position_id'];
		if($cat_name=="")
		{
			$array=array("error"=>1,"info"=>"请输入广告位名称");
			echo json_encode($array);die();
		}
		$sql = "select position_id from ". $GLOBALS['ecs']->table($this->table_name)." where position_name='".$cat_name."' and position_id!=".$cat_id_post." limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"广告位名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'position_name'     =>trim($_POST['position_name']),//广告位名称
		'ad_width'          =>trim($_POST['ad_width']),//广告位宽度
		'ad_height'         =>trim($_POST['ad_height']),//广告位高度
		'position_desc'     =>trim($_POST['position_desc']),//广告位描述
		'position_style'    =>trim($_POST['position_style']),//广告位模板
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"position_id=".$cat_id_post." limit 1");
		$array=array("error"=>0,"info"=>"修改成功");
		echo json_encode($array);die();
	}
	function edit()
	{
		$position_id=$_GET['position_id'];
		$cate = parent::table_get_row($this->table_name,$position_id,"position_id");
		$GLOBALS['smarty']->assign('cat_info', $cate);
		$GLOBALS['smarty']->display('ad_position_edit.htm');
	}
	/**
	 * 删除分类
	 *
	 */
	function delete()
	{
		$position_id=$_POST['position_id'];
		$cate = parent::table_get_row($this->table_name,$position_id,"position_id");
		if(!empty($cate))
		{
			$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name). " where position_id=".$position_id." limit 1";
			$GLOBALS['db']->query($sql);
		}
	}
	/**
	 * ajax保存排序
	 *
	 */
	function ajax_save_sort_order()
	{
		$position_id=$_POST['position_id'];
		$orderby=$_POST['orderby'];
		$data=array("sort_order"=>$orderby);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"position_id=".$position_id." limit 1");
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$ad_position = new ad_position();
$sign=@is_callable(array($ad_position,$act));
if($sign)
$ad_position->$act();
else
ecs_header("Location:http://m.wm18.com/\n");