<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $id = $_POST['id'];

    $datum = substr($id,  strrpos($id, '_')+1);
    
    // vytvorit novy lkw
    
    $returnArray = array(
	'id'=>$id,
	'datum'=>$datum,
	
    );

    
echo json_encode($returnArray);