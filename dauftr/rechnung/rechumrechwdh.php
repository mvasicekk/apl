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

	// nastaveni pole s datumem
	// pokud dostanu hodnotu naposledy pouziteho datumu, tak ho nastavim na toto
	// jinak nastavim aktualni datum
/*	
	if(isset($_GET['lastdatum'])) 
		$smarty->assign("datumvalue",date('d.m.Y',strtotime($_GET['lastdatum'])));
	else
		$smarty->assign("datumvalue",date('d.m.Y'));
*/		
//	$smarty->assign("lagervalue",$lagervalue);
//	$smarty->assign("lageroutput",$lageroutput);
	$smarty->display('rechumrechwdh.tpl');
?>
