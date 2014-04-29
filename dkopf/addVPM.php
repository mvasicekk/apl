<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    
    $n_vpm_nr= $_POST['n_vpm_nr'];
    $n_anzahl= intval($_POST['n_anzahl']);
    $n_bemerkung= trim($_POST['n_bemerkung']);

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $ar = $apl->addTeilVPM($teil,$n_vpm_nr,$n_anzahl,$n_bemerkung,$user);
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'teil'=>$teil,
	'n_vpm_nr'=>$n_vpm_nr,
	'n_anzahl'=>$n_anzahl,
	'n_bemerkung'=>$n_bemerkung,
    );
    echo json_encode($returnArray);

?>

