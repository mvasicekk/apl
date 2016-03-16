<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$p = $o->params->r;

$dpos_id = intval($p->dpos_id);
$a = AplDB::getInstance();

// sanace promennych
$abgnr = intval($p->{'TaetNr-Aby'});
if($abgnr>0){
    $teil = $p->{'Teil'};
    $kzgut = strtoupper(trim($p->{'KzGut'}));
    $bezD = trim($p->{'TaetBez-Aby-D'});
    $bezT = trim($p->{'TaetBez-Aby-T'});
    $vzkd = floatval(strtr($p->{'VZ-min-kunde'},',','.'));
    $vzaby = floatval(strtr($p->{'vz-min-aby'},',','.'));
    $kzdruck = $p->{'kz-druck'};
    $lVon = $p->{'lager_von'}->lager;
    $lBis = $p->{'lager_nach'}->lager;
    $bedarfTyp = trim($p->{'bedarf_typ'});
    
    
    if ($dpos_id == 0) {
	//nova pozice
	$sqlInsert = "insert into dpos (`TaetNr-Aby`,Teil,KzGut,`TaetBez-Aby-D`,`TaetBez-Aby-T`,`VZ-min-kunde`,`vz-min-aby`,`kz-druck`,lager_von,lager_nach,bedarf_typ)";
	$sqlInsert.= " values('$abgnr','$teil','$kzgut','$bezD','$bezT','$vzkd','$vzaby','$kzdruck','$lVon','$lBis','$bedarfTyp')";
	$insertId = $a->insert($sqlInsert);
	if ($insertId > 0) {
	    $sql = "select * from dpos where teil='$teil' order by `TaetNr-Aby`";
	    $dpos = $a->getQueryRows($sql);
	}
    } else {
	// update
	$sqlUpdate = "update dpos set";
	$sqlUpdate.=" KzGut='$kzgut',";
	$sqlUpdate.= " `TaetBez-Aby-D`='$bezD',";
	$sqlUpdate.= " `TaetBez-Aby-T`='$bezT'";
	$sqlUpdate.= ",`VZ-min-kunde`='$vzkd',";
	$sqlUpdate.= " `vz-min-aby`='$vzaby',";
	$sqlUpdate.= " `kz-druck`='$kzdruck',";
	$sqlUpdate.= " lager_von='$lVon',";
	$sqlUpdate.= " lager_nach='$lBis',";
	$sqlUpdate.= " bedarf_typ='$bedarfTyp'";
	$sqlUpdate.=" where dpos_id='$dpos_id' limit 1";
	
	$ar = $a->query($sqlUpdate);
	if($ar>0){
	    $sql = "select * from dpos where dpos_id='$dpos_id'";
	    $updatedRows = $a->getQueryRows($sql);
	    $updatedRow = $updatedRows[0];
	    $updatedRow['edit'] = 0;
	}
    }
}

$returnArray = array(
	'p'=>$p,
	'abgnr'=>$abgnr,
	'teil'=>$teil,
	'sqlInsert'=>$sqlInsert,
	'insertId'=>$insertId,
	'dpos'=>$dpos,
	'ar'=>$ar,
	'dpos_id'=>$dpos_id,
	'updatedRow'=>$updatedRow,
    );
    
echo json_encode($returnArray);
