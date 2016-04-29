<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class staff_invite_fired extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_staff";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"staff_invite_fired");
		$GLOBALS['smarty']->assign('nav', "staff");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"已清退员工");
		$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
	}
	function main()
	{
		/* 显示列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		$sql = "select count(*) from ". $GLOBALS['ecs']->table($this->table_name)." where status=2 ";
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		if ($total > $this->page_size) {//总记录数大于每页显示数，显示分页
			$page = new pagination($total, $this->page_size, $curpage, $url, 2);
			$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		}
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where status=2  order by id desc LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		//分页
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$row)
		{
			$sql = "select name from ". $GLOBALS['ecs']->table("pocket_staff_invite")." where id='".$row['invatation']."' limit 0,1";
			$invatation = $GLOBALS['db']->getOne($sql);
			$info[$key]['invatation_name']=$invatation;
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
		$GLOBALS['smarty']->display('staff_invite_fired.htm');
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$staff_invite_fired = new staff_invite_fired();
$sign=@is_callable(array($staff_invite_fired,$act));
if($sign)
$staff_invite_fired->$act();
else
ecs_header("Location:http://m.wm18.com/\n");