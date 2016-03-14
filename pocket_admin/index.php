<?php
require(dirname(dirname(__FILE__))."/includes/common.php");
require(dirname(dirname(__FILE__))."/includes/common_admin.php");
class index extends permission
{
	function main()
	{
		print_r(parent::get_admin_id());die();
		//get_admin_id=
		$row=parent::table_get_row("users",1467933,"user_id");
		var_dump($row);
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$index = new index();
$sign=@is_callable(array($index,$act));
if($sign)
$index->$act();
else
ecs_header("Location:http://m.wm18.com/\n");