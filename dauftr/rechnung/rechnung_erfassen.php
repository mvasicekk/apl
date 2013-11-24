<?
 session_start();
?>
<?
include "../../fns_dotazy.php";
dbConnect();

	$auftragsnr=$_GET['auftragsnr'];
	$auslieferdatum = make_DB_datum($_GET['auslieferdatum']);

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	$ident=get_user_pc();
	
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';

	$auslieferdatum2time=strtotime($auslieferdatum);

	$output	.= "<auslieferdatum>$auslieferdatum</auslieferdatum>";
	$output	.= "<auslieferdatum2time>$auslieferdatum2time</auslieferdatum2time>";
    $subdatum = substr($auslieferdatum,0,2);
	if((isset($auftragsnr))&&($subdatum!="--"))
	{
		mysql_query('set names utf8');
		// 1. nastavim auslieferdatum podle zadaneho
		$sql="update daufkopf set ausliefer_datum='$auslieferdatum' where (auftragsnr='$auftragsnr')";
		mysql_query($sql);

		// 2. budu kopirovat radky z dauftr do drech
		// zjistim si aktualni datum
		$now = date('Y-m-d');
		// zjistit cislo zakaznika podle exportu
		$kunde = get_kunde_von_auftrag($auftragsnr);

		$sql="select dauftr.id_dauftr as id,dauftr.teil, dauftr.auftragsnr, `stück` as importstk, `mehrarb-kz` as tatkz, preis, `pos-pal-nr` as pal,";
		$sql.=" `stk-exp` as exportstk, fremdauftr, fremdpos, preis*`stk-exp` as gespreis, `stk-exp`-`stück` as diff,auss4_stk_exp as auss, teilbez,";
		$sql.=" dtaetkz.text, bestellnr, kzgut, `auftragsnr-exp` as export,abgnr";
		$sql.=" from dauftr";
		$sql.=" join dkopf using(teil)";
		$sql.=" join dtaetkz on dauftr.`mehrarb-kz`=dtaetkz.dtaetkz";
		$sql.=" join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
		$sql.=" where (`auftragsnr-exp`='$auftragsnr')";
		$sql.=" order by dauftr.teil,dauftr.auftragsnr,pal";

		$vlozenoradku=0;
		$res=mysql_query($sql);
		while ($dr = mysql_fetch_array($res)) {
        $sql_insert = "insert into drech ";
        $sql_insert.=" (origauftrag,auftragsnr,rechnr_druck,teil,`stück`,ausschuss,dm,datum,text1,`taet-kz`,`best-nr`,`datum-auslief`,`pos-pal-nr`,fremdauftr,fremdpos,teilbez,kunde,abgnr,comp_user)";
        $teil = $dr['teil'];
        $exportstk = $dr['exportstk'];
        $auss = $dr['auss'];
        $preis = $dr['preis'];
        $text = $dr['text'];
        $tatkz = $dr['tatkz'];
        $bestellnr = $dr['bestellnr'];
        $pal = $dr['pal'];
        $kzgut = $dr['kzgut'];
        $abgnr = $dr['abgnr'];

        if (strlen($dr['fremdauftr']) == 0)
            $fremdauftr = 'NULL';
        else
            $fremdauftr = $dr['fremdauftr'];

        // list of yetti


        if (strlen($dr['fremdpos']) == 0)
            $fremdpos = 'NULL';
        else
            $fremdpos = $dr['fremdpos'];

        $teilbez = mysql_real_escape_string($dr['teilbez']);

        $importAuftrag = $dr['auftragsnr'];

        // 2012-10-24 do origauftrag pridana informace o importu
        
        $sql_insert.=" values('$importAuftrag','$auftragsnr','$auftragsnr','$teil','$exportstk','$auss','$preis','$now','$text','$tatkz','$bestellnr','$auslieferdatum','$pal',";
        if ($fremdauftr == 'NULL')
            $sql_insert.="$fremdauftr,";
        else
            $sql_insert.="'$fremdauftr',";
        if ($fremdpos == 'NULL')
            $sql_insert.="$fremdpos,";
        else
            $sql_insert.="'$fremdpos',";
        $sql_insert.="'$teilbez','$kunde','$abgnr','$ident')";

        mysql_query($sql_insert);
        echo($sql_insert);
        if (mysql_affected_rows() > 0)
            $vlozenoradku++;
        // vlozeni do skladu
        // vkladam jen v pripade operace G
        if ($kzgut == 'G') {
            $l_von = '8X';
            $l_nach = '9R';
            $sql_lager_delete = "delete from dlagerbew where ((teil='$teil') and (auftrag_import='$importAuftrag') and (pal_import='$pal') and (lager_von='$l_von') and (lager_nach='$l_nach'))";
            $sql_lager_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
            $sql_lager_insert.= " values('$teil','$importAuftrag','$pal','$exportstk','$auss','$l_von','$l_nach','$ident')";
            mysql_query($sql_lager_delete);
            mysql_query($sql_lager_insert);
            $output.="<lagerdelete>$sql_lager_delete</lagerdelete>";
            $output.="<lagerinsert>$sql_lager_insert</lagerinsert>";
        }
    }
    $output .= '<vlozenoradku>';
		$output .= $vlozenoradku;
		$output	.= '</vlozenoradku>';


		// 3. nastavim datum faktury v daufkopf
		set_rechnung_datum($auftragsnr,$now);
	}

	else
	{
			// chyba v datumu
			$output.= "<error>auslieferdatum</error>";
			$output.= "<errordescription>Auslieferdatum Fehler</errordescription>";
	}
	$output .= '</response>';
	
	echo $output;
?>
