<?php
define('IN_ECS', true);
require(dirname(dirname(dirname(__FILE__))) . '/includes/init.php');
require(dirname(__FILE__) . '/lib_base.php');
require(dirname(__FILE__) . '/function.php');
/**Smarty**/

$smarty = new cls_template;
$smarty->template_dir  = ROOT_PATH . 'pocket/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/pocket/';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}
$GLOBALS['smarty']->assign('path_dir',dirname(PHP_SELF)."/");
$GLOBALS['smarty']->assign('path_dir_head',dirname(dirname(PHP_SELF))."/");