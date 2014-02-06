<?
session_start();
require "../../fns_dotazy.php";
require_once "../../db.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$list = trim($_GET['list']);
	//$export = trim($_GET['export']);

	$listArray = explode(',',$list);


 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	// limit 1 je pro jistotu, kdyby se pokazilo kriterium ve where
	
	//$sql="update daufkopf set `bestellnr`='".$bestellnr."'";
	//$sql.=",`Aufdat`='".$aufdat."', `ex_datum_soll`='".$ex_datum_soll."'";
	//$sql.=" where (AuftragsNr='".$auftragsnr."') limit 1";
	
	//$result=mysql_query($sql);
	//$affected_rows=mysql_affected_rows();
	//$mysql_error=mysql_error();
	 $output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<export>'.$export.'</export>';
	
	$ident = get_user_pc();
	$a = AplDB::getInstance();
	
	foreach($listArray as $idArray)
	{
	    
	    //------------------------------------------------------------------
		$idGutAussArray = explode(':',$idArray);
		list($auftragsnr,$pal) = $idGutAussArray;

		$sql="update dauftr set `auftragsnr-exp`=null,`pal-nr-exp`=null,`stk-exp`='0',auss2_stk_exp='0',auss4_stk_exp='0',auss6_stk_exp='0' where ((auftragsnr='$auftragsnr') and (`pos-pal-nr`='$pal'))";
		$output.='<idrow>';
		$output.="<auftragsnr>$auftragsnr</auftragsnr>";
		$output.="<pal>$pal</pal>";
		$output.="<sql>$sql</sql>";
		mysql_query($sql);
		$mysqlerror=mysql_error();
		$output.="<mysqlerror>chyba:$mysqlerror</mysqlerror>";
		$output.='</idrow>';
		//dalsi prvky budou pridany do rootu dokumentu

		// smazat zaznam z versand lagru
		$sql_delete= "delete from dlagerbew where auftrag_import='$auftragsnr' and pal_import=$pal and lager_von='8V' and lager_nach='9V'";
		mysql_query($sql_delete);
		// zjistit cislo dilu podle importu a palety
		$dauftrRow = $a->getDauftrRow($a->getDauftrIdGPal1($auftragsnr, $pal));
		$dil = $dauftrRow['teil'];
		// storno v dlagerbew
		$a->stornoLastDlagerBewExport($auftragsnr, $pal, $dil, $ident);
	}

	$output .= '</response>';
	
	echo $output;
	
?>

