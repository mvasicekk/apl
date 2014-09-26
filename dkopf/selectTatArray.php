<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $imarray = trim($_POST['imarray']);
    $palarray = trim($_POST['palarray']);
    $tatarray = trim($_POST['tatarray']);
    $tatarray_g = trim($_POST['tatarray_g']);
    $tatarray_a = trim($_POST['tatarray_a']);
    $tatarray_gem = trim($_POST['tatarray_gem']);
    
    $genehmigt = $_GET['genehmigt'];
    $anforderung = $_GET['anforderung'];
    $ma = $_GET['ma'];
    
    //test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
    $e =  substr($id, strrpos($id, '_')+1);
    $editSuffix = '';
    if($e=='e') $editSuffix = 'e';
    if($e=='gen') $editSuffix = 'gen';
    if($e=='anf') $editSuffix = 'anf';
    if($e=='gem') $editSuffix = 'gem';

    
    $tatArrayBox = NULL;
    $tatArrayBoxRaw = $tatarray;
    $tatArrayBoxRaw_a = $tatarray_a;
    $tatArrayBoxRawVzKd = "";
    $tatArrayBox_g = NULL;
    $tatArrayBox_a = NULL;
    $tatArrayBox_gem = NULL;
    
    $tatnrArray = array();
    $tatnrArray_g = array();
    $tatnrArray_a = array();
    $tatnrArray_gem = array();
    
    
    $tatBoxArray = array();
    $tatBoxArray_g = array();
    $tatBoxArray_a = array();
    $tatBoxArray_gem = array();
    
    if(strlen($tatarray)>0) $tatArrayBox = split (';', $tatarray);
    if($tatArrayBox!==NULL){
	foreach ($tatArrayBox as $tA){
	    list($tnr,$vzaby) = split(':',$tA);
	    array_push($tatnrArray, $tnr);
	    $vzaby=floatval(strtr($vzaby,',','.'));
	    $tatBoxArray[$tnr]=  $vzaby;
	    $tatArrayBoxRawVzKd.="$tnr:$vzaby:$vzaby".";";
	}
    }
    
    if(strlen($tatArrayBoxRawVzKd)>0) $tatArrayBoxRawVzKd = substr ($tatArrayBoxRawVzKd, 0, strlen($tatArrayBoxRawVzKd)-1);
    
    if(strlen($tatarray_g)>0) $tatArrayBox_g = split (';', $tatarray_g);
    if($tatArrayBox_g!==NULL){
	foreach ($tatArrayBox_g as $tA){
	    list($tnr,$vzaby) = split(':',$tA);
	    array_push($tatnrArray_g, $tnr);
	    $tatBoxArray_g[$tnr]=  floatval(strtr($vzaby,',','.'));
	}
    }
    
    if(strlen($tatarray_a)>0) $tatArrayBox_a = split (';', $tatarray_a);
    if($tatArrayBox_a!==NULL){
	foreach ($tatArrayBox_a as $tA){
	    list($tnr,$vzaby,$vzkd) = split(':',$tA);
	    array_push($tatnrArray_a, $tnr);
	    $tatBoxArray_a[$tnr]=  array(floatval(strtr($vzaby,',','.')),floatval(strtr($vzkd,',','.')));
	}
    }

    if(strlen($tatarray_gem)>0) $tatArrayBox_gem = split (';', $tatarray_gem);
    if($tatArrayBox_gem!==NULL){
	foreach ($tatArrayBox_gem as $tA){
	    list($tnr,$vzaby,$vzkd) = split(':',$tA);
	    array_push($tatnrArray_gem, $tnr);
	    $tatBoxArray_gem[$tnr]=  array(floatval(strtr($vzaby,',','.')),floatval(strtr($vzkd,',','.')));
	}
    }

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $tatArray = $apl->getTatInfoArray(2000, 4999);
    $tatArray_a = $apl->getTatInfoArray(2000, 2999);
    $tatArray_gem = $apl->getTatInfoArray(2000, 2999);

if (($anforderung==0) && ($ma=='ema')) {
    $formDiv.="<div id='imaselecttatform'>";
    if ($tatArray_a !== NULL) {
	$formDiv.="<table>";
	$formDiv.="<tr>";
	$formDiv.="<td colspan='2'>tatnr</td><td>vzaby</td><td>vzkd</td>";
	$formDiv.="</tr>";
	foreach ($tatArray_gem as $tat) {
	    $formDiv.="<tr>";
	    $formDiv.="<td>";
	    $p = sprintf("%d - %s", $tat['tatnr'], $tat['tatbez']);
	    $formDiv.=$p;
	    $formDiv.="</td>";
	    $checked = '';
	    if($tatArrayBox_gem !== NULL){
		if (in_array($tat['tatnr'], $tatnrArray_gem)){
		    $checked = 'checked';
		    $vzAbyValue = floatval($tatBoxArray_gem[$tat['tatnr']][0]);
		    $vzKdValue = floatval($tatBoxArray_gem[$tat['tatnr']][1]);
		}
		else{
		    $vzAbyValue = 0;//floatval($tatBoxArray_a[$tat['tatnr'][0]]);
		    $vzKdValue = 0;//floatval($tatBoxArray_a[$tat['tatnr'][1]]);
		}
	    }
	    else {
		if ($tatnrArray_a !== NULL) {
		    if (in_array($tat['tatnr'], $tatnrArray_a))
			$checked = 'checked';
		    $vzAbyValue = floatval($tatBoxArray_a[$tat['tatnr']][0]);
		    $vzKdValue = floatval($tatBoxArray_a[$tat['tatnr']][1]);
		}
	    }
	    $formDiv.="<td>";
	    $formDiv.="<input $checked type='checkbox' id='seltat" . $editSuffix . "_" . $tat['tatnr'] . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzaby" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzAbyValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzkd" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzKdValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	}
	$formDiv.="</table>";
    }
    $formDiv.="</div>";
}
    
