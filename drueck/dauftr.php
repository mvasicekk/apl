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


	if(isset($_GET['auftragsnr']))
	{
		$sql="select AuftragsNr as auftragsnr,kunde,minpreis,bestellnr,DATE_FORMAT(Aufdat,'%d.%m.%Y') as aufdat,DATE_FORMAT(fertig,'%d.%m.%Y') as fertig,DATE_FORMAT(ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y %H:%i') as ex_datum_soll from daufkopf where (auftragsnr='".$_GET['auftragsnr']."')";
		$res=mysql_query($sql);
		$row=mysql_fetch_array($res);
		
		$smarty->assign("auftragsnr_value",$row['auftragsnr']);
		$smarty->assign("kunde_value",$row['kunde']);
		$smarty->assign("minpreis_value",$row['minpreis']);
		$smarty->assign("bestellnr_value",$row['bestellnr']);
		$smarty->assign("aufdat_value",$row['aufdat']);
		$smarty->assign("fertig_value",$row['fertig']);
		$smarty->assign("ausliefer_datum_value",$row['ausliefer_datum']);
		$smarty->assign("ex_datum_soll_value",$row['ex_datum_soll']);
		$kunde = $row['kunde'];
		
 
		// vytahnout informace o polozkach zakazky planu
		$sql="select id_dauftr,Teil,`pos-pal-nr` as  pos_pal_nr,`St�ck` as stk,Preis,`MehrArb-KZ` as mehrarb_kz,abgnr,KzGut,Termin,`auftragsnr-exp` as auftragsnr_exp,`pal-nr-exp` as pos_pal_nr_exp,`stk-exp` as stk_exp,fremdauftr,fremdpos  from dauftr where (auftragsnr='".$_GET['auftragsnr']."') order by pos_pal_nr,abgnr";
		$res=mysql_query($sql);
		while($dauftr_row=mysql_fetch_array($res))
		{
			$dauftr_rows[$dauftr_row['id_dauftr']]=$dauftr_row;
		}
		$smarty->assign("dauftr",$dauftr_rows);


		// informace o zakaznikovi
		$sql="select Kunde,Name1,Name2,Ort,`Preis-VZh`,`waehr-kz` from dksd where (Kunde='".$kunde."')";
		$res=mysql_query($sql);
		
		$row=mysql_fetch_array($res);
		
		$smarty->assign("name1",$row['Name1']);
		$smarty->assign("name2",$row['Name2']);
		$smarty->assign("ort",$row['Ort']);
		$smarty->assign("preis_vzh",$row['Preis-VZh']);
		$smarty->assign("waehr_kz",$row['waehr-kz']);

		
	}
	$smarty->display('dauftr.tpl');
?>
