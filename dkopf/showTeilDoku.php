<?
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();

    
    $formDiv = "<div id='dokuform'>";
    // radek pro pridani noveho dokumentu
    $formDiv.="<table id='dokutable'>";
    $formDiv.="<tr style='background-color:#eef;'>";
    $formDiv.="<th>DokuTyp</th>";
    $formDiv.="<th>Einlag. Datum</th>";
    $formDiv.="<th>MusterPlatz / Datei</th>";
    $formDiv.="<th>Freigabe am</th>";
    $formDiv.="<th>Freigabe vom</th>";
    $formDiv.="<th>&nbsp;</th>";
    $formDiv.="</tr>";
    
    $formDiv.="<tr style='background-color:#eef;'>";
    $formDiv.="<td>";
    $formDiv.="<input type='text' id='n_doku_nr' size='3' maxlength='3' />";
    $formDiv.="</td>";
    
    $formDiv.="<td>";
    $formDiv.="<input type='text' class='datepicker' id='n_einlag_datum' size='10' maxlength='10' value='".date('d.m.Y')."' />";
    $formDiv.="</td>";

    $formDiv.="<td>";
    $formDiv.="<input type='text' id='n_musterplatz' size='45' maxlength='255' />";
    $formDiv.="</td>";
    
    $formDiv.="<td>";
    $formDiv.="<input type='text' class='datepicker' id='n_freigabe_am' size='10' maxlength='10' />";
    $formDiv.="</td>";

    $formDiv.="<td>";
    $formDiv.="<input type='text' id='n_freigabe_vom' size='15' maxlength='30' />";
    $formDiv.="</td>";

    $formDiv.="<td>";
    $formDiv.="<input type='button' acturl='./addDoku.php' id='n_doku_add' value='+' />";
    $formDiv.="</td>";

    $formDiv.="</tr>";
    $formDiv.="<tr><td colspan='6'>&nbsp</td></tr>";
    // seznam jiz prirazenych dokumentu
    $teilDokuArray = $apl->getTeilDokuArray($teil);
    if ($teilDokuArray !== NULL) {
    $cisloRadku=0;
    foreach ($teilDokuArray as $teilDoku) {
	if($cisloRadku%2==0)
	    $rowStyle = "class='sudy'";
	else
	    $rowStyle = "class='lichy'";
	
	$formDiv.="<tr $rowStyle>";
	$formDiv.="<td>";
	$formDiv.="<input type='text' readonly='readonly' id='r_doku_nr_".$teilDoku['id']."' value='".$teilDoku['doku_nr']."' size='3' maxlength='3' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' acturl='./dokuFieldUpdate.php' class='datepicker' value='".$teilDoku['einlag_datum']."' id='r_einlag_datum_".$teilDoku['id']."' size='10' maxlength='10' value='" . date('d.m.Y') . "' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' acturl='./dokuFieldUpdate.php' value='".$teilDoku['musterplatz']."' id='r_musterplatz_".$teilDoku['id']."' size='45' maxlength='255' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' acturl='./dokuFieldUpdate.php' class='datepicker' value='".$teilDoku['freigabe_am']."' id='r_freigabe_am_".$teilDoku['id']."' size='10' maxlength='10' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' acturl='./dokuFieldUpdate.php' value='".$teilDoku['freigabe_vom']."' id='r_freigabe_vom_".$teilDoku['id']."' size='15' maxlength='30' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='button' acturl='./delDoku.php' id='i_doku_del_".$teilDoku['id']."' value='-' />";
	$formDiv.="</td>";
	$formDiv.="</tr>";
	$cisloRadku++;
    }
}

$formDiv.="</table>";
    $formDiv.= "</div>";
    
    echo json_encode(array(
                            'id'=>$id,
			    'teil'=>$teil,
			    'teilDokuArray'=>$teilDokuArray,
			    'formDiv'=>$formDiv,
    ));
?>
