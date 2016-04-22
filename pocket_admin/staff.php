<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class staff extends base
{
	private $table_name="pocket_staff";
	private $page_size = 20; //一页显示的行数
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"staff");
		$GLOBALS['smarty']->assign('nav', "staff");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"商品分组");
		$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
	}
	function main()
	{
		/* 显示商品列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		$where=$this->get_search_count_where();
		$sql = "select count(*) from ". $GLOBALS['ecs']->table($this->table_name)." where status=1 ".$where;
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		if ($total > $this->page_size) {//总记录数大于每页显示数，显示分页
			$page = new pagination($total, $this->page_size, $curpage, $url, 2);
			$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		}
		$where=$this->get_search_where();
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where status=1 ".$where." order by id desc LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		//分页
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$row)
		{
			$time=($row['time']) ? $row['time'] : time();
			$info[$key]['time1']=date("Y-m-d");
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('staff.htm');
	}
	function get_search_count_where()
	{
		if($this->type=="is_show")
		return 'where is_show=1';
		else if($this->type=="is_show_no")
		return 'where is_show=2';
		else if($this->type=="sell_out")
		return 'where goods_number=0';
		else if($this->type=="search")
		{
			$keyword=$_REQUEST['keyword'];
			if(!empty($keyword))
			{
				if(preg_match("/^\d+$/is",$keyword))
				return "where goods_id='$keyword' or goods_name='$keyword'";
				else
				{
					return "where goods_sn='$keyword' or goods_name like '%$keyword%'";
				}
			}
		}
	}
	function get_search_where()
	{
		if($this->type=="is_show")
		return 'where is_show=1';
		else if($this->type=="is_show_no")
		return 'where is_show=2';
		else if($this->type=="sell_out")
		return 'where goods_number=0';
		else if($this->type=="search")
		{
			$keyword=$_REQUEST['keyword'];
			if(!empty($keyword))
			{
				if(preg_match("/^\d+$/is",$keyword))
				return "where goods_id='$keyword' or goods_name='$keyword'";
				else
				{
					return "where goods_sn='$keyword' or goods_name like '%$keyword%'";
				}
			}
		}

	}
	/**
	 * 修改名字
	 *
	 */
	function ajax_save_name()
	{
		$id=$_POST['id'];
		$name=$_POST['name'];
		$data=array("name"=>$name);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
		echo json_encode(array("info"=>"ok"));die();
	}
		/**
	 * 清退会员
	 *
	 */
	function ajax_qingtui()
	{
		$id=$_POST['id'];
		$data=array("status"=>2);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
		echo json_encode(array("info"=>"ok"));die();
	}
	
	/**
	 * 删除分类
	 *
	 */
	function delete()
	{
		$cat_id=$_POST['cat_id'];
		$cate = parent::table_get_row($this->table_name,$cat_id,$id="cat_id");
		if(!empty($cate))
		{
			$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name). " where cat_id=".$cat_id." limit 1";
			$GLOBALS['db']->query($sql);
		}
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$staff = new staff();
$sign=@is_callable(array($staff,$act));
if($sign)
$staff->$act();
else
ecs_header("Location:http://m.wm18.com/\n");