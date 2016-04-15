<?php
/*
统计代码
//http://blog.csdn.net/kof820/article/details/7333531
//http://www.admin10000.com/document/1089.html
*/
require(dirname(__FILE__)."/includes/common.php");
ini_set('display_errors',"On");
class count extends base
{
	private $cookie_name="count_pv_uv";
	//private $server_name="wm18.com";
	private $server_name="localhost";
	function __construct()
	{
		if($_SERVER['SERVER_NAME']!=$this->server_name) exit;
	}
	function main()
	{
		$this->saveData();
		$add['ip']=$this->get_client_ip();
		if(!isset($_COOKIE[$this->cookie_name])){
			$value=md5(microtime().$add['ip'].rand());
			$overTime=mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();
			setcookie($this->cookie_name,$value,time()+$overTime);
		}
		$add['cookie']=$_COOKIE[$this->cookie_name];
		$add['date']=date("Y-m-d");
		$add['time']=time();
		$add['url']=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$add['referer']=$_SERVER['HTTP_REFERER'];
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("pocket_count"),$add);
	}
	public function tally()
	{
		$tally_=D('Tally');
		$tallydata_=D('Tallydata');
		$nowDate=date('Y-m-d');
		$now['date']=$nowDate;
		$now['iptotal']=$this->gototal($nowDate,'ip');
		$now['pvtotal']=$tallydata_->count(array('date'=>$nowDate),'tdid');
		$now['dltotal']=$this->gototal($nowDate,'cookie');

		if($tally=$tally_->find(array('date'=>$nowDate))){
			$tally_->save(array('iptotal'=>$now['iptotal'],'pvtotal'=>$now['pvtotal'],'dltotal'=>$now['dltotal']),array('date'=>$nowDate));
		}else{
			$tally_->create($now);
			$tally_->add();
		}
		$today['pv']=$now['pvtotal'];
		$today['ip']=$now['iptotal'];
		$today['dl']=$now['dltotal'];
		$yesterdayDate=date('Y-m-d',time()-3600*24);
		//echo $yesterdayDate;
		$yesterday=$tally_->find(array('date'=>$yesterdayDate));
		//dump($yesterday);
		$yesterday['pv']=isset($yesterday['pvtotal'])?$yesterday['pvtotal']:'0';
		$yesterday['ip']=isset($yesterday['iptotal'])?$yesterday['iptotal']:'0';
		$yesterday['dl']=isset($yesterday['dltotal'])?$yesterday['dltotal']:'0';

		$maxpv=$this->gomax('pvtotal');
		$maxip=$this->gomax('iptotal');
		$maxdl=$this->gomax('dltotal');
		$max['pv']=$maxpv['pvtotal'];
		$max['pvdate']=$maxpv['date'];
		$max['ip']=$maxip['iptotal'];
		$max['ipdate']=$maxip['date'];
		$max['dl']=$maxdl['dltotal'];
		$max['dldate']=$maxdl['date'];
		$this->assign('today',$today);
		$this->assign('yesterday',$yesterday);
		$this->assign('max',$max);
		$this->assign('nowtime',date('Y年m月d日 H:i:s'));
		$this->display();
	}
	function gomax($a)
	{
		$tally_=D('Tally');
		$max=$tally_->query("select * from `tally` order by `$a` desc limit 1");
		return $max[0];
	}

	function gototal($nowDate,$a)
	{
		$tallydata_=D('Tallydata');
		$now['iptotal']=$tallydata_->query("select count(distinct $a) from `tallydata` where `date`='$nowDate' ");
		return $now['iptotal'][0]["count(distinct $a)"];
	}
	public function saveData()
	{
		$nowDate=date('Y-m-d',time()-3600*24);
		$now['date']=$nowDate;
		$now['ip_total']=$this->get_total($nowDate,'ip');
		$sql = "select count(*) as total from ". $GLOBALS['ecs']->table("pocket_count")." where `date`='$nowDate' ";
		$info = $GLOBALS['db']->getRow($sql);
		$now['pv_total']=$info['total'];
		$now['uv_total']=$this->get_total($nowDate,'cookie');

		$sql = "select * from ". $GLOBALS['ecs']->table("pocket_count_total")." where `date`='$nowDate' limit 0,1 ";
		$info = $GLOBALS['db']->getRow($sql);
		if(!empty($info))
		{
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('pocket_count_total'),$now,'',"date='".$nowDate."' limit 1");
		}
		else
		{
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('pocket_count_total'),$now);
		}
		//$timeDel=time()-3600*24*50;
		//$sql = "DELETE FROM " .$GLOBALS['ecs']->table('pocket_count_total'). " where time<$timeDel";
		//$GLOBALS['db']->query($sql);
		echo 'Success '+date('Y-m-d H:i:s');
	}

	function get_total($nowDate,$a)
	{
		$sql = "select count(distinct $a) as total from ". $GLOBALS['ecs']->table("pocket_count")." where `date`='$nowDate' ";
		$info = $GLOBALS['db']->getRow($sql);
		return $info['total'];
	}
	function get_client_ip($type = 0) {
		$type       =  $type ? 1 : 0;
		static $ip  =   NULL;
		if ($ip !== NULL) return $ip[$type];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos    =   array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip     =   $_SERVER['HTTP_CLIENT_IP'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = ip2long($ip);
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$count = new count();
$sign=@is_callable(array($count,$act));
if($sign)
$count->$act();
else
ecs_header("Location:http://m.wm18.com/\n");