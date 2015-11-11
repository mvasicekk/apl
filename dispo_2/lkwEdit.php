<?
require_once '../db.php';
require_once './commons.php';

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
    
    $sectionA = getLkwFormDivs($id);
//    $exCount = $sectionA['exCount'];
    $divAnKundeZielorte = $sectionA['divAnKundeZielorte'];
//    $ab_aby_soll_dateVorschlag = $sectionA['ab_aby_soll_dateVorschlag'];
//    $ab_aby_soll_timeVorschlag = $sectionA['ab_aby_soll_timeVorschlag'];
    $payloadDiv = $sectionA['payloadDiv'];
//    $imexArrayToUpdate = $sectionA['imexArrayToUpdate'];
    $imexArray = $sectionA['imexArray'];

    $div = "";
    if ($lkwInfoArray !== NULL) {
    $lkw = $lkwInfoArray[0];
    $div.= "<div class='editlkwdiv' id='editlkw_$id'>";
    $div.="<div style='float:right;' class='closebutton' id='closebutton_lkwedit_$id'>X</div>";
    $operace = $newLkw==1?'Neu':'Edit';
    
    $div.="<h4 style='margin:0;'>LKW - $operace</h4>";
    $div.="<div class='payloadList'>";
    
    $div.=$payloadDiv;
    
    $div.="</div>";
    $div.="<table class='formtable'>";
    //abbfahrt Abydos
    $div.="<tbody id='ab_aby_div_$id'>";
    $div.="<tr class='header'>";
	$div.="<td>Abfahrt Abydos</td>";
	$div.="<td>Soll Tag/Zeit</td>";
	$div.="<td>Ist Tag/Zeit</td>";
    $div.="</tr>";
    
    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Abfahrt Abydos - Ort";
    $div.="</td>";
    //soll
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['ab_aby_soll_datetime']));
    $div.="<input disabled='disabled' class='datepicker' type='text' id='ab_aby_soll_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="/";
    $dateValue = date('H:i',strtotime($lkw['ab_aby_soll_datetime']));
    $div.="<input disabled='disabled' type='text' id='ab_aby_soll_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    
    //ist
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['ab_aby_ist_datetime']));
    $div.="<input disabled='disabled' class='datepicker' type='text' id='ab_aby_ist_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="/";
    $dateValue = date('H:i',strtotime($lkw['ab_aby_ist_datetime']));
    $div.="<input disabled='disabled' type='text' id='ab_aby_ist_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";

    $div.="</tr>";
    
    $div.="</tbody>";
    //--------------------------------------------------------------------------
    //spediteur
    $div.="<tbody id='spediteur_div_$id'>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "Proforma";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $dateValue = $lkw['proforma'];
    $div.="<input type='text' id='proforma_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Spediteur";
    $div.="</td>";
    $div.="<td colspan='2'>";
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
    $div.="<td colspan='2'>";
    $dateValue = $lkw['fahrername'];
    $div.="<input type='text' id='fahrername_$id' maxlength='32' style='width:100%;text-align:left;' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "LKW-Nr.";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $dateValue = $lkw['lkw_kz'];
    $div.="<input type='text' id='lkw_kz_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    
    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Anh-Nr.";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $dateValue = $lkw['naves_kz'];
    $div.="<input type='text' id='naves_kz_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="</td>";
    $div.="</tr>";
    $div.="</tbody>";

    // ankunft kunde -----------------------------------------------------------
    $div.="<tbody id='an_kunde_div_$id'>";
    $div.=$divAnKundeZielorte;
    $div.="</tbody>";
    //--------------------------------------------------------------------------
    
    // ankunft Abydos
    $div.="<tbody id='an_aby_div_$id'>";
        $div.="<tr  class='startsection header'>";
	$div.="<td>Ankunft Abydos</td>";
	$div.="<td>Soll Tag/Zeit</td>";
	$div.="<td>Ist Tag/Zeit</td>";
    $div.="</tr>";
    
    $div.="<tr>";
    $div.="<td>";
    $div.= "Akunft Abydos - Ort";
    $div.="</td>";
    //soll
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_aby_soll_datetime']));
    $div.="<input disabled='disabled' class='datepicker' type='text' id='an_aby_soll_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="/";
    $dateValue = date('H:i',strtotime($lkw['an_aby_soll_datetime']));
    $div.="<input disabled='disabled' type='text' id='an_aby_soll_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";
    
    //ist
    $div.="<td>";
    $dateValue = date('d.m.Y',strtotime($lkw['an_aby_ist_datetime']));
    $div.="<input disabled='disabled' class='datepicker' type='text' id='an_aby_ist_date_$id' maxlength='10' size='10' value='".$dateValue."' />";
    $div.="/";
    $dateValue = date('H:i',strtotime($lkw['an_aby_ist_datetime']));
    $div.="<input disabled='disabled' type='text' id='an_aby_ist_time_$id' maxlength='5' size='5' value='".$dateValue."' />";
    $div.="</td>";

    $div.="</tr>";
//------------------------------------------------------------------------------
    $div.="<tr class='endsection'>";
    $div.="<td>";
    $div.= "Ankunft Abydos - Nutzlast";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $div.="<input type='text' id='an_aby_nutzlast_$id' maxlength='5' size='6' style='text-align:right;' value='".$lkw['an_aby_nutzlast']."' />";
    $div.="</td>";
    $div.="</tr>";
    $div.="</tbody>";
    
    
    //sonst --------------------------------------------------------------------
    $div.="<tbody id='sonst_div_$id'>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "Bemerkung";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $div.="<input type='text' id='bemerkung_$id' maxlength='30' style='width:100%;' value='".$lkw['bemerkung']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Preis vereinbart";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $div.="<input type='text' id='preis_$id' maxlength='6' size='6' style='text-align:right;' value='".$lkw['preis']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="<tr>";
    $div.="<td>";
    $div.= "Rabatt [%]";
    $div.="</td>";
    $div.="<td colspan='2'>";
    $div.="<input type='text' id='rabatt_$id' maxlength='6' size='6' style='text-align:right;' value='".$lkw['rabatt']."' />";
    $div.="</td>";
    $div.="</tr>";

    $div.="</tbody>";
    $div.="</table>";
    //odeslat pozadavek
    $div.= "<input style='margin-top:0.2em;' type='button' id='savelkwbutton_$id' acturl='lkwSave.php' value='speichern' />";
    $div.= "<input style='margin-top:0.2em;' type='button' id='deletelkwbutton_$id' acturl='lkwDelete.php' value='loeschen' />";
    $div.= "<input type='hidden' id='th' value='tagheader_$datum' />";
    $div.= "</div>";
    
}


$returnArray = array(
	'sectionA'=>$sectionA,
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

