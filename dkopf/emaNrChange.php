<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $value = $_POST['value'];
    
    $imaid = substr($id, strrpos($id, '_')+1);
    
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

if (strlen(trim($value)) == 0) {
    $valueOK = TRUE;
    $ar = $apl->updateIMAField('emanr', '', $imaid);
} else {
    $valueOK = FALSE;

    $iA = $apl->getIMAInfoArray($imaid);
    $imanr = $iA[0]['imanr'];
    $kunde = intval(substr($imanr, strpos($imanr, '_') + 1, 3));

    // zkontrolovat format prvni casti ema cisla, musi byt ve tvaru EMA_XXX_YYYY
    $firstPart = sprintf("EMA_%03d_", $kunde);
    if (substr($value, 0, 8) == $firstPart)
	$valueOK = TRUE;

    //z posledni casti vytvorim cislo
    $lastPartInt = intval(substr($value, strrpos($value, '_') + 1));
    if ($lastPartInt == 0)
	$valueOK = FALSE;

    if ($valueOK) {
	$emaNrNew = sprintf("EMA_%03d_%04d", $kunde, $lastPartInt);
	// zkontrolovat, jestli uz takove cislo nemam v databazi
	$r = $apl->getIMAInfoArray(NULL, $emaNrNew);
	if ($r === NULL) {
	    $ar = $apl->updateIMAField('emanr', $emaNrNew, $imaid);
	} else {
	    $valueOK = FALSE;
	}
    }
}

$returnArray = array(
	'id'=>$id,
	'imaid'=>$imaid,
	'imanr'=>$imanr,
	'kunde'=>$kunde,
	'lastEmaNr'=>$lastEmaNr,
	'emaNrNew'=>$emaNrNew,
	'value'=>$value,
	'newValue'=>$newValue,
	'user'=>$user,
	'valueOK'=>$valueOK,
	'lastPartInt'=>$lastPartInt,
	'ar'=>$ar,
    );
    echo json_encode($returnArray);

?>

