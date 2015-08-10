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
	$rekl['teilbezeichnung'] = trim($teilbezeichnung);
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
    }
    
    $returnArray = array(
	"inputData"=>$inputData,
	"rekl"=>$rekl,
    );
    
    echo json_encode($returnArray);
