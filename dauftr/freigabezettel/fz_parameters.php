<?
require_once '../../security.php';
require("../../libs/Smarty.class.php");
$smarty = new Smarty;

$smarty->assign('promenna','hodnota promenne');
if(isset($_SESSION['user'])&&isset($_SESSION['level']))
{
	$smarty->assign("user",$_SESSION['user']);
	$smarty->assign("level",$_SESSION['level']);
	$smarty->assign("prihlasen",1);
}
else
    header("Location: ../../index.php");


$smarty->display('fz_parameters.tpl');

?>

