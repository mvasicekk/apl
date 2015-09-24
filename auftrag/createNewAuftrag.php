<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$auftragsnr = $o->params->auftragsnr;
$kunde = $o->params->kunde;
$created = FALSE;
$createNewError = "";

// zjistit cenu za minutu pro daneho zakaznika
$sql = "select preismin,`waehr-kz` as waehr from dksd where (kunde='$kunde')";
$rows = $a->getQueryRows($sql);
if ($rows!==NULL) {
    $row = $rows[0];
    $preismin = $row['preismin'];
    $waehrung = $row['waehr'];
    $sql = "insert into daufkopf (auftragsnr,kunde,minpreis,aufdat,im_datum_soll,waehr_kz)";
    $sql.=" values('$auftragsnr','$kunde','$preismin',NOW(),NOW(),'$waehrung')";
    $a->query($sql);
    $created=TRUE;
}
else{
    $createNewError="Kunde nicht in Kundenstamm gefunden / zakaznik nenalezen !";
}

$returnArray = array(
	'created'=>$created,
	'auftragsnr'=>$auftragsnr,
	'kunde'=>$kunde,
	'createError'=>$createNewError,
    );
    
echo json_encode($returnArray);
