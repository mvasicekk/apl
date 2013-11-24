<?
require_once '../db.php';

    $id = $_POST['id'];
    $data = $_POST['data'];

    $apl = AplDB::getInstance();

    $adressId = substr($id, strpos($id, '_')+1);
    
    $ar=0;
    foreach ($data as $input) {
	$str.='('.$input["name"].' '.$input["value"].')';
	$a=$apl->updateAdresyField($input["name"],$input["value"],$adressId);
	$ar+=$a;
    }
    
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'data'=>$data,
			    'ar'=>$ar,
    ));
?>
