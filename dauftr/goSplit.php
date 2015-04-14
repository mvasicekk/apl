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

$dauftrLog="";
$drueckLog="";

$u = $a->get_user_pc();

//pozice z dauftr
$dauftrRows = $a->getDauftrRowsForImportPal($import, $pal1);
if($dauftrRows!==NULL){
    foreach ($dauftrRows as $dr){
	$id = $dr['id'];
	$kzgut = $dr['kzgut'];
	$newstk = $dr['stk']-$pal2stk;
	$updateDauftrSql = "update dauftr set `stück`='$newstk' where id_dauftr=$id";
	$dauftrLog.=$updateDauftrSql." (".$dr['abgnr'].")";
	$dauftrLog.="<br>";
	$insertDauftrSql = "insert into dauftr (auftragsnr,teil,`pos-pal-nr`,preis,`stück`,`mehrarb-kz`,fremdauftr,fremdpos,KzGut,abgnr,VzKd,VzAby,comp_user_accessuser,inserted)";
	$insertDauftrSql.= "values(";
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
	    
	    //mysql_query($sql_delete);
	    //mysql_query($sql_insert);
	}
    }
}

//pozice do drueck
$rmArray = $a->getRMArray($import, $pal1);
if($rmArray!==NULL){
    foreach ($rmArray as $rm){
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
	
	$oe = "?";
	
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
	
	if(trim($oe)=="") $oe="?";
	
	$insertDrueckMinus = "insert into drueck";
	$insertDrueckMinus.= "(auftragsnr,teil,taetnr,`stück`,`auss-stück`,`vz-soll`,`vz-ist`,`verb-zeit`,persnr,datum,`pos-pal-nr`,";
	$insertDrueckMinus.= "`auss-art`,`verb-von`,`verb-bis`,`verb-pause`,schicht,oe,auss_typ,comp_user_accessuser,insert_stamp,kzgut)";
	$insertDrueckMinus.="values(";
	$insertDrueckMinus.="'".$rm['import']."',";
	$insertDrueckMinus.="'".$rm['teil']."',";
	$insertDrueckMinus.="'".$rm['abgnr']."',";
	$insertDrueckMinus.="'".-$pal2stk."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'".$vzkd."',";
	$insertDrueckMinus.="'".$vzaby."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'".$persnr."',";
	$insertDrueckMinus.="'".date('Y-m-d')."',";
	$insertDrueckMinus.="'".$pal1."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'".$verbVon."',";
	$insertDrueckMinus.="'".$verbBis."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'".$oe."',";
	$insertDrueckMinus.="'"."0"."',";
	$insertDrueckMinus.="'".$u."',";
	$insertDrueckMinus.="NOW()";
	$insertDrueckMinus.=")";

	$drueckLog.="drueckMinus:$insertDrueckMinus<br>";
	$a->query($insertDrueckMinus);
	$a->insertDlagerBew($rm['teil'], $rm['import'], $pal1, -$pal2stk, 0, '', '', $u, $rm['abgnr'], 'pal_split');
	$drueckLog.="insertDlagerBew(".$rm['teil'].", ".$rm['import'].", ".$pal1.", -$pal2stk, 0, '', '', $u, ".$rm['abgnr'].", 'pal_split')<br>";

	$insertDrueckPlus = "insert into drueck";
	$insertDrueckPlus.= "(auftragsnr,teil,taetnr,`stück`,`auss-stück`,`vz-soll`,`vz-ist`,`verb-zeit`,persnr,datum,`pos-pal-nr`,";
	$insertDrueckPlus.= "`auss-art`,`verb-von`,`verb-bis`,`verb-pause`,schicht,oe,auss_typ,comp_user_accessuser,insert_stamp,kzgut)";
	$insertDrueckPlus.="values(";
	$insertDrueckPlus.="'".$rm['import']."',";
	$insertDrueckPlus.="'".$rm['teil']."',";
	$insertDrueckPlus.="'".$rm['abgnr']."',";
	$insertDrueckPlus.="'".$pal2stk."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'".$vzkd."',";
	$insertDrueckPlus.="'".$vzaby."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'".$persnr."',";
	$insertDrueckPlus.="'".date('Y-m-d')."',";
	$insertDrueckPlus.="'".$pal2."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'".$verbVon."',";
	$insertDrueckPlus.="'".$verbBis."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'".$oe."',";
	$insertDrueckPlus.="'"."0"."',";
	$insertDrueckPlus.="'".$u."',";
	$insertDrueckPlus.="NOW()";
	$insertDrueckPlus.=")";

	$drueckLog.="drueckPlus:$insertDrueckPlus<br>";
	$a->query($insertDrueckPlus);
	$a->insertDlagerBew($rm['teil'], $rm['import'], $pal2, $pal2stk, 0, '', '', $u, $rm['abgnr'], 'pal_split');
	$drueckLog.="insertDlagerBew(".$rm['teil'].", ".$rm['import'].", ".$pal2.", $pal2stk, 0, '', '', $u, ".$rm['abgnr'].", 'pal_split')<br>";
	
	
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
    'oes4frSp' => $oes4frSp
);


echo json_encode($retArray);