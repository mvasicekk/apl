<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    
    $n_mittel_nr= $_POST['n_mittel_nr'];
    $n_abgnr= intval($_POST['n_abgnr']);

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $ar = $apl->addTeilMittel($teil,$n_mittel_nr,$n_abgnr,$user);
    
    $id = 'showmittel';
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'teil'=>$teil,
	'n_mittel_nr'=>$n_vpm_nr,
	'n_abgnr'=>$n_abgnr,
    );
    echo json_encode($returnArray);

?>

