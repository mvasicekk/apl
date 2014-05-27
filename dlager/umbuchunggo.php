<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id = $_POST['id'];
    $teil = $_POST['teil'];
    $im = $_POST['im'];
    $pal = $_POST['pal'];
    $gut = intval($_POST['gut_stk']);
    $auss = intval($_POST['auss_stk']);
    $von = $_POST['lager_von'];
    $nach = $_POST['lager_nach'];

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $ar = $apl->insertDlagerBew($teil, $im, $pal, $gut, $auss, $von, $nach, $user);
    
    $returnArray = array(
	'id'=>$id,
	'teil'=>$teil,
	'im'=>$im,
	'pal'=>$pal,
	'gut'=>$gut,
	'auss'=>$auss,
	'von'=>$von,
	'nach'=>$nach,
	'user'=>$user,
    );
    echo json_encode($returnArray);

?>