if (($anforderung==1) && ($ma=='ema')) {
    $formDiv.="<div id='imaselecttatform'>";
    if ($tatArray_a !== NULL) {
	$formDiv.="<table>";
	$formDiv.="<tr>";
	$formDiv.="<td colspan='2'>tatnr</td><td>vzaby</td><td>vzkd</td>";
	$formDiv.="</tr>";
	foreach ($tatArray_a as $tat) {
	    $formDiv.="<tr>";
	    $formDiv.="<td>";
	    $p = sprintf("%d - %s", $tat['tatnr'], $tat['tatbez']);
	    $formDiv.=$p;
	    $formDiv.="</td>";
	    $checked = '';
	    if($tatArrayBox_a !== NULL){
		if (in_array($tat['tatnr'], $tatnrArray_a)){
		    $checked = 'checked';
		    $vzAbyValue = floatval($tatBoxArray_a[$tat['tatnr']][0]);
		    $vzKdValue = floatval($tatBoxArray_a[$tat['tatnr']][1]);
		}
		else{
		    $vzAbyValue = 0;//floatval($tatBoxArray_a[$tat['tatnr'][0]]);
		    $vzKdValue = 0;//floatval($tatBoxArray_a[$tat['tatnr'][1]]);
		}
	    }
	    else {
		if ($tatnrArray !== NULL) {
		    if (in_array($tat['tatnr'], $tatnrArray))
			$checked = 'checked';
		    $vzAbyValue = floatval($tatBoxArray[$tat['tatnr']]);
		    $vzKdValue = $vzAbyValue;
		}
	    }
	    $formDiv.="<td>";
	    $formDiv.="<input $checked type='checkbox' id='seltat" . $editSuffix . "_" . $tat['tatnr'] . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzaby" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzAbyValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzkd" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzKdValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	}
	$formDiv.="</table>";
    }
    $formDiv.="</div>";
}
    
if (($genehmigt == 1)&&($anforderung==0) && ($ma=='ima')) {
    $formDiv.="<div id='imaselecttatform'>";
    if ($tatArray !== NULL) {
	$formDiv.="<table>";
	foreach ($tatArray as $tat) {
	    $formDiv.="<tr>";
	    $formDiv.="<td>";
	    $p = sprintf("%d - %s", $tat['tatnr'], $tat['tatbez']);
	    $formDiv.=$p;
	    $formDiv.="</td>";
	    $checked = '';
	    if($tatArrayBox_g !== NULL){
		if (in_array($tat['tatnr'], $tatnrArray_g))
		    $checked = 'checked';
		$vzAbyValue = floatval($tatBoxArray_g[$tat['tatnr']]);
	    }
	    else {
		if ($tatnrArray !== NULL) {
		    if (in_array($tat['tatnr'], $tatnrArray))
			$checked = 'checked';
		    $vzAbyValue = floatval($tatBoxArray[$tat['tatnr']]);
		}
	    }
	    $formDiv.="<td>";
	    $formDiv.="<input $checked type='checkbox' id='seltat" . $editSuffix . "_" . $tat['tatnr'] . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzaby" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzAbyValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	}
	$formDiv.="</table>";
    }
    $formDiv.="</div>";
}


if(($anforderung==1) && ($ma=='ima')){
    $formDiv.="<div id='imaselecttatform'>";
    if ($tatArray !== NULL) {
	$formDiv.="<table>";
	foreach ($tatArray as $tat) {
	    $formDiv.="<tr>";
	    $formDiv.="<td>";
	    $p = sprintf("%d - %s", $tat['tatnr'], $tat['tatbez']);
	    $formDiv.=$p;
	    $formDiv.="</td>";
	    $checked = '';
	    if ($tatnrArray !== NULL) {
		if (in_array($tat['tatnr'], $tatnrArray))
		    $checked = 'checked';
		$vzAbyValue = floatval($tatBoxArray[$tat['tatnr']]);
	    }
	    $formDiv.="<td>";
	    $formDiv.="<input $checked type='checkbox' id='seltat" . $editSuffix . "_" . $tat['tatnr'] . "' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzaby" . $editSuffix . "" . "_" . $tat['tatnr'] . "' value='" . $vzAbyValue . "' />";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	}
	$formDiv.="</table>";
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
	'palarray'=>$palarray,
	'tatarray'=>$tatarray,
	'tatArray'=>$tatArray,
	'user'=>$user,
	'formDiv'=>$formDiv,
	'imarraybox'=>$imArrayBox,
	'palarraybox'=>$palArrayBox,
	'tatarraybox'=>$tatArrayBox,
	'tatArrayBox_a'=>$tatArrayBox_a,
	'tatBoxArray'=>$tatBoxArray,
	'tatBoxArray_a'=>$tatBoxArray_a,
	'tatnrArray'=>$tatnrArray,
	'tatnrArray_a'=>$tatnrArray_a,
	'tatarrayboxraw'=>$tatArrayBoxRaw,
	'tatarrayboxraw_a'=>$tatArrayBoxRaw_a,
	'tatarrayboxrawvzkd'=>$tatArrayBoxRawVzKd,
	'tatArray_a'=>$tatArray_a,
    );
    echo json_encode($returnArray);

?>

