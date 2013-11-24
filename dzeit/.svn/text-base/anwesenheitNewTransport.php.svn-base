<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$preis = floatval($_POST['preis']);
$kfzId = $_POST['value'];
$transportId = intval(substr($id, strrpos($id, '_')+1));
$poziceId = strpos($id, 'id_');
$datum = substr($id, $poziceId+3, 10);
$datumDB = AplDB::getInstance()->make_DB_datum($datum);
$persnr = $_GET['persnr'];
//$altPreisId =
$insertedId = AplDB::getInstance()->insertTransport($preis, $kfzId,$datumDB,$persnr);
// vystup promennych do Ajaxu
echo json_encode(array('insertedId'=>$insertedId,'preis'=>$preis,'kfz'=>$kfzId,'datumDB'=>$datumDB,'persnr'=>$persnr,'oldid'=>$id));
?>
