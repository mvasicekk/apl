<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$persnr_val = intval(trim($_POST['persnr_val']));
$datum_val = $a->make_DB_datum(trim($_POST['datum_val']));

if(strlen($datum_val)==0) $datum_val=NULL;
if($persnr_val==0) $persnr_val=NULL;





$vorschussArray = $a->getVorschussArray($persnr_val,$datum_val);

$retArray = array(
    'id'=>$id,
    'persnr_val'=>$persnr_val,
    'datum_val'=>$datum_val,
    'rows'=>$vorschussArray
);


echo json_encode($retArray);