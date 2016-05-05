<?php
require(dirname(__FILE__)."/includes/common.php");
class api extends base
{
	function main()
	{
		$row=parent::table_get_row("users",1467933,"user_id");
		var_dump($row);
	}
}
$act=(empty($_REQUEST['act'])) ? "main" : $_REQUEST['act'];
$api = new api();
$sign=@is_callable(array($api,$act));
if($sign)
$api->$act();
else
ecs_header("Location:http://m.wm18.com/\n");