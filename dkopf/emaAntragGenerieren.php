<?
session_start();
require_once '../db.php';

    $id = $_POST['id'];
    $imanr = $_POST['ima'];

    $apl = AplDB::getInstance();
    
    $user = $apl->get_user_pc();
    $username = substr($user, strrpos($user, '/')+1);
    
    $userInfo = $apl->getUserInfoArray($username);
    $realname='';
    if($userInfo!==NULL)
	$realname=$userInfo['realname'];
    
    $imaArray = $apl->getIMAInfoArrayFromImaNr($imanr);
    if($imaArray!==NULL){
	$imaRow = $imaArray[0];
	$imaid = $imaRow['id'];
    }

    $apl->updateIMAField('ema_antrag_vom', $realname, $imaid);
    $apl->updateIMAField('ema_antrag_am', date('Y-m-d H:i:s'), $imaid);
    
    echo json_encode(array(
                            'id'=>$id,
			    'imanr'=>$imanr,
			    'imaid'=>$imaid,
			    'user'=>$user,
			    'username'=>$username,
			    'realname'=>$realname,
    ));
?>
