<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class goods extends base
{
	/*
	1.放一个保存按钮，保存的判断商品名称存在不存在，保存的时候，标题和内容存到一个新表里 pocket_goods_desc	 id,goods_id,desc,
	2.预览按钮保存的时候，标题和内容存到一个新表里 pocket_goods_preview uid,title,price,desc,读取的时候
	2.预览页面的前端要写一下,二维码的链接goods.php?id=232
	2.编辑的时候商品价格和名称
	2.商品搜索
	2.订单列表 加clop号 备注
	2.写入adsense_id 8
	2.删除收藏的接口
	*/
	private $table_name_pocket_goods="pocket_goods";
	private $page_size = 20; //一页显示的行数
	private $type = ''; //类型
	function __construct()
	{
		$this->type=$_GET['type'];
		$GLOBALS['smarty']->assign('nav_left',$this->type);
		$GLOBALS['smarty']->assign('nav', "goods");
		$GLOBALS['smarty']->assign('keyword', $_REQUEST['keyword']);
		$GLOBALS['smarty']->assign('title', $this->get_title());
	}
	function main()
	{
		$sql = "SELECT goods_id, goods_name, goods_type, goods_sn, style_sn, product_sn, shop_price,market_price, integral_price, is_on_sale, is_best, is_new, is_hot, sort_order, goods_number, integral FROM ". $GLOBALS['ecs']->table('goods')." AS g WHERE  is_delete='0'  AND is_real='1' and is_pocket=1 ORDER BY goods_id DESC";
		$info = $GLOBALS['db']->getAll($sql);
		foreach ($info as $key => $val)
		{
			$sql = "SELECT count(*) FROM ".$GLOBALS ['ecs']->table($this->table_name_pocket_goods) . " WHERE goods_id = " .$val['goods_id'];
			$count=$GLOBALS ['db']->getOne ( $sql );
			if ($count == 0)
			{
				$data=array(
				"goods_id"=>$val['goods_id'],
				"goods_name"=>$val['goods_name'],
				"goods_sn"=>$val['goods_sn'],
				"shop_price"=>$val['shop_price'],
				"market_price"=>$val['market_price'],
				"sort_order"=>0,
				"goods_number"=>$val['goods_number'],
				"is_on_sale"=>$val['is_on_sale'],
				"is_show"=>0,
				"postdate"=>gmtime()
				);
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name_pocket_goods),$data);
			}
		}
		/**如果产品的is_pocket=0那么删除此产品*/
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods);
		$info = $GLOBALS['db']->getAll($sql);
		foreach ($info as $key => $val)
		{
			$goods_id=$val['goods_id'];
			$sql = "select * from ". $GLOBALS['ecs']->table('goods')." where goods_id=".$goods_id;
			$info2 = $GLOBALS['db']->getRow($sql);
			if($info2['is_pocket']!=1)
			{
				$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name_pocket_goods). " where goods_id=".$goods_id." limit 1";
				$GLOBALS['db']->query($sql);
			}

		}
		/* 显示商品列表页面 */
		$curpage = (empty($_GET['page']) && !preg_match("/^\d+$/is",$_GET['page'])) ? 1 : $_GET['page'];
		$url = "?keyword=".$keyword."&page={page}";
		$where=$this->get_search_count_where();
		$sql = "select count(*) from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods)." ".$where;
		$total = $GLOBALS['db']->getOne($sql);
		if (!empty($_GET['page']) && $total != 0 && $curpage > ceil($total / $this->page_size))
		$curpage = ceil($total_rows / $this->page_size); //当前页数大于最后页数，取最后一页
		$curpage=($curpage) ? $curpage : 1;
		if ($total > $this->page_size) {//总记录数大于每页显示数，显示分页
			$page = new pagination($total, $this->page_size, $curpage, $url, 2);
			$GLOBALS['smarty']->assign('page_bar',$page->myde_write());
		}
		$where=$this->get_search_where();
		$sql = "select * from ". $GLOBALS['ecs']->table($this->table_name_pocket_goods)." ".$where." order by sort_order desc, pid desc LIMIT " . ($curpage - 1) * $this->page_size . ",$this->page_size";
		//分页
		$info = $GLOBALS['db']->getAll($sql);
		foreach($info as $key=>$row)
		{
			$goods = get_goods_info($row['goods_id']);
			if(empty($goods))
			$goods = parent::get_goods_info($row['goods_id']);
			$info[$key]['goods_info']=$goods;
		}
		$GLOBALS['smarty']->assign('info', $info);
		$GLOBALS['smarty']->display('goods.htm');
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
	function get_title()
	{
		if($this->type=="is_show")
		return '出售中的商品';
		else if($this->type=="is_show_no")
		return '未审核的商品';
		else if($this->type=="sell_out")
		return '已售罄的商品';
		else
		return '仓库中的商品';
	}
	function edit()
	{
		$goods_id=$_GET['goods_id'];
		$goods = get_goods_info($goods_id);
		if(empty($goods))
		$goods = parent::get_goods_info($goods_id);
		$goods_info=$goods;
		/**编辑器**/
		require(ROOT_PATH . 'includes/fckeditor/fckeditor.php');

		$editor             = new FCKeditor('goods_info');
		$editor->BasePath   = '../../includes/fckeditor/';
		$editor->ToolbarSet = 'Normal';
		$editor->Width      = '965px';
		$editor->Height     = '550';
		$editor->Value = $goods['goods_desc'];
		$FCKeditor     = $editor->CreateHtml();
		$GLOBALS['smarty']->assign('FCKeditor', $FCKeditor);
		$GLOBALS['smarty']->assign('goods_info', $goods_info);
		$GLOBALS['smarty']->display('goods_edit.htm');
	}
	/**
	 * 删除操作
	 *
	 */
	function delete()
	{
		$goods_id=$_POST['goods_id'];
		$goods = parent::table_get_row($this->table_name_pocket_goods,$goods_id,$id="goods_id");
		if(!empty($goods))
		{
			$data=array("is_pocket"=>0);
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods'),$data,'',"goods_id=".$goods_id." limit 1");
			$sql = "DELETE FROM " .$GLOBALS['ecs']->table($this->table_name_pocket_goods). " where goods_id=".$goods_id." limit 1";
			$GLOBALS['db']->query($sql);
		}
	}
	/**
	 * ajax保存商品排序
	 *
	 */
	function ajax_save_sort_order()
	{
		$goods_id=$_POST['goods_id'];
		$orderby=$_POST['orderby'];
		$data=array("sort_order"=>$orderby);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name_pocket_goods),$data,'',"goods_id=".$goods_id." limit 1");
	}
	/**
	 * 商品下架
	 *
	 */
	function ajax_sale_on()
	{
		$goods_str=$_POST['id'];
		$goods_str=explode(";",$goods_str);
		foreach($goods_str as $v)
		{
			if($v)
			{
				$is_show = parent::table_get_one($this->table_name_pocket_goods,"is_show",$v,"goods_id");
				if($is_show==0)
				{
					$array=json_encode(array("error"=>1,"info"=>"您选择的商品含有已经下架的商品！"));
					echo $array;die();
				}
			}
		}
		foreach($goods_str as $v)
		{
			if($v)
			{
				$data=array("is_show"=>0);
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name_pocket_goods),$data,'',"goods_id=".$v." limit 1");
			}
		}
		$array=json_encode(array("error"=>0,"info"=>"操作成功"));
		echo $array;die();
	}
	/**
	 * 控制商品显示
	 *
	 */
	function ajax_save_is_show()
	{
		$goods_id=$_POST['goods_id'];
		$is_show = parent::table_get_one($this->table_name_pocket_goods,"is_show",$goods_id,"goods_id");
		$is_show = ($is_show == 1) ? 2 : 1;
		$data=array("is_show"=>$is_show);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($this->table_name_pocket_goods),$data,'',"goods_id=".$goods_id." limit 1");
	}
	/**
	 * 商品预览保存
	 *
	 */
	function ajax_save_preview()
	{
		$data = array(
		'goods_id'        =>trim($_POST['goods_id']),//产品编号
		'goods_name'        =>trim($_POST['goods_name']),//产品名称
		'goods_desc'              =>trim($_POST['goods_info']),//姓名
		'time'              =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('pocket_goods_preview'),$data);
		$insert_id=$GLOBALS['db']->insert_id();
		if($insert_id)
		$array=array("error"=>0,"pid"=>$insert_id);
		else 
		$array=array("error"=>1,"info"=>"操作失败");
		echo json_encode($array);die();
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$goods = new goods();
$sign=@is_callable(array($goods,$act));
if($sign)
$goods->$act();
else
ecs_header("Location:http://m.wm18.com/\n");