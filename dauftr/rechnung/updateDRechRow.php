<?

session_start();
require_once '../../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$t = $o->t;


$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

$drechId = intval($t->drech_id);
if ($drechId>0) {
    $value = strip_tags(trim($t->text));
    $dbField = 'Text1';
    $ar = $a->updateDRechField($dbField, $value, $drechId);
}

$returnArray = array(
    'u' => $u,
    'value' => $value,
    'dbfield' => $dbField,
    'drechid' => $drechId,
    't' => $t,
    'ar' => $ar,
);

echo json_encode($returnArray);
