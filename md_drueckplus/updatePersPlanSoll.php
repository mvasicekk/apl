<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$p = $o->p;
$field = $o->field;

$id = $p->id;
$value = trim($p->{$field});

// sanace :-)

if($field=='stunden'){
    $value = floatval(strtr($value,',','.'));
}

if($field=='__delete'){
    $updateSql = "delete from dzeitsoll where id='$id' limit 1";
}
else{
    $updateSql = "update dzeitsoll set `$field`='$value' where id='$id' limit 1";
}

$ar = $a->query($updateSql);

$persnr = $p->persnr;
$month = $p->month;
$year = $p->year;


$sql = " select dzeitsoll.id,persnr,year(datum) as `year`,month(datum) as `month`,day(datum) as `day`,oe,stunden";
$sql.= " from dzeitsoll";
$sql.= " where";
$sql.= "     persnr='$persnr'";
$sql.= "     and";
$sql.= "     month(datum)='$month'";
$sql.= "     and";
$sql.= "     year(datum)='$year'";
$sql.= " order by datum,id";

$planObj = array();
$planObj[$persnr][$year][$month] = array();

$rs = $a->getQueryRows($sql);
if($rs!==NULL){
    foreach ($rs as $r)    {
	if(!is_array($planObj[$r['persnr']][$r['year']][$r['month']][$r['day']])){
	    $planObj[$r['persnr']][$r['year']][$r['month']][$r['day']] = array();
	}
	array_push($planObj[$r['persnr']][$r['year']][$r['month']][$r['day']],$r);
    }
}
else{
    $planObj[$persnr][$year][$month] = NULL;
}


$returnArray = array(
    'u' => $u,
    'ar'=>$ar,
    'value'=>$value,
    'updateSql'=>$updateSql,
    'p'=>$p,
    'field'=>$field,
    'persnr' => $persnr,
    'month' => $month,
    'year'=>$year,
    'planObj'=>$planObj,
);

echo json_encode($returnArray);
