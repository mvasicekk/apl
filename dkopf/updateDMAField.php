<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $imarray = $_POST['imarray'];
    $palarray = $_POST['palarray'];
    $tatarray = $_POST['tatarray'];
    $bemerkungid = $_POST['bemerkungid'];
    

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $field=FALSE;
    if(strstr($id,"selime")){
	$field="auftragsnrarray";
	$value = $imarray;
    }
    if(strstr($id,"selpale")){
	$field="palarray";
	$value = $palarray;
    }
    if(strstr($id,"seltate")){
	$field="tatundzeitarray";
	$value = $tatarray;
    }
    
    $imaid = substr($bemerkungid, strrpos($bemerkungid, '_')+1);
    if($field)
	$ar = $apl->updateIMAField($field,$value,$imaid);
    
    $returnArray = array(
	'field'=>$field,
	'ar'=>$ar,
	'id'=>$id,
	'imaid'=>$imaid,
	'value'=>$value,
	'user'=>$user,
	'imarray'=>$imarray,
	'palarray'=>$palarray,
	'tatarray'=>$tatarray,
	'bemerkungid'=>$bemerkungid,
    );
    echo json_encode($returnArray);

?>

