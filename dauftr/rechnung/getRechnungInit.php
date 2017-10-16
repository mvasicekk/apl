<?
session_start();
require_once '../../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$auftragsnr = $o->auftragsnr;
$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

if ($auftragsnr>0) {
    $sql = " select dkopf.rechnung_edit,dauftr.id_dauftr as id,dauftr.teil, dauftr.auftragsnr, `stück` as importstk, `mehrarb-kz` as tatkz, preis, `pos-pal-nr` as pal,";
    $sql .= " `stk-exp` as exportstk, fremdauftr, fremdpos, preis*`stk-exp` as gespreis, `stk-exp`-`stück` as diff,auss4_stk_exp as auss, teilbez,";
    $sql .= " dtaetkz.text, daufkopf.bestellnr, kzgut, `auftragsnr-exp` as export,dauftr.abgnr";
    $sql .= " from dauftr";
    $sql .= " join dkopf using(teil)";
    $sql .= " join dtaetkz on dauftr.`mehrarb-kz`=dtaetkz.dtaetkz";
    $sql .= " join daufkopf on dauftr.auftragsnr=daufkopf.auftragsnr";
    $sql .= " where (`auftragsnr-exp`='$auftragsnr')";
    $sql .= " order by dauftr.teil,dauftr.auftragsnr,pal";

    $s1 = $sql;
    $dauftrRows = $a->getQueryRows($sql);
    // zjistim si zda uz faktura existuje, podle datumu fertig v tabulce daufkopf
    $auftrInfo = $a->getAuftragInfoArray($auftragsnr);

    // zjistim minutovou sazbu z auftragu
    $minpreis = $a->getMinPreisProImport($auftragsnr);
    
    $hatMARechnung = $a->hatMARechnung($auftragsnr);
    if ($hatMARechnung) {
	$letzte_MA_RECHNR = $a->getMARechNr($auftragsnr);
    } else {
	// uprava vezmu podledni hodnotu ma faktury a zvetsim o jednicku
	// u noveho cislovani bude na 4.miste 8cka napr. z 13000001 bude 13080001
	if (strlen($auftragsnr) == 8) {
	    $letzte_MA_RECHNR = substr($auftragsnr, 0, 3) . '8' . substr($auftragsnr, 4);
	} else {
	    $letzte_MA_RECHNR = "00000000";
	}
    }

    $ma_rechnrVorschlag = $letzte_MA_RECHNR;
    // ----------------------------------------------------------------------------------------------------------------------------------------
    // spocitam vykon podle vykonu z druecku
    //
    $sql = "select sum(if(auss_typ=4,(drueck.`stück`+drueck.`auss-stück`)*`vz-soll`,(drueck.`stück`)*`vz-soll`)) as drueck_leistung from drueck";
    $sql .= " join dauftr on dauftr.auftragsnr=drueck.auftragsnr and drueck.teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr`";
    $sql .= " and drueck.taetnr=dauftr.abgnr where (dauftr.`auftragsnr-exp`='$auftragsnr')";
    $rs = $a->getQueryRows($sql);
    $row = $rs[0];
    $drueck = $row['drueck_leistung'] * $minpreis;
    
    // ----------------------------------------------------------------------------------------------------------------------------------------
    // spocitam vykon podle budouci faktury
    //
    $sql = "select sum((`stk-exp`+auss4_stk_exp)*`vzkd`) as dauftr_leistung from dauftr";
    $sql .= " where (dauftr.`auftragsnr-exp`='$auftragsnr')";
    $rs = $a->getQueryRows($sql);
    $row = $rs[0];
    $rechnung = $row['dauftr_leistung'] * $minpreis;
    
    // ----------------------------------------------------------------------------------------------------------------------------------------

    $drueckRechnungDiff = $drueck - $rechnung;
}

$returnArray = array(
    'u' => $u,
    'auftragsnr'=>$auftragsnr,
    'dauftrRows'=>$dauftrRows,
    'auftrInfo'=>$auftrInfo,
    'minpreis'=>$minpreis,
    'hatMARechnung'=>$hatMARechnung,
    'ma_rechnrVorschlag'=>$ma_rechnrVorschlag,
    'drueck'=>$drueck,
    'rechnung'=>$rechnung,
    'drueckRechnungDiff'=>$drueckRechnungDiff
);

echo json_encode($returnArray);
