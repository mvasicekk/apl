<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$oh = $o->oh;

$a = AplDB::getInstance();

$u = $_SESSION['user'];
$id = intval($oh->id);
$lockchanged = $o->lockchanged;


if ($lockchanged) {
    $locked = $oh->locked == TRUE ? 1 : 0;
    $sql = "update hodnoceni_osobni set locked='$locked',last_edit='$u' where id='$id'";
    $arLock = $a->query($sql);
} else {
    if ($id > 0) {
	$hodnoceni = floatval($oh->hodnoceni);
	if ($hodnoceni >= 0 && $hodnoceni < 10) {
	    // nejakym zpusobem se spocita castka
	    $vaha = $a->getVahaFromFaktor($oh->id_faktor);
	    $castka = AplDB::hodnoceni2Penize($vaha, $hodnoceni);

	    $sql = "update hodnoceni_osobni set hodnoceni='$hodnoceni',castka='$castka' where id='$id'";
	    $ar = $a->query($sql);
	}
    }
}

$returnArray = array(
    'jm'=>  substr($oh->datum,0,7),
    'castka'=>$castka,
    'ar'=>$ar,
    'oh'=>$oh,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
