<?
session_start();
require_once '../db.php';

// vztahne neje dpos ale i teildoku, mittel (AM/MM), prilohy atd ....
$data = file_get_contents("php://input");
$o = json_decode($data);

$teil = $o->teil;


$a = AplDB::getInstance();

$sql.=" select";
$sql.="     dauftr.auftragsnr,";
$sql.="     di.Aufdat,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.fremdauftr,";
$sql.="     daufkopf.auftragsnr as ex";
$sql.=" from dauftr";
$sql.=" join daufkopf di on di.auftragsnr=dauftr.auftragsnr";
$sql.=" left join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
$sql.=" where ";
$sql.="     (daufkopf.fertig='2100-01-01' or daufkopf.fertig is null)";
$sql.="     and";
$sql.="     (dauftr.KzGut='G')";
$sql.="     and";
$sql.="     (dauftr.teil='$teil')";
$sql.=" group by";
$sql.="     dauftr.auftragsnr,";
$sql.="     di.Aufdat,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.fremdauftr,";
$sql.="     daufkopf.auftragsnr";
$sql.=" order by";
$sql.="     di.Aufdat desc";


$dpos = $a->getQueryRows($sql);

$returnArray = array(
	'teil'=>$teil,
	'importe'=>$dpos,
    );
    
echo json_encode($returnArray);
