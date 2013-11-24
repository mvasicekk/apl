<?
require_once '../db.php';

    $apl = AplDB::getInstance();

    $id = $_POST['id'];
    $invnummer = $_POST['invnummer'];
    $persnr_reparatur = $_POST['persnr_reparatur'];
    $persnr_ma = $_POST['persnr_ma'];
    $datum = $apl->validateDatum($_POST['datum']);
    $datumDB = NULL;
    $inputOK = FALSE;
    $reparaturKopfArray = NULL;
    $reparaturPositionen = NULL;
    $reparaturPositionenDiv = '';

    if($datum!==NULL){
        $datumDB = $apl->make_DB_datum($datum);
    }

    //pokud budu mit zadane vsechny potrebne informace zkusim zjistit, zda uz mam opravu zadanou nebo zda to bude nova oprava
    if((strlen($invnummer)>0) && (strlen($persnr_ma)>0) && (strlen($persnr_reparatur)>0) && ($datumDB!==NULL)){
        $inputOK = TRUE;
        $reparaturKopfArray = $apl->getReparaturKopfArray($invnummer,$datumDB,$persnr_ma,$persnr_reparatur);

        // pokud uz oprava existuje, zkusim vytahnout mozne nahradni dily i s poctama pouzityma prip oprave
        if($reparaturKopfArray!==NULL){
            $reparaturID = $reparaturKopfArray[0]['id'];
            $reparaturPositionen = $apl->getReparaturPositionenArray($reparaturID);
            if($reparaturPositionen!==NULL){
                $reparaturPositionenDiv = "<table border='1'>";
                $reparaturPositionenDiv.= "<tr>";
                $reparaturPositionenDiv.= "<th>ET-Typ</th>";
                $reparaturPositionenDiv.= "<th>ETNr</th>";
                $reparaturPositionenDiv.= "<th>Name</th>";
                $reparaturPositionenDiv.= "<th>Anzahl benutzt</th>";
                $reparaturPositionenDiv.= "<th>ET - Alt</th>";
                $reparaturPositionenDiv.= "</tr>";
                foreach($reparaturPositionen as $repPos){
                    $reparaturPositionenDiv.= "<tr>";
                    $reparaturPositionenDiv.= "<td>".$repPos['et_typ']."</td>";
                    $reparaturPositionenDiv.= "<td>".$repPos['artnr']."</td>";
                    $reparaturPositionenDiv.= "<td>".$repPos['name1'].' - '.$repPos['name2']."</td>";
                    $anzahlInput = "<input acturl='./reparaturPosAnzahlUpdate.php' type='text' size='3' maxlength='6' id='etpos_".$reparaturID."_".$repPos['artnr']."' value='".$repPos['anzahl']."' style='text-align:right;'/>";
                    $reparaturPositionenDiv.= "<td style='text-align:right;'>".$anzahlInput."</td>";
                    $checkedAttr = $repPos['et_alt']!=0?"checked='checked'":"";
                    $etAlt = "<input acturl='./reparaturPosAltUpdate.php' $checkedAttr type='checkbox' id='etalt_" . $reparaturID . "_" . $repPos['artnr'] . "' />";
                    $reparaturPositionenDiv.= "<td style=''>" . $etAlt . "</td>";
                    $reparaturPositionenDiv.= "</tr>";
                }
                $reparaturPositionenDiv.= "</table>";
            }
        }
    }

    $returnArray = array(
        'id'=>$id,
        'invnummer'=>$invnummer,
        'datumDB'=>$datumDB,
        'persnr_ma'=>$persnr_ma,
        'persnr_reparatur'=>$persnr_reparatur,
        'inputOK'=>$inputOK,
        'reparaturKopfArray'=>$reparaturKopfArray,
        'reparaturPositionen'=>$reparaturPositionen,
        'reparaturPositionenDiv'=>$reparaturPositionenDiv,
    );

    echo json_encode($returnArray);

?>
