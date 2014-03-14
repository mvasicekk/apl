<?

session_start();
require "../../fns_dotazy.php";
require_once '../../db.php';
dbConnect();


// TODO: dodelat validaci parametru

$list = trim($_GET['list']);
$export = trim($_GET['export']);
$import = trim($_GET['import']);

$listArray = explode(',', $list);



header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: nocache');
header('Content-Type: text/xml');


// kontrola hodnot parametru
/////////////////////////////////////////////////////////////////////////////////////
//otestovat, jestli zadany export existuje a neni uz nahodou vyfakturovany

$a = AplDB::getInstance();
mysql_query('set names utf8');

$ident = get_user_pc();

$sql = "select auftragsnr from daufkopf where ((auftragsnr='$export') and (fertig='2100-01-01'))";
$ret = mysql_query($sql);
$pocet_vysledku = mysql_affected_rows();


$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
$output .= '<response>';
$output .= '<affectedrows>';
$output .= $affected_rows;
$output .= '</affectedrows>';
$output .= '<export>' . $export . '</export>';

if ($pocet_vysledku > 0) {

    foreach ($listArray as $idArray) {
	$idGutAussArray = explode(':', $idArray);
	list($id, $gut, $auss2, $auss4, $auss6, $pal, $kzgut) = $idGutAussArray;

	$sql = "update dauftr set `auftragsnr-exp`='$export',`pal-nr-exp`='$pal',`stk-exp`='$gut',auss2_stk_exp='$auss2',auss4_stk_exp='$auss4',auss6_stk_exp='$auss6' where (id_dauftr='$id') limit 1";
	$output.='<idrow>';
	$output.="<id>$id</id>";
	$output.="<gut>$gut</gut>";
	$output.="<auss2>$auss2</auss2>";
	$output.="<auss4>$auss4</auss4>";
	$output.="<auss6>$auss6</auss6>";
	$output.="<pal>$pal</pal>";
	$output.="<kzgut>$kzgut</kzgut>";
	$output.="<sql>$sql</sql>";
	mysql_query($sql);
	$mysqlerror = mysql_error();
	$output.="<mysqlerror>chyba:$mysqlerror</mysqlerror>";
	$output.='</idrow>';

	//dalsi prvky budou pridany do rootu dokumentu
	// zapisy do skladu
	// jen v pripade, ze mam polozku s G

	if ($kzgut == 'G') {
	    //vyber z versandlageru
	    $palStr = strval($pal);
	    $dauftrRow = $a->getDauftrRow($id);
	    $auftragsnr = $dauftrRow['auftragsnr'];
	    $dil = $dauftrRow['teil'];
	    if (substr($palStr, strlen($palStr) - 1) == "7") {
		$a->insertDlagerBew($dil, $auftragsnr, $pal,$gut, 0, '8V', '9V', $ident);
	    }
	    //------------------------------------------------------------------

	    $lVon = '8E';
	    $lNach = '8X';
	    
	    // 2014-02-05
	    // u export fullen uz nepotrebuju protoze to delam u export loeschen a dauftr edit
	    //$a->stornoLastDlagerBewExport($auftragsnr, $pal, $dil, $ident);
	    
	    $a->insertDlagerBew($dil, $auftragsnr, $pal, $gut, 0, "8E", "8X", $ident);
	    // presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
	    $a->insertDlagerBewXXDummy($dil, $auftragsnr, $pal, $ident);
	    // presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
	    $a->moveAussLagerA2B($auftragsnr, $pal, $dil, $ident);
	}
    }
} else {
    $output.="<error>zadana zakazka pro export neexistuje nebo jiz byla vyfakturovana</error>";
}
$output .= '</response>';

echo $output;
?>

