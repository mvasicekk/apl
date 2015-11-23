<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$spediteur = $_POST['spediteur'];

$action = $_POST['action'];

$datumVon = $a->make_DB_datum(trim($_POST['datum_val_von']));
$datumBis = $a->make_DB_datum(trim($_POST['datum_val_bis']))." 23:59:59";

if($action=="delRundlauf"){
    $rundlaufId = substr($id, strrpos($id, '_')+1);
    $a->deleteRundlaufImEx($rundlaufId);
    $a->deleteRundlauf($rundlaufId);
}

if(strlen(trim($datumVon))>0 && strlen(trim($datumBis))>0){
    $rundlaufArray = $a->getRundlaufMatch($datumVon,$datumBis,$spediteur);
    if($rundlaufArray!==NULL){
	foreach ($rundlaufArray as $index=>$row){
	    $imexStr = "";
	    $imexA = $a->getRundlaufImExArray($row['id']);
	    if($imexA!==NULL){
		foreach ($imexA as $imex){
		    $ie = $imex['imex']=='E'?'Ex':'Im';
		    $classIE = $imex['imex']=='E'?'payLoad_E':'payLoad_I';
		    $auftragsnr = $imex['auftragsnr'];
		    $auftragsnr4 = substr($auftragsnr,-4);
		    $zoN = $a->getZielortAuftrag($auftragsnr);
		    $zielortName = $imex['imex']=='E'?" - ".$zoN:'';
		    //
		    // auftrag/auftrag.php#/det/19500469
		    $imexStr.="<div class='payLoad $classIE'>"."<a target='_blank' href='../auftrag/auftrag.php#/det/$auftragsnr'>".$ie.$auftragsnr."</a>".$zielortName."</div>";
		}
	    }
	    
	    $rundlaufArray[$index]['imexstr'] = "<div class='imexStr'>".$imexStr."</div>";
	    $rowId = $row['id'];
	    $rundlaufArray[$index]['delstr'].="<div class='btn-group btn-group-xs' role='group'>";
	    $rundlaufArray[$index]['delstr'].="<button id='delRundlauf_$rowId' class='btn btn-sm btn-danger'>";
	    $rundlaufArray[$index]['delstr'].="<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>";
	    $rundlaufArray[$index]['delstr'].="</button>";
	    $rundlaufArray[$index]['delstr'].="</div>";
	    //$rundlaufArray[$index]['delstr'] = "<button class='btn btn-xs' id='del_$rowId'>DEL</button>";
	    $sA = $a->getSpediteurArray($row['dspediteur_id']);
	    $rundlaufArray[$index]['spedname'] = $sA[0]['name']."(".$row['dspediteur_id'].")";
	}
    }
}


$retArray = array(
    'id'=>$id,
    'action'=>$action,
    'spediteur'=>$spediteur,
    'datumVon'=>$datumVon,
    'datumBis'=>$datumBis,
    'rows'=>$rundlaufArray
);


echo json_encode($retArray);