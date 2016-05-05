<?php
require(dirname(__FILE__)."/includes/common.php");
class shop extends base
{
	function index()
	{
		$GLOBALS['smarty']->display('shop.htm');
	}
}
$act=(empty($_REQUEST['act'])) ? "index" : $_REQUEST['act'];
$shop = new shop();
$sign=@is_callable(array($shop,$act));
if($sign)
$shop->$act();
else
$shop->location_main();