<?

require_once '../db.php';

$apl = AplDB::getInstance();

$puser = 'ex';
$ppassword = 'ex';
$ip = $_SERVER["REMOTE_ADDR"];

echo "user:$puser<br>";
echo "pass:$ppassword<br>";
echo "ip  :$ip<br>";
$access = $apl->grantAccess($puser, $ppassword, $ip);

echo "<pre>";
var_dump($access);
echo "</pre>";
//$apl->insertAccessLog($puser, $ppassword, $prihlasen, $apl->get_pc_ip());
