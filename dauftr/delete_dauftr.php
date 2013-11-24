<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$dpos_id = $_GET['id'];
	$kzgut = $_GET['kzgut'];
	
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	
	
	// musim odmazat i zaznam ze skladu v pripade, ze jde o operaci G
	// a zaroven v pripade, ze jde o G smaznu celou paletu
	
	$dauftrRow = getDauftrRowFromId($dpos_id);
	if($dauftrRow['KzGut']=='G')
	{
		$auftrag=$dauftrRow['auftragsnr'];
		$pal=$dauftrRow['pos-pal-nr'];
		$teil=$dauftrRow['teil'];
		$sql_delete = "delete from dlagerbew where ((auftrag_import='$auftrag') and (pal_import='$pal') and (teil='$teil') and (lager_von='0'))";
		mysql_query($sql_delete);
		// mazu paletu z auftragu
		$sql_delete = "delete from dauftr where ((auftragsnr='$auftrag') and (`pos-pal-nr`='$pal') and (teil='$teil')) limit 10";
		mysql_query($sql_delete);
	}
	
	$sql="delete from dauftr where (id_dauftr='$dpos_id') limit 1;";
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<sql_delete>';
	$output .= "sqldel".$sql_delete;
	$output .= '</sql_delete>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';
	
	$output .= '</response>';
	
	echo $output;
	
?>

