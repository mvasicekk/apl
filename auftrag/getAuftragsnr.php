<?
require_once '../db.php';

    $e = $_GET['e'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $auftragsnrArray = NULL;
    if(strlen($e)>=3){
	$auftragsnrArray = $apl->getAuftragInfoArray($e, NULL,TRUE);
	//$persnrArray1 = $persnrArray['rows'];
    }
    
    if($auftragsnrArray!==NULL){
	foreach ($auftragsnrArray as $i=>$row){
	    $auftragsnrArray[$i]['formattedAuftragsnr'] = sprintf("<div class='list_kunde'>%03d</div> - <div class='list_import'>%8d</div> <div class='list_bestellnr'>BestellNr:%s</div> <div class='list_aufdat'>AE:%s</div> <div class='list_auslieferdatum'>AusDat:%s</div>",$row['kunde'],$row['auftragsnr'],$row['bestellnr'],$row['aufdat'],$row['ausliefer_datum']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'auftragsnrArray'=>$auftragsnrArray,
    );
    echo json_encode($returnArray);

