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


	mysql_query("set names utf8");
	
	if(isset($_GET['auftragsnr']))
	{
		$sql="select if(zielorte.zielort is null,'',zielorte.zielort) as zielort,AuftragsNr as auftragsnr,daufkopf.kunde,minpreis,bestellnr,bemerkung,DATE_FORMAT(Aufdat,'%d.%m.%Y') as aufdat,DATE_FORMAT(fertig,'%d.%m.%Y') as fertig,DATE_FORMAT(ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(ex_datum_soll,'%d.%m.%Y %H:%i') as ex_datum_soll,DATE_FORMAT(ex_datum_soll,'%Y-%m-%d %H:%i') as ex_datum_soll1 from daufkopf";
		$sql.=" left join zielorte on zielorte.id=daufkopf.zielort_id";
		$sql.=" where (auftragsnr='".$_GET['auftragsnr']."')";
//		var_dump($sql);
		$res=mysql_query($sql);
		$row=mysql_fetch_array($res);
		
		$smarty->assign("auftragsnr_value",$row['auftragsnr']);
		$smarty->assign("kunde_value",$row['kunde']);
		$smarty->assign("zielort_value",$row['zielort']);
		$smarty->assign("minpreis_value",$row['minpreis']);
		$smarty->assign("bestellnr_value",$row['bestellnr']);
		$smarty->assign("aufdat_value",$row['aufdat']);
		$smarty->assign("fertig_value",$row['fertig']);
		$smarty->assign("ausliefer_datum_value",$row['ausliefer_datum']);
		// prase ex_datum_soll
		$ex_zeit_soll='';
		$ex_datum_soll = '';
		if(strlen(trim($row['ex_datum_soll1']))>0){
		    $stamp = strtotime($row['ex_datum_soll1']);
		    $ex_zeit_soll = date('H:i',$stamp);
		    $ex_datum_soll = date('d.m.Y',$stamp);
		}
		
		$smarty->assign("ex_zeit_soll_value",$ex_zeit_soll);
		$smarty->assign("ex_datum_soll_value",$ex_datum_soll);
                $smarty->assign("bemerkung",$row['bemerkung']);
		$kunde = $row['kunde'];
		
 
		// vytahnout informace o polozkach zakazky planu, oznacit polozky s fakturou
		$sql="select id_dauftr,Teil,`pos-pal-nr` as  pos_pal_nr,`Stück` as stk,Preis,`MehrArb-KZ` as mehrarb_kz,abgnr,KzGut,Termin,`auftragsnr-exp` as auftragsnr_exp,`pal-nr-exp` as pos_pal_nr_exp,`stk-exp` as stk_exp,fremdauftr,fremdpos,vzkd,bemerkung  from dauftr where (auftragsnr='".$_GET['auftragsnr']."') order by pos_pal_nr,abgnr";
		$res=mysql_query($sql);
		$error= mysql_error();
		
		while($dauftr_row=mysql_fetch_array($res))
		{
			$dauftr_row['hasrechnung'] = has_rechnung($dauftr_row['auftragsnr_exp']);
                        $dauftr_row['e'] = $dauftr_row['auftragsnr_exp'];
			$dauftr_rows[$dauftr_row['id_dauftr']]=$dauftr_row;
		}
		$smarty->assign("dauftr",$dauftr_rows);
		$smarty->assign("sqldauftr",$sql);
		$smarty->assign("sqlerror",$error);


		
		// informace o zakaznikovi
		$sql="select Kunde,Name1,Name2,Ort,`Preis-VZh`,`waehr-kz` from dksd where (Kunde='".$kunde."')";
		$res=mysql_query($sql);
		
		
		$row=mysql_fetch_array($res);
		
		$smarty->assign("name1",$row['Name1']);
		$smarty->assign("name2",$row['Name2']);
		$smarty->assign("ort",$row['Ort']);
		$smarty->assign("preis_vzh",$row['Preis-VZh']);
		$smarty->assign("waehr_kz",$row['waehr-kz']);
		$smarty->assign("now",date("d.m.Y"));

		// ma tato zakazka hotovou fakturu ?
		$hasrechnung = has_rechnung($_GET['auftragsnr']);
		$smarty->assign("hasrechnung",$hasrechnung);
	
		
	}
	$smarty->display('dauftr.tpl');
?>
