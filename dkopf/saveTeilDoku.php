<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$p = $o->d;
$update = $o->update;


$a = AplDB::getInstance();


// sanace
$einlag_datum = strtotime($p->einlag_datum)>0?date('Y-m-d',strtotime($p->einlag_datum)):NULL;
$freigabe_am = strtotime($p->freigabe_am)>0?date('Y-m-d',strtotime($p->freigabe_am)):NULL;
$doku_nr = trim($p->doku_nr);
$teil = trim($p->teil);
$freigabe_vom = trim($p->freigabe_vom);
$muster_platz = trim($p->musterplatz);

if($einlag_datum!==NULL){
    if($update=="update"){
	//update stavajiciho
	$id = $p->id;
	$sqlUpdate = "update dteildokument set";
	$sqlUpdate.=" musterplatz='$muster_platz',";
	$sqlUpdate.=" einlag_datum='$einlag_datum',";
	$sqlUpdate.=" doku_nr='$doku_nr',";
	$sqlUpdate.=" freigabe_am='$freigabe_am',";
	$sqlUpdate.=" freigabe_vom='$freigabe_vom',";
	$sqlUpdate.=" teil='$teil'";
	$sqlUpdate.=" where id='$id' limit 1";
	$ar = $a->query($sqlUpdate);
    }
    else{
	//vlozit novy
	$sqlInsert = "insert into dteildokument (musterplatz,einlag_datum,doku_nr,freigabe_am,freigabe_vom,teil)";
	$freigabe_am_value = $freigabe_am===NULL?'NULL':"'$freigabe_am'";
	$sqlInsert.=" values('$muster_platz','$einlag_datum','$doku_nr',".$freigabe_am_value.",'$freigabe_vom','$teil')";
	$insertId = $a->insert($sqlInsert);
    }
}
$returnArray = array(
    'ar'=>$ar,
    'sqlUpdate'=>$sqlUpdate,
    'id'=>$id,
    'insertId'=>$insertId,
    'sqlInsert'=>$sqlInsert,
    'einlag_datum'=>$einlag_datum,
    'freigabe_am'=>$freigabe_am,
    'p'=>$p,
    );
    
echo json_encode($returnArray);
