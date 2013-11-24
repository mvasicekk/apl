<?
require_once '../db.php';

    $id = $_POST['id'];
    $apl = AplDB::getInstance();

    // vytahnu stavajici poznamku k palete
    
    $dauftrId = substr($id, 6);
    
    $bemerkung = $apl->getPalBemerkung($dauftrId);
    $gId = $apl->getDauftrIdForGPal($dauftrId);
    $dauftrRow = $apl->getDauftrRow($dauftrId);
    $pal = $dauftrRow['pal'];
    
    $div = "<div class='palbemerkungdiv'>";
    $div.= "Pal $pal Bemerkung:<input type=text acturl='./savePalBemerkung.php' value='$bemerkung' size='40' maxlength='255' id='bemerkung_$gId'/>";
    $div.= "</div>";
    
    echo json_encode(array('gid'=>$gId,'tdid'=>$id,'dauftrid'=>$dauftrId,'div'=>$div,'bemerkung'=>$bemerkung));
?>