<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

    $id = $_POST['id'];
    $behaelternr = $_POST['behaelternr'];
    $im = $_POST['im'];
    $ex = $_POST['ex'];
    $kundenach = $_POST['kundenach'];
    $kundevon = $_POST['kundevon'];
    $datum = $_POST['datum'];
    $stk = intval($_POST['stk']);
    $zustand = $_POST['zustand'];


    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $ident = get_user_pc();

    $fehler = 0;
    if(strlen(trim($behaelternr))==0) $fehler = 1;
    if(strlen(trim($im)==0)) $im = NULL;
    if(strlen(trim($ex)==0)) $ex = NULL;
    if(strlen(trim($kundevon)==0)) $fehler = 1;
    if(strlen(trim($kundenach)==0)) $fehler = 1;
    if(strlen(trim($datum)==0)) 
        $fehler = 1;
    else {
        $datum = $apl->make_DB_datum($datum);
    }
    if(strlen(trim($zustand)==0)) $fehler = 1;

    $ar = $apl->insertBehaelterBewegung($behaelternr,$im,$ex,$kundevon,$kundenach,$datum,$stk,$zustand,$ident);
    $kundeInfoArray = $apl->getKundeInfoArray($kunde);


    echo json_encode(array(
                            'id'=>$id,
                            'im'=>$im,
                            'ex'=>$ex,
                            'fehler'=>$fehler,
                            'ar'=>$ar,
                            'ident'=>$ident,
        ));
?>
