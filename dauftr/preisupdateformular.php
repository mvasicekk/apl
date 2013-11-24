<?
session_start();
require "../fns_dotazy.php";
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

	mysql_query('set names utf8');

	$auftragsnr = $_GET['auftragsnr'];
	$level=$_GET['level'];
	$id_dauftr = $_GET['id_dauftr'];
	
	
	//podle id_dauftr zjistim kolika radku v dauftr a v drueck se to tyka
	$dauftrRow = getDauftrRowFromId($id_dauftr);
	$smarty->assign("auftragsnr",$dauftrRow["auftragsnr"]);
	$smarty->assign("teil",$dauftrRow["teil"]);
	$smarty->assign("pal",$dauftrRow["pos-pal-nr"]);
	$smarty->assign("preis",number_format($dauftrRow["preis"],4,'.',''));
	$smarty->assign("vzkd",number_format($dauftrRow["VzKd"],4,'.',''));
	$smarty->assign("vzaby",number_format($dauftrRow["VzAby"],2,'.',''));
	$smarty->assign("abgnr",$dauftrRow["abgnr"]);
	$smarty->assign("id_dauftr",$id_dauftr);
	
	// pro lepsi praci si zjistim zakaznika a minutovou sazbu
	$kunde = get_kunde_von_auftrag($dauftrRow["auftragsnr"]);
	$minpreis = getMinPreisVomKunde($kunde);
	$smarty->assign("kunde",$kunde);
	$smarty->assign("minpreis",$minpreis);
	
	$smarty->assign("pozicdauftr",1);
	
	$pocetPozicDrueckArrayProPal = getPalArrayFromDrueckAuftragsnrTeilAbgnrProPal($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr'],$dauftrRow["pos-pal-nr"]);
	
	if(is_array($pocetPozicDrueckArrayProPal))
		$pocetPozicDauftr=sizeof($pocetPozicDrueckArrayProPal);
	else
		$pocetPozicDauftr=0;
	
	$smarty->assign("pozicdrueck",$pocetPozicDauftr);

	$pocetPozicDauftrArray = getPalArrayFromDauftrAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	$pocetPozicDrueckArray = getPalArrayFromDrueckAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	
	if(is_array($pocetPozicDauftrArray))
		$pocetPozicDauftr=sizeof($pocetPozicDauftrArray);
	else
		$pocetPozicDauftr=0;
		
	if(is_array($pocetPozicDrueckArray))	
		$pocetPozicDrueck=sizeof($pocetPozicDrueckArray);
	else
		$pocetPozicDrueck=0;
		
	$smarty->assign("pozicdauftrall",$pocetPozicDauftr);
	$smarty->assign("pozicdrueckall",$pocetPozicDrueck);
	
	
	
	
	
	// ktery uzivatel chce mazat
	$mazac = get_user_pc();
	$recipient = "jr@abydos.cz,";
	//$recipient.= "hl@abydos.cz";
	
	$subject = "Preis ".$auftragsnr." geaendert";
	$message = "<h3>Daten fur Rechnung <b>$auftragsnr</b> wurden geloescht.</h3>";
	$message .= "<h3>$smazanoRadku<b>Positionen nach drechdeleted kopiert !</b>.</h3>";
	
	$user = get_user_pc();
	$message.= "<br><br>mit freundlichen Gruessen<br>$user";
	if(strlen($chyba)>0)
		$message.= "Error: $chyba";
		
		
	$headers = "From: <apl@abydos.cz>\n";
	$headers = "Content-Type: text/html; charset=UTF-8\n";
	
	//echo $output;
	$smarty->display('preisupdateformular.tpl');
?>

