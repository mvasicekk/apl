<?
require_once '../security.php';
require("../libs/Smarty.class.php");
require_once '../db.php';

$smarty = new Smarty;
$a = AplDB::getInstance();

if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
    require_once '../assignsecurity.php';
} else {
    header("Location: ../index.php");
}

if (isset($_POST['eingeben'])) {
    if (isset($_POST['artikel']) && (strlen(trim($_POST['artikel'])) > 0)) {
	$artikel = $_POST['artikel'];
	$ks = intval($_POST['anzahl']);
	$bemerk = trim($_POST['bemerk']);
	$abdatum = $_POST['abdatum'];
	$anftyp = $_POST['anftyp'];
	$prio = $_POST['prio'];
	
	$user = $a->get_user_pc();
	$abdatDB = $a->make_DB_datum($a->validateDatum($_POST['abdatum']));
	if ($ks > 0) {
	    $insert_id = $a->insertEinkaufAnforderung($artikel,$ks,$bemerk,$abdatDB,$user,$anftyp,$prio);
	    // poslat email
	    
	    // debug info
	    $recipient = "jr@abydos.cz";
	    
	    // seznam prijemcu upravit - vybrat vsechny uzivatele s roli einkauf(?) a tem poslat email
	    
	    $recipient .= ",lv@abydos.cz";
	    $recipient .= ",ko@abydos.cz";
	    $recipient .= ",msu@abydos.cz";
	    
	    
	    $subject = "Einkaufaufforderung von $user ($artikel)";
	    $message = "<h3><b>Einkaufaufforderung</b> wurde erstellt.</h3>";
	    $message.=" Artikel : $artikel<br>";
	    $message.=" Stk : $ks<br>";
	    $message.=" Bemerkung : $bemerk<br>";
	    $message.=" Ab Datum : $abdatum<br>";
	    $message.=" Benutzer : $user<br>";
	
	    $headers = "From: <apl_einkauf@abydos.cz>\n";
	    $headers = "Content-Type: text/html; charset=UTF-8\n";
		
	    @mail($recipient,$subject,$message,$headers);
//	    $_SESSION['flashm'] = "pozadavek odeslan ($artikel,$ks,$bemerk,$abdatum), insertid=$insert_id";
	    $_SESSION['flashm'] = "pozadavek odeslan !";
	} else {
	    $_SESSION['flashm'] = "nic neulozeno, ks=0 !";
	}
    } else {
	$_SESSION['flashm'] = "nic neulozeno, artikel ist leer !";
    }
    unset($_POST);
    if(strlen($abdatum)>0)
	$d = $abdatum;
    else
	$d = date('Y-m-d');
    header("Location: ./anforderung.php?d=$d");
    exit;
}

if (isset($_SESSION['flashm'])){
    $smarty->assign('flashmessage', $_SESSION['flashm']);
    unset($_SESSION['flashm']);
}

if(isset($_GET['d'])){
    $smarty->assign('d', $_GET['d']);
}

//security
$elementsIdArray = $apl->getResourcesForFormId('einkaufaufforderung');
$display_sec = array();
$puser = $_SESSION['user'];
if ($elementsIdArray !== NULL) {
    foreach ($elementsIdArray as $elementId) {
	$display_sec[$elementId] = $apl->getDisplaySec('einkaufaufforderung', $elementId, $puser) ? 'block' : 'none';
    }
}


$smarty->assign("display_sec", $display_sec);


$smarty->display('anforderung.tpl');