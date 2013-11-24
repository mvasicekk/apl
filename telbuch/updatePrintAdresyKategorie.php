<?
require_once '../db.php';

    $id = $_POST['id'];
    $user = $_GET['user'];
    $report = $_GET['report'];
    $checked = $_POST['checked'];

    $katId = substr($id, strpos($id, '_')+1);
    
    $apl = AplDB::getInstance();

    $value = 0;
    if($checked=='true') $value = 1;
    
    $ar = $apl->updateReportPrintParams($report,$user,$id,$value);
//    // odstraneni z kategorie
//    if($checked=='false'){
//	$apl->deleteAdresyInKategorie($adressId,$katId);
//    }
//    else{
//	$apl->addAdresyInKategorie($adressId,$katId);
//    }
//    
//    
//    $aIk = $apl->getAdresyInKategorien($adressId);
//    $aikString = "";
//    $aikArray = array();
//    if($aIk!==NULL){
//	foreach ($aIk as $a){
//	    array_push($aikArray, $a['kategorie']);
//	}
//	$aikString = implode(",", $aikArray);
//    }
//    
//    $katStrMaxLength = 50;
//    $tecky = strlen($aikString)>$katStrMaxLength?'...':'';
//    $aikString = "&nbsp;".substr($aikString, 0, $katStrMaxLength).$tecky;

    echo json_encode(array(
                            'id'=>$id,
			    'user'=>$user,
			    'report'=>$report,
			    'katId'=>$katId,
//			    'adressId'=>$adressId,
			    'checked'=>$checked,
			    'value'=>$value,
			    'ar'=>$ar,
//			    'aikString'=>$aikString,
    ));
?>
