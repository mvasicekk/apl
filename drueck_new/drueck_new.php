<?
require_once '../security.php';
include "../fns_dotazy.php";
require("../libs/Smarty.class.php");
dbConnect();
mysql_query("set names utf8");
$smarty = new Smarty();

// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if(isset($_SESSION['user'])&&isset($_SESSION['level']))
{
    $smarty->assign("user",$_SESSION['user']);
    $smarty->assign("level",$_SESSION['level']);
    $smarty->assign("prihlasen",1);
}
	

$smarty->display('drueckn.tpl');