<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $value = trim($_POST['value']);
    $bemerkung_g = trim($_POST['bemerkung_g']);
    $ch = intval($_POST['ch']);
    $imaid = $_POST['imaid'];
    

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    
    if($ch==1){
	$arF = $apl->updateIMAField('ima_genehmigt',1,$imaid);
	$arB = $apl->updateIMAField('ima_genehmigt_bemerkung',$bemerkung_g,$imaid);
	$arU = $apl->updateIMAField('ima_genehmigt_user',$user,$imaid);
	$arS = $apl->updateIMAField('ima_genehmigt_stamp',date('Y-m-d H:i:s'),$imaid);
	// poslat email gu, + role dispo=3,D-Q-T=16
	$recipients = array();
	array_push($recipients, 'jr@abydos.cz');
	array_push($recipients, 'hl@abydos.cz');
	array_push($recipients, 'rk@abydos.cz');
//	array_push($recipients, 'gu@abydos.cz');
//	$rolesIdArray = array(3,16);
//	foreach ($rolesIdArray as $roleId){
//	    $usersarray = $apl->getUsersForRoleId($roleId);
//	    if($usersarray!==NULL){
//		foreach ($usersarray as $userrow){
//		    $userinfo = $apl->getUserInfoArray($userrow['benutzername']);
//		    if($userinfo!==NULL){
//			$email = trim($userinfo['email']);
//			if(strlen($email)>0){
//			    array_push($recipients, $email);
//			}
//		    }
//		}
//	    }
//	}
	
	$imaInfoArray = $apl->getIMAInfoArray($imaid);
	$ir = $imaInfoArray[0];
	$imanr = $ir['imanr'];
	$stkAntrag = $apl->getIMAStkForIMANr($imanr);
	$stkGenehmigt = $apl->getIMAStkGenehmigtForIMANr($imanr);
	
	$subject = "$imanr genehmigt von $user";
	$message = "<h3><b>$imanr</b> wurde genehmigt.</h3>";
	$message.=" Teil : ".$ir['teil']."<hr>";
	$message.=" <h4>IMA Antrag</h4>";
	$message.=" Bemerkung : ".$ir['bemerkung']."<br>";
	$message.=" Importe : ".$ir['auftragsnrarray']."<br>";
	$message.=" Paletten : ".$ir['palarray']."<br>";
	$message.=" Stk : $stkAntrag<br>";
	$message.=" Taetigkeiten/VzAby : ".$ir['tatundzeitarray']."<br>";
	$message.=" Benutzer : ".$ir['imavon']."<hr>";

	$message.=" <h4>IMA genehmigt</h4>";
	$message.=" Bemerkung : $bemerkung_g<br>";
	$message.=" Importe : ".$ir['ima_auftragsnrarray_genehmigt']."<br>";
	$message.=" Paletten : ".$ir['ima_palarray_genehmigt']."<br>";
	$message.=" Stk : $stkGenehmigt<br>";
	$message.=" Taetigkeiten/VzAby : ".$ir['ima_tatundzeitarray_genehmigt']."<br>";
	$message.=" genehmigt von : ".$user."<br>";
	
	$message.="<hr>gesendet an : ".join(',', $recipients)."<br>";

	$headers = "From: <apl_ima@abydos.cz>\r\n";
	$headers = "Content-Type: text/html; charset=UTF-8\r\n";
		
	foreach ($recipients as $recipient){
	    @mail($recipient,$subject,$message,$headers);
	}
    }
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'imaid'=>$imaid,
	'value'=>$value,
	'user'=>$user,
	'arF'=>$arF,
	'arB'=>$arB,
	'arU'=>$arU,
	'arS'=>$arS,
	'recipients'=>$recipients,
	'imanr'=>$imanr,
	'imaInfoArray'=>$imaInfoArray,
    );
    echo json_encode($returnArray);

?>

