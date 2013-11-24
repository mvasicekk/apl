<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

    $id = $_POST['id'];
    $behaelternr = $_POST['behaelternr'];
    $kunde = $_POST['kunde'];
    $datum = $_POST['datum'];
    $stk = intval($_POST['stk']);
    $bein = $_POST['bein'];
    $bezu = $_POST['bezu'];
    $lagplatz = $_POST['lagplatz'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $ident = get_user_pc();

    $fehler = 0;

    if(strlen(trim($behaelternr))==0) $fehler = 1;
//
    if(strlen(trim($kunde)==0)) $fehler = 1;
//
    if(strlen(trim($datum)==0))
        $fehler = 1;
    else {
        $datum = $apl->make_DB_datum($datum);
    }
//
    if(strlen(trim($bein)==0)) $fehler = 1;
    if(strlen(trim($bezu)==0)) $fehler = 1;
    if(strlen($lagplatz)==0) $fehler = 1;

    // jen pokud neni zadna chyba, tak budu zapisovat do db
    if($fehler==0)  $ar = $apl->insertBehInv ($behaelternr,$kunde,$datum,$stk,$bein,$bezu,$lagplatz,$ident);


    echo json_encode(array(
                            'id'=>$id,
                            'behaelternr'=>$behaelternr,
                            'kunde'=>$kunde,
                            'datum'=>$datum,
                            'bein'=>$bein,
                            'bezu'=>$bezu,
                            'lagplatz'=>$lagplatz,
                            'fehler'=>$fehler,
                            'ar'=>$ar,
                            'ident'=>$ident,
        ));
?>
