<?
require_once '../db.php';

    $e = $_GET['e'];
    $auftrag = $_GET['auftrag'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $a = AplDB::getInstance();

    $teilArray = NULL;
    if(strlen($e)>=2){
	$k = $a->getKundeFromAuftransnr($auftrag);
	$teilArray = $a->getTeilArrayForKundeMatch($k,$e);
    }
    
    if($teilArray!==NULL){
	foreach ($teilArray as $i=>$row){
	    $teilArray[$i]['formattedTeil'] = 
	    sprintf("<div class='".$row['status']."'><div class='list_kunde'>%03d</div> - <div class='list_teil'>%10s</div> <div class='list_bezeichnung'>%s</div> <div class='list_original'>%s</div> <div class='list_gew'>%.3f</div></div>"
		    ,$row['kunde'],$row['teil'],$row['teilbez'],$row['teillang'],$row['gew']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'kunde'=>$k,
	'teilArray'=>$teilArray,
    );
    echo json_encode($returnArray);

