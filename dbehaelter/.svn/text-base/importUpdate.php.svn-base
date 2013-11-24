<?
require_once '../db.php';

function getStk($bewArray,$behnr,$zustand){
    if($bewArray===NULL) return 0;
    foreach($bewArray as $bew){
        if($bew['behaelternr']==$behnr && $bew['zustand_id']==$zustand) return $bew['stk'];
    }
    return 0;
}
    $id = $_POST['id'];
    $import = $_POST['value'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $auftragArray = $apl->getAuftragInfoArray($import);

    $bewegungsArray = $apl->getBehaelterBewegungenFuerImEx($import,0);

    $kunde = $auftragArray[0]['kunde'];

    // zjistim seznam behaelteru, kteremaji pro daneho zakaznika zadanou inventuru zustand_id=9999, platz_id='KDKONTO'
    $behaelterKundeArray = $apl->getBehaelterKundeMitInventur($kunde);

    $behZustandArray = $apl->getBehaelterStandArray();

    // pripravit obsah divu pro zobrazeni prehledu behaelteru a poctu kusu
    
    if($behaelterKundeArray!==NULL){
    $beheingabeTableContent = "<table id='behtablecontent'>";
    $beheingabeTableContent.= '<tr>';
    $beheingabeTableContent.= '<th>';
    $beheingabeTableContent.= 'BehaelterNr (letzte Inventur am) / Zustand';
    $beheingabeTableContent.= '</th>';
    foreach($behZustandArray as $behZustand){
        //if($behZustand['zustand_id']>4) break;
        //if($behZustand['zustand_id']==2) continue;
        $beheingabeTableContent.= "<th style='width:160px;'>";
        $beheingabeTableContent.= $behZustand['zustand_id'].'.'.$behZustand['zustand_text'];
        $beheingabeTableContent.= '</th>';
    }
    $beheingabeTableContent.= '</tr>';
    foreach ($behaelterKundeArray as $key => $behaelterKunde) {
        $beheingabeTableContent.= '<tr>';
        $beheingabeTableContent.= '<td>';
        $beheingabeTableContent.= $behaelterKunde['behaelternr'].' ( '.$behaelterKunde['maxinvdatum'].' )';
        $beheingabeTableContent.= '</td>';
        // inputy
        foreach($behZustandArray as $behZustand){
            //if($behZustand['zustand_id']>4) break;
            //if($behZustand['zustand_id']==2) continue;
            $value = getStk($bewegungsArray, $behaelterKunde['behaelternr'], $behZustand['zustand_id']);
            $beheingabeTableContent.= "<th style='width:160px;'>";
            $beheingabeTableContent.= "<input class='entermove' acturl='./behEingabeStkUpdate.php' type='text' size='5' maxlength='6' style='text-align:right;' value='$value' id='beheingabe_stk_".$behaelterKunde['behaelternr'].'_'.$behZustand['zustand_id']."' />";
            $beheingabeTableContent.= '</th>';
        }

        $beheingabeTableContent.= '</tr>';
    }
    $beheingabeTableContent.= '</table>';
    }
    echo json_encode(array(
                            'id'=>$id,
                            'auftragArray'=>$auftragArray,
                            'kunde'=>$kunde,
                            'behArray'=>$behaelterKundeArray,
                            'behtablecontent'=>$beheingabeTableContent,
                            'bewegungsArray'=>$bewegungsArray
        ));

?>
