<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$keyword = $o->params->a;
$teile = NULL;

$a = AplDB::getInstance();
if(strlen($keyword)>2)
{
    $sql = "select * from dkopf where ((teilbez regexp '.*".$keyword.".*') or (teil regexp '.*".$keyword.".*') or (teillang regexp '.*".$keyword.".*') or (fremdauftr_dkopf regexp '.*".$keyword.".*')) order by kunde,teil limit 100";
    $teile = $a->getQueryRows($sql);
}
			
$returnArray = array(
	'teile'=>$teile,
	'teil_search'=>$keyword,
    );
    
echo json_encode($returnArray);
