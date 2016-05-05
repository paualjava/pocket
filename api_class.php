<?php
require(dirname(__FILE__)."/includes/common.php");
class api_class extends base
{
	function main()
	{
		$row=parent::table_get_row("users",1467933,"user_id");
		var_dump($row);
	}
	function detail()
	{

	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$api_class = new api_class();
$sign=@is_callable(array($api_class,$act));
if($sign)
$api_class->$act();
else
ecs_header("Location:http://m.wm18.com/\n");