<?

session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$o = $o->params;

$a = AplDB::getInstance();
$user = $a->get_user_pc();

$teil = $o->teil;
$pal_nr = $o->positionInfo->palstk;
$fremdauftr = $o->positionInfo->fremdauftr;
$stk_pro_pal = $o->positionInfo->stkpropal;
$fremdpos = $o->positionInfo->fremdpos;
$gt = $o->positionInfo->gt;
$pal_erst = $o->positionInfo->firstpal;
//$fremdausauftrag=$_GET['fremdausauftrag'];
$netgewicht = floatval($o->teilInfo->teil->Gew);
$increment = $o->positionInfo->increment;
$exgeplannt = $o->positionInfo->explanmit;
$positionen = $o->dpos;
$auftragsnr = $o->auftrag;
$kunde = $o->teilInfo->teil->Kunde;
$minpreis = $a->getMinPreisProImport($auftragsnr);
$dauftrSqlArray = array();
$dlagerSqlArray = array();

$paleta = $pal_erst;
for ($i = 0; $i < $pal_nr; $i++) {
    $kgut = "";
    //projdu vsechny abgnr pro danou paletu
    foreach ($positionen as $position) {

	$tatkz = $position->tat;
	$abgnr = $position->abgnr;
	$kz_druck = intval($position->kz_druck);
	$Gtat = strtoupper($position->kzgut);
	$preis = $position->preis;
	$vzkd = $position->vzkd;
	$vzaby = $position->vzaby;

	if ($kz_druck != 0) {
	    if ($Gtat == 'G') {
		$sql = "insert into dauftr (giesstag,auftragsnr,teil,`Stück`,preis,fremdauftr,fremdpos,kg_stk_bestellung,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
		$sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
		$sql.="	('$gt','$auftragsnr','$teil','$stk_pro_pal','$preis','$fremdauftr','$fremdpos','$netgewicht','$tatkz',";
		$sql.="'$paleta','$abgnr','$Gtat','$vzkd','$vzaby','$user',NOW(),'$exgeplannt')";
	    } else {
		$sql = "insert into dauftr (giesstag,auftragsnr,teil,`Stück`,preis,fremdauftr,fremdpos,`mehrarb-kz`,`pos-pal-nr`,abgnr,kzgut,";
		$sql.="vzkd,vzaby,comp_user_accessuser,inserted,termin) values";
		$sql.="	('$gt','$auftragsnr','$teil','$stk_pro_pal','$preis','$fremdauftr','$fremdpos','$tatkz',";
		$sql.="'$paleta','$abgnr','$Gtat','$vzkd','$vzaby','$user',NOW(),'$exgeplannt')";
	    }
	    array_push($dauftrSqlArray, $sql);

	    if ($Gtat == 'G') {
		$kgut = 'G';
	    }
	    
	      $result = mysql_query($sql);
	      $affected_rows = mysql_affected_rows();
	      $mysql_error = mysql_error();
	    
	}
    }


    if ($kgut == 'G') {
	// povolena vsuvka
	// mam zapsane operace, ted udelam zapis do lagru
	// ale jen v pripade ze mam na palete G operaci
	$el = erster_lager($teil, $auftragsnr, $paleta);
	//$el='0D';
	// 	nejdriv smazu eventuelni starou pozici v lagru
	$sql_delete = "delete from dlagerbew where ((teil='$teil') and (auftrag_import='$auftragsnr') and (pal_import='$paleta') and (lager_von='0'))";
	$sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser) ";
	$sql_insert.= "values ('$teil','$auftragsnr','$paleta','$stk_pro_pal',0,'0','$el','$user')";

	array_push($dlagerSqlArray, $sql_insert);
	
	  mysql_query($sql_delete);
	  mysql_query($sql_insert);
	
    }
    $paleta+=$increment;
}

$returnArray = array(
    'user' => $user,
    'teil' => $teil,
    'auftrag'=>$auftragsnr,
    'minpreis' => $minpreis,
    'dauftrSqlArray' => $dauftrSqlArray,
    'dlagerSqlArray' => $dlagerSqlArray,
);

echo json_encode($returnArray);
