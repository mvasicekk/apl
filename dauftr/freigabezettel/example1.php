<?php
require_once '../../security.php';
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------

$persnr = $_POST['persnr'];
$id = $_POST['id'];
$datum = $_POST['datum'];

$jahr = substr($datum, 6);
$monat = substr($datum, 3, 2);

        if($jahr==NULL || $monat==NULL) {
            $aktualMonth = date('m');
            $aktualJahr = date('Y');
        }
        else {
            $aktualMonth = $monat;
            $aktualJahr = $jahr;
        }

       $von = sprintf("%04d-%02d-%02d",$aktualJahr,$aktualMonth,1);
       $bis = sprintf("%04d-%02d-%02d",$aktualJahr,$aktualMonth,cal_days_in_month(CAL_GREGORIAN, $aktualMonth, $aktualJahr));

       $anwesenheitArray = AplDB::getInstance()->getAnwesenheitArray($persnr, $von, $bis);


        $divcontent = '';
        $divcontent .= "<div id='persnrdzeit'>";
        $divcontent.="<table>";
        $divcontent.="<tr><th>Datum</th><th>OE</th><th>Stunden</th><th>Pause1</th><th>Pause2</th>";
        if($anwesenheitArray!=NULL){
            foreach ($anwesenheitArray as $anwesenheit){
                $divcontent.="<tr id='anwesenheitrow_".$anwesenheit['id']."'>";
                    $divcontent.="<td>".$anwesenheit['datum']."</td>";

                    $oeSelect = "<select id='anwesenheit_oe_".$anwesenheit['id']."' acturl='./anwesenheitOEUpdate.php'>";
                    $oeInfoArray = AplDB::getInstance()->getOEInfoArray();
                    $flag_noselected = TRUE;
                    foreach ($oeInfoArray as $oeInfo){

                        if(!strcmp($oeInfo['tat'],$anwesenheit['oe'])){
                            $oeSelect.="<option selected='selected' value='".$oeInfo['tat']."'>".$oeInfo['tat']."</option>";
                            $flag_noselected = FALSE;
                        }
                        else{
                            $oeSelect.="<option value='".$oeInfo['tat']."'>".$oeInfo['tat']."</option>";
                        }
                    }
                    $oeSelect.= "</select>";
                    $divcontent.="<td>".$oeSelect."</td>";

                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_stunden_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['stunden'],1)."' readonly='readonly' /></td>";
                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_pause1_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['pause1'],2)."' readonly='readonly' /></td>";
                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_pause2_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['pause2'],2)."' readonly='readonly' /></td>";
                    $divcontent.="<td><input type='button' value='-' id='anwesenheit_delete_".$anwesenheit['id']."' acturl='./anwesenheitDeleteId.php' /></td>";
                $divcontent.="</tr>";
            }
        }
        $divcontent.="</table>";
        $divcontent .= "</div>";


 $value = array('a'=>5,'b'=>'asdas','persnr'=>$persnr,'id'=>$id,'datum'=>$datum,'divcontent'=>$divcontent);
 echo json_encode($value);
?>
