<?
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();
    
    
    $imaid = substr($id, strrpos($id, '_')+1);

    $imaInfoArray = $apl->getIMAInfoArray($imaid);
    if ($imaInfoArray !== NULL) {
	$ir = $imaInfoArray[0];
	$formDiv = "<div id='imaeditform'>";
	$formDiv.="<fieldset id='imapart'>";
	$formDiv.="<legend>IMA</legend>";	
	$formDiv.="<label for='imanr'>IMA_Nr:</label><input style='text-align:left;' type='text' id='imanr' size='17' readonly='readonly' value='".$ir['imanr']."'/>";
	$formDiv.="<label for='imabemerkung'>Bemerkung:</label><input style='text-align:left;' type='text' size='50' maxlength='255' id='imabemerkung_$imaid' acturl='./updateIMABemerkung.php' value='".$ir['bemerkung']."' /><br>";
	$formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php' id='ima_select_auftragsnr_e' value='IM ...' />";
	$formDiv.="<input type='text' size='80' readonly='readonly' id='ima_imarray_e' value='".$ir['auftragsnrarray']."' /><br>";
	$formDiv.="<input type='button' acturl='./selectPalArray.php' id='ima_select_pal_e' value='Pal ...' />";
	$formDiv.="<input type='text' size='80' readonly='readonly' id='ima_palarray_e' value='".$ir['palarray']."' /><br>";
	$formDiv.="<input type='button' acturl='./selectTatArray.php' id='ima_select_tat_e' value='Tat ...' />";
	$formDiv.="<input type='text' size='80' readonly='readonly' id='ima_tatarray_e' value='".$ir['tatundzeitarray']."' /><br>";
	$att2FolderArray = AplDB::$ATT2FOLDERARRAY;
	$kunde = $apl->getKundeFromTeil($teil);
	$kundeGdatPath = $apl->getKundeGdatPath($kunde);
	$gdatPath = "/mnt/gdat/Dat/";
	$att='mehr';
	$ppaDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teil . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
	$ppaDir.= "/".$ir['imanr'];
	$extensions = 'JPG|jpg|pdf|txt';
	$filter = "/.*.($extensions)$/";
	$docsArray = $apl->getFilesForPath($ppaDir,$filter);
	$upDiv="<div id='uploader_$att' folder='$ppaDir'>";
	$upDiv.="<input type='button' id='pickfiles' href='javascript:;' value='Dateien auswaehlen' />";
	$upDiv.="<div id='filelist'></div>";
	$upDiv.="</div>";
	//tabulka se seznamem souboru
	$formDiv.=$upDiv;
	$formDiv.="<div id='docutablescroller'>";
	$formDiv.="<table id='dokutable_edit'>";
	$formDiv.="<tr><td style='font-size:x-small;' colspan='5'>";
	$formDiv.="<input type='hidden' id='rootPath' value='0' />";
	$formDiv.="</td>";
	$formDiv.="<td style='text-align:right;font-size:x-small;' >"." (".$extensions.")</td></tr>";
	if ($docsArray !== NULL) {
	    $formDiv.="<tr>";
	    $formDiv.="<td class='filetableheader' style='' colspan='4'>Datei / soubor</td>";
	    $formDiv.="<td class='filetableheader' style='width:160px;'>Datum</td>";
	    $formDiv.="<td class='filetableheader' style='width:120px;text-align:right;'>Size</td>";
	    $formDiv.="</tr>";
	    $i = 0;
	    foreach ($docsArray as $doc) {
		if($doc['filename']=='..') continue;
		$trclass = $i++ % 2 == 0 ? 'sudy' : 'lichy';
		$typeclass = $doc['type'];
		$filetypeclass = $doc['ext'];
		if($typeclass=='file') $target="_blank";
		$formDiv.="<tr class='$trclass'>";
		$fN = $doc['filename'];
		if($filetypeclass=="JPG")
		    $text = $doc['filename'];//"<img src='".$doc['url']."' width='50'>".$doc['filename'];
		else
		    $text = $doc['filename'];
		$formDiv.="<td class='filetableitem' colspan='4'>";
		$formDiv.="<a title='$fN' target='$target' acturl='./getFilesTable.php' class='$typeclass $filetypeclass' href='" . $doc['url'] . "'>";
		$formDiv.= $text;
		$formDiv.= "</a></td>";
		$formDiv.="<td class='filetableitem' >" . date('Y-m-d H:i:s', $doc['mtime']) . "</td>";
		if($doc['type']=='file')
		    $formDiv.="<td class='filetableitem' style='text-align:right;'>" . number_format(floatval($doc['size']), 0, ',', ' ') . "</td>";
		if($doc['type']=='dir')
		    $formDiv.="<td class='filetableitem' style='text-align:right;'>" . "DIR" . "</td>";
		$formDiv.="</tr>";
	    }
	}
	$formDiv.="</table>";
	$formDiv.="</div>";
//------------------------------------------------------------------------------
	$formDiv.="</fieldset>";
//------------------------------------------------------------------------------
	$formDiv.="<fieldset id='emapart'>";
	$formDiv.="<legend>EMA</legend>";
	$lastEmaNr = $apl->getLastEMANr($kunde);
	$emaNr = '';
	//$emaNr = 'EMA_'.$kunde.'_'.sprintf("%04d",$lastEmaNr+1);
	if(strlen(trim($ir['emanr']))>0) $emaNr=$ir['emanr'];
	$formDiv.="<label for='emanr'>EMA_Nr:</label><input focusurl='./emaFocus.php' style='text-align:left;' type='text' size='12' maxlength='15' id='emanr_$imaid' acturl='./updateDMAField.php' value='".$emaNr."' /><br>";
	$formDiv.="</fieldset>";
//------------------------------------------------------------------------------	
	$formDiv.= "</div>";
    }


echo json_encode(array(
                            'id'=>$id,
			    'imaid'=>$imaid,
			    'teil'=>$teil,
			    'imaInfoArray'=>$imaInfoArray,
			    'formDiv'=>$formDiv,
    ));
?>
