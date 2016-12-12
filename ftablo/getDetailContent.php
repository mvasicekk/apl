<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);


$a = AplDB::getInstance();
$title = "Detail nedefinovan !";
$user = $_SESSION['user'];
$userpc = $a->get_user_pc();
$eId = $o->eId;

// do prvniho podtrzitka identifikator detailu
$detailId = substr($eId,  0, strpos($eId, '_'));
// persnr od prvniho do druheho podtrzitka
$persnrLength = strrpos($eId, '_')-strpos($eId, '_');
$persnr = substr($eId,  strpos($eId, '_')+1, $persnrLength-1);
$yearMonth = substr($eId,  strrpos($eId, '_')+1);
$bSumColumn = $yearMonth=='sum'?TRUE:FALSE;
$rolesArray = $a->getUserRolesArray($user);

$year = intval(substr($yearMonth, 0, 4));
$month = intval(substr($yearMonth, 5));
$dayCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$von = "$year-$month-01";
$bis = "$year-$month-$dayCount";

if ($detailId == "repkosten") {
    $title = "Detail repkosten $persnr v $yearMonth";
    //vytahnu informace o externich reklamacich robrazim odkazy
    $repIdAlt = 0;
    $repArray = $a->getReparaturenProPersnr($persnr,$von,$bis);
    if($repArray!==NULL){
	$content.="<table class='table table-condensed table-bordered table-striped'>";
	$content.="<tr>";
	$content.="<th class='text-right'>invnummer</th>";
	$content.="<th>typ</th>";
	$content.="<th class='text-right'>opravoval</th>";
	$content.="<th>datum</th>";
	$content.="<th>poznamka</th>";
	$content.="<th class='text-right'>cas. naklady</th>";
	$content.="<th>dil</th>";
	$content.="<th class='text-right'>pocet</th>";
	$content.="<th class='text-right'>cena</th>";
	$content.="</tr>";
	foreach ($repArray as $rep){
		$repId = $rep['id'];
		if($repId!=$repIdAlt){
		    $repIdAlt = $repId;
		    //zahlavi opravy
		    $content.="<tbody class='reparaturkopf'>";
		    $content.="<tr>";
		
		    $content.="<td class='text-right'>";
		    $content.="".$rep['invnummer']."";
		    $content.="</td>";
		
		    $content.="<td style='white-space:nowrap;'>";
		    $content.="".$rep['anlage_beschreibung']."";
		    $content.="</td>";
		
		    $content.="<td class='text-right'>";
		    $content.="".$rep['persnr_reparatur']."";
		    $content.="</td>";
		
		    $content.="<td>";
		    $content.="".date('d.m.Y',strtotime($rep['datum']))."";
		    $content.="</td>";
		
		    $content.="<td>";
		    $content.="".$rep['bemerkung']."";
		    $content.="</td>";
		
		    $content.="<td class='text-right' style='white-space:nowrap;'>";
		    $content.="".number_format($rep['rep_kosten'],0,',',' ')."";
		    $content.="</td>";
		
		    $content.="<td colspan='3'>";
		    $content.="</td>";
		    $content.="</tr>";
		    $content.="</tbody>";
		}
		if(strlen(trim($rep['artnr']))>0){
		    $content.="<tr>";
		
		$content.="<td class='text-right'>";
		//$content.="".$rep['invnummer']."";
		$content.="</td>";
		
		$content.="<td>";
		//$content.="".$rep['anlage_beschreibung']."";
		$content.="</td>";
		
		$content.="<td class='text-right'>";
		//$content.="".$rep['persnr_reparatur']."";
		$content.="</td>";
		
		
		$content.="<td>";
		//$content.="".date('d.m.Y',strtotime($rep['datum']))."";
		$content.="</td>";
		
		$content.="<td>";
		//$content.="".$rep['bemerkung']."";
		$content.="</td>";
		
		$content.="<td class='text-right' style='white-space:nowrap;'>";
		//$content.="".number_format($rep['rep_kosten'],0,',',' ')."";
		$content.="</td>";
		
		$content.="<td>";
		$content.="".$rep['artnr']." - ".$rep['artname'];
		$content.="</td>";
		
		$content.="<td class='text-right' style='white-space:nowrap;'>";
		$content.="".$rep['anzahl']."";
		$content.="</td>";
		
		$content.="<td class='text-right' style='white-space:nowrap;'>";
		$content.="".number_format($rep['rep_preis'],0,',',' ')."";
		$content.="</td>";
		$content.="</tr>";
		}
		
	}
	$content.="</table>";
    }
    
}
else {
    $content.="<div>";
    $content.="<h4 class='alert alert-warning'>Detaily zatim definovany pouze pro:</h4>";
    $content.="<p>repkosten</p>";
    $content.="</div>";
}
    


$returnArray = array(
    'title'=>$title,
    'user'=>$user,
    'persnr'=>$persnr,
    'detailId'=>$detailId,
    'content'=>$content,
    'yearMonth'=>$yearMonth,
    'bSumColumn'=>$bSumColumn
);

echo json_encode($returnArray);
