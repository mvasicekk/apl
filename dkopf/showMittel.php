<?
session_start();
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];

    $apl = AplDB::getInstance();

    // security
    $puser = $_SESSION['user'];
    $elementId='n_mittel_add';
    $display_sec[$elementId] = $apl->getDisplaySec('mittel',$elementId,$puser)?'table-row':'none';
    $edit_sec[$elementId] = $apl->getPrivilegeSec('mittel',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
//    
    $elementsArray = array("i_mittel_del");
    foreach ($elementsArray as $elementId) {
	$display_sec[$elementId] = $apl->getDisplaySec('mittel',$elementId,$puser)?'inline-block':'none';
	$edit_sec[$elementId] = $apl->getPrivilegeSec('mittel',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
    }
//
//    
    $formDiv = "<div id='mittelform'>";
    $formDiv.="<div class='closebutton' id='closebutton_mittelform'>X</div>";
    // radek pro pridani noveho dokumentu
    $formDiv.="<table id='dokutable'>";
    $formDiv.="<tr style='background-color:#eef;'>";
    $formDiv.="<th colspan='2' style='text-align:left;'>Arbmittel</th>";
    $formDiv.="<th style='text-align:left;'>tatnr</th>";
    $formDiv.="<th style='text-align:left;'>soubor</th>";
    $formDiv.="<th>&nbsp;</th>";
    $formDiv.="</tr>";
//    
    $formDiv.="<tr style='display:".$display_sec['n_mittel_add'].";background-color:#eef;'>";

    $formDiv.="<td colspan='2' style='text-align:left;'>";
    $formDiv.="<input style='text-align:left;' type='text' id='n_mittel_nr' size='20' maxlength='32' />";
    $formDiv.="</td>";
//
    $formDiv.="<td style='text-align:left;'>";
    $tatArrayForTeil = $apl->getTeilTatArray($teil);
    $formDiv.="<select id='n_abgnr'>";
    if($tatArrayForTeil!==NULL){
	foreach ($tatArrayForTeil as $t){
	    $formDiv.="<option value='".$t['abgnr']."'>".$t['abgnr']."</option>";
	}
    }
    $formDiv.="</select>";
    $formDiv.="</td>";
//
    $formDiv.="<td>";
//    $formDiv.="<a id='add_mittel_file' href='#'>addfile</a>";
    $formDiv.="&nbsp;";
    $formDiv.="</td>";
    
    $formDiv.="<td>";
    $formDiv.="<input type='button' acturl='./addArbMittel.php' id='n_mittel_add' value='+' />";
    $formDiv.="</td>";
//
    $formDiv.="</tr>";

    // seznam jiz prirazenych prostredku
    $teilMittelArray = $apl->getTeilMittelArray($teil);
    if ($teilMittelArray !== NULL) {
	$cisloRadku=0;
	foreach ($teilMittelArray as $tm){
	    if($cisloRadku%2==0)
		$rowStyle = "class='sudy'";
	    else
		$rowStyle = "class='lichy'";
	
	    $formDiv.="<tr $rowStyle>";

	    $formDiv.="<td>";
	    $formDiv.=$tm['nazev'];
	    $formDiv.="</td>";
	    
	    $formDiv.="<td>";
	    $formDiv.=$tm['poznamka'];
	    $formDiv.="</td>";
	    
	    $formDiv.="<td style='text-align:right;'>";
	    $formDiv.=$tm['abgnr'];
	    $formDiv.="</td>";

	    $fileLink = '';
	    $dir = $apl->getArbMittelAnlagenFullPath();
	    
	    $fileName = $tm['nazev'].".pdf";
	    $filePath = $dir."/".$fileName;
	    $urlPath = "/gdat/".$apl->getArbMittelAnlagenPath()."/".$fileName;
	    if(file_exists($filePath)){
		$fileLink = "<a target='_blank' href='$urlPath'>".$fileName."</a>";
	    }
	    
	    $formDiv.="<td>";
	    $formDiv.=$fileLink;
	    $formDiv.="</td>";
	    
	    $formDiv.="<td>";
	    $formDiv.="<input style='display:".$display_sec['i_mittel_del'].";' type='button' acturl='./delMittel.php' id='i_mittel_del_".$tm['id']."' value='-' />";
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
			    'teilMittelArray'=>$teilMittelArray,
			    'formDiv'=>$formDiv,
    ));
?>
