<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $imarray = trim($_POST['imarray']);

    //test pokud ma id koncovku _e musim pridat tuto koncovku i k ostatnim id-ckum
    $e =  substr($id, strrpos($id, '_')+1);
    $editSuffix = '';
    if($e=='e') $editSuffix = 'e';
    
    $imArrayBox = NULL;
    if(strlen($imarray)>0) $imArrayBox = split (';', $imarray);
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    
    $imArray = $apl->getImporteMitTeil($teil, '',TRUE);
    
    $formDiv.="<div id='imaselectimform'>";
    if($imArray!==NULL){
	foreach ($imArray as $im){
	    $formDiv.="<div>";
	    $formDiv.=$im['auftragsnr'];
	    $checked = '';
	    if($imArrayBox!==NULL){
		if(in_array($im['auftragsnr'],$imArrayBox)) $checked='checked';
	    }
	    $formDiv.="<input $checked type='checkbox' id='selim".$editSuffix."_".$im['auftragsnr']."' />";
	    $formDiv.="</div>";
	}
    }
    
    $formDiv.="</div>";
    $returnArray = array(
	'e'=>$e,
	'ar'=>$ar,
	'id'=>$id,
	'teil'=>$teil,
	'imarray'=>$imArray,
	'user'=>$user,
	'formDiv'=>$formDiv,
	'imarraybox'=>$imArrayBox,
    );
    echo json_encode($returnArray);

?>

