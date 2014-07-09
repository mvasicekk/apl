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
    $ima_tatarray= trim($_POST['ima_tatarray']);
    $bemerkung= trim($_POST['bemerkung']);

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $auftragsarray=$ima_imarray;
    $palarray = $ima_palarray;
    $tatarray=$ima_tatarray;
    
    $ar = $apl->addTeilIMA($teil,$imanr,$bemerkung,$auftragsarray,$palarray,$tatarray,$user);
    $stk = $apl->getIMAStkForIMANr($imanr);
    
    if($ar>0){
	// pokud je vlozen zadek poslu mail
	$recipient = "jr@abydos.cz,";
        $recipient .= "rk@abydos.cz,";
	$recipient .= "pvo@abydos.cz,";
	$recipient .= "hl@abydos.cz,";
	$recipient .= "pt@abydos.cz,";
	$recipient .= "jga@abydos.cz";
	$subject = "$imanr erstellt von $user";
	$message = "<h3><b>$imanr</b> wurde erstellt.</h3>";
	$message.=" Teil : $teil<br>";
	$message.=" Bemerkung : $bemerkung<br>";
	$message.=" Importe : $auftragsarray<br>";
	$message.=" Paletten : $palarray<br>";
	$message.=" Stk : $stk<br>";
	$message.=" Taetigkeiten/VzAby : $tatarray<br>";
	$message.=" Benutzer : $user<br>";
	
	$headers = "From: <apl_ima@abydos.cz>\n";
	$headers = "Content-Type: text/html; charset=UTF-8\n";
		
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

