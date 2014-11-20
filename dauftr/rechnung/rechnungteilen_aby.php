<?
require_once '../../security.php';
require_once '../../db.php';

    $id=$_POST['id'];
    $abgnr_von=$_POST['abgnr_von'];
    $abgnr_bis=$_POST['abgnr_bis'];
    $rechnr_regular=$_POST['rechnr_regular'];
    $rechnr_ma=$_POST['rechnr_ma'];

    $apl = AplDB::getInstance();

    // upravim hodnotu ma_rechnr v hlavicce zakazky
    $apl->updateMARechnr($rechnr_regular, $rechnr_ma);

    //upravim rechnr_druck v tabulce drech
    $ar = $apl->markMARechnung($rechnr_regular,$rechnr_ma,$abgnr_von,$abgnr_bis);

    echo json_encode(array(
            'id'=>$id,
            'abgnr_von'=>$abgnr_von,
            'abgnr_bis'=>$abgnr_bis,
            'rechnr_regular'=>$rechnr_regular,
            'rechnr_ma'=>$rechnr_ma,
            'ar'=>$ar,
        ));
?>
