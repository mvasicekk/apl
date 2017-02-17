<?
// prvni nastartuju session
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$direction = $o->direction;
$jenMA = $o->jenma;
$austritt60 = $o->austritt60;
$oeselected = $o->oeselected;
$statusarray = $o->statusarray;
$oearray = $o->oearray;


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
    
    if (count($oearray) == 1 && $oearray[0] == '*') {
    
}
else{
    $join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
    $join.=" join doe on doe.oe=dtattypen.oe";
}
//    if($oeselected!='*'){
//	$join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
//	$join.=" join doe on doe.oe=dtattypen.oe";
//    }
    
    $sql = "select * from dpers";
    $sql.= " $join";
    $sql.=" where ";
    $sql.=" $where";
    
    // pridat filtry
    /*
    if ($jenMA==TRUE) {
    if($austritt60==TRUE){
	$sql.=" and ((dpers.eintritt is not null) and ((dpers.dpersstatus='MA') or if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0)<60))";
    }
    else{
	$sql.=" and (dpers.dpersstatus='MA')";
    }
    }
    */
    //dpersstatus
    if(is_array($statusarray)){
	if(count($statusarray)>0){
	    $inStr = "( ";
	    foreach ($statusarray as $s){
		$inStr.= "'".$s."'";
		$inStr.=",";
	    }
	    $inStr = substr($inStr, 0, strlen($inStr)-1);
	    $inStr.= ")";
	    $sql.=" and dpers.dpersstatus IN $inStr";
	}
	else{
	    //pokud nemam zadne statusy nenajdu radeji nic
	    $sql.=" and ( dpers.dpersstatus='8515')";
	}
    }

    // pridani dalsiho filtru
    //oearray
    if (is_array($oearray)) {
	if (count($oearray) > 0) {
	//pokud mam jen jedet tag a to jen * nebudu podminku pridavat
	if (count($oearray) == 1 && $oearray[0] == '*') {
	    
	} else {
	    $inStr = "( ";
	    foreach ($oearray as $s) {
		$inStr.= "'" . $s . "'";
		$inStr.=",";
	    }
	    $inStr = substr($inStr, 0, strlen($inStr) - 1);
	    $inStr.= ")";
	    $sql.=" and ( doe.oe IN $inStr )";
	}
    } else {
	//pokud nemam zadne statusy nenajdu radeji nic
	//$sql.=" and ( dpers.dpersstatus='8515')";
    }
}
//    
//    if($oeselected!='*'){
//	$sql.=" and (doe.oe='$oeselected')";
//    }
    
    
    $sql.=" $order";
    $sql.=" $limit";

}

$ma = $a->getQueryRows($sql);

if($ma!==NULL){
    $persnrNew = $ma[0]['PersNr'];
    $oeInfo = $a->getPersOEInfo($persnrNew);
    $bewerber = $a->getQueryRows("select * from dpersbewerber where persnr='$persnrNew'");
}




$returnArray = array(
    'u' => $u,
    'ma' => $ma,
    'bewerber'=>$bewerber,
    'oeinfo'=>$oeInfo,
    'sql' => $sql,
);

echo json_encode($returnArray);
