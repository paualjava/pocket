<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class staff_invite extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_staff_invite";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"staff_invite");
		$GLOBALS['smarty']->assign('nav', "staff");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"创建邀请");
		$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
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
		/* 显示列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name);
		$total = $GLOBALS['db']->getOne($sql);

		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;

		$page = new pagination($total, $this->page_size, $curpage, $url, 2);
		$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by sort_order desc, id desc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$val)
		{
			$department="未分组";
			if(!empty($val['department']))
			{
				$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_department")." where id=".$val['department'];
				$department = $GLOBALS['db']->getOne($sql);
			}
			$info[$key]['department']=$department;
			$tag="";
			if(!empty($val['tag']))
			{
				$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_tag")." where id=".$val['tag'];
				$tag = $GLOBALS['db']->getOne($sql);
			}
			$info[$key]['tag']=$tag;
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('staff_invite.htm');
	}
	/**
	 * 添加邀请码
	 *
	 */
	function add()
	{
		//部门
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_department')." order by sort_order desc, id asc";
		$department = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('department', $department);
		//标签
		$sql = "select * from ". $GLOBALS['ecs']->table('pocket_staff_tag')." order by sort_order desc, id asc";
		$tag = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('tag', $tag);
		$GLOBALS['smarty']->display('staff_invite_add.htm');
	}
	function ajax_add()
	{
		$name=trim($_POST['name']);
		if($name=="")
		{
			$array=array("error"=>1,"info"=>"请输入邀请码名称");
			echo json_encode($array);die();
		}
		$sql = "select id from ". $GLOBALS['ecs']->table($this->table_name)." where name='".$name."' limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"邀请码名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'name'             =>$name,
		'department'       =>trim($_POST['department']),
		'tag'              =>trim($_POST['tag']),
		'info'             =>trim($_POST['info']),
		'postdate'         =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data);
		$array=array("error"=>0,"info"=>"添加成功");
		echo json_encode($array);die();
	}
	/**
	 * 编辑邀请码
	 *
	 */
	function ajax_edit()
	{
		$name=trim($_POST['name']);
		$id=$_POST['id'];
		if($name=="")
		{
			$array=array("error"=>1,"info"=>"请输入邀请码名称");
			echo json_encode($array);die();
		}
		$sql = "select id from ". $GLOBALS['ecs']->table($this->table_name)." where name='".$name."' and id!=".$id." limit 0,1";
		$cat_id = $GLOBALS['db']->getOne($sql);
		if($cat_id>0)
		{
			$array=array("error"=>1,"info"=>"邀请码名称已存在,请重新输入");
			echo json_encode($array);die();
		}
		$data = array(
		'name'         =>$name,
		'department'   =>trim($_POST['department']),
		'tag'          =>trim($_POST['tag']),
		'info'         =>trim($_POST['info']),
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name),$data,'',"id=".$id." limit 1");
		$array=array("error"=>0,"info"=>"修改成功");
		echo json_encode($array);die();
	}
	/**
	 * 编辑获取信息
	 *
	 */
	function ajax_edit_get_info()
	{
		$id=$_REQUEST['id'];
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where id='".$id."' limit 0,1";
		$info = $GLOBALS['db']->getRow($sql);
		$array=array("error"=>0,"info"=>$info);
		echo json_encode($array);die();
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$staff_invite = new staff_invite();
$sign=@is_callable(array($staff_invite,$act));
if($sign)
$staff_invite->$act();
else
ecs_header("Location:http://m.wm18.com/\n");