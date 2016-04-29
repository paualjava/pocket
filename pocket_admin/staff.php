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
		$GLOBALS['smarty']->assign('title',"商品分组");
		$GLOBALS['smarty']->assign('search_rank',trim($_GET['rank']));
		$GLOBALS['smarty']->assign('search_department',trim($_GET['department']));
		$GLOBALS['smarty']->assign('search_tag', $_REQUEST['tag']);
		$GLOBALS['smarty']->assign('time_start',trim($_GET['time_start']));
		$GLOBALS['smarty']->assign('time_end',trim($_GET['time_end']));
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('type', $_REQUEST['type']);
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
			$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_invite")." where id='".$row['invatation']."' limit 0,1";
			$invatation = $GLOBALS['db']->getOne($sql);
			$info[$key]['invatation']=$invatation;
			//等级
			$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_rank")." where id='".$row['rank']."' limit 0,1";
			$rank = $GLOBALS['db']->getOne($sql);
			$info[$key]['rank_name']=$rank;
			//标签
			$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_tag")." where id='".$row['tag']."' limit 0,1";
			$tag = $GLOBALS['db']->getOne($sql);
			$info[$key]['tag_name']=$tag;
		}
		//部门
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_department')." order by sort_order desc, id asc";
		$department = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('department', $department);
		//标签
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_tag')." order by sort_order desc, id asc";
		$tag = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('tag', $tag);
		//等级
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_rank')." order by sort_order desc, id asc";
		$rank = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('rank', $rank);
		//var_dump($info);die();
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->assign('info_count', count($info));
		$GLOBALS['smarty']->display('staff.htm');
	}
	function get_search_count_where()
	{
		$keyword=trim($_GET['keyword']);
		if(!empty(trim($_GET['rank'])))
		$where.=" and rank='".trim($_GET['rank'])."'";
		if(!empty($_GET['department']))
		$where.=" and department='".trim($_GET['department'])."'";
		if(!empty($_GET['tag']))
		$where.=" and tag='".trim($_GET['tag'])."'";
		if(!empty($_GET['type']) && $keyword)
		{
			if($_GET['type']==1)
			$where.=" and name='".$keyword."'";
			elseif($_GET['type']==2)
			$where.=" and phone='".$keyword."'";
			elseif($_GET['type']==3)
			$where.=" and shop_name='".$keyword."'";
			elseif($_GET['type']==4)
			{
				$sql = "select id from ". $GLOBALS['ecs']->table("pocket_staff_invite")." where name='".$keyword."' limit 0,1";
				$invatation_id = $GLOBALS['db']->getOne($sql);
				if($invatation_id>0)
				$where.=" and invatation='".$invatation_id."'";
				else 
				$where.=" id<0";
			}
			elseif($_GET['type']==5)
			$where.=" and invatation_person='".$keyword."'";
		}
		return $where;

		/*if($_REQUEST['rank'])
		return ' and rank='.$_REQUEST['rank'];
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
		}*/
	}
	function get_search_where()
	{
		$keyword=trim($_GET['keyword']);
		if(!empty(trim($_GET['rank'])))
		$where.=" and rank='".trim($_GET['rank'])."'";
		if(!empty($_GET['department']))
		$where.=" and department='".trim($_GET['department'])."'";
		if(!empty($_GET['tag']))
		$where.=" and tag='".trim($_GET['tag'])."'";
		if(!empty($_GET['type'])  && $keyword)
		{
			if($_GET['type']==1)
			$where.=" and name='".$keyword."'";
			elseif($_GET['type']==2)
			$where.=" and phone='".$keyword."'";
			elseif($_GET['type']==3)
			$where.=" and shop_name='".$keyword."'";
			elseif($_GET['type']==4)
			{
				$sql = "select id from ". $GLOBALS['ecs']->table("pocket_staff_invite")." where name='".$keyword."' limit 0,1";
				$invatation_id = $GLOBALS['db']->getOne($sql);
				if($invatation_id>0)
				$where.=" and invatation='".$invatation_id."'";
				else 
				$where.=" id<0";
			}
			elseif($_GET['type']==5)
			$where.=" and invatation_person='".$keyword."'";
		}
		return $where;
		/*if($this->type=="is_show")
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
		}*/

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
	 * 修改邀请人
	 *
	 */
	function ajax_save_phone()
	{
		$id=$_POST['id'];
		$phone=$_POST['phone'];
		if($phone && preg_match("/^0?1[3|4|5|6|8|7][0-9]{9}$/",$phone))
		{
			$data=array("invatation_person"=>$phone);
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
			echo json_encode(array("info"=>"ok","error"=>"0"));die();
		}
		else
		{
			echo json_encode(array("info"=>"'请输入正确的手机号'","error"=>"1"));die();
		}
	}
	/**
	 * 修改等级
	 *
	 */
	function ajax_save_rank()
	{
		$id=$_POST['id'];
		$rank=$_POST['rank'];
		if($rank)
		{
			$data=array("rank"=>$rank);
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
			echo json_encode(array("info"=>"ok","error"=>"0"));die();
		}
	}
	/**
	 * 修改部门
	 *
	 */
	function ajax_save_department()
	{
		$id=$_POST['id'];
		$department=$_POST['department'];
		$data=array("department"=>$department);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
		echo json_encode(array("info"=>"ok","error"=>"0"));die();
	}
	/**
	 * 修改标签
	 *
	 */
	function ajax_save_tag()
	{
		$id=$_POST['id'];
		$tag=$_POST['tag'];
		$data=array("tag"=>$tag);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
		echo json_encode(array("info"=>"ok","error"=>"0"));die();
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