<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $datumHeaderId = $_POST['datumHeaderId'];

//    $imSollDatum = substr($kundeBoxId, strpos($kundeBoxId, '_')+1,10);
//    $kunde = substr($kundeBoxId, strrpos($kundeBoxId, '_')+1);
//    
    $div = "";
    $div.= "<div class='newlkwdiv' id='newlkw_$datumHeaderId'>";
    $div.="<div class='closebutton' id='closebutton_$datumHeaderId'>X</div>";
    $div.="<h4>LKW - Neu</h4>";
    $div.="<table>";
    $div.="<tr>";
    $div.="<td>";
    $div.= "kunde:";
    $div.="</td>";
    $div.="<td>";
    $div.= "$datumHeaderId";
    $div.="</td>";
    $div.="</tr>";
    $div.="</table>";
    //odeslat pozadavek
    $div.= "<input type='button' id='erstellenbutton_$datumHeaderId' acturl='lkwErstellen.php' value='erstellen' />";    
    $div.= "</div>";
    
    
    $returnArray = array(
	'datumHeaderId'=>$datumHeaderId,
	'div'=>$div,
	'divid'=>"newlkw_$datumHeaderId",
    );

    
    echo json_encode($returnArray);
?>

