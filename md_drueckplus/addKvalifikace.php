<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$pa = NULL;
$persnr = $o->persnr;
$oekvalifikace = $o->oekvalifikace;
$hodnoceni = intval(trim($o->hodnoceni));
$poznamka = trim($o->poznamka);
$giltab = strtotime($o->giltab);
if($giltab!==FALSE){
    $giltab = date('Y-m-d',$giltab);
}

$k = $o->k;

$a = AplDB::getInstance();
$u = $_SESSION['user'];

//$id = intval($inventar->id);


if($k!==NULL){
    //smazani existujuciho
    $idDel = intval($k->id);
    $sql = "delete from dpersoekvalifikace where id='$idDel'";
    $a->query($sql);
    $delRows = 1;
}
else{
// vlozeni noveho 
    $sql = "insert into dpersoekvalifikace (persnr,oe,bewertung,`user`,gilt_ab,poznamka)";
    $sql.=" values(";
    $sql.=" '$persnr'";
    $sql.=" ,'$oekvalifikace'";
    $sql.=" ,'$hodnoceni'";
    $sql.=" ,'$u'";
    $sql.=" ,'$giltab'";
    $sql.=" ,'$poznamka'";
    $sql.=" )";
    //nejake testy
    if(($hodnoceni>=6 || $hodnoceni<=9)&&($giltab!==FALSE)){
	$insertId = $a->insert($sql);
    }
}

$returnArray = array(
    'k'=>$k,
    'idDel'=>$idDel,
    'delRows'=>$delRows,
    'pa'=>$pa,
    'insertId'=>$insertId,
    'id'=>$id,
    'persnr' => $persnr,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
