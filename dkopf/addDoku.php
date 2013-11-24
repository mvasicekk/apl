<?
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $n_doku_nr = $_POST['n_doku_nr'];
    $n_einlag_datum = $_POST['n_einlag_datum'];
    $n_musterplatz = $_POST['n_musterplatz'];
    $n_freigabe_am = $_POST['n_freigabe_am'];
    $n_freigabe_vom = $_POST['n_freigabe_vom'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $n_einlag_datum = $apl->make_DB_datum($apl->validateDatum($n_einlag_datum));
    $n_freigabe_am = $apl->make_DB_datum($apl->validateDatum($n_freigabe_am));
	
    $ar = $apl->addTeilDokument($teil,$n_doku_nr,$n_einlag_datum,$n_freigabe_am,$n_freigabe_vom,$n_musterplatz);
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'teil'=>$teil,
	'n_doku_nr'=>$n_doku_nr,
	'n_einlag_datum'=>$n_einlag_datum,
	'n_musterplatz'=>$n_musterplatz,
	'n_freigabe_am'=>$n_freigabe_am,
	'n_freigabe_vom'=>$n_freigabe_vom,
    );
    echo json_encode($returnArray);

?>

