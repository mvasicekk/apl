<?
require_once '../db.php';

    $id = $_POST['id'];
    $adressId = $_GET['addressId'];
    $checked = $_POST['checked'];

    $katId = substr($id, strpos($id, '_')+1);
    
    $apl = AplDB::getInstance();

    // odstraneni z kategorie
    if($checked=='false'){
	$apl->deleteAdresyInKategorie($adressId,$katId);
    }
    else{
	$apl->addAdresyInKategorie($adressId,$katId);
    }
    
    
    $aIk = $apl->getAdresyInKategorien($adressId);
    $aikString = "";
    $aikArray = array();
    if($aIk!==NULL){
	foreach ($aIk as $a){
	    array_push($aikArray, $a['kategorie']);
	}
	$aikString = implode(",", $aikArray);
    }
    
    $katStrMaxLength = 50;
    $tecky = strlen($aikString)>$katStrMaxLength?'...':'';
    $aikString = "&nbsp;".substr($aikString, 0, $katStrMaxLength).$tecky;

    echo json_encode(array(
                            'id'=>$id,
			    'katId'=>$katId,
			    'adressId'=>$adressId,
			    'checked'=>$checked,
			    'ar'=>$ar,
			    'aikString'=>$aikString,
    ));
?>
