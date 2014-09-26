<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    
    $imarray = trim($_POST['imarray']);
    $imarray_g = trim($_POST['imarray_g']);
    $imarray_a = trim($_POST['imarray_a']);
    $imarray_gem = trim($_POST['imarray_gem']);
    
    $palarray = trim($_POST['palarray']);
    $palarray_g = trim($_POST['palarray_g']);
    $palarray_a = trim($_POST['palarray_a']);
    $palarray_gem = trim($_POST['palarray_gem']);
    
    $genehmigt = $_GET['genehmigt'];
    $anforderung = $_GET['anforderung'];
    $ma = $_GET['ma'];

    
    //test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
    $e =  substr($id, strrpos($id, '_')+1);
    $editSuffix = '';
    if($e=='e') 
	$editSuffix = 'e';
    if ($e == 'gen')
	$editSuffix = 'gen';
    if ($e == 'anf')
	$editSuffix = 'anf';
    if ($e == 'gem')
	$editSuffix = 'gem';

    
    $palArrayBox = NULL;
    $palArrayBoxRaw = $palarray;
    $palArrayBoxRaw_a = $palarray_a;
    $palArrayBox_g = NULL;
    $palArrayBox_a = NULL;
    $palArrayBox_gem = NULL;

    
    if(strlen($palarray)>0) $palArrayBox = split (';', $palarray);
    if(strlen($palarray_g)>0) $palArrayBox_g = split (';', $palarray_g);
    if(strlen($palarray_a)>0) $palArrayBox_a = split (';', $palarray_a);
    if(strlen($palarray_gem)>0) $palArrayBox_gem = split (';', $palarray_gem);
    
    $imArrayBox = NULL;
    if(strlen($imarray)>0) $imArrayBox = split (';', $imarray);
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

    $palArray = array();
    if($imArrayBox!==NULL){
	foreach ($imArrayBox as $im){
	    $palArray1 = $apl->getPaletteMitAuftragTeil('', $im, $teil, TRUE);
	    if($palArray1!==NULL){
		foreach ($palArray1 as $p){
		    array_push($palArray, $p);
		}
	    }
	    else{
	    }
	}
    }

if (($anforderung==0) && ($ma=='ema')) {
    $formDiv.="<div id='imaselectpalform'>";
    if ($palArrayBoxRaw_a !== NULL) {
	foreach ($palArrayBox_a as $pal) {
	    $formDiv.="<div>";
	    $p = sprintf("%04d", $pal);
	    $formDiv.=$p;
	    $checked = '';
	    if ($palArrayBox_gem !== NULL) {
		if (in_array($pal, $palArrayBox_gem))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selpal" . $editSuffix . "_" . $pal . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";
}
    
if (($anforderung==1) && ($ma=='ema')) {
    $formDiv.="<div id='imaselectpalform'>";
    if ($palArrayBox !== NULL) {
	foreach ($palArrayBox as $pal) {
	    $formDiv.="<div>";
	    $p = sprintf("%04d", $pal);
	    $formDiv.=$p;
	    $checked = '';
	    if ($palArrayBox_a !== NULL) {
		if (in_array($pal, $palArrayBox_a))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selpal" . $editSuffix . "_" . $pal . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";
}

if (($genehmigt == 1)&&($anforderung==0) && ($ma=='ima')) {
    $formDiv.="<div id='imaselectpalform'>";
    if ($palArrayBox !== NULL) {
	foreach ($palArrayBox as $pal) {
	    $formDiv.="<div>";
	    $p = sprintf("%04d", $pal);
	    $formDiv.=$p;
	    $checked = '';
	    if ($palArrayBox_g !== NULL) {
		if (in_array($pal, $palArrayBox_g))
		    $checked = 'checked';
	    }
	    else {
		$checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selpal" . $editSuffix . "_" . $pal . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";
}

if(($anforderung==1) && ($ma=='ima')){
    $formDiv.="<div id='imaselectpalform'>";
    if ($palArray !== NULL) {
	foreach ($palArray as $pal) {
	    $formDiv.="<div>";
	    $p = sprintf("%6d - %04d", $pal['auftragsnr'], $pal['pal']);
	    $formDiv.=$p;
	    $checked = '';
	    if ($palArrayBox !== NULL) {
		if (in_array($pal['pal'], $palArrayBox))
		    $checked = 'checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selpal" . $editSuffix . "_" . $pal['pal'] . "' />";
	    $formDiv.="</div>";
	}
    }
    $formDiv.="</div>";
}


$returnArray = array(
	'e'=>$e,
	'ar'=>$ar,
	'id'=>$id,
	'ŧp'=>$tp,
	'teil'=>$teil,
	'imarray'=>$imarray,
	'palArray'=>$palArray,
	'user'=>$user,
	'formDiv'=>$formDiv,
	'imarraybox'=>$imArrayBox,
	'palarraybox'=>$palArrayBox,
	'palarraybox_a'=>$palArrayBox_a,
	'palarraybox_g'=>$palArrayBox_g,
	'palarrayboxraw'=>$palArrayBoxRaw,
        'palarrayboxraw_a'=>$palArrayBoxRaw_a,
    );
    echo json_encode($returnArray);

?>

