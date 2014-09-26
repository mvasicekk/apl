<?
session_start();
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();

    // security
    $puser = $_SESSION['user'];
    $elementId='n_vpm_add';
    $display_sec[$elementId] = $apl->getDisplaySec('vpm',$elementId,$puser)?'table-row':'none';
    $edit_sec[$elementId] = $apl->getPrivilegeSec('vpm',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
    
    $elementsArray = array("r_stk","r_bemerkung","i_vpm_del");
    foreach ($elementsArray as $elementId) {
	$display_sec[$elementId] = $apl->getDisplaySec('vpm',$elementId,$puser)?'inline-block':'none';
	$edit_sec[$elementId] = $apl->getPrivilegeSec('vpm',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
    }

    
    $formDiv = "<div id='vpmform'>";
    // radek pro pridani noveho dokumentu
    $formDiv.="<table id='dokutable'>";
    $formDiv.="<tr style='display:".$display_sec['n_vpm_add'].";background-color:#eef;'>";
    $formDiv.="<th colspan='2' style='text-align:left;'>ArtikelNr</th>";
    $formDiv.="<th style='text-align:left;'>Anzahl</th>";
    $formDiv.="<th style='text-align:left;'>Bemerkung</th>";
    $formDiv.="<th>&nbsp;</th>";
    $formDiv.="</tr>";
    
    $formDiv.="<tr style='display:".$display_sec['n_vpm_add'].";background-color:#eef;'>";
    $formDiv.="<td colspan='2' style='text-align:left;'>";
    $formDiv.="<input style='text-align:left;' type='text' id='n_vpm_nr' size='8' maxlength='8' />";
    $formDiv.="</td>";

    $formDiv.="<td style='text-align:left;'>";
    $formDiv.="<input style='text-align:left;' type='text' id='n_anzahl' size='4' maxlength='4' />";
    $formDiv.="</td>";

    $formDiv.="<td style='text-align:left;'>";
    $formDiv.="<input style='text-align:left;' type='text' id='n_bemerkung' size='45' maxlength='255' />";
    $formDiv.="</td>";

    $formDiv.="<td>";
    $formDiv.="<input type='button' acturl='./addVPM.php' id='n_vpm_add' value='+' />";
    $formDiv.="</td>";

    $formDiv.="</tr>";
    $formDiv.="<tr style='display:".$display_sec['n_vpm_add'].";'><td colspan='4'>&nbsp</td></tr>";

    $formDiv.="<tr style='background-color:#eef;'>";
    $formDiv.="<th colspan='2' style='text-align:left;'>ArtikelNr</th>";
    $formDiv.="<th colspan='' style='text-align:left;'>Anzahl</th>";
    $formDiv.="<th style='text-align:left;'>Bemerkung</th>";
    $formDiv.="<th>&nbsp;</th>";
    $formDiv.="</tr>";

    // seznam jiz prirazenych obalu
    $teilVPMArray = $apl->getTeilVPMArray($teil);
    if ($teilVPMArray !== NULL) {
    $cisloRadku=0;
    foreach ($teilVPMArray as $teilDoku) {
	if($cisloRadku%2==0)
	    $rowStyle = "class='sudy'";
	else
	    $rowStyle = "class='lichy'";
	
	$formDiv.="<tr $rowStyle>";
	$formDiv.="<td>";
	$formDiv.="<input type='text' readonly='readonly' id='r_vpm_nr_".$teilDoku['id']."' value='".$teilDoku['verp']."' size='8' maxlength='8' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$bezArr = $apl->getArtikelBezeichnung($teilDoku['verp']);
	$artBez = '';
	if($bezArr!==NULL){
	    $bezRow = $bezArr[0];
	    $artBez = $bezRow['name1'].' '.$bezRow['name2'].' '.$bezRow['name3'];
	}
	$formDiv.=$artBez;
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input ".$edit_sec['r_stk']." type='text' acturl='./vpmFieldUpdate.php' value='".$teilDoku['verp_stk']."' id='r_stk_".$teilDoku['id']."' size='4' maxlength='4' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input ".$edit_sec['r_bemerkung']." type='text' acturl='./vpmFieldUpdate.php' value='".$teilDoku['bemerkung']."' id='r_bemerkung_".$teilDoku['id']."' size='45' maxlength='255' />";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input style='display:".$display_sec['i_vpm_del'].";' type='button' acturl='./delVPM.php' id='i_vpm_del_".$teilDoku['id']."' value='-' />";
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
			    'teilVPMArray'=>$teilVPMArray,
			    'formDiv'=>$formDiv,
    ));
?>
