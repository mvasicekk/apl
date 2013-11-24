<?
require_once '../db.php';

    $id = $_POST['id'];
    $value = $_POST['value'];
    $dauftrId = intval(substr($id, 0,strpos($id, '_')+1));

    $dbField = NULL;
    $ar = 0;

    if(strstr($id, "abywaage_brutto")){
        $dbField = 'abywaage_brutto';
        $dbValue = round(floatval($value),2);
    }

    if(strstr($id, "stk_laut_waage")){
        $dbField = 'stk_laut_waage';
        $dbValue = intval($value);
    }

    if(strstr($id, "abywaage_behaelter_ist")){
        $dbField = 'abywaage_behaelter_ist';
        $dbValue = round(floatval($value),2);
    }

    if(strstr($id, "abywaage_kg_stk10")){
        $dbField = 'abywaage_kg_stk10';
        $dbValue = round(floatval($value),4);
    }
    if(strstr($id, "kg_stk_bestellung")){
        $dbField = 'kg_stk_bestellung';
        $dbValue = round(floatval($value),4);
    }

    if(strstr($id, "kunde_behaelter_bestellung_netto")){
        $dbField = 'kunde_behaelter_bestellung_netto';
        $dbValue = round(floatval($value),2);
    }

        $stk_laut_waage = 0;
        $ist_kg_netto = 0;
        $soll_kg_brutto = 0;

    if($dbField!=NULL){
        $apl = AplDB::getInstance();
        $ar = $apl->updateDauftrField($dauftrId,$dbField,$dbValue);

        //$newWaageParameters
        $newWaageParameters = $apl->getDauftrWaageParameters($dauftrId);
        if($newWaageParameters!=NULL){
            $abywaageBrutto = floatval($newWaageParameters['abywaage_brutto']);
            $abywaageBehaelterIst = floatval($newWaageParameters['abywaage_behaelter_ist']);
            $abywaageKgStk10 = floatval($newWaageParameters['abywaage_kg_stk10']);
            $expstk = intval($newWaageParameters['stkexp']);
            $kg_stk_bestellung = floatval($newWaageParameters['kg_stk_bestellung']);
            $impstk = intval($newWaageParameters['stkimp']);
            $stk_laut_waage = intval($newWaageParameters['stk_laut_waage']);
        }
        else{
            $stk_laut_waage = 0;
            $abywaageBrutto = 0;
            $abywaageBehaelterIst = 0;
            $abywaageKgStk10 = 0;
            $expstk = 0;
            $kg_stk_bestellung = 0;
            $impstk = 0;
        }

        //$stk_laut_waage = $abywaageKgStk10!=0?floor(($abywaageBrutto-$abywaageBehaelterIst)/$abywaageKgStk10):0;
        $ist_kg_netto = round(($abywaageBrutto-$abywaageBehaelterIst),2);
        //$soll_kg_brutto = round($expstk*round($abywaageKgStk10,4)+$abywaageBehaelterIst,2);
        $soll_kg_brutto = round($stk_laut_waage*round($abywaageKgStk10,4)+$abywaageBehaelterIst,2);
        $kunde_behaelter_bestellung_netto = round($impstk*$kg_stk_bestellung,2);

        // u zadani vahy behaeltru zkusim odhadnout typ behaeltru
        if($dbField=='abywaage_behaelter_ist'){
            $behTypenArray = $apl->getBehaelterTypen($dbValue);
            if($behTypenArray!==NULL)
                $updateBehTyp = $behTypenArray[0]['id'];
            else
                $updateBehTyp = NULL;
        }
        else
            $updateBehTyp = NULL;
    }
    echo json_encode(array(
                            'dauftrid'=>$dauftrId,
                            'affectedrows'=>$ar,
                            'id'=>$id,
                            'newvalue'=>$dbValue,
                            'dbField'=>$dbField,
                            //'stk_laut_waage'=>$stk_laut_waage,
                            'ist_kg_netto'=>$ist_kg_netto,
                            'soll_kg_brutto'=>$soll_kg_brutto,
                            'kunde_behaelter_bestellung_netto'=>$kunde_behaelter_bestellung_netto,
                            'auss'=>0,
                            'updateBehTyp'=>$updateBehTyp,
        ));
?>
