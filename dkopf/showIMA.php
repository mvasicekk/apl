<?
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();

    $kunde = $apl->getKundeFromTeil($teil);
    
    $formDiv = "<div id='imaform'>";
    $formDiv.="<div class='closebutton' id='closebutton_imaform'>X</div>";
    $formDiv.="<fieldset>";
    $formDiv.="<legend>IMA</legend>";
    // tabulka pro pridani noveho pozadavku
    $formDiv.= "<input type='button' id='showimanewdiv' value='Neue IMA' />";
    $formDiv.="<div id='imanewdiv'>";
    $imanewvalue = "IMA_$kunde"."_".date('ymdHi');
    $formDiv.="<label for='imanr'>IMA_Nr:</label><input style='text-align:left;' type='text' id='imanr' size='17' readonly='readonly' value='$imanewvalue'/><br>";
    $formDiv.="<label for='imabemerkung'>Bemerkung:</label><input style='text-align:left;' type='text' size='50' maxlength='255' id='imabemerkung' /><br>";
    $formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php?anforderung=1&ma=ima' id='ima_select_auftragsnr' value='IM ...' />";
    $formDiv.="<input type='text' size='80' readonly='readonly' id='ima_imarray' value='' /><br>";
    $formDiv.="<input type='button' acturl='./selectPalArray.php?anforderung=1&ma=ima' id='ima_select_pal' value='Pal ...' />";
    $formDiv.="<input type='text' size='80'  readonly='readonly' id='ima_palarray' value='' /><br>";
    $formDiv.="<input type='hidden' id='ima_dauftrid' value=''/>";
    $formDiv.="<input type='button' acturl='./selectTatArray.php?anforderung=1&ma=ima' id='ima_select_tat' value='Tat ...' />";
    $formDiv.="<input type='text' size='80'  readonly='readonly' id='ima_tatarray' value='' /><br>";
    $formDiv.="<br><input type='button' acturl='./addIMA.php' id='ima_add' value='+' />";
    $formDiv.="</div>";
    // radek pro pridani noveho dokumentu
    $formDiv.="<table id='dokutable'>";
    // seznam jiz prirazenych obalu
    $teilIMAArray = $apl->getTeilIMAArray($teil);
    if ($teilIMAArray !== NULL) {
    $cisloRadku=0;
    foreach ($teilIMAArray as $ima) {
	if($cisloRadku%2==0)
	    $rowStyle = "class='sudy'";
	else
	    $rowStyle = "class='lichy'";
	
	if($ima['ima_genehmigt']>0){
	    $imagenehmigtClass = 'genehmigt';
	}
	else if($ima['ima_genehmigt']<0){
	    $imagenehmigtClass = 'nichtgenehmigt';
	}
	else{
	    $imagenehmigtClass = 'inprocess';
	}
		
	if($ima['ema_genehmigt']>0){
	    $emagenehmigtClass = 'genehmigt';
	}
	else if($ima['ema_genehmigt']<0){
	    $emagenehmigtClass = 'nichtgenehmigt';
	}
	else{
	    $emagenehmigtClass = 'inprocess';
	}

	$q = $ima['ema_genehmigt'];
	$formDiv.="<tr $rowStyle>";
	$formDiv.="<td>";
	$formDiv.="<input class='$imagenehmigtClass' type='text' readonly='readonly' id='r_imanr_".$ima['id']."' value='".$ima['imanr']."' size='17'/>";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input class='$emagenehmigtClass $q' type='text' readonly='readonly' id='r_emanr_".$ima['id']."' value='".$ima['emanr']."' size='12'/>";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' readonly='readonly' id='r_bemerkung_".$ima['id']."' value='".$ima['bemerkung']."' size='50'/>";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="(".$apl->getIMAStkForIMANrNew($ima['imanr'])." Stk)";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$imavonValue = substr($ima['imavon'], strrpos($ima['imavon'], '/')+1);
	$formDiv.="<input type='text' readonly='readonly' id='r_imavon_".$ima['id']."' value='".$imavonValue."' size='4'/>";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='text' readonly='readonly' id='r_stamp_".$ima['id']."' value='".substr($ima['stamp'],0,10)."' size='8'/>";
	$formDiv.="</td>";

	$formDiv.="<td>";
	$formDiv.="<input type='button' acturl='./editIMA.php' id='i_ima_edit_".$ima['id']."' value='detail' />";
	$formDiv.="</td>";
	$formDiv.="</tr>";
	$cisloRadku++;
    }
}

$formDiv.="</table>";
$formDiv.="</fieldset>";

/**
 * EMA 
 */
//$formDiv.="<fieldset>";
//$formDiv.="<legend>EMA</legend>";
//$formDiv.="</fieldset>";
    
    $formDiv.= "</div>";
    
    
    echo json_encode(array(
                            'id'=>$id,
			    'teil'=>$teil,
			    'teilIMAArray'=>$teilIMAArray,
			    'formDiv'=>$formDiv,
    ));
?>
