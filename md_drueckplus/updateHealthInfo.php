<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();

$field = $o->field;
$healthInfo = $o->healthInfo;
$persnr = $healthInfo->persinfo->PersNr;
$ht = $healthInfo->healthtable;
$htId = intval($ht->id);
$ar = 0;

if($htId>0){
    if($field == 'vyska' ){
	$value = floatval(strtr($ht->vyska, ',','.'));
    }
    if($field == 'vaha1' || $field == 'vaha2' || $field == 'vaha3'){
	$value = floatval(strtr($ht->{$field}, ',','.'));
    }
    if($field == 'datum1' || $field == 'datum2' || $field == 'datum3'){
	$value = strtotime($ht->{$field});
	if($value===FALSE){
	    $value = date('Y_m-d');
	}
	else{
	    $value = date('Y-m-d',$value);
	}
    }
    if($field == 'zdrstav_pozn' || $field == 'health_pozn'){
	$value = trim($ht->{$field});
    }
    
    
    
    if($field == 'chb_hlava'||$field == 'chb_krk'||$field == 'chb_ramena'||$field == 'chb_zada'||$field == 'chb_loket'||$field == 'chb_karpaly'||$field == 'chb_kycle'||$field == 'chb_koleno'||$field == 'chb_lytka'||$field == 'chb_chodidla'){
	$value = intval($ht->{$field});
    }
    
    $updateSql = "update dpershealth set `$field`='$value' where id='$htId' limit 1";
    $ar = $a->query($updateSql);
}



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


$returnArray = array(
    'ar'=>$ar,
    'updateSql'=>$updateSql,
    'healthInfo'=>$healthInfo,
    'u' => $u,
    'sql' => $sql,
    'persnr'=>$persnr,
    'persinfoSql'=>$persInfoSql,
);

echo json_encode($returnArray);
