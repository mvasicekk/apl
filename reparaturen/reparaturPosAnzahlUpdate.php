<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';
require_once '../fns_dotazy.php';

    $id = $_POST['id'];
    $value = floatval($_POST['value']);
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $reparaturID = substr($id, strlen('etpos')+1, strpos($id, '_', strlen('etpos')+1)-strlen('etpos_'));
    $artnr = substr($id, strlen('etpos_')+strlen($reparaturID)+1);
    $apl = AplDB::getInstance();
    $p = sqldb::getInstance();
    
    $user = get_user_pc();
    $ar = $apl->updateReparaturPosAnzahl($reparaturID, $artnr, $value, $user);

    // 2016-06-08 odepisovani z premiera
    // vzdy ze skladu c. 5
    $sklad = 5;
    $amnr = "$artnr";
    $bemerkung = "HF_reparatur_$reparaturID";
    // osobni cislo bude persnr cloveka, ktery stroj prinesl do opravy
    // potrebuju hlavicku opravy
    $repKopfArray = $apl->getReparaturKopfArrayFromID($reparaturID);
    $persnr = $repKopfArray['persnr_ma']; // nicitel
    $datum = date('Y-m-d',  strtotime($repKopfArray['datum']));	//datum opravy
    // pocet vydanych kusu musim spocitat
    $ausstk = 0;
    // kolik kusu daneho dilu v dane reparaturID jsem uz vydal
    $sqlPremSelect = "select sum(pocet_vydane) as vydano from apl_am_pohyb where poznamka='HF_reparatur_$reparaturID' and cislo='$amnr'";
    $res = $p->getResult($sqlPremSelect);
    if($res!==NULL){
	foreach ($res as $r){
	    $vydano = intval($r['vydano']);
	    $ausstk += $vydano;
	}
    }
    
    // musim se dostat na hodnotu $value tj.
    // napr. v pohybech mam 2, zmenil jsem hodnotu na 0, tj. musim vlozit -2, abych se dostal na nulu
    $ausstk = $value - $ausstk;
    // vlozim i do premiera s pevnym cislem skladu 11 ( vydeje pasu kemper
    
    //$bemerk1250 = iconv('UTF-8', 'windows-1250', $bemerkung);
    $sqlPremier = "INSERT INTO apl_am_pohyb ( cislo,sklad,pocet_vydane,oscislo,datum,poznamka,insert_stamp )";
    $sqlPremier.= " values ( '$amnr',$sklad,'$ausstk','$persnr','".$datum."','$bemerkung','" . date('Y-m-d H:i:s') . "')";
    if(intval($ausstk)!=0){
	$p->exec($sqlPremier);
    }
    
    
    echo json_encode(array(
                            'id'=>$id,
                            'value'=>$value,
                            'artnr'=>$artnr,
                            'reparaturID'=>$reparaturID,
                            'ar'=>$ar,
			    'repKopfArray'=>$repKopfArray,
			    'sqlPremier'=>$sqlPremier,
			    'sqlPremSelect'=>$sqlPremSelect,
        ));

?>
