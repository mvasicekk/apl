<?php
//pri prvnim otervreni seznamu souboru
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];
    $att=$_GET['att'];

    $apl = AplDB::getInstance();

    
    $att2FolderArray = array(
	"ppa"=>"030",
	"vpa"=>"050",
	"rekl"=>"060",
    );
    
    
    
    $kunde = $apl->getKundeFromTeil($teil);
    $kundeGdatPath = $apl->getKundeGdatPath($kunde);
    $ppaDir='';
//    $vpaDir = '/mnt/gdat/Dat/';
    $gdatPath = "/mnt/gdat/Dat/";
    // seznam dilu
    if ($kundeGdatPath !== NULL) {
	$ppaDir = $gdatPath . $kundeGdatPath . "/" . $teil . "/" . AplDB::$DIRS_FOR_TEIL[$att2FolderArray[$att]];
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

    
    $formDiv = "<div id='dokuform'>";
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
    
    echo json_encode(array(
                            'id'=>$id,
			    'teil'=>$teil,
			    'docsArray'=>$docsArray,
			    'att'=>$att,
			    'ppaDir'=>$ppaDir,
			    'formDiv'=>$formDiv,
    ));
?>
