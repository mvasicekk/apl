<?
 require_once '../../security.php';
?>
<?
include "../../fns_dotazy.php";
dbConnect();
require("../../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


        

	if(isset($_GET['kunde']))
	{
	}
	
	$smarty->assign("auftragsnr_value",$_GET['auftragsnr']);
	$smarty->assign("minpreis_value",$_GET['minpreis']);
	$smarty->assign("kunde_value",$_GET['kunde']);
	$smarty->display('dauftr_schnell_erfassen.tpl');
?>
