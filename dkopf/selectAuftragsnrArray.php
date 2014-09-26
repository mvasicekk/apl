<?

session_start();
?>

<?

require_once '../fns_dotazy.php';
require_once '../db.php';

$id = $_POST['id'];
$teil = $_POST['teil'];
$imarray = trim($_POST['imarray']);
$imarray_g = trim($_POST['imarray_g']);
$imarray_a = trim($_POST['imarray_a']);
$imarray_gem = trim($_POST['imarray_gem']);
$genehmigt = $_GET['genehmigt'];
$anforderung = $_GET['anforderung'];
$ma = $_GET['ma'];


//test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
$e = substr($id, strrpos($id, '_') + 1);
$editSuffix = '';
if ($e == 'e')
    $editSuffix = 'e';
if ($e == 'gen')
    $editSuffix = 'gen';
if ($e == 'anf')
    $editSuffix = 'anf';
if ($e == 'gem')
    $editSuffix = 'gem';

$imArrayBox = NULL;
$imArrayBoxRaw = $imarray;
$imArrayBoxRaw_a = $imarray_a;
$imArrayBox_g = NULL;
$imArrayBox_a = NULL;
$imArrayBox_gem = NULL;

if (strlen($imarray) > 0)
    $imArrayBox = split(';', $imarray);
if (strlen($imarray_g) > 0)
    $imArrayBox_g = split(';', $imarray_g);
if (strlen($imarray_a) > 0)
    $imArrayBox_a = split(';', $imarray_a);
if (strlen($imarray_gem) > 0)
    $imArrayBox_gem = split(';', $imarray_gem);

// schvalena ema ************************************************************
if(($anforderung==0) && ($ma=='ema')){
    $formDiv.="<div id='imaselectimform'>";
    if ($imArrayBox_a !== NULL) {
	foreach ($imArrayBox_a as $im) {
	    $formDiv.="<div>";
	    $formDiv.=$im;
	    $checked = '';
	    if ($imArrayBox_gem !== NULL) {
		if (in_array($im, $imArrayBox_gem))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selim" . $editSuffix . "_" . $im . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";    
}
// pozadavek na ema ************************************************************
if(($anforderung==1) && ($ma=='ema')){
    $formDiv.="<div id='imaselectimform'>";
    if ($imArrayBox !== NULL) {
	foreach ($imArrayBox as $im) {
	    $formDiv.="<div>";
	    $formDiv.=$im;
	    $checked = '';
	    if ($imArrayBox_a !== NULL) {
		if (in_array($im, $imArrayBox_a))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selim" . $editSuffix . "_" . $im . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";    
}
// ima genehmigt
//******************************************************************************
if (($genehmigt == 1)&&($anforderung==0) && ($ma=='ima')) {
    $formDiv.="<div id='imaselectimform'>";
    if ($imArrayBox !== NULL) {
	foreach ($imArrayBox as $im) {
	    $formDiv.="<div>";
	    $formDiv.=$im;
	    $checked = '';
	    if ($imArrayBox_g !== NULL) {
		if (in_array($im, $imArrayBox_g))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selim" . $editSuffix . "_" . $im . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";    
} 
// navrh na ima
if(($anforderung==1) && ($ma=='ima')) {
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

    $imArray = $apl->getImporteMitTeil($teil, '', TRUE);

    $formDiv.="<div id='imaselectimform'>";
    if ($imArray !== NULL) {
	foreach ($imArray as $im) {
	    $formDiv.="<div>";
	    $formDiv.=$im['auftragsnr'];
	    $checked = '';
	    if ($imArrayBox !== NULL) {
		if (in_array($im['auftragsnr'], $imArrayBox))
		    $checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selim" . $editSuffix . "_" . $im['auftragsnr'] . "' />";
	    $formDiv.="</div>";
	}
    }

    $formDiv.="</div>";
}
$returnArray = array(
    'e' => $e,
    'ar' => $ar,
    'id' => $id,
    'teil' => $teil,
    'imarray' => $imArray,
    'user' => $user,
    'formDiv' => $formDiv,
    'imarraybox' => $imArrayBox,
    'imarrayboxraw' => $imArrayBoxRaw,
    'imarrayboxraw_a' => $imArrayBoxRaw_a,
    'imarraybox_g' => $imArrayBox_g,
    'imarraybox_a' => $imArrayBox_a,
    'imarraybox_gem' => $imArrayBox_gem,
    'imarray_gem'=>$imarray_gem,
    'genehmigt' => $genehmigt,
    'ma'=>$ma,
    'anforderung'=>$anforderung,
);
echo json_encode($returnArray);
?>