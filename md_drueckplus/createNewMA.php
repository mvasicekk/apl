<?

// prvni nastartuju session
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$a = AplDB::getInstance();

$u = $_SESSION['user'];

$dpersStatus = 'BEWERBER';

// pro jistotu zkusim zda opravdu toto cislo uz neexistuje
$sql = "select persnr from dpers where persnr='$persnr'";
$rs = $a->getQueryRows($sql);

if ($rs == NULL) {
    //$eintrittsdatum = date('Y-m-d');
    $sql_dpers = "insert into dpers (persnr,name,vorname,dpersstatus,premie_za_vykon,premie_za_kvalitu,premie_za_prasnost,premie_za_3_mesice,regelarbzeit,regeloe)";
    $sql_dpers.=" values(" . $persnr . ",'Name eingeben','Vorname eingeben','" . $dpersStatus . "',0,0,0,0,8,'-')";
    $a->query($sql_dpers);

    $sql_dpers = "insert into dpersdetail1 (persnr)";
    $sql_dpers.=" values(" . $persnr . ")";
    $a->query($sql_dpers);

    $sql_dpers = "insert into durlaub1 (persnr)";
    $sql_dpers.=" values(" . $persnr . ")";
    $a->query($sql_dpers);

    //bewerber table
    $sql_dpers = "insert into dpersbewerber (persnr)";
    $sql_dpers.=" values(" . $persnr . ")";
    $a->query($sql_dpers);
}
else{
    $persnr = NULL;
}



$returnArray = array(
    'u' => $u,
    'persnr'=>$persnr,
    'sql' => $sql,
);

echo json_encode($returnArray);
