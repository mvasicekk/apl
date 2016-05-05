<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$import = intval(trim($_POST['import']));
$impal = intval(trim($_POST['pal2']));

$exists = FALSE;
$dauftrArray = $a->getDauftrRowsForImportPal($import, $impal);
if($dauftrArray!=NULL){
    $exists = TRUE;
    $hatRechnung = strlen(trim($dauftrArray[0]['ex']))>0?TRUE:FALSE;
}
    

$retArray = array(
    'import'=>$import,
    'impal'=>$impal,
    'exists'=>$exists,
    'hatRechnung'=>$hatRechnung,
);


echo json_encode($retArray);