<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$sql = $_POST['sql'];


$data = $a->getQueryRows($sql);
$columnNames = array();
//sanity, hodnoty null nahradim prazdnym retezcem, jinak mam problem s vytvorenim handsontable
if($data!==NULL){
    $run = 0;
    foreach ($data as $index=>$row){
	if($run==0){
	    foreach ($row as $key=>$value){
		array_push($columnNames, $key);
	    }
	}

	foreach ($row as $key=>$value){
	    if($value==NULL){
		$data[$index][$key]=" ";
	    }
	}
	
	$run++;
    }
}

//
//if($data!==NULL){
//    $row = $data[0];
//    foreach ($row as $key=>$value){
//	array_push($columnNames, $key);
//    }
//}

$retArray = array(
    'sql'=>$sql,
    'data'=>$data,
    'columnNames'=>$columnNames,
);


echo json_encode($retArray);