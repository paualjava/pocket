<?php
class base
{
	function __construct()
	{

	}
	function table_get_row($table_name,$value,$id="id")
	{
		$sql = "SELECT * FROM " .$GLOBALS['ecs']->table($table_name). " where ".$id."='".$value."'";
		$row =$GLOBALS['db']->getRow($sql);
		return $row;
	}
	function table_get_one($table_name,$column,$value,$id="id")
	{
		$sql = "SELECT ".$column." FROM " .$GLOBALS['ecs']->table($table_name). " where ".$id."='".$value."'";
		$row =$GLOBALS['db']->getOne($sql);
		return $row;
	}
	function table_get_list($table_name,$where='')
	{
		$sql = "select * from ". $GLOBALS['ecs']->table($table_name)." ".$where;
		$info = $GLOBALS['db']->getAll($sql);
		return $info;
	}
	/**
	 * 获取提交的数据
	 *
	 * @param unknown_type $array
	 * @param unknown_type $post
	 * @param unknown_type $key
	 * @return unknown
	 */
	function get_input_data($array,$post="post",$key=0)
	{
		$data = array();
		foreach($array as $key=>$v)
		{
			if($key>0)
			{
				if($post=="post")
				$data[$key]		=trim($_POST[$v]);
				else
				$data[$key]		=trim($_GET[$v]);
			}
			else
			{
				if($post=="post")
				$data[$v]		=trim($_POST[$v]);
				else
				$data[$v]		=trim($_GET[$v]);
			}
		}
		return $data;
	}
	function redirect($url)
	{
		@header("Location: ".$url."\n", true);
		exit();
	}
	/**
	 * 获得商品的详细信息
	 *
	 * @access  public
	 * @param   integer     $goods_id
	 * @return  void
 	*/
	function get_goods_info($goods_id)
	{
		$time = gmtime();
		$sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money, ' .
		'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
		"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
		'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
		'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
		'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
		'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r '.
		'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
		'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
		"ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
		" LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
		"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
		"WHERE g.goods_id = '$goods_id'  AND g.is_delete = 0 " .
		"GROUP BY g.goods_id";
		$row = $GLOBALS['db']->getRow($sql);

		if ($row !== false)
		{
			/* 用户评论级别取整 */
			$row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

			/* 获得商品的销售价格 */
			$row['market_price0']        = $row['market_price'];
			$row['market_price']        = price_format($row['market_price']);
			$row['shop_price_formated'] = price_format($row['shop_price']);
			$row['market_price1']        = $row['market_price'];
			$row['shop_price1'] = $row['shop_price'];
			$row['sheng_price'] = $row['market_price']-$row['shop_price'];
			$row['cuxiao_sheng_price'] = $row['market_price']-$row['promote_price'];
			/* 修正促销价格 */
			if ($row['promote_price'] > 0)
			{
				$promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
			}
			else
			{
				$promote_price = 0;
			}

			/* 处理商品水印图片 */
			$watermark_img = '';

			if ($promote_price != 0)
			{
				$watermark_img = "watermark_promote";
			}
			elseif ($row['is_new'] != 0)
			{
				$watermark_img = "watermark_new";
			}
			elseif ($row['is_best'] != 0)
			{
				$watermark_img = "watermark_best";
			}
			elseif ($row['is_hot'] != 0)
			{
				$watermark_img = 'watermark_hot';
			}

			if ($watermark_img != '')
			{
				$row['watermark_img'] =  $watermark_img;
			}

			$row['promote_price_org'] =  $promote_price;
			$row['promote_price'] =  price_format($promote_price);

			/* 修正重量显示 */
			$row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
			$row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
			($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

			/* 修正上架时间显示 */
			$row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

			/* 促销时间倒计时 */
			$time = gmtime();
			if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
			{
				$row['gmt_end_time']  = $row['promote_end_date'];
			}
			else
			{
				$row['gmt_end_time'] = 0;
			}

			/* 是否显示商品库存数量 */
			$row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';

			/* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
			$row['integral']      = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;

			/* 修正优惠券 */
			$row['bonus_money']   = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

			/* 修正商品图片 */
			$row['goods_img']   = get_image_path($goods_id, $row['goods_img']);
			$row['goods_thumb'] = get_image_path($goods_id, $row['goods_thumb'], true);
			if($row['is_share'] !=1){
				$row['rank_price'] = $row['shop_price'];
			}

			return $row;
		}
		else
		{
			return false;
		}
	}
}