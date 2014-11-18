<?
 session_start();
?>
<?php

//pri prvnim otervreni seznamu souboru
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];
    $att=$_GET['att'];

    $apl = AplDB::getInstance();

    $att2FolderArray = AplDB::$ATT2FOLDERARRAY;
    
    $kunde = $apl->getKundeFromTeil($teil);
    $kundeGdatPath = $apl->getKundeGdatPath($kunde);
    $ppaDir='';
    $gdatPath = "/mnt/gdat/Dat/";
    // seznam dilu
    if ($kundeGdatPath !== NULL) {
	$ppaDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teil . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
	$extensions = 'JPG|jpg|pdf|txt';
	$filter = "/.*.($extensions)$/";
	if($att=='rekl'){
	    $extensions = '*';
	    $docsArray = $apl->getFilesForPath($ppaDir,NULL,TRUE);
	}
	else{
	    $docsArray = $apl->getFilesForPath($ppaDir,$filter);
	}
    }
 
    $upDiv="<div id='uploader_$att' folder='$ppaDir'>";
    $upDiv.="<a id='pickfiles' href='javascript:;'>Dateien auswaehlen</a>";
    $upDiv.="<div id='filelist'></div>";
    $upDiv.="</div>";
    
    $puser = $_SESSION['user'];
    $upid = 'uploader_'.$att;
    if($apl->getDisplaySec('dkopf',$upid,$puser)===FALSE) $upDiv='';
    
    //$upDiv.="$puser";
    
    $formDiv = "<div id='dokuform'>";
    $formDiv.="<div class='closebutton' id='closebutton_dokuform'>X</div>";
    $formDiv.=$upDiv;
    $formDiv.="<table id='dokutable'>";
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
	    //$formDiv.= $text;

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
	    // konec tabulky
	    
    }
}
$formDiv.="</table>";
$formDiv.= "</div>";
    
if($docsArray===NULL){
    $formDiv = "<div id='dokuform'>";
    $formDiv.=$upDiv;
    $formDiv.= "<div class='nodocsinfo'>keine Dateien / žádné soubory k dispozici</div>";
    $formDiv.= "</div>";
}
    
    echo json_encode(array(
                            'id'=>$id,
			    'teil'=>$teil,
			    'docsArray'=>$docsArray,
			    'att'=>$att,
			    'ppaDir'=>$ppaDir,
			    'formDiv'=>$formDiv,
    ));
?>
