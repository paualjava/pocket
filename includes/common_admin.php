<?php
require(dirname(__FILE__) . '/lib_permission.class.php');
require_once('pagination.class.php');
/**Smarty**/

$smarty = new cls_template;
$smarty->template_dir  = ROOT_PATH . 'pocket/pocket_admin/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/pocket_admin';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}
$GLOBALS['smarty']->assign('path_dir',dirname(PHP_SELF)."/");
$GLOBALS['smarty']->assign('path_dir_head',dirname(dirname(PHP_SELF))."/");