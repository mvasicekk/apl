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
    
    //test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
    $e =  substr($id, strrpos($id, '_')+1);
    $editSuffix = '';
    if($e=='e') $editSuffix = 'e';

    
    $tatArrayBox = NULL;
    $tatnrArray = array();
    $tatBoxArray = array();
    
    if(strlen($tatarray)>0) $tatArrayBox = split (';', $tatarray);
    if($tatArrayBox!==NULL){
	foreach ($tatArrayBox as $tA){
	    list($tnr,$vzaby) = split(':',$tA);
	    array_push($tatnrArray, $tnr);
	    $tatBoxArray[$tnr]=  floatval(strtr($vzaby,',','.'));
	}
    }
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    
    $tatArray = $apl->getTatInfoArray(2000,4999);
    
    $formDiv.="<div id='imaselecttatform'>";
    if($tatArray!==NULL){
	$formDiv.="<table>";
	foreach ($tatArray as $tat){
	    $formDiv.="<tr>";
	    $formDiv.="<td>";
	    $p = sprintf("%d - %s",$tat['tatnr'],$tat['tatbez']);
	    $formDiv.=$p;
	    $formDiv.="</td>";
	    $checked = '';
	    if($tatnrArray!==NULL){
		if(in_array($tat['tatnr'],$tatnrArray)) $checked='checked';
		$vzAbyValue = floatval($tatBoxArray[$tat['tatnr']]);
	    }
	    $formDiv.="<td>";
	    $formDiv.="<input $checked type='checkbox' id='seltat".$editSuffix."_".$tat['tatnr']."' />";
	    $formDiv.="</td>";
	    $formDiv.="<td>";
	    $formDiv.="<input type='input' style='text-align:right;' size='4' id='seltatvzaby".$editSuffix.""."_".$tat['tatnr']."' value='".$vzAbyValue."' />";
	    $formDiv.="</td>";
	    $formDiv.="</tr>";
	}
	$formDiv.="</table>";
    }
    
    $formDiv.="</div>";
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
	'tatBoxArray'=>$tatBoxArray,
	'tatnrArray'=>$tatnrArray,
    );
    echo json_encode($returnArray);

?>

