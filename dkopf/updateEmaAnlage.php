<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $value = $_POST['value'];
    $ima = $_POST['ima'];

    $filename = substr($id, strpos($id, '_')+1);
    
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

    $anlagenArrayStr = '';
    $imaArray = $apl->getIMAInfoArrayFromImaNr($ima);
    if($imaArray!==NULL){
	$imaRow = $imaArray[0];
	$anlagenArrayStr = $imaRow['ema_anlagen_array'];
	$imaid = $imaRow['id'];
    }
    
    $anlagenArray = array();
    $saveAnlagenArray = array();
    
    if(strlen($anlagenArrayStr)>0){
	$anlagenArray = split(';', $anlagenArrayStr);
    }
    
    if($value==0){
	// odebrat polozku z pole
	foreach ($anlagenArray as $file){
	    if($filename!=$file){
		array_push($saveAnlagenArray, $file);
	    }
	}
    }
    else{
	// pridat polozku 
	$saveAnlagenArray = $anlagenArray;
	array_push($saveAnlagenArray, $filename);
    }
    
    $saveAnlagenArrayStr = join(';', $saveAnlagenArray);
    
    
    $ar = $apl->updateIMAField('ema_anlagen_array', $saveAnlagenArrayStr, $imaid);
    $returnArray = array(
	'imaArray'=>$imaArray,
	'anlagenArrayStr'=>$anlagenArrayStr,
	'anlagenArray'=>$anlagenArray,
	'saveAnlagenArray'=>$saveAnlagenArray,
	'saveAnlagenArrayStr'=>$saveAnlagenArrayStr,
	'ar'=>$ar,
	'id'=>$id,
	'value'=>$value,
	'ima'=>$ima,
	'imaid'=>$imaid,
	'user'=>$user,
	'filename'=>$filename,
    );
    echo json_encode($returnArray);

?>

