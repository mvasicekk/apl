<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$persinfo = NULL;
$suchen = strtolower(trim($o->osoba));
$suchen = strtr($suchen, '*', '%');
$jenma = $o->jenma;
$austritt60 = $o->austritt60;
$oeselected = $o->oeselected;
$statusarray = $o->statusarray;
$oearray = $o->oearray;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

if (count($oearray) == 1 && $oearray[0] == '*') {
    
}
else{
    $join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
    $join.=" join doe on doe.oe=dtattypen.oe";
}

/*
if ($oeselected != '*') {
    $join.=" join dtattypen on dtattypen.tat=dpers.regeloe";
    $join.=" join doe on doe.oe=dtattypen.oe";
}
*/
$sql = "select  if(dpers.geboren is not null,DATE_FORMAT(dpers.geboren,'%d.%m.%Y'),'') as geboren,DATE_FORMAT(dpers.austritt,'%d.%m.%Y') as austritt,DATE_FORMAT(dpers.eintritt,'%d.%m.%Y') as eintritt,dpers.persnr,`name`,vorname,regeloe,dpersstatus from dpers";
$sql.= " $join";
$sql.=" where (";
$sql.=" ((`PersNr` like'" . $suchen . "%') or (LOWER(`name`) like '%" . $suchen . "%')  or (LOWER(`Vorname`) like '%" . $suchen . "%'))";


//if ($jenma === TRUE) {
//    $sql.=" and (dpersstatus='MA')";
//}
/*
if ($jenma==TRUE) {
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
	    $sql.=" and ( dpers.dpersstatus IN $inStr )";
	}
	else{
	    //pokud nemam zadne statusy nenajdu radeji nic
	    $sql.=" and ( dpers.dpersstatus='8515')";
	}
    }

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
//if ($oeselected != '*') {
//    $sql.=" and (doe.oe='$oeselected')";
//}


$sql.=" )";
$sql.=" order by persnr";
if (strlen($suchen) >= 1) {
    $osoby = $a->getQueryRows($sql);
}


$returnArray = array(
    'u' => $u,
    'osoby' => $osoby,
    'suchen' => $suchen,
    'sql' => $sql,
    'jenma' => $jenma,
);

echo json_encode($returnArray);
