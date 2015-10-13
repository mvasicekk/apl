<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$import = $_POST['import'];
$pal1 = $_POST['pal1'];
$pal2 = $_POST['pal2'];
$pal2stk = $_POST['pal2stk'];
$persnr = $_POST['persnr'];
$gt = $_POST['gt'];
$termin = $_POST['termin'];
$drueckHotData = $_POST['drueckHotData'];

$dauftrLog="";
$drueckLog="";

$u = $a->get_user_pc();

//pozice z dauftr
$dauftrRows = $a->getDauftrRowsForImportPal($import, $pal1);
if($dauftrRows!==NULL){
    foreach ($dauftrRows as $dr){
	$id = $dr['id'];
	$kzgut = $dr['kzgut'];
	$newstk = intval($dr['stk'])-$pal2stk;
	$updateDauftrSql = "update dauftr set `stück`='$newstk' where id_dauftr=$id";
	$a->query($updateDauftrSql);
	$dauftrLog.=$updateDauftrSql." (".$dr['abgnr'].")";
	$dauftrLog.="<br>";
	//otestuju zda uz paletu s cinnosti pro presunuti mam, pokud ano, tak k ni prictu, jinak vlozim
	$daTest = $a->getDauftrRowsForImportPalAbgnr($dr['auftragsnr'], $pal2,$dr['abgnr']);
	if($daTest!==NULL){
	    // bude update
	    $dauftrid = $daTest[0]['id'];
	    $stk = intval($daTest[0]['stk']);
	    $nstk = intval($stk)+intval($pal2stk);
	    // a provedu update
	    $updSql="update dauftr set `stück`='$nstk' where id_dauftr='$dauftrid'";
	    $dauftrLog.= $updSql;
	    $a->query($updSql);
	}
	else{
	    // bude insert
	    $insertDauftrSql = "insert into dauftr (giesstag,termin,auftragsnr,teil,`pos-pal-nr`,preis,`stück`,`mehrarb-kz`,fremdauftr,fremdpos,KzGut,abgnr,VzKd,VzAby,comp_user_accessuser,inserted)";
	    $insertDauftrSql.= "values(";
	    $insertDauftrSql.= "'".$gt."',";
	    $insertDauftrSql.= "'".$termin."',";
	    $insertDauftrSql.= "'".$dr['auftragsnr']."',";
	    $insertDauftrSql.= "'".$dr['teil']."',";
	    $insertDauftrSql.= "'".$pal2."',";
	    $insertDauftrSql.= "'".$dr['preis']."',";
	    $insertDauftrSql.= "'".$pal2stk."',";
	    $insertDauftrSql.= "'".$dr['tatkz']."',";
	    $insertDauftrSql.= "'".$dr['fremdauftr']."',";
	    $insertDauftrSql.= "'".$dr['fremdpos']."',";
	    $insertDauftrSql.= "'".$dr['kzgut']."',";
	    $insertDauftrSql.= "'".$dr['abgnr']."',";
	    $insertDauftrSql.= "'".$dr['vzkd']."',";
	    $insertDauftrSql.= "'".$dr['vzaby']."',";
	    $insertDauftrSql.= "'".$u."',";
	    $insertDauftrSql.= "NOW()";
	    $insertDauftrSql.= ")";
	    $dauftrLog.= $insertDauftrSql;
	}
	
	
	$a->query($insertDauftrSql);
	$dauftrLog.="<br>";

	if($kzgut=='G'){
    	    //zaznamy do dlagerbew
	    //$a->updateDlagerImportStkForDauftrId($id, $newstk, 'pal_split');
	    $dauftrLog.="updateDlagerImportStkForDauftrId($id, $newstk, 'pal_split')<br>";
	    // pridat info do dlagerbew
	    $el = $a->erster_lager($dr['teil'], $dr['auftragsnr'], $pal2);
            //nejdriv smazu eventuelni starou pozici v lagru
            $sql_delete = "delete from dlagerbew where ((teil='".$dr['teil']."') and (auftrag_import='".$dr['auftragsnr']."') and (pal_import='$pal2') and (lager_von='0'))";
            $sql_insert = "insert into dlagerbew (teil,auftrag_import,pal_import,gut_stk,auss_stk,lager_von,lager_nach,comp_user_accessuser,prog_module) ";
            $sql_insert.= "values ('".$dr['teil']."','".$dr['auftragsnr']."','$pal2','$pal2stk',0,'0','$el','$u','pal_split')";
	    $dauftrLog.="$sql_delete<br>";
	    $dauftrLog.="$sql_insert<br>";
	    $a->query($sql_delete);
	    $a->query($sql_insert);
//	    
	    //mysql_query($sql_delete);
	    //mysql_query($sql_insert);
	}
    }
}

