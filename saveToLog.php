<?
session_start();
require_once './db.php';


$id = $_POST['id'];
$pin = $_POST['pin'];

$a = AplDB::getInstance();
$ar = 0;
$u = $a->get_user_pc();
$login = $_SESSION['user'];

$user = $u."( ".md5($pin).  " )";
$action = "pristup k brane ".$id;

// overit pin
$pinOk = $a->checkUserPIN($login,$pin);

$a->insertToSecurityLog($user,$action);

$returnArray = array(
	'id'=>$id,
	'u'=>$u,
	'action'=>$action,
	'pin'=>$pin,
	'login'=>$login,
	'pinOk'=>$pinOk
    );
    
echo json_encode($returnArray);
