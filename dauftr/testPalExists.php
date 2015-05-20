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
}
    

$retArray = array(
    'import'=>$import,
    'impal'=>$impal,
    'exists'=>$exists
);


echo json_encode($retArray);