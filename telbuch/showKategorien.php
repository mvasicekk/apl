<?
require_once '../db.php';

    $id = $_GET['id'];

    $apl = AplDB::getInstance();

    $adressId = $id;
    
    $ar=0;
    $katArray = $apl->getAdresyKategorien();
    $adrInKategorienArray = $apl->getAdresyInKategorien($adressId);
    $aInKatA = array();
    if($adrInKategorienArray!==NULL){
	foreach ($adrInKategorienArray as $aik){
	    array_push($aInKatA, $aik['adresy_kategorie_id']);
	}
    }
    
    $formDiv.="<div id='kategoriendiv'>";
    $formDiv.="<table>";
    foreach ($katArray as $kat){
	
	$as = in_array($kat['id'], $aInKatA);
	$checked =  $as===TRUE?"checked='checked'":"";
	
	$formDiv.="<tr>";
//	$formDiv.="<td>kat[id]=".$kat['id'].",aInKatArray=".  implode(',', $aInKatA).",as=$as</td>";
	$formDiv.="<td>";
	$formDiv.="<input type='checkbox' $checked' acturl='./updateAdresyKategorie.php?addressId=".$adressId."' id='kat_".$kat['id']."'/>";
	$formDiv.="</td>";
	$formDiv.="<td>";
	$formDiv.= $kat['kategorie'].'&nbsp;';
	$formDiv.="</td>";
	$formDiv.="</tr>";
    }
    $formDiv.="</table>";
    $formDiv.="</div>";
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'div'=>$formDiv,
			    'ar'=>$ar,
			    'aik'=>$aInKatA,
    ));
?>
