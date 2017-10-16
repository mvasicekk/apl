<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $imanr= trim($_POST['imanr']);
    $ima_imarray= trim($_POST['ima_imarray']);
    $ima_palarray= trim($_POST['ima_palarray']);
    $ima_dauftrid= trim($_POST['ima_dauftrid']);
    $ima_tatarray= trim($_POST['ima_tatarray']);
    $bemerkung= trim($_POST['bemerkung']);

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $auftragsarray=$ima_imarray;
    $palarray = $ima_palarray;
    $tatarray=$ima_tatarray;
    
    $ar = $apl->addTeilIMA($teil,$imanr,$bemerkung,$auftragsarray,$palarray,$ima_dauftrid,$tatarray,$user);
    $stk = $apl->getIMAStkForIMANrNew($imanr);
    
    if($ar>0){
	// pokud je vlozen zadek poslu mail
	
	$recipients = $apl->getRecipientsArray(
	array(2,3,6),
	array(
//	    "jr@abydos.cz",
	    //"hl@abydos.cz",
	    "rk@abydos.cz",
	    "gu@abydos.cz"),
	array(
	    "hl@abydos.cz",
	    "jr@abydos.cz",
	    "bb@abydos.cz",
	    "rb@abydos.cz",
	    "ok@abydos.cz",
	    "ho@abydos.cz",
	    "ne@abydos.cz")
		,FALSE		//nebrat uzivatele s level=0,2016-09-06
	);
	
	$recipientsStr = join(',', $recipients);
	
//	$recipient = "jr@abydos.cz,";
//        $recipient .= "rk@abydos.cz,";
//	$recipient .= "pvo@abydos.cz,";
//	$recipient .= "hl@abydos.cz,";
//	$recipient .= "pt@abydos.cz,";
//	$recipient .= "jga@abydos.cz";
	$onlyUser = substr($user, strpos($user, '/')+1);
	$subject = "$imanr - $teil vytvorena ( $onlyUser )";
	$message = "<h3><b>$imanr</b> vytvorena.</h3>";
	$message.=" Teil : $teil<br>";
	$message.=" poznamka : $bemerkung<br>";
	$message.=" Importy : $auftragsarray<br>";
	$message.=" Palety : $palarray<br>";
	$message.=" ks : $stk<br>";
	$message.=" operace/VzAby : $tatarray<br>";
	$message.=" vytvoril : $user<br>";
	$message.=" email odeslan na : $recipientsStr<br>";
	
	
	$headers = "From: <apl_ima@abydos.cz>\n";
	$headers = "Content-Type: text/html; charset=UTF-8\n";

	foreach ($recipients as $recipient)
	    @mail($recipient,$subject,$message,$headers);
    }
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'teil'=>$teil,
	'imanr'=>$imanr,
	'bemerkung'=>$bemerkung,
	'user'=>$user,
    );
    echo json_encode($returnArray);

?>

