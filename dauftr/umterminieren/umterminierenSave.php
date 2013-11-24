<?
require '../../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$id = $_POST['id'];
$kunde = $_POST['kunde'];
$terminOldArray = $_POST['terminOldArray'];
$terminNeuArray = $_POST['terminNeuArray'];
$exSollDatumArray = $_POST['exSollDatumArray'];
$exSollUhrzeitArray = $_POST['exSollUhrzeitArray'];

$a = AplDB::getInstance();

// vztvorim si jen jedno pole
$citac = 0;
$terminArray = array();
$changedArray = array();

if (count($terminOldArray) > 0) {
    foreach ($terminOldArray as $terminOld) {
	$terminArray[$citac]['old'] = $terminOld;
	$terminArray[$citac]['neu'] = 'P' . $terminNeuArray[$citac];
	$terminArray[$citac]['datumneu'] = $exSollDatumArray[$citac];
	$terminArray[$citac]['uhrzeitneu'] = $exSollUhrzeitArray[$citac];
	// sestavim datum planovaneho exportu
	if (strlen(trim($exSollDatumArray[$citac])) > 0) {
	    if (strlen(trim($exSollUhrzeitArray[$citac])) > 0) {
		$time = trim($exSollUhrzeitArray[$citac]);
	    } else {
		$time = "00:00";
	    }
	    $terminArray[$citac]['datetime'] = $a->make_DB_datetime($time, $exSollDatumArray[$citac]);
	} else {
	    $terminArray[$citac]['datetime'] = NULL;
	}
	$citac++;
    }

// 1.ulozim si aktualni stav terminu v dauftr
    $pocetUlozenych = $a->saveDauftrTermine($kunde);
//$exportInfo = $a->getAuftragInfoArray($export,$kunde);
// 2. projit pole s dvojicema a pro kazdou dvojici zmenit terminy

    $index = 0;
    $chDiv="<table id='changedtable'>";
    $chDiv.="<tr>";
    $chDiv.="<th>Export Alt</th>";
    $chDiv.="<th>Export Neu</th>";
    $chDiv.="<th>Positionen geandert</th>";
    $chDiv.="</tr>";
    foreach ($terminArray as $termin) {
	$chDiv.="<tr>";
	$chDiv.="<td>";
	$changedArray[$index]['from'] = $termin['old'];
	$chDiv.=$termin['old'];
	$chDiv.="</td>";
	$chDiv.="<td>";
	$changedArray[$index]['to'] = $termin['neu'];
	$chDiv.=$termin['neu'];
	$chDiv.="</td>";
	// posledni parametr urcuje rezim 1=test neprovede zmeny, 0 = updatuje v tabulce
	$zmen = $a->changeTermin($termin['old'], $termin['neu'],0);
	$chDiv.="<td style='text-align:right;'>";
	$changedArray[$index]['changed'] = $zmen;
	$chDiv.=$zmen;
	$chDiv.="</td>";
	$index++;
	//update datumu a casu planovaneho exportu
	$a->updateDaufkopfField('ex_datum_soll', $termin['datetime'], substr($termin['neu'],1));
	$chDiv.="</tr>";
    }
    $chDiv.="</table>";
}

$value = array(
    'id' => $id,
    'ip' => $ip,
    'dt' => $dt,
    'terminArray' => $terminArray,
     'saved'=>$pocetUlozenych,
     'kunde'=>$kunde,
     'changedArray'=>$changedArray,
    'chdiv'=>$chDiv,
);

echo json_encode($value);
