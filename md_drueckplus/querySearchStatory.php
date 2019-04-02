<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$stator = trim($o->stator);
$drueck_id = $o->drueck_id;


if((strlen($stator)>0)){
    $a = AplDB::getInstance();
    $ident = $a->get_user_pc();
    $sql.= "select dstator.stator from dstator";
    $sql.= " join dstator_pal on dstator_pal.id=dstator.paleta";
    $sql.= " join drueck on drueck.`pos-pal-nr`=dstator_pal.paleta";
    $sql.= " where";
    $sql.= "	drueck.drueck_id='$drueck_id'";
    $sql.= " and";
    $sql.= " stator like '$stator%'";
    $sql.= " order by";
    $sql.= " dstator.stator";
    
    $statory = $a->getQueryRows($sql);

}


$returnArray = array(
	'ident'=>$ident,
	'statory'=>$statory,
    );
    
echo json_encode($returnArray);
