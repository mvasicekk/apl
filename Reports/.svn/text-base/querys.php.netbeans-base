<?
 session_start();
?>
<?
include "../fns_dotazy.php";
dbConnect();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


	// vytvorit rozsah od prvniho do posledniho minuleho mesice
	
	$akt_den=date("d");
	$akt_mesic=date("m");
	$akt_rok=date("Y");
	
	// posledni datum minuleho mesice ziskam jako den 0 aktualniho mesice
	
	$lastday = mktime(0,0,0,$akt_mesic,0,$akt_rok);
	$firstday = mktime(0,0,0,date('m',$lastday),1,$akt_rok);
	
	$min_mesic_od=date('d.m.Y',$firstday);
	$min_mesic_do=date('d.m.Y',$lastday);
	
	$predchozi_den=date('d.m.Y',mktime(0,0,0,$akt_mesic,$akt_den-1,$akt_rok));
	
	$smarty->assign("min_mesic_od",$min_mesic_od);
	$smarty->assign("min_mesic_do",$min_mesic_do);
	
	$smarty->assign("predchozi_den",$predchozi_den);
	
	$smarty->assign("now",date("d.m.Y"));
	
	$smarty->display('querys.tpl');
?>
