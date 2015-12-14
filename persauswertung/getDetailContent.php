<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);


$a = AplDB::getInstance();
$title = "Detail not defined !";
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
    $title = "Detail Reklamationen $group/$groupDetail in $yearMonth";
    //vytahnu informace o externich reklamacich robrazim odkazy
    $von = "$year-$month-01";
    $bis = "$year-$month-$dayCount";
    $reklArray = $a->getReklamationenMitVerursacherVonBis($persnr,$von,$bis);
    $ie = substr($groupDetail, strrpos($groupDetail, '_')+1);
    if($reklArray!==NULL){
	foreach ($reklArray as $rekl){
	    $reklIE = substr($rekl['rekl_nr'],0,1);
	    if($ie=='I' && $reklIE=='I'){
		
		$content.="<a href='../Reports/S362_pdf.php?report=S362&reklnr=".$rekl['rekl_nr']."&reklnr_label=ReklNr&tl_tisk=pdf' target='_blank' class='btn btn-warning'>".$rekl['rekl_nr']."</a>";
		$content.="<span>&nbsp;Interne Bewertung: <strong>".$rekl['interne_bewertung']."</strong></span>";
		$content.="<div><h4>Teil: "."<a href='../dkopf/dkopf.php?teil=".$rekl['teil']."' target='_blank' >".$rekl['teil']."</a>"."</h4>";
		$content.="<p>".$rekl['beschr_abweichung']."</p>";
		$content.="<p>".$rekl['beschr_ursache']."</p>";
		$content.="<hr></div>";
	    }
	    if($ie=='E' && $reklIE=='E'){
		$content.="<a href='../Reports/S362_pdf.php?report=S362&reklnr=".$rekl['rekl_nr']."&reklnr_label=ReklNr&tl_tisk=pdf' target='_blank' class='btn btn-danger'>".$rekl['rekl_nr']."</a>";
		$content.="<span>&nbsp;Interne Bewertung: <strong>".$rekl['interne_bewertung']."</strong></span>";
		$content.="<div><h4>Teil: "."<a href='../dkopf/dkopf.php?teil=".$rekl['teil']."' target='_blank' >".$rekl['teil']."</a>"."</h4>";
		$content.="<p>".$rekl['beschr_abweichung']."</p>";
		$content.="<p>".$rekl['beschr_ursache']."</p>";
		$content.="<hr></div>";
	    }
	}
    }
    
}
else if (($group == 'A6') && (($groupDetail == 'a6_gew'))) {
    $title = "Detail Ausschuss $group/$groupDetail in $yearMonth";
    //vytahnu informace o externich reklamacich robrazim odkazy
    $von = "$year-$month-01";
    $bis = "$year-$month-$dayCount";
    $aussArray = $a->getAussArrayPersnrVonBis(6,$persnr,$von,$bis);
//    $content.="$aussArray";
    if($aussArray!==NULL){
	$content.="<table class='table table-condensed table-bordered table-striped'>";
	$content.="<tr>";
	$content.="<th>Datum</th>";
	$content.="<th>Auftragsnr</th>";
	$content.="<th>Teil</th>";
	$content.="<th>Teilgewicht</th>";
	$content.="<th>Pal</th>";
	$content.="<th>Abgnr</th>";
	$content.="<th>Auss-Stk</th>";
	$content.="</tr>";
	foreach ($aussArray as $auss){
		$content.="<tr>";
		$content.="<td>";
		    $content.="".date('d.m.Y',strtotime($auss['datum']))."";
		$content.="</td>";
		
		$content.="<td>";
		    $content.="<a href='../auftrag/auftrag.php#/det/".$auss['auftragsnr']."' target='_blank' >".$auss['auftragsnr']."</a>";
		$content.="</td>";
		
		$content.="<td>";
		    $content.="<a href='../dkopf/dkopf.php?teil=".$auss['teil']."' target='_blank' >".$auss['teil']."</a>";
		$content.="</td>";
		
		$content.="<td class='text-right'>";
		    $content.="".number_format($auss['gew'],2,',',' ')." kg";
		$content.="</td>";
		
		$content.="<td class='text-right'>";
		    $content.="".$auss['pal']."";
		$content.="</td>";
		
		$content.="<td class='text-right'>";
		    $content.="".$auss['abgnr']."";
		$content.="</td>";
		
		$content.="<td class='text-right'>";
		    $content.="".$auss['auss_stk']."";
		$content.="</td>";
		
		$content.="</tr>";
	}
	$content.="</table>";
    }
    
}
else if (($group == 'leistung') && (($groupDetail == 'vzaby_akkord')||($groupDetail == 'vzaby_zeit'))) {
    $title = "Detail Leistung $group/$groupDetail in $yearMonth";
    //vytahnu informace o externich reklamacich robrazim odkazy
    $von = "$year-$month-01";
    $bis = "$year-$month-$dayCount";
    $aussArray = $a->getLeistungArrayPersVonBis($persnr,$von,$bis);
//    $content.="$aussArray";
    if($aussArray!==NULL){
	$content.="<table class='table table-condensed table-bordered table-striped'>";
	$content.="<tr>";
	$content.="<th>Kunde</th>";
	$content.="<th>abgnr</th>";
	$content.="<th>Tātigkeit</th>";
	$content.="<th>vzaby</th>";
	$content.="</tr>";
	foreach ($aussArray as $auss) {
	    $vzaby = $groupDetail == 'vzaby_akkord' ? $auss['vzaby_akkord'] : $auss['leistfaktor']*($auss['sum_vzaby'] - $auss['vzaby_akkord']);
	    if ($vzaby != 0) {
		$content.="<tr>";
		$content.="<td class='text-right'>";
		$content.="" . $auss['kunde'] . "";
		$content.="</td>";
		$content.="<td class='text-right'>";
		$content.="" . $auss['abgnr'] . "";
		$content.="</td>";
		$content.="<td>";
		$content.="" . $auss['abgnr_name'] . "";
		$content.="</td>";
		$content.="<td class='text-right'>";
		$content.=number_format($vzaby, 0, ',', ' ');
		$content.="</td>";
		$content.="</tr>";
	    }
	}
	$content.="</table>";
    }
    
}else {
    $content.="<div>";
    $content.="<h4 class='alert alert-warning'>Detaily zatim definovany pouze pro:</h4>";
    $content.="<p>rekl / (sum_bewertung_E nebo sum_bewertung_I)</p>";
    $content.="<p>A6 / a6_gew</p>";
    $content.="<p>leistung / ( vzaby_akkord nebo vzaby_zeit )</p>";
    $content.="</div>";
//    $content.="<div>$eId</div>";
//    $content.="<div>$group</div>";
//    $content.="<div>$groupDetail</div>";
//    $content.="<div>$persnr</div>";
//    $content.="<div>$yearMonth</div>";
}


$returnArray = array(
    'title'=>$title,
    'user'=>$user,
    'content'=>$content,
);

echo json_encode($returnArray);
