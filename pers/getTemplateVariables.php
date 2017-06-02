<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$persnr = intval($o->persnr);

$a = AplDB::getInstance();
$u = $_SESSION['user'];
$persinfo  = $a->getPersInfoArray($persnr);
$persdetail = $a->getPersDetailInfoArray($persnr);
$bewinfo = $a->getBewerberInfoArray($persnr);
$userinfo = $a->getUserInfoArray($u);

function getPInfo($table,$index){
    global $persinfo,$persdetail,$bewinfo;
    
    if($table=='dpers'){
	return $persinfo[0][$index];
    }
    if($table=='dpersdetail'){
	return $persdetail[0][$index];
    }
    if($table=='bewerber'){
	return $bewinfo[0][$index];
    }
}

function getTemplateVariablesArray($persnr) {
global $a, $userinfo;

// jmeno promenne=>array(jmeno indexu z persinfo, popis promenne, hodnota promenne)
$promenneArray = array(
'name' => array('table' => 'dpers', 'pindex' => 'Name', 'label' => 'prijmeni pracovnika', 'value' => ''),
 'vorname' => array('table' => 'dpers', 'pindex' => 'Vorname', 'label' => 'krestni jmeno pracovnika', 'value' => ''),
 'persnr' => array('table' => 'dpers', 'pindex' => 'PersNr', 'label' => 'osobni cislo pracovnika', 'value' => ''),
 'eintritt' => array('table' => 'dpers', 'pindex' => 'eintritt', 'label' => 'datum nastupu pracovnika', 'value' => ''),
 'austritt' => array('table' => 'dpers', 'pindex' => 'austritt', 'label' => 'datum ukonceni prac. pomeru pracovnika', 'value' => ''),
 'geboren' => array('table' => 'dpers', 'pindex' => 'geboren', 'label' => 'datum narozeni pracovnika', 'value' => ''),
 'strasse_op' => array('table' => 'dpersdetail', 'pindex' => 'strasse_op', 'label' => 'trvale bydliste - ulice', 'value' => ''),
 'ort_op' => array('table' => 'dpersdetail', 'pindex' => 'ort_op', 'label' => 'trvale bydliste - mesto', 'value' => ''),
 'plz_op' => array('table' => 'dpersdetail', 'pindex' => 'plz_op', 'label' => 'trvale bydliste - PSC', 'value' => ''),
 'strasse_aktuell' => array('table' => 'dpersdetail', 'pindex' => 'strasse', 'label' => 'dorucovaci bydliste - ulice', 'value' => ''),
 'ort_aktuell' => array('table' => 'dpers', 'pindex' => 'komm_ort', 'label' => 'dorucovaci bydliste - mesto', 'value' => ''),
 'plz_aktuell' => array('table' => 'dpersdetail', 'pindex' => 'plz', 'label' => 'dorucovaci bydliste - psc', 'value' => ''),
 'regeloe' => array('table' => 'dpers', 'pindex' => 'regeloe', 'label' => 'smena = OE', 'value' => ''),
 'geburtsort' => array('table' => 'dpers', 'pindex' => 'gebort', 'label' => 'misto narozeni', 'value' => ''),
 'stat' => array('table' => 'bewerber', 'pindex' => 'staats_angehoerigkeit_id', 'label' => 'statni prislusnost', 'value' => ''),
);

$promenneArray['name']['value'] = getPInfo($promenneArray['name']['table'], $promenneArray['name']['pindex']);
$promenneArray['vorname']['value'] = getPInfo($promenneArray['vorname']['table'], $promenneArray['vorname']['pindex']);
$promenneArray['persnr']['value'] = getPInfo($promenneArray['persnr']['table'], $promenneArray['persnr']['pindex']);
$promenneArray['eintritt']['value'] = date('d.m.Y', strtotime(getPInfo($promenneArray['eintritt']['table'], $promenneArray['eintritt']['pindex'])));
$promenneArray['austritt']['value'] = strlen(trim(getPInfo($promenneArray['austritt']['table'], $promenneArray['austritt']['pindex'])))>0?date('d.m.Y', strtotime(getPInfo($promenneArray['austritt']['table'], $promenneArray['austritt']['pindex']))):' ';
$promenneArray['geboren']['value'] = strlen(trim(getPInfo($promenneArray['geboren']['table'], $promenneArray['geboren']['pindex'])))>0?date('d.m.Y', strtotime(getPInfo($promenneArray['geboren']['table'], $promenneArray['geboren']['pindex']))):' ';
$promenneArray['strasse_op']['value'] = getPInfo($promenneArray['strasse_op']['table'], $promenneArray['strasse_op']['pindex']);
$promenneArray['ort_op']['value'] = getPInfo($promenneArray['ort_op']['table'], $promenneArray['ort_op']['pindex']);
$promenneArray['plz_op']['value'] = getPInfo($promenneArray['plz_op']['table'], $promenneArray['plz_op']['pindex']);
$promenneArray['strasse_aktuell']['value'] = getPInfo($promenneArray['strasse_aktuell']['table'], $promenneArray['strasse_aktuell']['pindex']);
$promenneArray['ort_aktuell']['value'] = getPInfo($promenneArray['ort_aktuell']['table'], $promenneArray['ort_aktuell']['pindex']);
$promenneArray['plz_aktuell']['value'] = getPInfo($promenneArray['plz_aktuell']['table'], $promenneArray['plz_aktuell']['pindex']);
$promenneArray['regeloe']['value'] = getPInfo($promenneArray['regeloe']['table'], $promenneArray['regeloe']['pindex']);
$promenneArray['geburtsort']['value'] = getPInfo($promenneArray['geburtsort']['table'], $promenneArray['geburtsort']['pindex']);

// aktualni datum
$promenna = 'aktdatum';
$popis = 'aktualni datum';
$promenneArray[$promenna] = array('pindex' => '', 'label' => $popis, 'value' => date('d.m.Y'));

// stat
$promenna = 'stat';
$saArray = $a->getStaatFromId(getPInfo($promenneArray[$promenna]['table'], $promenneArray[$promenna]['pindex']));
$promenneArray[$promenna]['value'] = $saArray[0]['staat_name'];

// prihlaseny uzivatel
$promenna = 'user';
$popis = 'jmeno prihlaseneho uzivatele';
$promenneArray[$promenna] = array('pindex' => '', 'label' => $popis, 'value' => $userinfo['realname'], 'edit' => 0);
// skolitel, defaultne nastavim na prihlaseneho uzivatele
$promenna = 'skolitel';
$popis = 'skolitel, vychozi = jmeno prihlaseneho uzivatele';
$promenneArray[$promenna] = array('pindex' => '', 'label' => $popis, 'value' => $userinfo['realname'], 'edit' => 1);

// befristet datum
$promenna = 'befristetdatum';
$popis = 'doba určitá datum';
$value = strlen(trim(getPInfo('dpersdetail', 'dobaurcita')))>0?date('d.m.Y', strtotime(getPInfo('dpersdetail', 'dobaurcita'))):' ';
$promenneArray[$promenna] = array('pindex' => '', 'label' => $popis, 'value' => $value);

// probezeit datum
$promenna = 'probezeitdatum';
$popis = 'zkusebni doba datum';
$value = strlen(trim(getPInfo('dpersdetail', 'zkusebni_doba_dobaurcita')))>0?date('d.m.Y', strtotime(getPInfo('dpersdetail', 'zkusebni_doba_dobaurcita'))):' ';
$promenneArray[$promenna] = array('pindex' => '', 'label' => $popis, 'value' => $value);

ksort($promenneArray);
return $promenneArray;
}

$promenneArray = getTemplateVariablesArray($persnr);

$returnArray = array(
    'variables'=>$promenneArray,
    'persdetail'=>$persdetail,
    'bewerber'=>$bewinfo,
    'userrinfo'=>$userinfo,
    'persnr'=>$persnr,
    'u' => $u,
);

echo json_encode($returnArray);
