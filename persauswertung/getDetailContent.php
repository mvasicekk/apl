<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);


$a = AplDB::getInstance();

$user = $_SESSION['user'];
$userpc = $a->get_user_pc();
$eId = $o->eId;
$group = $o->r->group;
$groupDetail = $o->r->groupDetail;
$persnr = $o->r->persnr;
$yearMonth = substr($eId,  strrpos($eId, '_')+1);
$year = 2000 + intval(substr($yearMonth, 0, 2));
$month = intval(substr($yearMonth, 3));
$dayCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$rolesArray = $a->getUserRolesArray($user);

if (($group == 'rekl') && (($groupDetail == 'sum_bewertung_E')||$groupDetail == 'sum_bewertung_I')) {
    //vytahnu informace o externich reklamacich robrazim odkazy
    $von = "$year-$month-01";
    $bis = "$year-$month-$dayCount";
    $reklArray = $a->getReklamationenMitVerursacherVonBis($persnr,$von,$bis);
    $ie = substr($groupDetail, strrpos($groupDetail, '_')+1);
    if($reklArray!==NULL){
	foreach ($reklArray as $rekl){
	    $reklIE = substr($rekl['rekl_nr'],0,1);
	    if($ie=='I' && $reklIE=='I'){
		$content.="<a href='../reklamation/reklamation.php#/detail/".$rekl['id']."' target='_blank' class='btn btn-primary'>".$rekl['rekl_nr']."</a>";
		$content.="<div><h4>Teil: ".$rekl['teil']."</h4><p>".$rekl['beschr_abweichung']."</p></div>";
	    }
	    if($ie=='E' && $reklIE=='E'){
		$content.="<a href='../reklamation/reklamation.php#/detail/".$rekl['id']."' target='_blank' class='btn btn-primary'>".$rekl['rekl_nr']."</a>";
		$content.="<div><h4>Teil: ".$rekl['teil']."</h4><p>".$rekl['beschr_abweichung']."</p></div>";
	    }
	}
    }
    
} else {
    $content.="<div>$eId</div>";
    $content.="<div>$group</div>";
    $content.="<div>$groupDetail</div>";
    $content.="<div>$persnr</div>";
    $content.="<div>$yearMonth</div>";
}


$returnArray = array(
    'user'=>$user,
    'content'=>$content,
);

echo json_encode($returnArray);
