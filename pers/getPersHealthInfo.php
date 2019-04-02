<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$persInfoSql = "select if(dpers.geboren is not null,YEAR(CURDATE())-YEAR(dpers.geboren)-IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(dpers.geboren), '-', DAY(dpers.geboren)) ,'%Y-%c-%e') > CURDATE(), 1, 0),0) AS vek,";
$persInfoSql.= " dpers.*,dtattypen.tatBezeichnung from dpers left join dtattypen on dtattypen.tat=dpers.regeloe where persnr='$persnr'";
$persInfo = $a->getQueryRows($persInfoSql);

if($persInfo!==NULL){
    $healthInfo = array(
	"persinfo"=>$persInfo[0],
	);
	// tabulka health + eventuelne pridat zaznam pokud neexistuje
	$sql = "select * from dpershealth where persnr='$persnr'";
	$ht = $a->getQueryRows($sql);
	if($ht===NULL){
	    // nemam, tak vytvorim radek
	    $a->insert("insert into dpershealth (persnr) values('$persnr')");
	}
	
	$ht = $a->getQueryRows($sql);
	$htr = $ht[0];
	if($htr["datum1"]==NULL || $htr["datum1"]=="0000-00-00"){
	    $htr["datum1"] = date('Y-m-d H:i:s');
	}
	if($htr["datum2"]==NULL || $htr["datum2"]=="0000-00-00"){
	    $htr["datum2"] = date('Y-m-d H:i:s');
	}
	if($htr["datum3"]==NULL || $htr["datum3"]=="0000-00-00"){
	    $htr["datum3"] = date('Y-m-d H:i:s');
	}
	$healthInfo["healthtable"] = $htr;
}





// vek MA, pokud je vyplneno geboren
//YEAR(CURDATE()) -
//YEAR(birthdate) -
//IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birthdate), '-', DAY(birthdate)) ,'%Y-%c-%e') > CURDATE(), 1, 0)
//AS age

$returnArray = array(
    'healthInfo'=>$healthInfo,
    'u' => $u,
    'sql' => $sql,
    'persnr'=>$persnr,
    'persinfoSql'=>$persInfoSql,
);

echo json_encode($returnArray);
