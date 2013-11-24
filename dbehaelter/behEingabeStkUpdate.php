<?
session_start();
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id = $_POST['id'];
    $stk = intval($_POST['value']);
    $im = $_POST['im'];
    $ex = $_POST['ex'];
    $datum = $_POST['datum'];
    $von = $_POST['von'];
    $nach = $_POST['nach'];

    // rozebrat id, abych zjistil zustand_id a platz_id
    // id vypada takto inventur_stk_(zustand_id)_(platz_id)

    $behaelternr = substr($id, strlen('beheingabe_stk')+1, strpos($id, '_', strlen('beheingabe_stk')+1)-strlen('beheingabe_stk_'));
    $zustand_id = substr($id, strlen('beheingabe_stk_')+strlen($behaelternr)+1);
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $datumDB = $apl->make_DB_datum($datum);

    $ident = get_user_pc();
    // update provedu tak, ze smazu puvodni radek/radky s importem/exportem, behaeltrem a zustandem a nahradim ho jednim s novym poctem kusu
    $insertedId = $apl->insertBehaelterBewegungAfterDelete($im,$ex,$behaelternr,$von,$nach,$zustand_id,$datumDB,$stk,$ident);

    echo json_encode(array(
                            'id'=>$id,
                            'behaelternr'=>$behaelternr,
                            'zustand_id'=>$zustand_id,
                            'im'=>$im,
                            'ex'=>$ex,
                            'stk'=>$stk,
                            'datumDB'=>$datumDB,
                            'insertId'=>$insertedId,
                            'ident'=>$ident,
        ));
?>
