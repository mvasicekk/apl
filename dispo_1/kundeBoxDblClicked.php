<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $kundeBoxId = $_POST['kundeBoxId'];

    $imSollDatum = substr($kundeBoxId, strpos($kundeBoxId, '_')+1,10);
    $kunde = substr($kundeBoxId, strrpos($kundeBoxId, '_')+1);
    
    $div = "";
    $div.= "<div class='newimportdiv' id='newimp_$kundeBoxId'>";
    $div.="<div class='closebutton' id='closebutton_$kundeBoxId'>X</div>";
    $div.="<h4>Import - Neu</h4>";
    $div.="<table>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "kunde:";
    $div.="</td>";
    $div.="<td>";
    $div.= "$kunde";
    $div.="</td>";
    $div.="</tr>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "IM Soll Datum:";
    $div.="</td>";
    $div.="<td>";
    $div.="$imSollDatum";
    $div.="</td>";
    $div.="</tr>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "IM Soll Zeit:";
    $div.="</td>";
    $div.="<td>";
    $importSollTimeVorschlag = $apl->getLastImportSollTime($kunde);
    $div.="<input type='text' id='imzeit_$kundeBoxId' maxlength='5' size='5' value='$importSollTimeVorschlag' />";
    $div.="</td>";
    $div.="</tr>";
    //navrh na imnr, podle datumu pujdu zpatky, vyberu auftragsnr s nejvyssim cislem a nabidnu o 1 vetsi
    $imnrNavrh = $apl->getLastImportNr($kunde);
    $imnrNavrh++;
    $div.="<tr>";
    $div.="<td>";
    $div.= "IM Nr:";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='imnr_$kundeBoxId' maxlength='7' size='7' value='$imnrNavrh' />";
    $div.="</td>";
    $div.="</tr>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "BestellNr:";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='bestellnr_$kundeBoxId' maxlength='30' size='15' value='' />";
    $div.="</td>";
    $div.="</tr>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "Bemerkung:";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='bemerkung_$kundeBoxId' maxlength='255' size='30' value='' />";
    $div.="</td>";
    $div.="</tr>";
    
    //navrh na termin
    $exVorschlag = substr($imnrNavrh, 0,3)."999";
    $div.="<tr>";
    $div.="<td>";
    $div.= "Ex geplant mit:";
    $div.="</td>";
    $div.="<td>";
    $div.="<input type='text' id='termin_$kundeBoxId' maxlength='7' size='7' value='$exVorschlag' />";
    $div.="</td>";
    $div.="</tr>";
    $div.="</table>";
    //plan
    $div.= "<fieldset>";
    $div.= "<legend>Plan</legend>";
    // vytahnout info o dilu pro planovani
    $planTeil = $apl->getPlanTeilProKunde($kunde);
    // pocet ks pro plan
    $planTeilStk = $apl->getPlanStkProKunde($kunde);
    
    $div.= "$planTeil: <input type='text' id='planteilstk_$kundeBoxId' maxlength='7' size='7' value='$planTeilStk' /> Stk<br>";
    $div.= "</fieldset>";
    
    //odeslat pozadavek
    $div.= "<input type='button' id='erstellenbutton_$kundeBoxId' acturl='importPlanErstellen.php' value='erstellen' />";    
    $div.= "</div>";
    
    
    $returnArray = array(
	'kundeBoxId'=>$kundeBoxId,
	'imSollDatum'=>$imSollDatum,
	'kunde'=>$kunde,
	'div'=>$div,
	'divid'=>"newimp_$kundeBoxId",
    );

    
    echo json_encode($returnArray);
?>

