<?
session_start();
require_once '../db.php';
require_once '../sqldb.php'; '';

$data = file_get_contents("php://input");
$o = json_decode($data);
$params = $o->params;
$search = $params->e;
$persnr = intval($params->persnr);

$sqlDB = sqldb::getInstance();
$a = AplDB::getInstance();

// vsechen majetek, drobny hmotny = DH
$ispSql = " SELECT INTER,CISLO,DRUH,POPIS,TEXT_2,CENA,DATUM_P,UMISTENI,POZNAMKA,STKOD,ZKKOD,DATUM_V,IDEN1,JMENO_ODP,VYRCISLO,CENA_KS,DOKLAD FROM MAJETEK";
$ispSql.=" WHERE (DOKLAD='DH') AND (IDEN5='1') AND (CISLO LIKE '%$search%' OR POPIS LIKE '%$search%' OR TEXT_2 LIKE '%$search%') ORDER BY CISLO";
$veskeryMajetek = array(); //klic bude inventarni cislo
$nevydanyMajetek = array();
$majetekStrediska = array();
$vydanyMajetek = array();

if(strlen(trim($search))>0){
    $res = $sqlDB->getResult($ispSql);
    if($res!==NULL){
	foreach ($res as $r1){
	    $invnr = trim($r1['CISLO']);
	    $stkod = trim(intval($r1['STKOD']));
	    //seznam roli pro stredisko
	    $rA = $a->getRolesForStredisko($stkod);
	    $veskeryMajetek[$invnr] = $r1;
	    $veskeryMajetek[$invnr]['roles'] = $rA;
	}
    }
    // projit a odebrat polozky, ktere ma nekdo vydane
    // vydane inventarni cisla , vydano znamena suma AussgabeStk > RueckgabeStk
    $s = " select ";
    $s.= " dambew.invnr,";
    $s.= " sum(dambew.AusgabeStk) as sum_aus_stk,";
    $s.= " sum(dambew.RueckgabeStk) as sum_rueck_stk";
    $s.= " from dambew";
    $s.= " where ";
    $s.= " dambew.invnr>0";
    $s.= " group by";
    $s.= " dambew.invnr";
    $s.= " having sum_aus_stk>sum_rueck_stk";
    $s.= " order by dambew.invnr";
    $rs = $a->getQueryRows($s);
    if($rs!==NULL){
	foreach ($rs as $r){
	    $invnr = $r['invnr'];
	    //zkusim inventarni cislo najit v poli s veskerym majetkem
	    unset($veskeryMajetek[$invnr]);
	}
    }
}

$u = $_SESSION['user'];
// seznam roli pro prihlaseneho uzivatele
$userRolesArray = $a->getUserRolesArray($u);
$userRoles = array();
if($userRolesArray!==NULL){
    foreach ($userRolesArray as $ur){
	array_push($userRoles, $ur['role_id']);
    }
}

foreach ($veskeryMajetek as $m){
    // a jeste omezeni podle roli a stredisek
    $roleProMajetek = $m['roles'];
    if(is_array($roleProMajetek)){
	// majetek ma nejake role, ktere ho mohou vydavat
	foreach ($roleProMajetek as $rpm){
	    //zkusit jestli najdu roli v seznamu roli ktere jsou prirazeny uzivateli
	    if(in_array($rpm, $userRoles)){
		array_push($nevydanyMajetek, $m);
		break;
	    }
	}
    }
}




if($persnr>0){
    //dostal jsem i hodnotu osobniho cisla, zjistim jaky majetek ma persnr prirazeny
    //vydany majetek
    $s = " select ";
    $s.= " dambew.invnr,";
    $s.= " sum(dambew.AusgabeStk) as sum_aus_stk,";
    $s.= " sum(dambew.RueckgabeStk) as sum_rueck_stk";
    $s.= " from dambew";
    $s.= " where ";
    $s.= " dambew.invnr>0";
    $s.= " group by";
    $s.= " dambew.invnr";
    $s.= " having sum_aus_stk>sum_rueck_stk";
    $s.= " order by dambew.invnr";
    $rs = $a->getQueryRows($s);
    if($rs!==NULL){
	foreach ($rs as $r){
	    $invnr = $r['invnr'];
	    $vydanyMajetek[$invnr] = intval($r['sum_aus_stk'])-intval($r['sum_rueck_stk']);
	}
    }
    
    $sql = "";
    $sql.=" select ";
    $sql.=" dambew.*";
    $sql.=" from dambew";
    $sql.=" where";
    $sql.=" PersNr='$persnr'";
    $sql.=" and invnr>0";
    $sql.=" order by datum desc,amnr asc";
    $majetekPersArray = $a->getQueryRows($sql);
    //pridat info, jestli dane inventarni cislo muzu vratit
    if($majetekPersArray!==NULL){
	foreach ($majetekPersArray as $index=>$mpa){
	    $invnr = $mpa['invnr'];
	    $canReturn = array_key_exists($invnr, $vydanyMajetek)?TRUE:FALSE;
	    //popis majetku
	    $popisMajetku = "";
	    $ispSql = " SELECT POPIS,TEXT_2 FROM MAJETEK";
	    $ispSql.=" WHERE (CISLO='$invnr')";
	    $res = $sqlDB->getResult($ispSql);
	    if($res!==NULL){
		$popisMajetku = $res[0]['POPIS'].' '.$res[0]['TEXT_2'];
	    }
	    $majetekPersArray[$index]['canreturn'] = $canReturn;
	    $majetekPersArray[$index]['popismajetku'] = $popisMajetku;
	}
    }
}

$returnArray = array(
    'veskeryMajetek'=>$veskeryMajetek,
//    'majetekArrayPocet'=>count($res),
    //'userRolesArray'=>$userRolesArray,
    'userRoles'=>$userRoles,
//    'vydanyMajetek'=>$vydanyMajetek,
    'majetekArrayBezVydanych'=>$nevydanyMajetek,
    'majetekArrayPocetBezVydanych'=>count($nevydanyMajetek),
    'majetekPersArray'=>$majetekPersArray,
    'u'=>$u,
    'params'=>$params,
    'search'=>$search,
);

echo json_encode($returnArray);

