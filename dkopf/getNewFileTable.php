<?

require_once '../db.php';

$id = $_POST['id'];
$teil = $_POST['teil'];
$ppaDir = $_POST['ppaDir'];
$imaid = $_POST['imaid'];

$apl = AplDB::getInstance();

//vzgenerovat novou tabulku se souborama k IMA
$imaInfoArray = $apl->getIMAInfoArray($imaid);
$emaAnlagenArray = array();
    
if ($imaInfoArray !== NULL) {
    $ir = $imaInfoArray[0];
    $emaAnlagenStr = $ir['ema_anlagen_array'];
    if(strlen($emaAnlagenStr)>0){
        $emaAnlagenArray = explode(';', $emaAnlagenStr);
    }
}

$extensions = 'JPG|jpg|pdf|txt';
$filter = "/.*.($extensions)$/";
$docsArray = $apl->getFilesForPath($ppaDir, $filter);
//tabulka se seznamem souboru
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
	$trclass = $i++% 2 == 0 ? 'sudy' : 'lichy';
	$typeclass = $doc['type'];
	$filetypeclass = $doc['ext'];
	$checkBoxId = 'anlage_'.$doc['filename'];
	if($typeclass=='file') $target = "_blank";
	$formDiv.="<tr class='$trclass'>";
	$fN = $doc['filename'];
	if($filetypeclass=="JPG")
	    $text = $doc['filename']; //"<img src='".$doc['url']."' width='50'>".$doc['filename'];
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
	if($filetypeclass=='JPG')
	    $formDiv.="<td class='filetableitem' style='text-align:center;'>" . "<input acturl='./updateEmaAnlage.php' id='$checkBoxId' type='checkbox' $checked>" . "</td>";
	else
	    $formDiv.="<td class='filetableitem' style='text-align:center;'></td>";

	$formDiv.="</tr>";
    }
}


echo json_encode(array(
'id' => $id,
 'imaid' => $imaid,
 'teil' => $teil,
 'imaInfoArray' => $imaInfoArray,
 'formDiv' => $formDiv,
));
?>
