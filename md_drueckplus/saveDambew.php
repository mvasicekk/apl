<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$amnr = $o->amnr;

$datum = date('Y-m-d',strtotime($o->datum));
$persnr = intval($o->persnr);
$oe = $o->oe->tat;
$amnr = trim($o->amnr);
$sklad = $o->sklad->cislo;
$ausgabe = intval($o->ausgabe);
$ruckgabe = intval($o->ruckgabe);
$bemerkung = trim($o->bemerkung);

$amnrinfo = NULL;

$a = AplDB::getInstance();
$p = sqldb::getInstance();


$u = $a->get_user_pc();

$aplDambewTable = "dambew_test";

$sqlInsert = "insert into `$aplDambewTable` (PersNr,Datum,oe,AMNr,amnr_typ,AusgabeStk,RueckgabeStk,Bemerkung,comp_user_accessuser,insert_stamp)";
$sqlInsert.=" values('$persnr','$datum','$oe','$amnr','1','$ausgabe','$ruckgabe','$bemerkung','$u','".date('Y-m-d H:i:s')."')";

$insertId = $a->insert($sqlInsert);

// a jeste vlozit do premiera, sklad 99 je majetek a ten do premiera neposilam
if (intval($sklad) != 99) {
    $bemerk1250 = iconv('UTF-8', 'windows-1250', $bemerkung);
    $sqlPremier = "INSERT INTO apl_am_pohyb ( cislo,sklad,pocet_vydane,oscislo,datum,poznamka,insert_stamp )";
    $sqlPremier.= " values ( '$amnr','$sklad','$ausgabe','$persnr','$datum','$bemerk1250','" . date('Y-m-d H:i:s') . "')";
    $p->exec($sqlPremier);
}


$returnArray = array(
    'datum' => $datum,
    'persnr' => $persnr,
    'oe' => $oe,
    'amnr' => $amnr,
    'sklad' => $sklad,
    'ausgabe' => $ausgabe,
    'ruckgabe' => $ruckgabe,
    'bemerkung' => $bemerkung,
    'u'=>$u,
    'o' => $o,
    'sqlInsert' => $sqlInsert,
    'sqlPremier' => $sqlPremier,
    'insertId'=>$insertId,
);

echo json_encode($returnArray);
