<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$sql = $_POST['sql'];
$tabid = $_POST['tabid'];

$s = "select dschltabellen.`sql` from dschltabellen where tabid='$tabid'";
$rs = $a->getQueryRows($s);
if($rs!==null){
    $sql1 = $rs[0]['sql'];
    //$sql=$sql1;
}

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


//projdu columnNames a vytvorim pole columns s volbama pro sloupce

$columns = array();
$columnsOptions = array();

$schTabInfo = $a->getSchlTabellenArray($tabid);
if($schTabInfo!==NULL){
    $sr = $schTabInfo[0];
    $c = trim($sr['columns']);
    if(strlen($c)>0){
	//rozdelit podle stredniku jednotlive sloupce
	$sloupce = explode(';', $c);
	if(is_array($sloupce)){
	    foreach ($sloupce as $k=>$v){
		$key = substr($v, 0,  strpos($v, ':'));
		$value = substr($v, strpos($v, ':')+1);
		//pred prvni zavorku jeste vlozim vlastnost data
		$value = "{".'"data":"'.$key.'"'.",$value}";
		$columns[$key]=$value;
	    }
	}
	//$columns = $sloupce;
    }
}

foreach ($columnNames as $columnName){
    if(array_key_exists($columnName, $columns)){
	array_push($columnsOptions, json_decode($columns[$columnName]));
	//array_push($columnsOptions, json_decode('{"readOnly":true}'));
    }
    else{
	array_push($columnsOptions, json_decode('{"data":"'.$columnName.'"}'));
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
    'sql1'=>$sql1,
    'data'=>$data,
    'tabid'=>$tabid,
    'columns'=>$columns,
    'columnsOptions'=>$columnsOptions,
    'columnNames'=>$columnNames,
);


echo json_encode($retArray);