<?
session_start();
require_once '../fns_dotazy.php';
require_once '../db.php';

$id = $_POST['id'];
$value = trim($_POST['value']);
$bemerkung_g = trim($_POST['bemerkung_g']);
$ch = intval($_POST['ch']);
if(strstr($id, 'ima2ema')!==FALSE){
    $ch = 2;
}
$imaid = $_POST['imaid'];
$nicht = $_GET['nicht'];
$ma = $_GET['ma'];


// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$apl = AplDB::getInstance();
$ar = 0;
$user = get_user_pc();

$recipients = $apl->getRecipientsArray(
	array(2,3,6),
	array(
	    "jr@abydos.cz",
	    "hl@abydos.cz",
	    "rk@abydos.cz",
	    "gu@abydos.cz"),
	array(
	    "bb@abydos.cz",
	    "rb@abydos.cz",
	    "ok@abydos.cz",
	    "ne@abydos.cz")
	);


if ($ma == 'ema') {

    if (($ch == 1)) {
	$valgenehmigt = $nicht == 0 ? 1 : -1;
	$arF = $apl->updateIMAField('ema_genehmigt', $valgenehmigt, $imaid);

	$imaInfoArray = $apl->getIMAInfoArray($imaid);
	$ir = $imaInfoArray[0];
	$imanr = $ir['imanr'];
	$emanr = $ir['emanr'];
//	$stkAntrag = $apl->getIMAStkForIMANrNew($imanr);
//	$stkGenehmigt = $apl->getIMAStkGenehmigtForIMANrNew($imanr);

	$styleGenehmigt = $nicht == 0 ? "style='background-color:#8f8;'" : "style='background-color:#f88;'";
	if ($nicht == 0)
	    $subject = "$emanr byla schvalena uzivatelem $user";
	else
	    $subject = "$emanr byla zamitnuta uzivatelem $user";

	if ($nicht == 0)
	    $message = "<h3 $styleGenehmigt><b>$emanr</b> byla schvalena.</h3>";
	else
	    $message = "<h3 $styleGenehmigt><b>$emanr</b> byla zamitnuta.</h3>";

	$message.=" Teil : " . $ir['teil'] . "<hr>";
	$message.=" <h4>IMA pozadavek ($imanr)</h4>";
	$message.=" poznamka : " . $ir['bemerkung'] . "<br>";
	$message.=" Importy : " . $ir['auftragsnrarray'] . "<br>";
	$message.=" Palety : " . $ir['palarray'] . "<br>";
//	$message.=" ks : $stkAntrag<br>";
	$message.=" operace/VzAby : " . $ir['tatundzeitarray'] . "<br>";
	$message.=" uzivatel : " . $ir['imavon'] . "<hr>";

	if ($nicht == 0) {
	    $message.=" <h4>EMA byla schválena</h4>";
	    $message.=" poznamka : " . $ir['ema_genehmigt_bemerkung'] . "<br>";
	    $message.=" Importy : " . $ir['ema_auftragsarray_genehmigt'] . "<br>";
	    $message.=" Palety : " . $ir['ema_palarray_genehmigt'] . "<br>";
//	    $message.=" ks : $stkGenehmigt<br>";
	    $message.=" operace/VzAby : " . $ir['ema_tatundzeitarray_genehmigt'] . "<br>";
	    $message.=" schvalil : " . $user . "<br>";
	} else {
	    $message.=" <h4>EMA byla zamítnuta</h4>";
	    $message.=" poznamka : " . $ir['ema_genehmigt_bemerkung'] . "<br>";
	    $message.=" zamítnul : " . $user . "<br>";
	}

	$message.="<hr>odeslano na : " . join(',', $recipients) . "<br>";
//	$message.="<hr>$recipientsStr<br>";

	$headers = "From: <apl_ima@abydos.cz>\r\n";
	$headers = "Content-Type: text/html; charset=UTF-8\r\n";

	foreach ($recipients as $recipient) {
	    @mail($recipient, $subject, $message, $headers);
	}
    }
} else {
    if (($ch == 1)) {
	$valgenehmigt = $nicht == 0 ? 1 : -1;
	$arF = $apl->updateIMAField('ima_genehmigt', $valgenehmigt, $imaid);
	$arB = $apl->updateIMAField('ima_genehmigt_bemerkung', $bemerkung_g, $imaid);
	$arU = $apl->updateIMAField('ima_genehmigt_user', $user, $imaid);
	$arS = $apl->updateIMAField('ima_genehmigt_stamp', date('Y-m-d H:i:s'), $imaid);


	$imaInfoArray = $apl->getIMAInfoArray($imaid);
	$ir = $imaInfoArray[0];
	$imanr = $ir['imanr'];
	$stkAntrag = $apl->getIMAStkForIMANrNew($imanr);
	$stkGenehmigt = $apl->getIMAStkGenehmigtForIMANrNew($imanr);

	$styleGenehmigt = $nicht == 0 ? "style='background-color:#8f8;'" : "style='background-color:#f88;'";
	if ($nicht == 0)
	    $subject = "$imanr byla schvalena uzivatelem $user";
	else
	    $subject = "$imanr byla zamitnuta uzivatelem $user";

	if ($nicht == 0)
	    $message = "<h3 $styleGenehmigt><b>$imanr</b> byla schválena.</h3>";
	else
	    $message = "<h3 $styleGenehmigt><b>$imanr</b> byla zamítnuta.</h3>";

	$message.=" Teil : " . $ir['teil'] . "<hr>";
	$message.=" <h4>IMA pozadavek</h4>";
	$message.=" poznamka : " . $ir['bemerkung'] . "<br>";
	$message.=" Importy : " . $ir['auftragsnrarray'] . "<br>";
	$message.=" Palety : " . $ir['palarray'] . "<br>";
	$message.=" ks : $stkAntrag<br>";
	$message.=" operace/VzAby : " . $ir['tatundzeitarray'] . "<br>";
	$message.=" uzivatel : " . $ir['imavon'] . "<hr>";

	if ($nicht == 0) {
	    $message.=" <h4>IMA byla schválena</h4>";
	    $message.=" poznamka : $bemerkung_g<br>";
	    $message.=" Importy : " . $ir['ima_auftragsnrarray_genehmigt'] . "<br>";
	    $message.=" Palety : " . $ir['ima_palarray_genehmigt'] . "<br>";
	    $message.=" ks : $stkGenehmigt<br>";
	    $message.=" operace/VzAby : " . $ir['ima_tatundzeitarray_genehmigt'] . "<br>";
	    $message.=" schvalil : " . $user . "<br>";
	} else {
	    $message.=" <h4>IMA byla zamitnuta</h4>";
	    $message.=" poznamka : $bemerkung_g<br>";
	    $message.=" byla zamitnuty : " . $user . "<br>";
	}

	$message.="<hr>odeslano na : " . join(',', $recipients) . "<br>";

	$headers = "From: <apl_ima@abydos.cz>\r\n";
	$headers = "Content-Type: text/html; charset=UTF-8\r\n";

	foreach ($recipients as $recipient) {
	    @mail($recipient, $subject, $message, $headers);
	}
    }
    if($ch ==2){
	//stisknuto ima2ema
	$imaInfoArray = $apl->getIMAInfoArray($imaid);
	$ir = $imaInfoArray[0];
	$imanr = $ir['imanr'];
	//ima bude opet otevrena
	$arF = $apl->updateIMAField('ima_genehmigt', 0, $imaid);
	//k povolovaci poznamce pridam info o tom, kdo imu opet uvolnil
	$d = date('Y-m-d H:i:s');
	$gbemerk = $bemerkung_g." - IMA->EMA von $user am $d";
	$arB = $apl->updateIMAField('ima_genehmigt_bemerkung',$gbemerk , $imaid);
	// vztvoreni pozic v auftragu
    }
}

$returnArray = array(
    'ar' => $ar,
    'id' => $id,
    'imaid' => $imaid,
    'value' => $value,
    'user' => $user,
    'arF' => $arF,
    'arB' => $arB,
    'arU' => $arU,
    'arS' => $arS,
    'recipients' => $recipients,
    'imanr' => $imanr,
    'imaInfoArray' => $imaInfoArray,
    'ma' => $ma,
    'nicht' => $nicht,
    'ch'=>$ch,
    'gbemerk'=>$gbemerk,
);
echo json_encode($returnArray);
?>