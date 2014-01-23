<?
 session_start();
?>
<?
include "../../fns_dotazy.php";
dbConnect();
require("../../libs/Smarty.class.php");

require("../../db.php");

$apl = AplDB::getInstance();

$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


	$auftragsnr=$_GET['auftragsnr'];

        mysql_query('SET names utf8');

	if(isset($auftragsnr))
	{
            $sql = " select dauftr.id_dauftr as id,dauftr.teil, dauftr.auftragsnr, `stück` as importstk, `mehrarb-kz` as tatkz, preis, `pos-pal-nr` as pal,";
            $sql.= " `stk-exp` as exportstk, fremdauftr, fremdpos, preis*`stk-exp` as gespreis, `stk-exp`-`stück` as diff,auss4_stk_exp as auss, teilbez,";
            $sql.= " dtaetkz.text, daufkopf.bestellnr, kzgut, `auftragsnr-exp` as export,dauftr.abgnr";
            $sql.= " from dauftr";
            $sql.= " join dkopf using(teil)";
            $sql.= " join dtaetkz on dauftr.`mehrarb-kz`=dtaetkz.dtaetkz";
            $sql.= " join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
            $sql.= " where (`auftragsnr-exp`='$auftragsnr')";
            $sql.= " order by dauftr.teil,dauftr.auftragsnr,pal";

            $s1 = $sql;
		$res=mysql_query($sql);
		while($dauftr_row=mysql_fetch_array($res))
		{
			$dauftr_rows[$dauftr_row['id']]=$dauftr_row;
		}
		$smarty->assign("dauftr",$dauftr_rows);


		// zjistim si zda uz faktura existuje, podle datumu fertig v tabulce daufkopf
		$hasrechnung = has_rechnung($auftragsnr);
		
		$smarty->assign("hasrechnung",$hasrechnung);
		$fertig_value="";
		if($hasrechnung)
			$fertig_value = get_rechnung_datum($auftragsnr);
		
		$smarty->assign("fertig_value",$fertig_value);
		 
		// zjistim minutovou sazbu z auftragu
		$minpreis=get_minpreis_von_auftrag($auftragsnr);

                $hatMARechnung = $apl->hatMARechnung($auftragsnr);
                $hatMARechnung=$hatMARechnung==TRUE?1:0;
                $smarty->assign("hat_MARechnung",$hatMARechnung);
                if($hatMARechnung){
		    $letzte_MA_RECHNR = $apl->getMARechNr ($auftragsnr);
		}
                else{
		    // uprava vezmu podledni hodnotu ma faktury a zvetsim o jednicku
		    $kunde = $apl->getKundeFromAuftransnr($auftragsnr);
		    $letzteMARechnung = $apl->getLetzteMARechNrKunde($kunde);
		    $letzte_MA_RECHNR = $letzteMARechnung + 1;
		}

                $ma_rechnrVorschlag = $letzte_MA_RECHNR;
                $smarty->assign("ma_rechnr",$ma_rechnrVorschlag);
		// ----------------------------------------------------------------------------------------------------------------------------------------
		// spocitam vykon podle vykonu z druecku
		//
		$sql="select sum(if(auss_typ=4,(drueck.`stück`+drueck.`auss-stück`)*`vz-soll`,(drueck.`stück`)*`vz-soll`)) as drueck_leistung from drueck";
		$sql.=" join dauftr on dauftr.auftragsnr=drueck.auftragsnr and drueck.teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr`";
		$sql.=" and drueck.taetnr=dauftr.abgnr where (dauftr.`auftragsnr-exp`='$auftragsnr')";
		$ret = mysql_query($sql);
		$row = mysql_fetch_array($ret);
		$drueck=$row['drueck_leistung']*$minpreis;
		$smarty->assign("drueck_gesamt_preis",$drueck);
		// ----------------------------------------------------------------------------------------------------------------------------------------
		//
		// ----------------------------------------------------------------------------------------------------------------------------------------
		// spocitam vykon podle budouci faktury
		//
		$sql="select sum((`stk-exp`+auss4_stk_exp)*`vzkd`) as dauftr_leistung from dauftr";
		$sql.=" where (dauftr.`auftragsnr-exp`='$auftragsnr')";
		$ret = mysql_query($sql);
		$row = mysql_fetch_array($ret);
		$rechnung=$row['dauftr_leistung']*$minpreis;
		$smarty->assign("rechnung_gesamt_preis",$rechnung);
		// ----------------------------------------------------------------------------------------------------------------------------------------

		$smarty->assign("drueck_rechnung_differenz",$drueck-$rechnung);
	}
	
	$smarty->assign("auftragsnr_value",$_GET['auftragsnr']);
        $smarty->assign("sql",$s1);
	$smarty->display('rechnung_berechnen.tpl');
?>
