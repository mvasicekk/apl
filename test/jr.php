<?
session_start();
require("../libs/Smarty.class.php");
$smarty = new Smarty;
require_once '../db.php';

$apl = AplDB::getInstance();

$smarty->assign('u',$_SESSION['user']);
$smarty->assign('jr',"sel tudy mel dudy");
$smarty->display('jr.tpl');
?>

