<?
require_once '../db.php';

    $kd_von = $_POST['kd_von'];
    $kd_bis = $_POST['kd_bis'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $planyArray = $apl->getPlaene($kd_von,$kd_bis);
    
    foreach ($planyArray as $plan){
	$planyDiv.=$plan['auftragsnr'].",";
    }
    $returnArray = array(
	'kd_von'=>$kd_von,
	'kd_bis'=>$kd_bis,
	'planydiv'=>$planyDiv
    );

    echo json_encode($returnArray);
?>

