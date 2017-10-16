<?

require_once '../../security.php';
require_once '../../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$rechnungInfo = $o->rechnungInfo;

$a = AplDB::getInstance();
$u = $_SESSION['user'];
$ident = $a->get_user_pc();
$auftragsnr = $rechnungInfo->auftragsnr;
$auslieferdatum = date('Y-m-d', strtotime($rechnungInfo->auftrInfo[0]->ausliefer_raw));
$sqlInsertArray = array();

if ($auslieferdatum == '1970-01-01') {
    $ausliefError = TRUE;
} else {
    $ausliefError = FALSE;
}

if ((intval($auftragsnr) > 0) && (!$ausliefError)) {
    // 1. nastavim auslieferdatum podle zadaneho
    $sql = "update daufkopf set ausliefer_datum='$auslieferdatum' where (auftragsnr='$auftragsnr')";
    $a->query($sql);
    // 2. budu kopirovat radky z dauftr do drech
    // zjistim si aktualni datum
    $now = date('Y-m-d');
    // zjistit cislo zakaznika podle exportu
    $kunde = $a->getKundeFromAuftransnr($auftragsnr);

    $sql = "select dauftr.id_dauftr as id,dauftr.teil, dauftr.auftragsnr, `stück` as importstk, `mehrarb-kz` as tatkz, preis, `pos-pal-nr` as pal,";
    $sql .= " `stk-exp` as exportstk, fremdauftr, fremdpos, preis*`stk-exp` as gespreis, `stk-exp`-`stück` as diff,auss4_stk_exp as auss, teilbez,";
    $sql .= " dtaetkz.text, bestellnr, kzgut, `auftragsnr-exp` as export,abgnr";
    $sql .= " from dauftr";
    $sql .= " join dkopf using(teil)";
    $sql .= " join dtaetkz on dauftr.`mehrarb-kz`=dtaetkz.dtaetkz";
    $sql .= " join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
    $sql .= " where (`auftragsnr-exp`='$auftragsnr')";
    $sql .= " order by dauftr.teil,dauftr.auftragsnr,pal";

    $vlozenoradku = 0;
    $dauftrRowIndex = 0;
    $res = $a->getQueryRows($sql);
    foreach ($res as $dr) {
	$formRow = $rechnungInfo->dauftrRows[$dauftrRowIndex++];
	$sql_insert = "insert into drech ";
	$sql_insert .= " (origauftrag,auftragsnr,rechnr_druck,teil,`stück`,ausschuss,dm,datum,text1,`taet-kz`,`best-nr`,`datum-auslief`,`pos-pal-nr`,fremdauftr,fremdpos,teilbez,kunde,abgnr,comp_user)";
	$teil = $dr['teil'];
	$exportstk = $dr['exportstk'];
	$auss = $dr['auss'];
	$preis = $dr['preis'];
	
	$text = $dr['text'];
	$text = mysql_real_escape_string($formRow->text);
	
	$tatkz = $dr['tatkz'];
	$bestellnr = $dr['bestellnr'];
	$pal = $dr['pal'];
	$kzgut = $dr['kzgut'];
	$abgnr = $dr['abgnr'];

	if (strlen($dr['fremdauftr']) == 0) {
	    $fremdauftr = 'NULL';
	} else {
	    $fremdauftr = $dr['fremdauftr'];
	}
	if (strlen($dr['fremdpos']) == 0) {
	    $fremdpos = 'NULL';
	} else {
	    $fremdpos = $dr['fremdpos'];
	}
	$teilbez = mysql_real_escape_string($dr['teilbez']);
	$teilbez = mysql_real_escape_string($formRow->teilbez);
	
	$importAuftrag = $dr['auftragsnr'];
	// 2012-10-24 do origauftrag pridana informace o importu
	$sql_insert .= " values('$importAuftrag','$auftragsnr','$auftragsnr','$teil','$exportstk','$auss','$preis','$now','$text','$tatkz','$bestellnr','$auslieferdatum','$pal',";
	if ($fremdauftr == 'NULL') {
	    $sql_insert .= "$fremdauftr,";
	} else {
	    $sql_insert .= "'$fremdauftr',";
	}
	if ($fremdpos == 'NULL') {
	    $sql_insert .= "$fremdpos,";
	} else {
	    $sql_insert .= "'$fremdpos',";
	}
	$sql_insert .= "'$teilbez','$kunde','$abgnr','$ident')";

	$a->insert($sql_insert);
	array_push($sqlInsertArray, $sql_insert);
	//echo($sql_insert);
	if (mysql_affected_rows() > 0)
	    $vlozenoradku++;
	// vlozeni do skladu
	// vkladam jen v pripade operace G
	if ($kzgut == 'G') {
	    $l_von = '8X';
	    $l_nach = '9R';
	    $sql_lager_delete = "delete from dlagerbew where ((teil='$teil') and (auftrag_import='$importAuftrag') and (pal_import='$pal') and (lager_von='$l_von') and (lager_nach='$l_nach'))";
	    $sql_lager_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	    $sql_lager_insert .= " values('$teil','$importAuftrag','$pal','$exportstk','$auss','$l_von','$l_nach','$ident')";
	    $a->query($sql_lager_delete);
	    $a->query($sql_lager_insert);
	}
    }
    // 3. nastavim datum faktury v daufkopf
    $a->updateDaufkopfField('fertig', $now, $auftragsnr);
    // 4.rechnung text
    $text = mysql_real_escape_string($rechnungInfo->auftrInfo[0]->rechnung_kopf_text);
    $a->updateDaufkopfField('rechnung_kopf_text', $text, $auftragsnr);
    //set_rechnung_datum($auftragsnr,$now);
}


$returnArray = array(
    'u' => $u,
    'ident' => $ident,
    'sqlInsertArray'=>$sqlInsertArray,
    'rechnung_kopf_text'=>$text,
    'vlozenoradku' => $vlozenoradku,
    'auslieferdatum' => $auslieferdatum,
    'ausliefError' => $ausliefError,
    'auftragsnr' => $auftragsnr,
    'rechnungInfo' => $rechnungInfo
);

echo json_encode($returnArray);
