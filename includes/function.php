<?php
define('IN_ECS', true);
function get_site_url()
{
	$url="http://".$_SERVER['SERVER_NAME'].":8090/pocket/";
	return $url;
}
/**
 * 发送短信
 *
 * @param unknown_type $mobile
 * @param unknown_type $content
 */
function send_phone_code($mobile,$content)
{
	include_once(dirname(dirname(dirname(__FILE__)))."/Client.php");
	$smsInfo['server_url'] = 'http://ws.montnets.com:9006/MWGate/wmgw.asmx?wsdl';
	$smsInfo['user_name'] = 'J01253';
	$smsInfo['password'] = '232656';
	$smsInfo['pszSubPort'] = '9006';
	if($mobile!="13575856510" && !empty($mobile) && strlen($mobile)==11 && !empty($content))
	{
		$mobiles = array(0=>$mobile);
		$sms = new Client($smsInfo['server_url'],$smsInfo['user_name'],$smsInfo['password']);
		$sms->pszSubPort = $smsInfo['pszSubPort'];
		$sms->setOutgoingEncoding("UTF-8");
		$result = $sms->sendSMS($mobiles,$content);
		//header("Content-Type:text/plain;charset=utf-8");
		echo $result['msg'];
	}
}
function send_verification_code_new($name,$type_id ='4')
{
	//取该用户三分钟之内的验证码
	$now =  gmtime();
	$this_old=$now-86400;
	//1小时3次
	$sql = "SELECT count(*) as num FROM ". $GLOBALS['ecs']->table('user_verification_code') ." WHERE name= '" . $name."' and add_time >" . $this_old."";
	$row = $GLOBALS['db']->getRow($sql);
	if($row['num'] > 3){
		return "gather_3";
	}
	$code = rand(100000,900000);
	$data = array();
	$name =  trim($name);
	$data['name'] = trim($name);
	$data['code'] = $code ;
	$data['add_time'] =$now;
	$data['ip'] =real_ip()."-2";
	$data['type_id'] = trim($type_id);
	$GLOBALS['db']->autoExecute( $GLOBALS['ecs']->table("user_verification_code"),$data);
	if ($type_id == 1) {
		$content = '麦考林注册验证码：【'.$code.'】。请勿将验证码告知他人并确认该申请是您本人操作！m.wm18.com';;
	}elseif ($type_id == 2){
		$content = '麦考林找回密码验证码：【'.$code.'】。请勿将验证码告知他人并确认该申请是您本人操作！m.wm18.com';
	}elseif ($type_id == 3){
		$content = '麦考林贵宾激活验证码：【'.$code.'】。请勿将验证码告知他人并确认该申请是您本人操作！m.wm18.com';
	}else {
		$content = '验证码：【'.$code.'】。请勿将验证码告知他人并确认该申请是您本人操作！m.wm18.com';
	}
	send_phone_code($name,$content);
}