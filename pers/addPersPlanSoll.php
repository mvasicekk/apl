<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$p = $o->p;
$persnr = $p->persnr;
$month = $p->month;
$year = $p->year;
$day = $p->day;

$u = $a->get_user_pc();
$datum = sprintf("%04d-%02d-%02d",$year,$month,$day);
$svatkyArray = $a->getSvatkyArray($year, $month);

$regeloe = $a->getRegelOE($persnr);
if($regeloe==null){
    $regeloe = '-';
} 
$alteroe = $a->getAlternativOE($persnr);
if($alteroe==null){
    $alteroe = $regeloe;
} 

$regelarbzeit = floatval($a->getRegelarbzeit($persnr));
// vychytavka, podivam se, co je pro dany den naplanovano a stunden spocitam jako rozdim mezi $regelarbzeit a sumou jiz naplanovaneho
$stundenGeplant = $a->getStundenPlanTagPersnr($persnr,$datum);
$regelarbzeit -= $stundenGeplant;

$cislodne = date('w',mktime(0, 1, 1, $month, $day, $year));
$svatek = array_search($day, $svatkyArray);
// zjistim cislo tydne, rozlisim sudy a lichy tyden
// v lichem tydnu pouziju regeloe v sudem pouziju alteroe
$cislotydne = date('W',mktime(0, 1, 1, $month, $day, $year));
$lichyTyden = $cislotydne%2==0?FALSE:TRUE;

if ($cislodne == 0 || $cislodne == 6 || $svatek !== FALSE) {
    $insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','-','0','$datum','$u')";
} else {
    if ($lichyTyden == TRUE){
	$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$regeloe','$regelarbzeit','$datum','$u')";
    }
    else{
	$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$alteroe','$regelarbzeit','$datum','$u')";
    }
}

//$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','-','0','$datum','$u')";

$ar = $a->insert($insertSql);



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
    'insertSql'=>$insertSql,
    'p'=>$p,
    'persnr' => $persnr,
    'month' => $month,
    'year'=>$year,
    'planObj'=>$planObj,
    'svatkyArray'=>$svatkyArray,
);

echo json_encode($returnArray);
