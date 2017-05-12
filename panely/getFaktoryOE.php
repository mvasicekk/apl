<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

//osobni faktory
$sql = "select * from hodnoceni_osobni_faktory order by `sort`";
$osobniFaktory = $a->getQueryRows($sql);


//seznam OE
$sql= "select doe.* from doe where stredisko_isp is not null order by oe";
$oeArray = $a->getQueryRows($sql);

foreach ($osobniFaktory as $of){
    $id_faktor = $of['id'];
    foreach ($oeArray as $oe){
	$id_hodnoceni_faktory_oe = 0;
	$sql = "select id from hodnoceni_faktory_oe where id_faktor='$id_faktor' and oe='".$oe['oe']."'";
	$rs = $a->getQueryRows($sql);
	if($rs!==NULL){
	    $id_hodnoceni_faktory_oe = $rs[0]['id'];
	}
	$faktoryOE[$id_faktor][$oe['oe']]['id_hodnoceni_faktory_oe'] = $id_hodnoceni_faktory_oe;
    }
}

$returnArray = array(
	'faktoryOE'=>$faktoryOE,
	'u'=>$u
    );
    
echo json_encode($returnArray);