//pozice do drueck
//$rmArray = $a->getRMArray($import, $pal1);
$rmArray = $drueckHotData;
if($rmArray!==NULL){
    foreach ($rmArray as $rm){
	//$oe = "?";
	$vzArray = $a->getVZFromDauftr($import,$pal1,$rm['abgnr']);
	if($vzArray!=NULL){
	    $vzkd = $vzArray[0]['vzkd'];
	    $vzaby = $vzArray[0]['vzaby'];
	}
	else{
	    $vzkd = 0;
	    $vzaby = 0;
	}
	
	$verbVon = date('Y-m-d')." 00:00:00";
	$verbBis = date('Y-m-d')." 00:00:00";
	
	
	
	$oes4abgnr = $a->getOEForAbgnr($rm['abgnr']);
	$oes4abgnrA1 = split(";", $oes4abgnr);
	$oes4abgnrA = array();
	foreach ($oes4abgnrA1 as $oe1){
	    array_push($oes4abgnrA, trim($oe1));
	}
	$pg=$a->getPGFromAuftragsnr($import);
	$oes4PG = $a->getOESForPG($pg);
	$oes4frSp = $a->getOEForFrSp('N');
	
	$intersectOE = array_intersect($oes4abgnrA, $oes4PG,$oes4frSp);
	if(is_array($intersectOE)){
	    if(count($intersectOE)>0){
		foreach ($intersectOE as $k=>$val){
		    $oe=$val;
		}
	    }
	}
	
	//posledni zachrana (napr. pro abgnr=95
	if(trim($oe)==""){
	    if(count($oes4abgnrA)>0){
		$oe = $oes4abgnrA[0];
	    }
	}
	if(trim($oe)=="") $oe="?";

	$gutStk = 0;
	$aussStk = 0;
	$aussArt = 0;
	$aussTyp = 0;
	if($rm['gutFlag']=="1"){
	    $gutStk = -$pal2stk;
	}
	else{
	    if($rm['aussFlag']=="1"){
		$gutStk = 0;
		$aussStk = -$pal2stk;
		$aussArt = $rm['aussart'];
		$aussTyp = $rm['auss_typ'];
	    }
	}
	if ($gutStk != 0 || $aussStk != 0) {
	    $insertDrueckMinus = "insert into drueck";
	    $insertDrueckMinus.= "(auftragsnr,teil,taetnr,`stück`,`auss-stück`,drueck.`auss-art`,drueck.auss_typ,`vz-soll`,`vz-ist`,`verb-zeit`,persnr,datum,`pos-pal-nr`,";
	    $insertDrueckMinus.= "`verb-von`,`verb-bis`,`verb-pause`,schicht,oe,comp_user_accessuser,insert_stamp)";
	    $insertDrueckMinus.="values(";
	    $insertDrueckMinus.="'" . $rm['import'] . "',";
	    $insertDrueckMinus.="'" . $rm['teil'] . "',";
	    $insertDrueckMinus.="'" . $rm['abgnr'] . "',";
	    $insertDrueckMinus.="'" . $gutStk . "',";
	    $insertDrueckMinus.="'" . "$aussStk" . "',";
	    $insertDrueckMinus.="'" . "$aussArt" . "',";
	    $insertDrueckMinus.="'" . "$aussTyp" . "',";
	    $insertDrueckMinus.="'" . $vzkd . "',";
	    $insertDrueckMinus.="'" . $vzaby . "',";
	    $insertDrueckMinus.="'" . "0" . "',";
	    $insertDrueckMinus.="'" . $persnr . "',";
	    $insertDrueckMinus.="'" . date('Y-m-d') . "',";
	    $insertDrueckMinus.="'" . $pal1 . "',";
	    $insertDrueckMinus.="'" . $verbVon . "',";
	    $insertDrueckMinus.="'" . $verbBis . "',";
	    $insertDrueckMinus.="'" . "0" . "',";
	    $insertDrueckMinus.="'" . "0" . "',";
	    $insertDrueckMinus.="'" . $oe . "',";
	    $insertDrueckMinus.="'" . $u . "',";
	    $insertDrueckMinus.="NOW()";
	    $insertDrueckMinus.=")";

	    $drueckLog.="drueckMinus:$insertDrueckMinus<br>";
	$a->query($insertDrueckMinus);
	$a->insertDlagerBew($rm['teil'], $rm['import'], $pal1, -$pal2stk, 0, '', '', $u, $rm['abgnr'], 'pal_split');
	    $drueckLog.="insertDlagerBew(" . $rm['teil'] . ", " . $rm['import'] . ", " . $pal1 . ", -$pal2stk, 0, '', '', $u, " . $rm['abgnr'] . ", 'pal_split')<br>";
	}

	$gutStk = -$gutStk;
	$aussStk = -$aussStk;
	if ($gutStk != 0 || $aussStk != 0) {
	    $insertDrueckPlus = "insert into drueck";
	    $insertDrueckPlus.= "(auftragsnr,teil,taetnr,`stück`,`auss-stück`,drueck.`auss-art`,drueck.auss_typ,`vz-soll`,`vz-ist`,`verb-zeit`,persnr,datum,`pos-pal-nr`,";
	    $insertDrueckPlus.= "`verb-von`,`verb-bis`,`verb-pause`,schicht,oe,comp_user_accessuser,insert_stamp)";
	    $insertDrueckPlus.="values(";
	    $insertDrueckPlus.="'" . $rm['import'] . "',";
	    $insertDrueckPlus.="'" . $rm['teil'] . "',";
	    $insertDrueckPlus.="'" . $rm['abgnr'] . "',";
	    $insertDrueckPlus.="'" . $gutStk . "',";
	    $insertDrueckPlus.="'" . "$aussStk" . "',";
	    $insertDrueckPlus.="'" . "$aussArt" . "',";
	    $insertDrueckPlus.="'" . "$aussTyp" . "',";
	    $insertDrueckPlus.="'" . $vzkd . "',";
	    $insertDrueckPlus.="'" . $vzaby . "',";
	    $insertDrueckPlus.="'" . "0" . "',";
	    $insertDrueckPlus.="'" . $persnr . "',";
	    $insertDrueckPlus.="'" . date('Y-m-d') . "',";
	    $insertDrueckPlus.="'" . $pal2 . "',";
	    $insertDrueckPlus.="'" . $verbVon . "',";
	    $insertDrueckPlus.="'" . $verbBis . "',";
	    $insertDrueckPlus.="'" . "0" . "',";
	    $insertDrueckPlus.="'" . "0" . "',";
	    $insertDrueckPlus.="'" . $oe . "',";
	    $insertDrueckPlus.="'" . $u . "',";
	    $insertDrueckPlus.="NOW()";
	    $insertDrueckPlus.=")";

	    $drueckLog.="drueckPlus:$insertDrueckPlus<br>";
	$a->query($insertDrueckPlus);
	$a->insertDlagerBew($rm['teil'], $rm['import'], $pal2, $pal2stk, 0, '', '', $u, $rm['abgnr'], 'pal_split');
	    $drueckLog.="insertDlagerBew(" . $rm['teil'] . ", " . $rm['import'] . ", " . $pal2 . ", $pal2stk, 0, '', '', $u, " . $rm['abgnr'] . ", 'pal_split')<br>";
	}
    }
}

$retArray = array(
    'id' => $id,
    'persnr' => $persnr,
    'import' => $import,
    'pal1' => $pal1,
    'pal2' => $pal2,
    'pal2stk' => $pal2stk,
    'rows' => $rmArray,
    'dauftrRows' => $dauftrRows,
    'rmRows' => $rmArray,
    'dauftrLog' => $dauftrLog,
    'drueckLog' => $drueckLog,
    'intersectOE' => $intersectOE,
    'oes4abgnrA' => $oes4abgnrA,
    'oes4PG' => $oes4PG,
    'oes4frSp' => $oes4frSp,
    'drueckHotData'=>$drueckHotData,
    'rmArray'=>$rmArray
);


echo json_encode($retArray);