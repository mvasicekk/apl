<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$inventar = $o->i;
$persnr = $o->persnr;
$addparents = $o->addparents;


$a = AplDB::getInstance();
$u = $_SESSION['user'];

$id = intval($inventar->id);
$parent_id = intval($inventar->parent_id);

if($id>0){
    $sql = "insert into dpersinventar (persnr,inventar_id,pozn,vydej_datum,`user`)";
    $sql.=" values(";
    $sql.=" '$persnr'";
    $sql.=" ,'$id'";
    $sql.=" ,''";
    $sql.=" ,'".date('Y-m-d')."'";
    $sql.=" ,'$u'";
    $sql.=" )";
    $insertId = $a->insert($sql);
    //pridat potomky
    if($addparents==TRUE){
	$sql = "select id from inventar where parent_id='$id'";
	$rr = $a->getQueryRows($sql);
	if($rr!==NULL){
	    foreach ($rr as $r){
		$id = $r['id'];
		$sql = "insert into dpersinventar (persnr,inventar_id,pozn,vydej_datum,`user`)";
		$sql.=" values(";
		$sql.=" '$persnr'";
		$sql.=" ,'$id'";
		$sql.=" ,''";
		$sql.=" ,'".date('Y-m-d')."'";
		$sql.=" ,'$u'";
		$sql.=" )";
		$insertId = $a->insert($sql);
	    }
	}
    }
    //pridat rodice
    if($addparents==TRUE){
	$sql = "select id from inventar where id='$parent_id'";
	$rr = $a->getQueryRows($sql);
	if($rr!==NULL){
	    foreach ($rr as $r){
		$id = $r['id'];
		$sql = "insert into dpersinventar (persnr,inventar_id,pozn,vydej_datum,`user`)";
		$sql.=" values(";
		$sql.=" '$persnr'";
		$sql.=" ,'$id'";
		$sql.=" ,''";
		$sql.=" ,'".date('Y-m-d')."'";
		$sql.=" ,'$u'";
		$sql.=" )";
		$insertId = $a->insert($sql);
	    }
	}
    }
}

$returnArray = array(
    'addparents'=>$addparents,
    'persInventarArray'=>$persInventarArray,
    'insertId'=>$insertId,
    'inventar'=>$inventar,
    'id'=>$id,
    'persnr' => $persnr,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
