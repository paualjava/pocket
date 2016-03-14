<?php
class permission extends base
{
	private $session_name_admin="pocket_admin_name";
	function __construct()
	{
	}
	function get_admin_id()
	{
		session_start();
		if(isset($_SESSION[$this->session_name_admin]))
		{
			if($_SESSION[$this->session_name_admin] == 1)
			echo "欢迎管理员".$_SESSION['username']."登陆";
		}
		else
		{
			parent::redirect();
		}
	}
}