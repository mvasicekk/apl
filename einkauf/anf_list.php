<?
require_once '../security.php';
require("../libs/Smarty.class.php");
require_once '../db.php';

$smarty = new Smarty;
$a = AplDB::getInstance();

if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
    require_once '../assignsecurity.php';
} else {
    header("Location: ../index.php");
}

$smarty->assign('test',1234);
$smarty->display('anf_list.tpl');