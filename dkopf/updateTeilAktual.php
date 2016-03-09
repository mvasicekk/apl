<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$field = $o->field;
$teilaktual = $o->teilaktual;
$value = $teilaktual->{$field};
$newValue = $value;
$dbValue = $newValue;

$teil = $teilaktual->Teil;

// nejaka sanace
if($field=='status'){
    $newValue = strtoupper($value);
}

//uprava desetinnych cisel i celych
if(	   $field=='Gew' 
	|| $field=='BrGew' 
	|| $field=='verpackungmenge' 
	|| $field=='stk_pro_gehaenge' 
	|| $field=='FA' 
	|| $field=='preis_stk_gut' 
	|| $field=='preis_stk_auss' 
	|| $field=='kosten_stk_auss'
	|| $field=='jb_lfd_2'
	|| $field=='jb_lfd_1'
	|| $field=='jb_lfd_j'
	|| $field=='jb_lfd_plus_1'
	){
    $newValue = strtr($value,',','.');
    $newValue = floatval($newValue);
}

$dbValue = $newValue;

//objekty
if($field=='Wst'){
    $dbValue = $newValue->id;
}

$ar = 1;
//$sql = "select * from werkstoffe order by beschreibung";
//$werkstoffe = $a->getQueryRows($sql);
			
$returnArray = array(
	'ar'=>$ar,
	'field'=>$field,
	'teilaktual'=>$teilaktual,
	'value'=>$value,
	'teil'=>$teil,
	'newValue'=>$newValue,
	'dbValue'=>$dbValue
    );
    
echo json_encode($returnArray);
