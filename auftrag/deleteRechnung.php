<?
session_start();

require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$auftragsnr = $o->params->r->auftragsnr;

// ktery uzivatel chce mazat
$mazac = $a->get_user_pc();
// prekopirovat starou fakturu do zalozni tabulky
$chyba = $a->backupRechnung($auftragsnr, $mazac);
// zjistim lieferdatum a rechnungssatum pro mazanou fakturu
$datumArray = $a->getRechnungDatums($auftragsnr);
// smazat starou fakturu
$smazanoRadku = $a->deleteRechnung($auftragsnr);

// poslat informacni email

$recipient = "jr@abydos.cz,";
$recipient.= "hl@abydos.cz,";
$recipient.= "in@abydos.cz,";
$recipient.= "jk@abydos.cz";

$subject = "Rechnung " . $auftragsnr . " wurde geloescht";
$message = "<h3>Daten fur Rechnung <b>$auftragsnr</b> wurden geloescht.</h3>";
$message .= "<h3>$smazanoRadku<b>Positionen nach drechdeleted kopiert !</b>.</h3>";
$message.="<h3>Rechnungsdatum: " . $datumArray['fertig'] . " Auslieferdatum: " . $datumArray['ausliefer_datum'];

$user = get_user_pc();
$message.= "<br><br>mit freundlichen Gruessen<br>$user";
if (strlen($chyba) > 0){
    $message.= "Error: $chyba";
}

$headers = "From: <apl@abydos.cz>\n";
$headers = "Content-Type: text/html; charset=UTF-8\n";

@mail($recipient, $subject, $message, $headers);

$returnArray = array(
    'auftragsnr' => $auftragsnr,
    'auftragInfo' => $auftragInfo,
    'dauftrPos' => $dauftrPos,
    'mazac' => $mazac,
    'chyba' => $chyba,
    'smazanoRadku' => $smazanoRadku,
);


echo json_encode($returnArray);
