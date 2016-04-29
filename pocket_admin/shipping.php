<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class shipping extends base
{
	private $page_size = 20; //一页显示的行数
	private $table_name="pocket_shipping_area";
	function __construct()
	{
		$GLOBALS['smarty']->assign('nav_left',"pocket_shipping_area");
		$GLOBALS['smarty']->assign('nav', "order");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title',"物流工具");
		//$GLOBALS['smarty']->assign('BASEE_URL','http://'.$_SERVER['SERVER_NAME'].":8090". str_replace( '/pocket_admin' , '' , str_replace( $_SERVER['DOCUMENT_ROOT'],'' , str_replace('\\', '/', dirname(__FILE__) ))  ).'/' );
	}
	function main()
	{
		/* 显示列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?".$_SERVER['QUERY_STRING'];
		$url=preg_replace("/&page=\d+$/is","",$url)."&page={page}";
		$keyword=$_GET['keyword'];
		if(!empty($keyword))
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name)." where shipping_area_name like '%".$keyword."%'";
		else
		$sql = "select COUNT(*) from ". $GLOBALS['ecs']->table($this->table_name);
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		$page = new pagination($total, $this->page_size, $curpage, $url, 2);
		$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		if(!empty($keyword))
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)." where name like '%".$keyword."%'  order by shipping_area_id desc  LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		else
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name)."  order by shipping_area_id desc LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		$info = $GLOBALS['db']->getAll($sql);
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('shipping.htm');
	}
	/**
	 * 添加分组
	 *
	 */
	function add()
	{
		$GLOBALS['smarty']->display('shipping_add.htm');
	}
	function ajax_insert()
	{
		/* 检查同类型的配送方式下有没有重名的配送区域 */
		$sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table($this->table_name).
		" WHERE shipping_id='$_POST[shipping]' AND shipping_area_name='$_POST[shipping_area_name]'";
		if ($GLOBALS['db']->getOne($sql) > 0)
		{
			$array=array("error"=>1,"info"=>"已经存在一个同名的配送区域");
			echo json_encode($array);die();
		}
		else
		{
			$config=array();
			$shipping_array=array("base_fee","step_fee","free_money","fee_compute_mode","pay_fee");
			foreach($shipping_array as $key=>$val)
			{
				$config[$key]['name']=$val;
				$config[$key]['value']=$_POST[$val];
			}
			$sql = "INSERT INTO " .$GLOBALS['ecs']->table($this->table_name).
			" (shipping_area_name,configure,postdate) VALUES ('$_POST[shipping_area_name]','" .serialize($config). "','".gmtime()."')";
			$GLOBALS['db']->query($sql);
			$new_id = $GLOBALS['db']->insert_Id();
			/* 添加选定的城市和地区 */
			if (isset($_POST['regions']) && is_array($_POST['regions']))
			{
				foreach ($_POST['regions'] AS $key => $val)
				{
					$sql = "INSERT INTO ".$GLOBALS['ecs']->table('pocket_shipping_area_region')." (shipping_area_id, region_id) VALUES ('$new_id', '$val')";
					$GLOBALS['db']->query($sql);
				}
			}
			$array=array("error"=>0,"info"=>"添加成功");
			echo json_encode($array);die();
		}
	}
	function edit()
	{
		$shipping_area_id=$_GET['shipping_area_id'];
		$info = parent::table_get_row($this->table_name,$shipping_area_id,"shipping_area_id");
		$configure=unserialize(@$info['configure']);
		/* 获得该区域下的所有地区 */
		$regions = array();
		$sql = "SELECT a.region_id, r.region_name FROM ".$GLOBALS['ecs']->table('pocket_shipping_area_region')." AS a, ".$GLOBALS['ecs']->table('region'). " AS r ".
		"WHERE r.region_id=a.region_id AND a.shipping_area_id='$shipping_area_id'";
		$res = $GLOBALS['db']->query($sql);
		while ($arr = $GLOBALS['db']->fetchRow($res))
		{
			$regions[$arr['region_id']] = $arr['region_name'];
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->assign('configure', $configure);
		$GLOBALS['smarty']->assign('regions', $regions);
		$GLOBALS['smarty']->display('shipping_edit.htm');
	}
	function ajax_edit()
	{
		/* 检查同类型的配送方式下有没有重名的配送区域 */
		$id=$_POST['id'];
		$sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table($this->table_name).
		" WHERE shipping_id='$_POST[shipping]' AND shipping_area_name='$_POST[shipping_area_name]' and shipping_area_id!='".$id."'";
		if ($GLOBALS['db']->getOne($sql) > 0)
		{
			$array=array("error"=>1,"info"=>"已经存在一个同名的配送区域");
			echo json_encode($array);die();
		}
		else
		{
			$config=array();
			$shipping_array=array("base_fee","step_fee","free_money","fee_compute_mode","pay_fee");
			foreach($shipping_array as $key=>$val)
			{
				$config[$key]['name']=$val;
				$config[$key]['value']=$_POST[$val];
			}
			$sql = "UPDATE " .$GLOBALS['ecs']->table($this->table_name)." SET shipping_area_name='$_POST[shipping_area_name]',
			 configure='" .serialize($config). "' WHERE shipping_area_id='$id'";
			$GLOBALS['db']->query($sql);
			/* 过滤掉重复的region */
			$selected_regions = array();
			if (isset($_POST['regions']))
			{
				foreach ($_POST['regions'] AS $region_id)
				{
					$selected_regions[$region_id] = $region_id;
				}
			}
			// 查询所有区域 region_id => parent_id
			$sql = "SELECT region_id, parent_id FROM " . $GLOBALS['ecs']->table('region');
			$res = $GLOBALS['db']->query($sql);
			while ($row = $GLOBALS['db']->fetchRow($res))
			{
				$region_list[$row['region_id']] = $row['parent_id'];
			}
			// 过滤掉上级存在的区域
			foreach ($selected_regions AS $region_id)
			{
				$id = $region_id;
				while ($region_list[$id] != 0)
				{
					$id = $region_list[$id];
					if (isset($selected_regions[$id]))
					{
						unset($selected_regions[$region_id]);
						break;
					}
				}
			}
			/* 清除原有的城市和地区 */
			$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table("pocket_shipping_area_region")." WHERE shipping_area_id='$id'");
			/* 添加选定的城市和地区 */
			foreach ($selected_regions AS $key => $val)
			{
				$sql = "INSERT INTO ".$GLOBALS['ecs']->table('pocket_shipping_area_region')." (shipping_area_id, region_id) VALUES ('$id', '$val')";
				$GLOBALS['db']->query($sql);
			}
			$array=array("error"=>0,"info"=>"添加成功");
			echo json_encode($array);die();
		}
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
	function ajax()
	{
		$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
		$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
		$arr['regions'] = get_regions($type, $parent);
		$arr['type']    = $type;
		$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
		$arr['target']  = htmlspecialchars($arr['target']);
		echo json_encode($arr);die();
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$shipping = new shipping();
$sign=@is_callable(array($shipping,$act));
if($sign)
$shipping->$act();
else
ecs_header("Location:http://m.wm18.com/\n");