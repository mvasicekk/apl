<?
require_once '../db.php';

    $inputData = $_GET;
    
    $reklid = $inputData['reklid'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $rekl = $apl->getReklamationenArray($reklid);
    if($rekl!==NULL){
	$rekl = $rekl[0];
	$rekl['savePath']=$apl->getGdatPath()."".$apl->getKundeGdatPath($rekl['kunde'])."/200 Teile/".$rekl['teil']."/".AplDB::$DIRS_FOR_TEIL_FINAL['100']."/".$rekl['rekl_nr'];
	$files = $apl->getFilesForPath($rekl['savePath']);
	$rekl['files'] = $files;
    }
    
    $returnArray = array(
	"inputData"=>$inputData,
	"rekl"=>$rekl,
    );
    
    echo json_encode($returnArray);
