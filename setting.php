<?php
require(dirname(__FILE__)."/includes/common.php");
class setting extends base
{
	private $user_id="";
	private $site_url="";
	function __construct()
	{
		$this->user_id=parent::get_user_id();
		$this->site_url=get_site_url();
		$GLOBALS['smarty']->assign('site_url',$this->site_url);
	}
	function index()
	{
		/**判断是否设置过密码**/
		$password_withdrawals=parent::table_get_one("users2","password_withdrawals",$this->user_id,"user_id");
		$GLOBALS['smarty']->assign('password_withdrawals',$password_withdrawals);
		$GLOBALS['smarty']->assign('nav_title', "设置");
		$GLOBALS['smarty']->display('setting.htm');
	}
	/**
	 * 意见反馈
	 *
	 */
	function feedback()
	{
		$GLOBALS['smarty']->assign('nav_title', "意见反馈");
		$GLOBALS['smarty']->display('setting_feedback.htm');
	}
	/**
	 * 提现密码设置
	 *
	 */
	function withdraw_set_password()
	{
		$GLOBALS['smarty']->assign('nav_title', "提现密码设置");
		$GLOBALS['smarty']->display('setting_withdraw_set_password.htm');
	}
	/**
	 * 提现密码设置
	 *
	 */
	function ajax_withdraw_set_password()
	{
		$password=$_POST['password'];
		if(preg_match("/^[\d]{6}$/is",$password))
		{
			$sql = "update ".$GLOBALS['ecs']->table('users2')." SET `password_withdrawals`='".md5($password)."' WHERE user_id='".$this->user_id."'";
			$GLOBALS['db']->query($sql);
			$array=array("error"=>0,"info"=>"");
			echo json_encode($array);die();
		}
		else
		{
			$array=array("error"=>1,"info"=>"密码格式不对,请输入六位数字密码");
			echo json_encode($array);die();
		}
	}
	/**
	 * 修改提现密码
	 *
	 */
	function withdraw_modify_password()
	{
		$GLOBALS['smarty']->assign('nav_title', "修改提现密码");
		$GLOBALS['smarty']->display('setting_withdraw_modify_password.htm');
	}
	/**
	 * 修改提现密码
	 *
	 */
	function ajax_withdraw_modify_password()
	{
		$password_old=$_POST['password_old'];
		$password=$_POST['password'];
		if(preg_match("/^[\d]{6}$/is",$password_old))
		{
			$password_withdrawals=parent::table_get_one("users2","password_withdrawals",$this->user_id,"user_id");
			if($password_withdrawals==md5($password_old))
			{
				if(preg_match("/^[\d]{6}$/is",$password_old))
				{
					$sql = "update ".$GLOBALS['ecs']->table('users2')." SET `password_withdrawals`='".md5($password)."' WHERE user_id='".$this->user_id."'";
					$GLOBALS['db']->query($sql);
					$array=array("error"=>0,"info"=>"");
					echo json_encode($array);die();
				}
				else
				{
					$array=array("error"=>1,"info"=>"密码格式不对,请输入六位数字密码");
					echo json_encode($array);die();
				}
			}
			else
			{
				$array=array("error"=>1,"info"=>"原密码输入错误,请输入六位数字密码");
				echo json_encode($array);die();
			}
			$GLOBALS['db']->query($sql);
			$array=array("error"=>0,"info"=>"");
			echo json_encode($array);die();

		}
		else
		{
			$array=array("error"=>1,"info"=>"原密码格式不对,请输入六位数字密码");
			echo json_encode($array);die();
		}
	}
	/**
	 * 忘记提现密码
	 *
	 */
	function withdraw_forget_password()
	{
		$GLOBALS['smarty']->assign('nav_title', "忘记提现密码");
		$GLOBALS['smarty']->display('setting_withdraw_forget_password.htm');
		//$temp_str = '麦考林手机验证码：【' . $randmun . '】。请勿将验证码告知他人并确认该申请是您本人操作！www.wm18.com';
	}
	/**
	 * 忘记提现密码
	 *
	 */
	function ajax_withdraw_forget_password()
	{
		$phone=trim($_POST['phone']);
		$code=trim($_POST['code']);
		$password=$_POST['password'];

		if(empty($this->user_id))
		{
			echo json_encode(array("error"=>1,"info"=>"请先登录!"));exit;
		}
		if(!preg_match("/^(13|14|15|17|18)\d{9}$/",$phone))
		{
			echo json_encode(array("error"=>1,"info"=>'请输入正确的手机号码'));exit;
		}
		//判断验证码是否正确
		if ( !$this->matching_verification_code ($phone,$code,4) )
		{
			//验证失败
			echo json_encode(array("error"=>1,"info"=>'您填写的验证码不正确，请重新输入。'));exit;
		}
		if(preg_match("/^[\d]{6}$/is",$password))
		{
			$sql = "update ".$GLOBALS['ecs']->table('users2')." SET `password_withdrawals`='".md5($password)."' WHERE user_id='".$this->user_id."'";
			$GLOBALS['db']->query($sql);
			$array=array("error"=>0,"info"=>"提现密码修改成功!");
			echo json_encode($array);die();
		}
		else
		{
			$array=array("error"=>1,"info"=>"密码格式不对,请输入六位数字密码");
			echo json_encode($array);die();
		}
	}
	//判断验证码是否正确
	function  matching_verification_code($name,$code,$type_id = 4,$minute = 0)
	{
		$where = " ";
		$new = gmtime();
		if( !empty($minute) && $minute>0 )
		{
			$minute = $minute*60;
			$where .= " AND  `add_time` >=  '".$new-$minute."'";
		}
		$sql = "SELECT `code` FROM ".$GLOBALS['ecs']->table('user_verification_code') .
		" WHERE `name` = '$name' AND type_id = '$type_id'  $where ORDER BY add_time DESC LIMIT 1" ;
		$data = $GLOBALS['db']->getOne($sql);
		if(empty($data)) return false;
		if( $code == $data)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function ajax_send_phone_code()
	{
		$phone=trim($_POST['phone']);

		if(empty($phone)){
			$array=array("error"=>1,"info"=>"请填写手机");
			echo json_encode($array);die();
		}
		if (!preg_match("/^(13|15|18|17|14)\d{9}$/",$phone))
		{
			$array=array("error"=>1,"info"=>"请填写正确的手机号码");
			echo json_encode($array);die();
		}
		$result=send_verification_code_new($phone);
		if($result=="gather_3")
		{
			$array=array("error"=>1,"info"=>"获取验证码1小时不得超过3次,请稍候再试!");
			echo json_encode($array);die();
		}
		die();
	}
	function ajax_feedback()
	{
		$info=$_POST['info'];
		$contact=$_POST['contact'];
		$data = array(
		'info'       	 =>trim($_POST['info']),
		'contact'        =>trim($_POST['contact']),
		'postdate'       =>gmtime()//时间
		);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('pocket_feedback'),$data);
		$array=array("error"=>0,"info"=>"");
		echo json_encode($array);die();
	}
	/**我要提现**/
	function withdrawals()
	{
		$GLOBALS['smarty']->assign('nav_title', "我要提现");
		$GLOBALS['smarty']->display('setting_withdrawals.htm');
	}

}
$act=(empty($_REQUEST['act'])) ? "index" : $_REQUEST['act'];
$setting = new setting();
$sign=@is_callable(array($setting,$act));
if($sign)
$setting->$act();
else
$setting->location_main();