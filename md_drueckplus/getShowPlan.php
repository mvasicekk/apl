<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$u = $a->get_user_pc();
$routeParams = $o->routeParams;
$datumDB = $routeParams->datum;
$view = $routeParams->view;

if(strtotime($routeParams->datum)){
    $datumDB = date('Y-m-d',strtotime($routeParams->datum));
    $datum = date('d.m.Y',strtotime($routeParams->datum));
}
else{
    $datumDB = date('Y-m-d');
    $datum = date('d.m.Y');
}

$dny = array(
    "Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota","Neděle"
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

$sql = " select dzeitsoll.id,dzeitsoll.persnr,dzeitsoll.oe,sum(dzeitsoll.stunden) as stunden,dpers.`name`,dpers.vorname,dpers.dpersstatus,dpers.eintritt";
$sql.= " ,min(edata_access_events.dt) as edata_min";
$sql.= " ,max(edata_access_events.dt) as edata_max";
$sql.= " from dzeitsoll";
$sql.= " left join edata_access_events on edata_access_events.persnr=dzeitsoll.persnr and DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d')=dzeitsoll.datum";
$sql.= " join dpers on dpers.persnr=dzeitsoll.persnr";
$sql.= " where";
$sql.= "     dzeitsoll.oe like '$oe'";
$sql.= "     and";
$sql.= "     dzeitsoll.datum='$datumDB'";
$sql.= "     and";
$sql.= "     dpers.dpersstatus='MA'";

$sql.= " group by dzeitsoll.persnr,dzeitsoll.oe";

$osoby = $a->getQueryRows($sql);

$osobypodleoe = array();

if($view=="matagplan"){
    // pro tento view si jeste pridam dalsi vhodne osoby, ktere by se daly pro vybrane oe a datum pridat do planu
    // TODO, mel bych vyhodit ty, kteri uz maji v dany datum stejne oe naplanovane => upravit SQL dotaz
	    //$sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3,regelarbzeit from dpers where (regeloe='$oe' or alteroe='$oe' or oe3='$oe') and dpersstatus='MA' order by persnr";
	    // jen podle regeloe
	    $sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3,regelarbzeit from dpers where (regeloe='$oe') and dpersstatus='MA' order by persnr";
	    $rs = $a->getQueryRows($sql);
	    if($rs!==NULL){
		foreach ($rs as $r){
		    array_push($osobypodleoe, $r);
		}
	    }
}

$returnArray = array(
    'u' => $u,
    'view'=>$view,
    'routeParams' => $routeParams,
    'datum'=>$datum,
    'oe'=>$oe,
    'oeOriginal'=>$oeOriginal,
    'sql'=>$sql,
    'osoby'=>$osoby,
    'osobypodleoe'=>$osobypodleoe,
    'jmenoDneE'=>$jmenoDneE,
    'jmenoDneCZ'=>$jmenoDneCZ,
);

echo json_encode($returnArray);
