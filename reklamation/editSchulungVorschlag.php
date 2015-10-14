<?

require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$apl = AplDB::getInstance();
$affectedRows = 0;

$field = $o->f;
$value = $o->value;

if ($o->id > 0) {
    $sql = "update dpersschulung set `$field`='$value' where id='" . $o->id . "' limit 1";
    $ar = $apl->query($sql);
}


$returnArray = array(
    "ar" => $ar,
);

echo json_encode($returnArray);
