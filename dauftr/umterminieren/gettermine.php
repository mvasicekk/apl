<?
require '../../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$kunde = $_POST['value'];
$a = AplDB::getInstance();
$hasRows = 0;
$termineRows = $a->getTermineRowsArray($kunde);
$panelydiv.="<table id='termine_table1'>";
$panelydiv.="<thead>";
$panelydiv.="<tr><th>Export</th><th>exportiert am:</th><th>export plan</th><th>Export NEU</th><th>Export NEU Datum</th><th>Export NEU Uhrzeit</th><th>Zielort</th><th>Bemerkung</th></tr>";
$panelydiv.="</thead>";
if($termineRows!==NULL){
    $index=0;
    $hasRows=1;
    foreach ($termineRows as $termin){
	if(($index%2)==0) 
	    $zebraClass='odd';
	else
	    $zebraClass='even';
	$index++;
        $panelydiv.="<tr class='$zebraClass'>";
        $panelydiv.="<td><strong><span id='export_old_".$termin['termin']."'>".$termin['termin']."</span></strong></td>";
	$panelydiv.="<td><strong>".substr($termin['ausliefer_datum'],0,10)."</strong></td>";
	$panelydiv.="<td><strong>".substr($termin['ex_datum_soll'],0,16)."</strong></td>";
        $panelydiv.="<td>"."<input acturl='./terminneuUpdate.php' id='terminneu_".$termin['termin']."' type='text' value='' size='6' maxlength='6'"."</td>";
        $panelydiv.="<td>"."<input class='datepicker' id='ex_datum_soll_neu_".$termin['termin']."' type='text' value='' size='10' maxlength='10'"."</td>";
	$panelydiv.="<td>"."<input id='ex_time_soll_neu_".$termin['termin']."' type='text' value='' size='5' maxlength='5'"."</td>";
	$panelydiv.="<td>"."<input acturl='./zielortUpdate.php' id='zielort_".$termin['termin']."' type='text' value='".$termin['zielort']."' size='20' maxlength='255'"."</td>";
	$panelydiv.="<td>"."<input acturl='./bemerkungUpdate.php' id='bemerkung_".$termin['termin']."' type='text' value='".$termin['bemerkung']."' size='20' maxlength='255'"."</td>";
	// table end
        $panelydiv.="</tr>";
    }
}
else{
    $panelydiv.="<tr><td>Keine Termine ?!?!</td></tr>";
}
$panelydiv.="</table>";

 $value = array('divcontent'=>$panelydiv,'ip'=>$ip,'dt'=>$dt,'hasRows'=>$hasRows);
 
 echo json_encode($value);
