<?php
require '../../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------

$apl = AplDB::getInstance();


$id = $_POST['id'];
$export = $_POST['value'];

$row = $apl->getExDatumSoll($export);

if($row!=NULL){
    $export = $row['export'];
    // pokud je ex_datum_soll = null nastavim ho na aktualni datum
    if($row['ex_datum_soll']==NULL)
        $ex_datum_soll = date('d.m.Y');
    else
        $ex_datum_soll = $row['ex_datum_soll'];
}
else{
    $export = NULL;
    $ex_datum_soll = NULL;
}


$value = array('id'=>$id,'export'=>$export,'ex_datum_soll'=>$ex_datum_soll,'row'=>$row);
echo json_encode($value);
?>
