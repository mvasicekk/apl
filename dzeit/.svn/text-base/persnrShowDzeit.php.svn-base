<?php
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

$a = AplDB::getInstance();

$bCopyVerbToAnw = $a->getCopyVzAbyToVerbFlag($persnr);
$verbDatum = 0;
if($bCopyVerbToAnw)
    $verbDatum = $a->getVerbPersNrDatum($persnr,$a->make_DB_datum ($datum));

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

       $dny = array("Ne","Po","Ut","St","Ct","Pa","So");
       $regelTransportPreis = AplDB::getInstance()->getRegelTransportPreis($persnr);
       $anwesenheitArray = AplDB::getInstance()->getAnwesenheitArray($persnr, $von, $bis);
       $kfzInfoArray = array();
       array_push($kfzInfoArray, array('id'=>0,'fahrzeug'=>'-'));
       $kfzInfoArray1 = AplDB::getInstance()->getKfzInfoArray();
       foreach ($kfzInfoArray1 as $kf1) array_push ($kfzInfoArray, $kf1);

        $divcontent = '';
        $divcontent .= "<div id='persnrdzeit'>";
        $divcontent.="<table>";
        $divcontent.="<tr><th>Datum</th><th>OE</th><th>Stunden</th><th>Pause1</th><th>Pause2</th><th>Essen</th><th>Essen J/N</th>";
        $datumDBOld = '';

        if($anwesenheitArray!=NULL){
            $dateOld='';$citac=0;
            foreach ($anwesenheitArray as $anwesenheit){
                
                $date = AplDB::getInstance()->make_DB_datum($anwesenheit['datum']);
                
                if ($date != $dateOld) {
                    $dateOld = $date;
                    if ($citac > 0) {
                        //odradkovani po zmene datumu
                        $divcontent.="<tr style='height:2px;'><td colspan='12' style='padding:0px;background-color:white;height=2px;'><hr></td></tr>";
                    }
                }

                $divcontent.="<tr id='anwesenheitrow_".$anwesenheit['id']."'>";
                    // zjistim den v tydnu
                    
                    $denVTydnu = date('N',strtotime($date));
                    $divcontent.="<td style='font-size:12px;'><strong>".$anwesenheit['datum']." (".$dny[$denVTydnu].")</strong></td>";
                    //------------------------------------------------------------------------------------------------------------
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
                    //------------------------------------------------------------------------------------------------------------
                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_stunden_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['stunden'],1)."' readonly='readonly' /></td>";
                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_pause1_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['pause1'],2)."' readonly='readonly' /></td>";
                    $divcontent.="<td><input style='text-align: right' size='4' maxlength='5' id='anwesenheit_pause2_".$anwesenheit['id']."' type='text' value='".number_format($anwesenheit['pause2'],2)."' readonly='readonly' /></td>";
                    //------------------------------------------------------------------------------------------------------------
                    $essenSelect = "<select id='anwesenheit_essentyp_".$anwesenheit['id']."' acturl='./anwesenheitEssenTypUpdate.php'>";
                    $essenInfoArray = AplDB::getInstance()->getEssenInfoArray();
                    $flag_noselected = TRUE;
                    foreach ($essenInfoArray as $essenInfo){
                        if(!strcmp($essenInfo['id'],$anwesenheit['id_essen'])){
                            $essenSelect.="<option selected='selected' value='".$essenInfo['id']."'>".$essenInfo['essen_kz']."</option>";
                            $flag_noselected = FALSE;
                        }
                        else{
                            if(($flag_noselected) && ($essenInfo['id']==2))
                                $essenSelect.="<option selected='selected' value='".$essenInfo['id']."'>".$essenInfo['essen_kz']."</option>";
                            else
                                $essenSelect.="<option value='".$essenInfo['id']."'>".$essenInfo['essen_kz']."</option>";
                        }
                    }
                    $essenSelect.= "</select>";
                    $divcontent.="<td>".$essenSelect."</td>";
                    //------------------------------------------------------------------------------------------------------------
                    $checkedEssen = $anwesenheit['essen']==0?'':"checked='checked'";
                    $divcontent.="<td><input id='anwesenheit_essen_".$anwesenheit['id']."' type='checkbox' value='' $checkedEssen acturl='./anwesenheitEssenUpdate.php' /></td>";
                    //------------------------------------------------------------------------------------------------------------
                    $datumDB = AplDB::getInstance()->make_DB_datum($anwesenheit['datum']);
                    if($datumDB!=$datumDBOld){
                        // radek s transportem zobrazim jen pri zmene datumu
                        $transportDatumPersnrArray = AplDB::getInstance()->getTransportArrayDatumPersnr($datumDB,$persnr);
                        $divcontent .= "<td>";
                        $pocetJizd = 0;
                        if ($transportDatumPersnrArray !== NULL) {
                            foreach ($transportDatumPersnrArray as $tdp) {
                                $idtr = $tdp['id'];
                                $preis = $tdp['preis'];
                                $kfzId = $tdp['kfz'];
                                $kfzSelect = "<select id='transport_kfz_id_$idtr' acturl='./anwesenheitTransportKfzUpdate.php'>";
                                
                                foreach ($kfzInfoArray as $kfzInfo) {
                                    if ($kfzInfo['id'] == $kfzId)
                                        $kfzSelect.="<option selected='selected' value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                                    else
                                        $kfzSelect.="<option value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                                }
                                $kfzSelect.= "</select>";
                                $kfzPreis = "<input size='2' maxlength='5' id='transport_preis_$idtr' type='text' value='$preis' acturl='./anwesenheitTransportPreisUpdate.php' />";
                                $divcontent .="<span id='anwesenheit_transport_row_$idtr'>$kfzSelect $kfzPreis</span>";
                                $pocetJizd++;
                            }
                        }
                        for($i=0;$i<(2-$pocetJizd);$i++){
                                $kfzSelect = "<select id='transport_newkfz_id_".$anwesenheit['datum']."_$i' acturl='./anwesenheitNewTransport.php?persnr=".$persnr."'>";
                                foreach ($kfzInfoArray as $kfzInfo) {
                                    if ($kfzInfo['id'] == 0)
                                        $kfzSelect.="<option selected='selected' value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                                    else
                                        $kfzSelect.="<option value='" . $kfzInfo['id'] . "'>" . $kfzInfo['fahrzeug'] . "</option>";
                                }
                                $kfzSelect.= "</select>";
                                $kfzPreis = "<input size='2' maxlength='5' id='transport_newpreis_$i' type='text' value='$regelTransportPreis' acturl='' />";
                                $divcontent .="<span id='anwesenheit_newtransport_row_$i'>$kfzSelect $kfzPreis</span>";
                        }
                        $divcontent .= "</td>";
                        $datumDBOld = $datumDB;
                    }
                    else
                        $divcontent .= "<td>&nbsp;</td>";
                    $divcontent.="<td><input type='button' value='-' id='anwesenheit_delete_".$anwesenheit['id']."' acturl='./anwesenheitDeleteId.php' /></td>";
                $divcontent.="</tr>";
                $citac++;
            }
        }
        $divcontent.="</table>";
        $divcontent .= "</div>";


 $value = array('verbDatum'=>$verbDatum,'copyVerb'=>$bCopyVerbToAnw,'a'=>5,'b'=>'asdas','persnr'=>$persnr,'id'=>$id,'datum'=>$datum,'divcontent'=>$divcontent);
 echo json_encode($value);
?>
