<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$dauftr_id = $o->params->r->id_dauftr;
$auftragsnr = $o->params->r->auftragsnr;
$KzGut=chop($o->params->r->KzGut);

if($KzGut=='G'){
    $sql = $a->deleteDauftr($dauftr_id,TRUE);
}
else{
    $sql = $a->deleteDauftr($dauftr_id,FALSE);
}


// vztahnu updatnute radky -----------------------------------------------------
$dauftrPos = $a->getDauftrRowsForImport($auftragsnr);
if($dauftrPos!==NULL){
    $oldpal = $dauftrPos[0]['imp_pal'];
    foreach($dauftrPos as $p=>$row){
	//zjistim zda ma exportni cislo u pozice fakturu
	$ex = $row['ex'];
	$hatRechnung = 0;
	if(strlen(trim($ex))>0){
	    $exInfoArray = $a->getAuftragInfoArray($ex);
	    if($exInfoArray!==NULL){
		$hatRechnung = $exInfoArray[0]['hatrechnung'];
	    }
	    else{
		$hatRechnung = 0;
	    }
	}
	$dauftrPos[$p]['hatrechnung']=$hatRechnung;
	$dauftrPos[$p]['edit']=0;
	if($row['imp_pal']!=$oldpal){
	    $dauftrPos[$p]['newpal']=1;
	    $oldpal = $row['imp_pal'];
	}
	else{
	    $dauftrPos[$p]['newpal']=0;
	}
    }
}

$returnArray = array(
	'ar'=>$ar,
	'sql'=>$sql,
	'dauftragPositionen'=>$dauftrPos,
	'myerror'=>$myerror,
    );
    
    echo json_encode($returnArray);
