<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$von = $o->von;
$bis = $o->bis;

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];


//osobni faktory
$sql = "select * from hodnoceni_osobni_faktory order by `sort`";
$osobniFaktory = $a->getQueryRows($sql);


//seznam OE
$sql= "select doe.* from doe where stredisko_isp is not null order by oe";
$oeArray = $a->getQueryRows($sql);

//firemni faktory
$sql = "select * from hodnoceni_firemni_faktory order by `sort`";
$firemniFaktory = $a->getQueryRows($sql);


$returnArray = array(
	'osobniFaktory'=>$osobniFaktory,
	'firemniFaktory'=>$firemniFaktory,
	'oeArray'=>$oeArray,
	'u'=>$u
    );
    
echo json_encode($returnArray);
