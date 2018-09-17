<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$u = $a->get_user_pc();
$routeParams = $o->routeParams;
$datumDB = $routeParams->datum;

if(strtotime($routeParams->datum)){
    $datumDB = date('Y-m-d',strtotime($routeParams->datum));
    $datum = date('d.m.Y',strtotime($routeParams->datum));
}
else{
    $datumDB = date('Y-m-d');
    $datum = date('d.m.Y');
}

$dny = array(
    "Neděle","twl, zase Prndělí","Úterý","Středa, je ho tam ..","Čtvrtek Bedřichu","předfikend Pátek","fikend So","fikend Ne"
);

$jmenoDneE = date('l', strtotime($datumDB));
$weekDayIndex = date('w', strtotime($datumDB));
$jmenoDneCZ = $dny[$weekDayIndex];

if(strlen($routeParams->oe)>0){
    $oeOriginal = $routeParams->oe;
    $oe = strtr($routeParams->oe, '*', '%');
    //$oe = $routeParams->oe;
}
else{
    $oe = "GF";
    $oeOriginal = $oe;
}

$oe= strtoupper($oe);

$sql = " select dzeitsoll.id,dzeitsoll.persnr,dzeitsoll.oe,dzeitsoll.stunden,dpers.`name`,dpers.vorname,dpers.dpersstatus,dpers.eintritt";
$sql.= " ,min(edata_access_events.dt) as edata_min";
$sql.= " from dzeitsoll";
$sql.= " left join edata_access_events on edata_access_events.persnr=dzeitsoll.persnr and DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d')=dzeitsoll.datum";
$sql.= " join dpers on dpers.persnr=dzeitsoll.persnr";
$sql.= " where";
$sql.= "     dzeitsoll.oe like '%$oe%'";
$sql.= "     and";
$sql.= "     dzeitsoll.datum='$datumDB'";
$sql.= "     and";
$sql.= "     dpers.dpersstatus='MA'";

$sql.= " group by dzeitsoll.persnr,dzeitsoll.oe";

$osoby = $a->getQueryRows($sql);

$returnArray = array(
    'u' => $u,
    'routeParams' => $routeParams,
    'datum'=>$datum,
    'oe'=>$oe,
    'oeOriginal'=>$oeOriginal,
    'sql'=>$sql,
    'osoby'=>$osoby,
    'jmenoDneE'=>$jmenoDneE,
    'jmenoDneCZ'=>$jmenoDneCZ,
);

echo json_encode($returnArray);
