<?php
// pro zobrazeni seznamu souboru pomoci ajaxu
require_once '../db.php';

    $url = $_POST['url'];
    $rootPath = $_POST['rootPath'];

    $firstLevel = substr($url, 6);
    
    $apl = AplDB::getInstance();

    $gdatPath = "/mnt/gdat/Dat/";
    
    
    $ppaDir=$gdatPath.$firstLevel;
    $extensions = '*';
    
    $docsArray = $apl->getFilesForPath($ppaDir,NULL,TRUE);
    $formDiv = "<div id='dokuform'>";
    $formDiv.="<table id='dokutable'>";
    $formDiv.="<tr><td style='font-size:x-small;' colspan='5'><input type='button' id='pickfiles' href='javascript:;' value='Dateien auswaehlen' /></td>";
    $formDiv.="<input type='hidden' id='rootPath' value='$rootPath' />";
    $formDiv.="<td style='text-align:right;font-size:x-small;' >"." (".$extensions.")</td></tr>";
    if ($docsArray !== NULL) {
    $formDiv.="<tr>";
    $formDiv.="<td class='filetableheader' style='' colspan='4'>Datei / soubor</td>";
    $formDiv.="<td class='filetableheader' style='width:160px;'>Datum</td>";
    $formDiv.="<td class='filetableheader' style='width:120px;text-align:right;'>Size</td>";

    $formDiv.="</tr>";
    $i = 0;
    foreach ($docsArray as $doc) {
	// pro rootPath nezobrazim prechod o slozku nahoru
	    if((intval($rootPath)==0)&&($doc['filename']=='..')) continue;
	    $trclass = $i++ % 2 == 0 ? 'sudy' : 'lichy';
	    $typeclass = $doc['type'];
	    $filetypeclass = $doc['ext'];
	    if($typeclass=='file') $target="_blank";
	    if($doc['filename']=='..') {
		$typeclass = 'prevdir';
		$formDiv.="<tr class='$trclass'>";
		$formDiv.="<td class='filetableitem' colspan='7'><a acturl='./getFilesTable.php' class='$typeclass' href='" . $doc['url'] . "'>" . $doc['filename'] . "</a></td>";
		$formDiv.="</tr>";
	    }		
	    else{
		$formDiv.="<tr class='$trclass'>";
		$fN = $doc['filename'];
		$formDiv.="<td class='filetableitem' colspan='4'><a target='$target' acturl='./getFilesTable.php' title='$fN' class='$typeclass $filetypeclass' href='" . $doc['url'] . "'>";
		
		if($filetypeclass=="JPG")
		    $text = $doc['filename'];//"<img src='".$doc['url']."' width='50'>".$doc['filename'];
		else
		    $text = $doc['filename'];
		
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
}
$formDiv.="</table>";
$formDiv.= "</div>";
    

    
    echo json_encode(array(
                            'id'=>'folder',
			    'url'=>$url,
			    'docsArray'=>$docsArray,
			    'ppaDir'=>$ppaDir,
			    'formDiv'=>$formDiv,
			    'rootPath'=>intval($rootPath),
    ));
    
    
?>
