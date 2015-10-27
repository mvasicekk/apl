<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $lid = $_POST['id'];
    $id = substr($lid, strpos($lid,'_')+1);
    $newLkw = $_POST['newLkw'];
    
    if($newLkw==1){
	// vytvorit novy lkw
	$datum = substr($lid, strrpos($lid, '_')+1);
	$id = $apl->makeNewRundlauf($datum);
    }

    $lkwInfoArray = $apl->getRundlaufInfoArray($id);
    $datum = $lkwInfoArray[0]['ab_datum_f'];
    
//    $imSollDatum = substr($kundeBoxId, strpos($kundeBoxId, '_')+1,10);
//    $kunde = substr($kundeBoxId, strrpos($kundeBoxId, '_')+1);
//    
    $div = "";
    if ($lkwInfoArray !== NULL) {
    $lkw = $lkwInfoArray[0];
    $div.= "<div class='editlkwdiv' id='editlkw_$id'>";
    $div.="<div style='float:right;' class='closebutton' id='closebutton_lkwedit_$id'>X</div>";
    $operace = $newLkw==1?'Neu':'Edit';
    
    $div.="<h4 style='margin:0;'>LKW - $operace</h4>";
    $div.="<div class='payloadList'>";
    $imexArray = $apl->getRundlaufImExArray($id);
    if($imexArray!==NULL){
	$anKundeOrtArray = array();
	foreach ($imexArray as $imex){
	    $payload = $imex['imex'].$imex['auftragsnr'];
	    $payloadId = $imex['id'];
	    $ie=$imex['imex'];
    	    if($ie=='E'){
		// v pripade exportu muzu podle nejnizsiho ex_soll navrhnout ab_aby_soll_datetime
		// take muzu navrhnout An Kunde Ort
		$aI = $apl->getAuftragInfoArray($imex['auftragsnr']);
		if($aI!==NULL){
		    $anKundeOrtArray[$aI[0]['zielort_id']]=$imex['auftragsnr'];
		}
	    }
	    $div.="<div id='payloadId_$payloadId' class='lkwPayLoad payLoad_$ie'>$payload</div>";
	}
	//an kunde ort div, pripravit
	$divAnKundeOrtId= "<select id='an_kunde_ort_id_$id'>";
	foreach ($anKundeOrtArray as $zielort_id=>$ex){
	    $zielortName = $apl->getZielortName($zielort_id);
	    $selected = $zielort_id==$lkwInfoArray[0]['an_kunde_ort_id']?'selected':'';
	    $divAnKundeOrtId.= "<option $selected value='".$zielort_id."'>$zielortName</option>";
	}
	$divAnKundeOrtId.= "</select>";
    }
    
    $div.="</div>";
    $div.="<table class='formtable'>";
    //abbfahrt Abydos
    $div.="<tr>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Ort";
    $div.="</td>";
    $div.="<td>";
    $div.="<input readonly type='text' id='ab_aby_ort_$id' maxlength='30' size='20' value='".$lkw['ab_aby_ort']."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Soll Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['ab_aby_soll_datetime']));
    $div.="<input class='datepicker' type='text' id='ab_aby_soll_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Soll Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['ab_aby_soll_datetime']));
    $div.="<input type='text' id='ab_aby_soll_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Ist Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['ab_aby_ist_datetime']));
    $div.="<input class='datepicker' type='text' id='ab_aby_ist_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Ist Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['ab_aby_ist_datetime']));
    $div.="<input type='text' id='ab_aby_ist_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Proforma";
    $div.="</td>";
    $div.="<td>";
    $dateValue = $lkw['proforma'];
    $div.="<input type='text' id='proforma_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Spediteur";
    $div.="</td>";
    $div.="<td>";
    $div.="<select id='spediteur_id_$id'>";
    $spedArray = $apl->getSpediteurArray();
    foreach ($spedArray as $sped){
	$selected = $sped['id']==$lkw['dspediteur_id']?'selected':'';
	$div.="<option $selected value='".$sped['id']."'>".$sped['name']."</option>";
    }
    $div.="</select>";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Fahrername";
    $div.="</td>";
    $div.="<td>";
    $dateValue = $lkw['fahrername'];
    $div.="<input type='text' id='fahrername_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "LKW-Nr.";
    $div.="</td>";
    $div.="<td>";
    $dateValue = $lkw['lkw_kz'];
    $div.="<input type='text' id='lkw_kz_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Anh-Nr.";
    $div.="</td>";
    $div.="<td>";
    $dateValue = $lkw['naves_kz'];
    $div.="<input type='text' id='naves_kz_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    // ankunft kunde
//    $div.="<tr>";
//    $div.="<td>";
//    $div.= "Ankunft Kunde - Ort";
//    $div.="</td>";
//    $div.="<td>";
//    $div.="<input type='text' id='an_kunde_ort_$id' maxlength='30' size='20' value='".$lkw['an_kunde_ort']."' />";
//    $div.="</td>";
//    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Kunde - Ort (ID)";
    $div.="</td>";
    $div.="<td id='an_kunde_ort_td_$id'>";
    
    $div.= $divAnKundeOrtId;
    
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Kunde - Soll Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_kunde_soll_datetime']));
    $div.="<input class='datepicker' type='text' id='an_kunde_soll_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Kunde - Soll Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['an_kunde_soll_datetime']));
    $div.="<input type='text' id='an_kunde_soll_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Kunde - Ist Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_kunde_ist_datetime']));
    $div.="<input class='datepicker' type='text' id='an_kunde_ist_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Ankunft Kunde - Ist Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['an_kunde_ist_datetime']));
    $div.="<input type='text' id='an_kunde_ist_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    
    // ankunft Abydos
    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Ort";
    $div.="</td>";
    $div.="<td>";
    $div.="<input readonly type='text' id='an_aby_ort_$id' maxlength='30' size='20' value='".$lkw['an_aby_ort']."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Soll Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_aby_soll_datetime']));
    $div.="<input class='datepicker' type='text' id='an_aby_soll_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Soll Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['an_aby_soll_datetime']));
    $div.="<input type='text' id='an_aby_soll_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Ist Tag";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_aby_ist_datetime']));
    $div.="<input class='datepicker' type='text' id='an_aby_ist_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Ist Zeit";
    $div.="</td>";
    $div.="<td>";
    $dateValue = date('H:i',strtotime($lkw['an_aby_ist_datetime']));
    $div.="<input type='text' id='an_aby_ist_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Nutzlast";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='an_aby_nutzlast_$id' maxlength='5' size='5' value='".$lkw['an_aby_nutzlast']."' />";
    $div.="</td>";
    $div.="</tr>";

    //sonst
    $div.="<tr>";
    $div.="<td>";
    $div.= "Bemerkung";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='bemerkung_$id' maxlength='30' size='20' value='".$lkw['bemerkung']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Preis vereinbart";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='preis_$id' maxlength='6' size='6' value='".$lkw['preis']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Rabatt [%]";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='rabatt_$id' maxlength='6' size='6' value='".$lkw['rabatt']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Betrag";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='betrag_$id' maxlength='6' size='6' value='".$lkw['betrag']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Rechnung";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='rechnung_$id' maxlength='8' size='8' value='".$lkw['rechnung']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="</table>";
    //odeslat pozadavek
    $div.= "<input style='margin-top:0.2em;' type='button' id='savelkwbutton_$id' acturl='lkwSave.php' value='speichern' />";
    $div.= "<input style='margin-top:0.2em;' type='button' id='deletelkwbutton_$id' acturl='lkwDelete.php' value='loeschen' />";
    $div.= "<input type='hidden' id='th' value='tagheader_$datum' />";
    $div.= "</div>";
    
}


$returnArray = array(
	'newLkw'=>$newLkw,
	'lid'=>$lid,
	'lkwInfoArray'=>$lkwInfoArray,
	'lkwId'=>$id,
	'div'=>$div,
	'divid'=>"editlkw_$id",
	'tagheaderid'=>'tagheader_'.$datum,
    );

    
    echo json_encode($returnArray);
?>

