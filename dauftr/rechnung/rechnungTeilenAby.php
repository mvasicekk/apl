<?
require_once '../../security.php';
require_once '../../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$apl = AplDB::getInstance();
$abgnr_von=$o->abgnr_von;
$abgnr_bis=$o->abgnr_bis;

$rechnr_regular=$o->rechnr_regular;
$rechnr_ma=$o->rechnr_ma;

    // upravim hodnotu ma_rechnr v hlavicce zakazky
    $apl->updateMARechnr($rechnr_regular, $rechnr_ma);

    //upravim rechnr_druck v tabulce drech
    $ar = $apl->markMARechnung($rechnr_regular,$rechnr_ma,$abgnr_von,$abgnr_bis);

    echo json_encode(array(
            'abgnr_von'=>$abgnr_von,
            'abgnr_bis'=>$abgnr_bis,
            'rechnr_regular'=>$rechnr_regular,
            'rechnr_ma'=>$rechnr_ma,
            'ar'=>$ar,
        ));
?>
