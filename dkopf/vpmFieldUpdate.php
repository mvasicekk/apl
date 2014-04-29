<?
require_once '../db.php';

    $id=$_POST['id'];
    $val = $_POST['val'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $dokuId = substr($id, strrpos($id, '_')+1);
    $fieldName = substr($id, 2, strrpos($id, '_')-2);
    
    $goUpdate = FALSE;
    $field = "";
    
    if($fieldName=="stk"){
	// muze byt prazdne
	$field = "verp_stk";
        $val = intval(trim($val));
        $goUpdate = TRUE;
    }

    if($fieldName=="bemerkung"){
	// muze byt prazdne
	$field = "bemerkung";
        $val = trim($val);
        $goUpdate = TRUE;
    }

    if($goUpdate){
	// validace parametru mi prosla, provedu vlastni update v databazi
	$ar = $apl->updateVPMField($dokuId,$field,$val);
    }
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'val'=>$val,
	'dokuId'=>$dokuId,
	'fieldName'=>$fieldName,
	'field'=>$field,
	'goUpdate'=>$goUpdate,
    );
    echo json_encode($returnArray);

?>

