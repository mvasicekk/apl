<?
require_once '../db.php';

    $id = $_POST['id'];
    $data = $_POST['data'];

    $apl = AplDB::getInstance();

    $adressId = substr($id, strpos($id, '_')+1);
    
    $ar=0;
    foreach ($data as $input) {
	$column = $input['name'];
	if($input['name']=="geboren1") $column="geboren";
	$columns.="`".$column.'`,';
	$values.="'".$input['value']."',";
    }
    
    $columns = "(".substr($columns, 0,strlen($columns)-1).")";
    $values = "(".substr($values, 0,strlen($values)-1).")";
    
    $sql="insert into adresy $columns values $values";

    $apl->query($sql);
    
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'data'=>$data,
			    'ar'=>$ar,
			    'sql'=>$sql,
    ));
?>
