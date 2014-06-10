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
    
    //test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
    $e =  substr($id, strrpos($id, '_')+1);
    $editSuffix = '';
    if($e=='e') $editSuffix = 'e';

    $palArrayBox = NULL;
    if(strlen($palarray)>0) $palArrayBox = split (';', $palarray);
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
    
    
    $formDiv.="<div id='imaselectpalform'>";
    if($palArray!==NULL){
	foreach ($palArray as $pal){
	    $formDiv.="<div>";
	    $p = sprintf("%6d - %04d",$pal['auftragsnr'],$pal['pal']);
	    $formDiv.=$p;
	    $checked = '';
	    if($palArrayBox!==NULL){
		if(in_array($pal['pal'],$palArrayBox)) $checked='checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selpal".$editSuffix."_".$pal['pal']."' />";
	    $formDiv.="</div>";
	}
    }
    
    $formDiv.="</div>";
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
    );
    echo json_encode($returnArray);

?>

