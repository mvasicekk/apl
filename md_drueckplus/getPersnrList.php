<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$persnrList = trim($o->persnrList);
$view = $o->view;
$persnrArray = preg_split("/[\s,]+/", $persnrList);

$osoby = array();

if(count($persnrArray)>0){
    $persnrArray = array_unique($persnrArray);
    sort($persnrArray);
    foreach ($persnrArray as $persnr){
	if($view=="matagplan"){
	    // pro view planovani pro den a oe musim vybirat jinak
	    $sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3,regelarbzeit from dpers where persnr='$persnr' and dpersstatus='MA'";
	}
	else{
	    $sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3 from dpers where persnr='$persnr' and dpersstatus='MA'";
	}
	
	$rs = $a->getQueryRows($sql);
	if($rs!==NULL){
	    array_push($osoby, $rs[0]);
	}
    }
}
$returnArray = array(
    'view'=>$view,
    'u' => $u,
    'persnrList' => $persnrList,
    'persnrArray' => $persnrArray,
    'osoby'=>$osoby,
    'persnrExists'=>$persnrExists,
);

echo json_encode($returnArray);
