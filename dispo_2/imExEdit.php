<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $id = $_POST['id'];

    $auftragsnr = substr($id, strpos($id, '_')+1);
    $imExKz = substr($id,0,  strpos($id, '_'));
    
    $auftragsnrInfo = $apl->getAuftragInfoArray($auftragsnr);
    $divid="";
    if ($auftragsnrInfo !== NULL) {
	
	$zielort_id = $auftragsnrInfo[0]['zielort_id'];
	$rechnungam_date = substr($auftragsnrInfo[0]['fertig_raw'],0,10);
	$rechnungamdateCZ = date('d.m.Y',  strtotime($rechnungam_date));		
	$ausgeliefertdate = substr($auftragsnrInfo[0]['ausliefer_raw'],0,10);
	$ausgelieferdateCZ = date('d.m.Y',  strtotime($ausgeliefertdate));
	if($ausgelieferdateCZ=="01.01.1970") $ausgelieferdateCZ="";
	$ausgelieferttime = substr($auftragsnrInfo[0]['ausliefer_raw'],11,5);
	
	$aufdat_date = substr($auftragsnrInfo[0]['aufdat_raw'],0,10);
	$aufdat_dateCZ = date('d.m.Y',  strtotime($aufdat_date));
	$aufdat_time = substr($auftragsnrInfo[0]['aufdat_raw'],11,5);
	
	$ex_soll_date = substr($auftragsnrInfo[0]['ex_soll_datetime'],0,10);
	$ex_soll_dateCZ = date('d.m.Y',  strtotime($ex_soll_date));
	if($ex_soll_dateCZ=="01.01.1970") $ex_soll_dateCZ="";
	$ex_soll_time = substr($auftragsnrInfo[0]['ex_soll_datetime'],11,5);
	
	$im_soll_date = substr($auftragsnrInfo[0]['im_soll_datetime'],0,10);
	$im_soll_dateCZ = date('d.m.Y',  strtotime($im_soll_date));
	$im_soll_time = substr($auftragsnrInfo[0]['im_soll_datetime'],11,5);
	
	$kunde = $auftragsnrInfo[0]['kunde'];
	$bestellnr = $auftragsnrInfo[0]['bestellnr'];
	$bemerkung = $auftragsnrInfo[0]['bemerkung'];
	$zielortStr = $apl->getZielortAuftrag($auftragsnr);
	
	if($imExKz=='im')
	    $datum = $aufdat_date;
	else
	    $datum = $ex_soll_date;
	
	$kundeBoxId = "tag_$datum"."_"."$kunde";
    $divid = "editdraggableimex_$kundeBoxId";
    $div = "";
    $div.= "<div class='newimportdiv' id='$divid'>";
    $div.="<div class='closebutton' id='closebutton_$kundeBoxId'>X</div>";
    $imexHeader = $imExKz=='im'?'Import':'Export';
    $div.="<h2>$auftragsnr $imexHeader - Edit</h2>";
    $div.="<table>";
	$div.="<tr>";
	
	    //auftragsnr
	    $div.="<td>";
	    $div.= "Auftragsnr";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input type='text' disabled='disabled' id='auftragsnr_$kundeBoxId' maxlength='7' size='7' value='$auftragsnr' />";
	    $div.="</td>";
	    
	    //bestellnr
	    $div.="<td>";
	    $div.= "Bestellnr";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input type='text' id='bestellnr_$kundeBoxId' maxlength='30' size='20' value='$bestellnr' />";
	    $div.="</td>";
	    
	    //bemerkung
	    $div.="<td>";
	    $div.= "Bemerkung";
	    $div.="</td>";
    
	    $div.="<td colspan='3'>";
	    $div.="<input type='text' id='bemerkung_$kundeBoxId' maxlength='255' size='30' value='$bemerkung' />";
	    $div.="</td>";
	$div.="</tr>";
	
	$div.="<tr>";
	    //im soll
	    $div.="<td>";
	    $div.= "Im SOLL";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input class='datepicker' type='text' id='imsolldate_$kundeBoxId' maxlength='10' size='10' value='$im_soll_dateCZ' />";
	    $div.="</td>";

	    //time
	    $div.="<td>";
	    $div.="<input type='text' id='imsolltime_$kundeBoxId' maxlength='5' size='5' value='$im_soll_time' />";
	    $div.="</td>";
	    
	    //ex soll
	    $div.="<td>";
	    $div.= "Ex SOLL";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input class='datepicker' type='text' id='exsolldate_$kundeBoxId' maxlength='10' size='10' value='$ex_soll_dateCZ' />";
	    $div.="</td>";

	    //time
	    $div.="<td>";
	    $div.="<input type='text' id='exsolltime_$kundeBoxId' maxlength='5' size='5' value='$ex_soll_time' />";
	    $div.="</td>";

	    //zielort
	    $div.="<td>";
	    $div.= "Zielort";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input type='text' id='zielort_$kundeBoxId' maxlength='255' size='20' value='$zielortStr' />";
	    $div.="<input type='hidden' id='ziel_value' value='$zielort_id' />";
	    $div.="</td>";
	$div.="</tr>";
	
	$div.="<tr>";
	    //auftragseingang
	    $div.="<td>";
	    $div.= "Auftragseingang";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input class='datepicker' type='text' id='aufdatdate_$kundeBoxId' maxlength='10' size='10' value='$aufdat_dateCZ' />";
	    $div.="</td>";

	    //time
	    $div.="<td>";
	    $div.="<input type='text' disabled='disabled' id='aufdattime_$kundeBoxId' maxlength='5' size='5' value='$aufdat_time' />";
	    $div.="</td>";
	    
	    //ausgeliefert am
	    $div.="<td>";
	    $div.= "ausgeliefert am";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input class='datepicker' type='text' id='ausgeliefertamdate_$kundeBoxId' maxlength='10' size='10' value='$ausgelieferdateCZ' />";
	    $div.="</td>";

	    //time
	    $div.="<td>";
	    $div.="<input disabled='disabled' type='text' id='ausgeliefertamtime_$kundeBoxId' maxlength='5' size='5' value='$ausgelieferttime' />";
	    $div.="</td>";

	    //rechnung am
	    $div.="<td>";
	    $div.= "Rechnung am";
	    $div.="</td>";
    
	    $div.="<td>";
	    $div.="<input disabled='disabled' type='text' id='rechnungam_$kundeBoxId' maxlength='10' size='10' value='$rechnungamdateCZ' />";
	    $div.="</td>";
	$div.="</tr>";
    $div.="</table>";

    //odeslat edit
    $div.= "<input type='button' id='editimexbutton_$kundeBoxId' acturl='imExEditSave.php' value='speichern' />";
    $div.= "</div>";
}

    
    $returnArray = array(
	'id'=>$id,
	'auftragsnr'=>$auftragsnr,
	'imExKz'=>$imExKz,
	'auftragsnrInfo'=>$auftragsnrInfo,
	'kundeBoxId'=>$kundeBoxId,
	'imSollDatum'=>$imSollDatum,
	'kunde'=>$kunde,
	'div'=>$div,
	'divid'=>$divid,
	'focusTo'=>$imExKz=='im'?'imsolltime_'.$kundeBoxId:'exsolltime_'.$kundeBoxId,
    );

    
    echo json_encode($returnArray);
?>

