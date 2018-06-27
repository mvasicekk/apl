<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $lautteil = $_POST['lautteil'];
    $user = get_user_pc();
    
    $apl = AplDB::getInstance();
    
    $teilVPMArray = $apl->getTeilVPMArray($lautteil);
    $counter = 0;
    if($teilVPMArray!==NULL){
	foreach ($teilVPMArray as $vpm){
	    $n_vpm_nr= $vpm['verp'];
	    $n_anzahl= intval($vpm['verp_stk']);
	    $n_bemerkung= trim($vpm['bemerkung'])." (laut $lautteil)";
	    $ar = $apl->addTeilVPM($teil,$n_vpm_nr,$n_anzahl,$n_bemerkung,$user);
	    if($ar>0){
		$counter++;
	    }
	}
    }
    

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    
    $ar = 0;
    
    //$ar = $apl->addTeilVPM($teil,$n_vpm_nr,$n_anzahl,$n_bemerkung,$user);
    
    $returnArray = array(
	'ar'=>$counter,
	'id'=>$id,
	'teil'=>$teil,
	'n_vpm_nr'=>$n_vpm_nr,
	'n_anzahl'=>$n_anzahl,
	'n_bemerkung'=>$n_bemerkung,
    );
    echo json_encode($returnArray);

?>

