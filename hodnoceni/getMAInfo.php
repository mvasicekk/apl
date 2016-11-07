<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$direction = $o->direction;
$jenMA = $o->jenma;
$oeselected = $o->oeselected;


$persinfo = NULL;
$a = AplDB::getInstance();

$u = $_SESSION['user'];



// pokud dostanu persnr = 0, vratim prvniho zamestnance se statusem MA
if (intval($persnr) == 0) {
    $sql = "select dpers.* from dpers where ";
    $sql.=" ((`dpersstatus`='MA'))";
    $sql.=" order by persnr limit 1";
} else {
    if ($direction != 0) {
	//vratit nasledujiciho/predchoziho MA , podle filtru
	$where = $direction>0?" (`persnr`>'$persnr')":" (`persnr`<'$persnr')";
	$order = $direction>0?" order by persnr asc":" order by persnr desc";
	$limit = " limit 1";
    }
    else{
	$where =" (`PersNr`='$persnr')";
    }
    
    if($oeselected!='*'){
	$join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
	$join.=" join doe on doe.oe=dtattypen.oe";
    }
    
    $sql = "select * from dpers";
    $sql.= " $join";
    $sql.=" where ";
    $sql.=" $where";
    
    // pridat filtry
    if($jenMA===TRUE){
	$sql.=" and (dpersstatus='MA')";
    }
    if($oeselected!='*'){
	$sql.=" and (doe.oe='$oeselected')";
    }
    
    
    $sql.=" $order";
    $sql.=" $limit";

}

$ma = $a->getQueryRows($sql);

if($ma!==NULL){
    $persnrNew = $ma[0]['PersNr'];
    $oeInfo = $a->getPersOEInfo($persnrNew);
}




$returnArray = array(
    'u' => $u,
    'ma' => $ma,
    'oeinfo'=>$oeInfo,
    'sql' => $sql,
);

echo json_encode($returnArray);
