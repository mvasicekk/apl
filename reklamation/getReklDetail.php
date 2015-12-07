<?
session_start();
require_once '../db.php';

    $inputData = $_GET;
    
    $reklid = $inputData['reklid'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    //informace
    $rekl = $apl->getReklamationenArray($reklid);
    if($rekl!==NULL){
	$rekl = $rekl[0];
	$teilbezeichnungA = $apl->getTeilInfoArray($rekl['teil']);
	$teilbezeichnung = $teilbezeichnungA['Teilbez'];
	$gew = floatval($teilbezeichnungA['Gew']);
	$rekl['teilbezeichnung'] = trim($teilbezeichnung);
	$rekl['teilgew'] = $gew;
	//files
	$rekl['savePath']=$apl->getGdatPath()."".$apl->getKundeGdatPath($rekl['kunde'])."/200 Teile/".$rekl['teil']."/".AplDB::$DIRS_FOR_TEIL_FINAL['100']."/".$rekl['rekl_nr'];
	$files = $apl->getFilesForPath($rekl['savePath']);
	$rekl['files'] = $files;
	//abmahnungen
	$abmahnungen = $apl->getAbmahnungenForReklamation($rekl['id']);
	$rekl['abmahnungen'] = $abmahnungen;
	//pridam si prihlaseneho uzivatele
	$rekl['user'] = $_SESSION['user'];
	//schulungen
	$schulungen = $apl->getSchulungenForReklamation($rekl['id']);
	$rekl['schulungen'] = $schulungen;
	
	// kurs
	$kurs = $apl->getKurs($rekl['rekl_datum'], 'EUR', 'CZK');
	$rekl['kurs_EUR_CZK'] = $kurs;
	
	
	// prepocitat kosten v EUR na CZK
	$rekl['anerkannt_ausschuss_preis_czk'] = $kurs * $rekl['anerkannt_ausschuss_preis_eur'];
	$rekl['anerkannt_nacharbeit_preis_czk'] = $kurs * $rekl['anerkannt_nacharbeit_preis_eur'];
	$rekl['dif_falsch_deklariert_preis_czk'] = $kurs * $rekl['dif_falsch_deklariert_preis_eur'];
	$rekl['verpackung_preis_czk'] = $kurs * $rekl['verpackung_preis_eur'];
	$rekl['kreislauf_preis_czk'] = $kurs * $rekl['kreislauf_preis_eur'];
	$rekl['pauschale_preis_czk'] = $kurs * $rekl['pauschale_preis_eur'];
    }
    
    $returnArray = array(
	"inputData"=>$inputData,
	"rekl"=>$rekl,
    );
    
    echo json_encode($returnArray);
