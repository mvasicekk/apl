<?
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$persnr = $o->persnr;

$a = AplDB::getInstance();


$sql = "select dpersinventar.*";
$sql.=" ,inventar.cislo as inventar_cislo";
$sql.=" ,inventar.popis as inventar_popis";
$sql.=" ,inventartyp.typ as inventar_typ";
$sql.=" from dpersinventar ";
$sql.=" join inventar on inventar.id=dpersinventar.inventar_id";
$sql.=" left join inventartyp on inventartyp.id=inventar.typinventare_id";
$sql.=" where persnr='$persnr'";
$sql.=" order by ";
$sql.=" dpersinventar.vydej_datum desc";
$sql.=" ,inventartyp.typ asc";

$persInventarArray = $a->getQueryRows($sql);


$returnArray = array(
    'persnr'=>$persnr,
    'persInventarArray' => $persInventarArray,
    'sql' => $sql
);

echo json_encode($returnArray);

