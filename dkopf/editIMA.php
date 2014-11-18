<?
session_start();
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();
    
    
    $imaid = substr($id, strrpos($id, '_')+1);

    $imaInfoArray = $apl->getIMAInfoArray($imaid);
    $emaAnlagenArray = array();

    //security
    $puser = $_SESSION['user'];
    $elementsArray = array(
	'emanr',
	'ima_genehmigt_bemerkung',
	'imagenehmigtflag',
	'ima_select_auftragsnr_gen',
	'ima_select_pal_gen',
	'ima_select_tat_gen',
	'ema_select_auftragsnr_anf',
	'ema_select_pal_anf',
	'ema_select_tat_anf',
	'ema_antrag_text',
	'ema_antrag_generieren',
	'ema_select_auftragsnr_gem',
	'ema_select_pal_gem',
	'ema_select_tat_gem',
	'anlage',
	'imabemerkung',
	'ema_genehmigt_bemerkung',
	'ema_genehmigt_user',
	'ema_genehmigt_stamp',
	'ema_dauftr_generieren',
    );
    foreach ($elementsArray as $elementId){
	$display_sec[$elementId] = $apl->getDisplaySec('dkopf',$elementId,$puser)?'inline-block':'none';
	$edit_sec[$elementId] = $apl->getPrivilegeSec('dkopf',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
    }
	
	
    if ($imaInfoArray !== NULL) {
	$ir = $imaInfoArray[0];
	$emaAnlagenStr = $ir['ema_anlagen_array'];
	if(strlen($emaAnlagenStr)>0){
	    $emaAnlagenArray = explode(';', $emaAnlagenStr);
	}


	if($ir['ima_genehmigt']>0){
	    $imagenehmigtClass = 'genehmigt';
	}
	else if($ima['ima_genehmigt']<0){
	    $imagenehmigtClass = 'nichtgenehmigt';
	}
	else{
	    $imagenehmigtClass = 'inprocess';
	}

	if($ir['ema_genehmigt']>0){
	    $emagenehmigtClass = 'genehmigt';
	}
	else if($ima['ema_genehmigt']<0){
	    $emagenehmigtClass = 'nichtgenehmigt';
	}
	else{
	    $emagenehmigtClass = 'inprocess';
	}

	$formDiv = "<div id='imaeditform'>";
	$formDiv.="<div class='closebutton' id='closebutton_imaeditform'>X</div>";
	$formDiv.="<input type='hidden' id='imaid' value='$imaid'>";
	$formDiv.="<p id='spinner'>zpracovávám dotaz ....</p>";
	$formDiv.="<fieldset id='imapart'>";
	$formDiv.="<legend>IMA</legend>";	
	$formDiv.="<label for='imanr'>IMA_Nr:</label><input class='$imagenehmigtClass' style='text-align:left;' type='text' id='imanr' size='17' readonly='readonly' value='".$ir['imanr']."'/>";
	
	$rdonlyIfGenehmigt = $ir['ima_genehmigt']<>0?'readonly="readonly"':'';
	$rdonlyIfEMAGenehmigt = $ir['ema_genehmigt']<>0?'readonly="readonly"':'';
	$disabledIfGenehmigt = $ir['ima_genehmigt']<>0?'disabled="disabled"':'';
	$disabledIfEMAGenehmigt = $ir['ema_genehmigt']<>0?'disabled="disabled"':'';
	$checkedIfGenehmigt = $ir['ima_genehmigt']<>0?'checked="checked"':'';
	$genehmigtUser = substr($ir['ima_genehmigt_user'],strrpos($ir['ima_genehmigt_user'],'/')+1);
	$genehmigtStamp = substr($ir['ima_genehmigt_stamp'],0,10);
	$genehmigtBemerkung = trim($ir['ima_genehmigt_bemerkung']);
	$genehmigtButtonText = $ir['ima_genehmigt']>0?'genehmigt':'genehmigen';
	
	$formDiv.="<label for='imabemerkung'>Bemerkung:</label><input $disabledIfEMAGenehmigt $rdonlyIfGenehmigt style='text-align:left;' type='text' size='50' maxlength='255' id='imabemerkung_$imaid' acturl='./updateIMABemerkung.php' value='".$ir['bemerkung']."' /><br>";
	
	$formDiv.="<label for=ima_genehmigt_bemerkung_$imaid>Bemerk.:</label><input $disabledIfEMAGenehmigt ".$edit_sec['ima_genehmigt_bemerkung']." id='ima_genehmigt_bemerkung_$imaid' $rdonlyIfGenehmigt style='text-align:left;' type='text' size='35' maxlength='255' value='$genehmigtBemerkung'/>";
	$formDiv.="<label for=ima_genehmigt_user_$imaid>vom:</label><input id='ima_genehmigt_user_$imaid' readonly='readonly' style='text-align:left;' type='text' size='4' value='$genehmigtUser'/>";
	$formDiv.="<label for=ima_genehmigt_stamp_$imaid>am:</label><input id='ima_genehmigt_stamp_$imaid' readonly='readonly' style='text-align:left;' type='text' size='10' value='$genehmigtStamp'/>";
	
	if($ir['ema_genehmigt']==0){
	    $elementId='imagenehmigtflag';
	    $formDiv.="<span style='display:".$display_sec[$elementId].";'>";
	    $formDiv.="<input $disabledIfGenehmigt style='text-align:left;' type='button' id='imagenehmigtflag_$imaid' value='genehmigen' acturl='./updateIMAGenehmigt.php?nicht=0'/>";
	    $formDiv.="<input $disabledIfGenehmigt style='text-align:left;' type='button' id='imangenehmigtflag_$imaid' value='nicht genehmigen' acturl='./updateIMAGenehmigt.php?nicht=1'/>";
	    $formDiv.="</span>";
	}
	
	//tabulka pro rozdeleni na Anforderung a genehmigt
	$formDiv.="<table>";
	    $formDiv.="<tr>";
	    //anforderung ******************************************************
	    $formDiv.="<td>";
	    $formDiv.="<fieldset>";
		$formDiv.="<legend>Anforderung</legend>";
		//tabulka pro pozadavek ****************************************
		$formDiv.="<table>";
		    //auftragsnr_array *****************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td>";
		    if(($ir['ima_genehmigt']==0)&&($ir['ema_genehmigt']==0))
			$formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php?anforderung=1&ma=ima' id='ima_select_auftragsnr_e' value='IM ...' />";
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_imarray_e' value='".$ir['auftragsnrarray']."' />";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //pal_array ************************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td>";
		    if(($ir['ima_genehmigt']==0)&&($ir['ema_genehmigt']==0))
			$formDiv.="<input type='button' acturl='./selectPalArray.php?anforderung=1&ma=ima' id='ima_select_pal_e' value='Pal...' />";
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_palarray_e' value='".$ir['palarray']."' />";
		    $formDiv.="<input type='hidden' id='ima_dauftrid_e' value='".$ir['ima_dauftrid_array']."' />";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //tatundzeit_array *****************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td>";
		    if(($ir['ima_genehmigt']==0)&&($ir['ema_genehmigt']==0))
			$formDiv.="<input type='button' acturl='./selectTatArray.php?anforderung=1&ma=ima' id='ima_select_tat_e' value='Tat ...' />";
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_tatarray_e' value='".$ir['tatundzeitarray']."' />";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		// konec tabulky pro pozadavek *********************************
		$formDiv.="</table>";
	    $formDiv.="</fieldset>";
	    $formDiv.="</td>";
	    //genehmigt ********************************************************
	    $formDiv.="<td>";
	    if($ir['ema_genehmigt']==0){
	    $formDiv.="<fieldset class='genehmigt'>";
		$formDiv.="<legend>genehmigt</legend>";
		//tabulka pro schvaleni ****************************************
		$formDiv.="<table>";
		    //auftragarray *********************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td class='genehmigt'>";
		    if($ir['ima_genehmigt']==0){
			$elementId='ima_select_auftragsnr_gen';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php?anforderung=0&ma=ima&genehmigt=1' id='ima_select_auftragsnr_gen' value='IM genehmigt' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class='genehmigt'>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_imarray_gen' value='".$ir['ima_auftragsnrarray_genehmigt']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //palarray *************************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td class='genehmigt'>";
		    if($ir['ima_genehmigt']==0){
			$elementId='ima_select_pal_gen';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectPalArray.php?anforderung=0&ma=ima&genehmigt=1' id='ima_select_pal_gen' value='Pal genehmigt' />";
			$formDiv.="</span>";			
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class='genehmigt'>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_palarray_gen' value='".$ir['ima_palarray_genehmigt']."' /><br>";
		    $formDiv.="<input type='hidden' id='ima_dauftrid_gen' value='".$ir['ima_dauftrid_array_genehmigt']."' />";		    
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //tatundzeit_array
		    $formDiv.="<tr>";
		    $formDiv.="<td class='genehmigt'>";
		    if($ir['ima_genehmigt']==0){
			$elementId='ima_select_tat_gen';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectTatArray.php?anforderung=0&ma=ima&genehmigt=1' id='ima_select_tat_gen' value='Tat genehmigt' />";
			$formDiv.="</span>";			
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class='genehmigt'>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ima_tatarray_gen' value='".$ir['ima_tatundzeitarray_genehmigt']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    $formDiv.="</table>";
		    $formDiv.="</fieldset>";
	    }
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	$formDiv.="</table>";
	
//	$formDiv.="</table>";
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
	    $formDiv.="<td class='filetableheader' style='width:120px;text-align:center;'>als EMA Anlage</td>";
	    $formDiv.="</tr>";
	    $i = 0;
	    foreach ($docsArray as $doc) {
		if($doc['filename']=='..') continue;
		$trclass = $i++ % 2 == 0 ? 'sudy' : 'lichy';
		$typeclass = $doc['type'];
		$filetypeclass = $doc['ext'];
		$checkBoxId = 'anlage_'.$doc['filename'];
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

		$checked = '';
		if(in_array($doc['filename'], $emaAnlagenArray)) $checked = "checked='checked'";
		if($filetypeclass=='JPG'){
		    $elementId='anlage';
		    $formDiv.="<td class='filetableitem' style='text-align:center;'>";
		    $formDiv.="<span style='display:".$display_sec[$elementId].";'>";
		    $formDiv.="<input acturl='./updateEmaAnlage.php' id='$checkBoxId' type='checkbox' $checked>";
		    $formDiv.="</span>";		    
		    $formDiv.="</td>";
		}
		else
		    $formDiv.="<td class='filetableitem' style='text-align:center;'></td>";
		$formDiv.="</tr>";
	    }
	}
	$formDiv.="</table>";
	$formDiv.="</div>";
//------------------------------------------------------------------------------
	$formDiv.="</fieldset>";
	
	
// EMA part
// zobrazit jen v pripade, ze neni povolena IMA
	if($ir['ima_genehmigt']==0){
	$formDiv.="<fieldset id='emapart'>";
	$formDiv.="<legend>EMA</legend>";
	$lastEmaNr = $apl->getLastEMANr($kunde);
	$emaNr = '';

	if(strlen(trim($ir['emanr']))>0) $emaNr=$ir['emanr'];
	$elementId='emanr';
	$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
	$formDiv.="<label for='emanr'>EMA_Nr:</label><input class='$emagenehmigtClass' $rdonlyIfEMAGenehmigt ".$edit_sec[$elementId]." changeurl='./emaNrChange.php' focusurl='./emaFocus.php' style='text-align:left;' type='text' size='12' maxlength='15' id='emanr_$imaid' acturl='./updateDMAField.php' value='".$emaNr."' /><br>";
	$formDiv.="</span>";

	if($ir['ema_genehmigt']==0){
	    $elementId='imagenehmigtflag';
	    $formDiv.="<span style='display:".$display_sec[$elementId].";'>";
	    $formDiv.="<input $disabledIfEMAGenehmigt style='text-align:left;' type='button' id='emagenehmigtflag_$imaid' value='genehmigen' acturl='./updateIMAGenehmigt.php?nicht=0&ma=ema'/>";
	    $formDiv.="<input $disabledIfEMAGenehmigt style='text-align:left;' type='button' id='emangenehmigtflag_$imaid' value='nicht genehmigen' acturl='./updateIMAGenehmigt.php?nicht=1&ma=ema'/>";
	    $formDiv.="</span>";
	}

	$formDiv.="<table>";
	$formDiv.="<tr>";
	$formDiv.="<td>";
	//tabulka pro zadost o vicepraci EMA
	$formDiv.="<fieldset class=''>";
	$formDiv.="<legend>EMA Anforderung</legend>";
	//tabulka pro zadost EMA ****************************************
	    $formDiv.="<table>";
	    //auftragarray *********************************************
		$formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_auftragsnr_anf';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php?anforderung=1&ma=ema' id='ema_select_auftragsnr_anf' value='IM angef.' />";
			$formDiv.="</span>";			
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_imarray_anf' value='".$ir['ema_auftragsarray']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //palarray *************************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_pal_anf';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectPalArray.php?anforderung=1&ma=ema' id='ema_select_pal_anf' value='Pal angef.' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_palarray_anf' value='".$ir['ema_palarray']."' /><br>";
		    $formDiv.="<input type='hidden' id='ema_dauftrid_anf' value='".$ir['ema_dauftrid_array']."' />";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //tatundzeit_array
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_tat_anf';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectTatArray.php?anforderung=1&ma=ema' id='ema_select_tat_anf' value='Tat angef.' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_tatarray_anf' value='".$ir['ema_tatundzeitarray']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    // antrag text
		    $formDiv.="<tr>";
		    $formDiv.="<td colspan='2'>";
//		    if($ir['ema_genehmigt']==0){
			$formDiv.="Antrag auf Mehrleistung:<br>";
			$elementId='ema_antrag_text';
//			$formDiv.="<span style='display:".$display_sec[$elementId].";width:99%;'>";
			$formDiv.="<span style='width:99%;'>";
			$formDiv.="<textarea $disabledIfEMAGenehmigt ".$edit_sec[$elementId]." acturl='./updateDMAField.php' id='ema_antrag_text'>";
			$formDiv.= $ir['ema_antrag_text'];
			$formDiv.="</textarea>";
			$formDiv.="</span>";
//		    }
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    $formDiv.="<tr>";
		    $formDiv.="<td colspan='2'>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_antrag_generieren';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./emaAntragGenerieren.php' id='ema_antrag_generieren' value='Antrag generieren' />";
			$formDiv.="</span>";
		    }
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    $formDiv.="</table>";
		    $formDiv.="</fieldset>";
	$formDiv.="</td>";
	$formDiv.="<td>";
	//tabulka pro schvalenou vicepraci EMA
	$formDiv.="<fieldset class='genehmigt'>";
	$formDiv.="<legend>EMA genehmigt</legend>";
	//tabulka pro zadost EMA ****************************************
	    $formDiv.="<table>";
	    //auftragarray *********************************************
		$formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_auftragsnr_gem';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectAuftragsnrArray.php?anforderung=0&ma=ema' id='ema_select_auftragsnr_gem' value='IM angef.' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_imarray_gem' value='".$ir['ema_auftragsarray_genehmigt']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //palarray *************************************************
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_pal_gem';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectPalArray.php?anforderung=0&ma=ema' id='ema_select_pal_gem' value='Pal angef.' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_palarray_gem' value='".$ir['ema_palarray_genehmigt']."' /><br>";
		    $formDiv.="<input type='hidden' id='ema_dauftrid_gem' value='".$ir['ema_dauftrid_array_genehmigt']."' />";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    //tatundzeit_array
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_select_tat_gem';
			$formDiv.="<span style='display:".$display_sec[$elementId].";'>";
			$formDiv.="<input type='button' acturl='./selectTatArray.php?anforderung=0&ma=ema' id='ema_select_tat_gem' value='Tat angef.' />";
			$formDiv.="</span>";
		    }
		    else
			$formDiv.="";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $formDiv.="<input type='text' size='40' readonly='readonly' id='ema_tatarray_gem' value='".$ir['ema_tatundzeitarray_genehmigt']."' /><br>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
		    // ema_genehmigt_bemerkung
		    $formDiv.="<tr>";
		    $formDiv.="<td colspan='2'>";
//		    if($ir['ema_genehmigt']==0){
			$formDiv.="EMA genehmigt Bemerkung:<br>";
			$elementId='ema_genehmigt_bemerkung';
//			$formDiv.="<span style='display:".$display_sec[$elementId].";width:99%;'>";
			$formDiv.="<span style='width:99%;'>";
			$formDiv.="<textarea $disabledIfEMAGenehmigt ".$edit_sec[$elementId]." acturl='./updateDMAField.php' id='ema_genehmigt_bemerkung'>";
			$formDiv.= $ir['ema_genehmigt_bemerkung'];
			$formDiv.="</textarea>";
			$formDiv.="</span>";
//		    }
		    $formDiv.="</td>";
		    $formDiv.="</tr>";
    		    // ema_genehmigt_user
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    $formDiv.="genehmigt vom";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $elementId='ema_genehmigt_user';
		    $formDiv.="<span style='display:".$display_sec[$elementId].";'>";
		    $formDiv.="<input $disabledIfEMAGenehmigt ".$edit_sec[$elementId]." type='text' size='40' id='ema_genehmigt_user' value='".$ir['ema_genehmigt_user']."' />";
		    $formDiv.="</span>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";

    		    // ema_genehmigt_stamp
		    $formDiv.="<tr>";
		    $formDiv.="<td class=''>";
		    $formDiv.="genehmigt am";
		    $formDiv.="</td>";
		    $formDiv.="<td class=''>";
		    $obsah = substr($ir['ema_genehmigt_stamp'],0,10);
		    if($obsah=='0000-00-00') $obsah='';
    		    $elementId='ema_genehmigt_stamp';
		    $formDiv.="<span style='display:".$display_sec[$elementId].";'>";
		    $formDiv.="<input $disabledIfEMAGenehmigt ".$edit_sec[$elementId]." class='datepicker' type='text' size='40' id='ema_genehmigt_stamp' value='".$obsah."' />";
		    $formDiv.="</span>";
		    $formDiv.="</td>";
		    $formDiv.="</tr>";

		    // dauftr positionen generieren
		    $formDiv.="<tr>";
		    $formDiv.="<td colspan='2' class=''>";
		    $elementId='ema_dauftr_generieren';
		    if($ir['ema_genehmigt']==0){
			$elementId='ema_dauftr_generieren';
			$formDiv.="<span style='display:".$display_sec[$elementId].";width:99%;'>";
			$formDiv.="<input acturl='./emaDauftrGenerieren.php' type='button' id='ema_dauftr_generieren' value='DAuftrpositionen - Vorschau' />";
			$formDiv.="</span>";
		    }
		    $formDiv.="</td>";
		    $formDiv.="</tr>";

		    $formDiv.="</table>";
		    $formDiv.="</fieldset>";

	$formDiv.="</td>";
	$formDiv.="</tr>";
	$formDiv.="</table>";
	$formDiv.="</fieldset>";
	    
	}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------	
	$formDiv.= "</div>";
    }


echo json_encode(array(
                            'id'=>$id,
			    'imaid'=>$imaid,
			    'teil'=>$teil,
			    'imaInfoArray'=>$imaInfoArray,
			    'emaAnlagenArray'=>$emaAnlagenArray,
			    'formDiv'=>$formDiv,
    ));
?>
