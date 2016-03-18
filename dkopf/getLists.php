<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$sql = "select * from werkstoffe order by beschreibung";
$werkstoffe = $a->getQueryRows($sql);

$sql = "select dlager.Lager as lager from dlager order by dlager.Lager";
$lager = $a->getQueryRows($sql);

$sql = "select dmittel.* from dmittel order by dmittel.nazev";
$mittel = $a->getQueryRows($sql);

$sql = "select * from dokumenttyp order by doku_nr";
$dokumenttyp = $a->getQueryRows($sql);

$returnArray = array(
	'werkstoffe'=>$werkstoffe,
	'lager'=>$lager,
	'mittelList'=>$mittel,
	'dokumenttyp'=>$dokumenttyp,
    );
    
echo json_encode($returnArray);
