<?php

require_once '../../security.php';
require '../../db.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------

$apl = AplDB::getInstance();


$id = $_POST['id'];
$teil = $_POST['value'];
$export = $_POST['export'];

$kunde = $apl->getKundeFromAuftransnr($export);

//patri dil k danemu zakaznikovi ?
$teilKunde = $apl->getKundeFromTeil($teil);

if ($kunde == $teilKunde) {
    $row = $apl->getVerpackungMenge($teil);

    if ($row != NULL) {
	$teil = $row['teil'];
	$verpackungmenge = intval($row['verpackungmenge']);
    } else {
	$teil = NULL;
	$verpackungmenge = 0;
    }
}


$value = array('id' => $id, 'teil' => $teil, 'verpackungmenge' => $verpackungmenge, 'row' => $row,'teilKunde'=>$teilKunde,'kunde'=>$kunde);
echo json_encode($value);
?>
