<?php
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];

$apl = AplDB::getInstance();


        $kfzFahrtenArray = $apl->getKfzFahrtenArray();

        $dny = array("Ne","Po","Ut","St","Ct","Pa","So","Ne");

        $divcontent = '';
        $divcontent .= "<div id='kfzfahrteninfo'>";
        $divcontent.="<table>";
        $divcontent.="<tr><th>Datum</th><th>KFZ</th><th>Fahrten</th></tr>";
        $divcontent.="<tr>";
        $divcontent.="<td><input type='text' size='10' maxlength='10' id='kfzfahrten_datum' value='".date('d.m.Y')."' /></td>";
        $kfzSelect = "<select id='kfzfahrten_kfz_id'>";
        $kfzInfoArray = $apl->getKfzInfoArray();
        foreach ($kfzInfoArray as $kfzInfo){
            $kfzSelect.="<option value='".$kfzInfo['id']."'>".$kfzInfo['fahrzeug']."</option>";
        }
        $kfzSelect.= "</select>";
        $divcontent.="<td>".$kfzSelect."</td>";
        $divcontent.="<td><input id='kfzfahrten_fahrten' type='text' value='0' size='3' maxlength='5' /></td>";
        $divcontent.="<td><input type='button' value='+' id='kfzfahrten_add' acturl='./kfzFahrten_add.php' /></td>";
        $divcontent.="</tr>";

        $maxPocetRadku = 50;
        $radek = 0;

        if($kfzFahrtenArray!=NULL){
            foreach ($kfzFahrtenArray as $fahrt){
                if($radek++>$maxPocetRadku) break;
                $divcontent.="<tr id='kfzfahrtrow_".$fahrt['id']."'>";
                $idtr = $fahrt['id'];
                // zjistim den v tydnu
                $date = $apl->make_DB_datum($fahrt['datumF']);
                $denVTydnu = date('N',strtotime($date));
                $divcontent.="<td>".$fahrt['datumF']." (".$dny[$denVTydnu].")</td>";
                $kfzSelect = "<select id='kfzfahrten_kfz_id_$idtr' acturl='./kfzFahrtenAnzahlUpdate.php'>";
                foreach ($kfzInfoArray as $kfzInfo) {
                    if ($kfzInfo['id'] == $fahrt['kfz_id'])
                        $kfzSelect.="<option selected='selected' value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                    else
                        $kfzSelect.="<option value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                }
                $kfzSelect.= "</select>";
                $fahrtenAnzahl = "<input size='2' maxlength='5' id='kfzfahrten_anzahl_$idtr' type='text' value='".$fahrt['anzahl_fahrten']."' acturl='./kfzFahrtenAnzahlUpdate.php' />";
                $divcontent .="<td>$kfzSelect</td>";
                $divcontent .="<td>$fahrtenAnzahl</td>";
                $divcontent.="<td><input type='button' value='-' id='kfzfahrten_delete_".$fahrt['id']."' acturl='./kfzFahrtenDeleteId.php' /></td>";
                $divcontent.="</tr>";
            }
        }
        $divcontent.="</table>";
        $divcontent .= "</div>";


 $value = array('id'=>$id,'divcontent'=>$divcontent);
 echo json_encode($value);
?>
