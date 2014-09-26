<?

require_once '../db.php';

$apl = AplDB::getInstance();

$puser = 'zh';
$ip = $_SERVER["REMOTE_ADDR"];

echo "user:$puser<br>";
echo "ip  :$ip<br>";
$allowed = $apl->getPrivilegeSec('dkopf', 'gew_sec', $puser, 'schreiben');

echo "<pre>";
var_dump($allowed);
echo "</pre>";
