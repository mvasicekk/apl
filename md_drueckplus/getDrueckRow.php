<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$sys = intval($o->sys);
$rows = NULL;
$rowsPlus = NULL;

if($sys>0){
    $a = AplDB::getInstance();
    $sql.= " select `dtaetkz-abg`.oper_CZ,dstator_pal.typ,drueck_id,dpers.persnr,CONCAT(dpers.Vorname,' ',dpers.`Name`) as jmeno,Datum,TaetNr,`Stück` as stk ";
    $sql.= " from drueck ";
    $sql.= " join dpers on dpers.PersNr=drueck.PersNr";
    $sql.= " join dstator_pal on dstator_pal.paleta=drueck.`pos-pal-nr`";
    $sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
    $sql.= " where drueck_id='$sys'";

    $rows = $a->getQueryRows($sql);
    $ident = $a->get_user_pc();
    // pokud mam drueck_id, zjistim, zda mam nejeka info k tomuto id v drueckplus
    $sql = "select * from drueckplus where drueck_id='$sys' order by et_invnummer";
    $rowsPlus = $a->getQueryRows($sql);
}


$returnArray = array(
	'rows'=>$rows,
	'rowsPlus'=>$rowsPlus,
	'sys'=>$sys,
	'ident'=>$ident,
    );
    
echo json_encode($returnArray);
